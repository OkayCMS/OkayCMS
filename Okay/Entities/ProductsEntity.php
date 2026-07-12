<?php

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Entity\RelatedProductsInterface;
use Okay\Core\Money;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Translit;

class ProductsEntity extends Entity implements RelatedProductsInterface
{
    protected static $fields = [
        'id',
        'url',
        'brand_id',
        'visible',
        'position',
        'created',
        'featured',
        'external_id',
        'rating',
        'votes',
        'last_modify',
        'main_category_id',
        'main_image_id',
    ];

    protected static $langFields = [
        'name',
        'annotation',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'special',
    ];

    protected static $additionalFields = [
        'r.slug_url',
    ];

    protected static $defaultOrderFields = [
        'p.position DESC',
    ];

    protected static $table = '__products';
    protected static $langObject = 'product';
    protected static $langTable = 'products';
    protected static $tableAlias = 'p';
    protected static $alternativeIdField = 'url';

    public function get($id)
    {
        if (empty($id)) {
            $this->flush();
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=p.url AND r.type="product"');
        return parent::get($id);
    }

    public function find(array $filter = [])
    {
        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=p.url AND r.type="product"');

        return parent::find($filter);
    }

    public function getSelect(array $filter = [])
    {
        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=p.url AND r.type="product"');

        return parent::getSelect($filter);
    }

    public function update($ids, $object)
    {
        $res = parent::update($ids, $object);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();
        return $res;
    }

    public function add($product)
    {
        $product = (object) $product;
        /** @var object{name: string, url: string|null, created: mixed, last_modify: mixed}&\stdClass $product */
        if (empty($product->url)) {
            $product->url = Translit::translit($product->name);
            $product->url = str_replace('.', '', $product->url);

            while ($url = $this->cols(['url'])->findOne(['url' => $product->url])) {
                if (preg_match('/(.+)?_([0-9]+)$/', $url, $parts)) {
                    $product->url = $parts[1] . '_' . ($parts[2] + 1);
                } else {
                    $product->url .= '_1';
                }
            }
        }

        $product->created     = 'NOW()';
        $product->last_modify = 'NOW()';

        return parent::add($product);
    }

    public function delete($ids)
    {
        if (empty($ids)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete->from($this->getTable())->where('id IN (:ids)');
        $delete->bindValue('ids', $ids);
        $this->db->query($delete);

        if (!empty($this->getLangTable()) && !empty($this->getLangObject())) {
            $delete = $this->queryFactory->newDelete();
            $delete->from($this->getLangTable())->where($this->getLangObject() . '_id IN (:lang_object_ids)');
            $delete->bindValue('lang_object_ids', $ids);
            $this->db->query($delete);
        }

        $variantsEntity = $this->entity->get(VariantsEntity::class);
        $variantsIds = $variantsEntity->cols(['id'])->find(['product_id' => $ids]);
        $variantsEntity->delete($variantsIds);

        $imagesEntity = $this->entity->get(ImagesEntity::class);
        $imagesIds = $imagesEntity->cols(['id'])->find(['product_id' => $ids]);
        $this->unlinkImageFiles($ids, $imagesIds);
        $imagesEntity->delete($imagesIds);

        $categoriesEntity = $this->entity->get(CategoriesEntity::class);
        $categoriesEntity->deleteProductCategory($ids);

        $featuresValuesEntity = $this->entity->get(FeaturesValuesEntity::class);
        $featuresValuesEntity->deleteProductValue($ids);

        $this->deleteRelatedProducts($ids);

        $commentsEntity = $this->entity->get(CommentsEntity::class);
        $commentsIds = $commentsEntity->cols(['id'])->find(['type' => 'product', 'object_id' => $ids]);
        $commentsEntity->delete($commentsIds);

        $this->forgetProductReferencesByProductsIds($ids);

        $this->updateLastModify($ids);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    private function unlinkImageFiles($productsIds, $imagesIds) // todo нужно ли?
    {
        if (empty($productsIds) || empty($imagesIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $select = $this->queryFactory->newSelect();
        $select->cols(['filename'])
            ->from('__images')
            ->where('id IN (:images_ids)')
            ->where('product_id IN (:products_ids)')
            ->bindValue('images_ids', $imagesIds)
            ->bindValue('products_ids', $productsIds);
        $this->db->query($select);
        $candidatesToDelete = $this->getResults('filename');

        $filesUsesInOtherProducts = [];
        if (!empty($candidatesToDelete)) {
            $select = $this->queryFactory->newSelect();
            $select->cols(['filename'])
                ->from('__images')
                ->where('filename IN (:images_filenames)')
                ->where('product_id NOT IN (:products_ids)')
                ->bindValue('images_filenames', $candidatesToDelete)
                ->bindValue('products_ids', $productsIds);
            $this->db->query($select);
            $filesUsesInOtherProducts = $this->getResults('filename');
        }

        $toDeleteFiles = array_diff($candidatesToDelete, $filesUsesInOtherProducts);
        foreach ($toDeleteFiles as $file) {
            @unlink($this->config->root_dir . $this->config->original_images_dir . $file);
            $this->removeAllResizes($file);
        }
    }

    private function removeAllResizes($file)
    {
        $parts = explode('.', $file);
        $ext = end($parts);

        array_pop($parts);
        $filenameWithoutExt = implode('.', $parts);

        $pattern = $this->config->root_dir . $this->config->resized_images_dir . $filenameWithoutExt . ".*x*." . $ext;
        $resizedImages = glob($pattern);

        if (!is_array($resizedImages)) {
            return;
        }

        foreach ($resizedImages as $resizedImage) {
            @unlink($resizedImage);
        }

        $webpPattern = $this->config->root_dir . $this->config->resized_images_dir . $filenameWithoutExt . '.*x*.' . $ext . '.webp';
        $resizedImagesWebp = glob($webpPattern);
        if (is_array($resizedImagesWebp)) {
            foreach ($resizedImagesWebp as $f) {
                @unlink($f);
            }
        }
    }

    private function forgetProductReferencesByProductsIds($productsIds)
    {
        if (empty($productsIds)) {
            return false;
        }

        $productsIds = (array) $productsIds;

        $update = $this->queryFactory->newUpdate();
        $update->table('__purchases')
            ->set('product_id', null)
            ->where('product_id IN (:products_ids)')
            ->bindValue('products_ids', $productsIds);
        $this->db->query($update);
        return true;
    }

    private function updateLastModify($productsIds)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['brand_id'])
            ->from('__products')
            ->where('id IN(:products_ids)')
            ->bindValue('products_ids', $productsIds);
        $this->db->query($select);
        $brandsIds = $this->db->results('brand_id');

        if (empty($brandsIds)) {
            return false;
        }

        $update = $this->queryFactory->newUpdate();
        $update->set('last_modify', 'NOW()')
            ->where('id IN(:brands_ids)')
            ->bindValue('brands_ids', $brandsIds);

        return true;
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function getPriceRange(array $filter = [])
    {
        /** @var Money $money */
        $money = $this->serviceLocator->getService(Money::class);
        $coef = $money->getCoefMoney();

        $this->setUp();

        $this->buildFilter($filter);
        $this->select->cols([
            "floor(min(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})) as min",
            "ceil(max(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})) as max",
        ]);

        $this->select->join('LEFT', '__variants AS pv', 'pv.product_id = p.id');
        $this->select->join('LEFT', '__currencies AS c', 'c.id=pv.currency_id');

        $this->select->resetGroupBy();
        $this->select->resetOrderBy();

        $this->db->query($this->select);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->getResult(), func_get_args());
    }

    public function getRelatedProducts(array $filter = [])
    {
        if (empty($filter['product_id'])) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
        }

        $select = $this->queryFactory->newSelect();
        $select->cols([
            'product_id',
            'related_id',
            'position',
        ])->from('__related_products')
            ->where('product_id IN (:products_ids)')
            ->orderBy(['position'])
            ->bindValue('products_ids', (array)$filter['product_id']);

        $this->db->query($select);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->db->results(), func_get_args());
    }

    /*Добавление связанных товаров*/
    public function addRelatedProduct($productId, $relatedId, $position = 0)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('__related_products')
            ->cols([
                'product_id',
                'related_id',
                'position',
            ])
            ->bindValues([
                'product_id' => $productId,
                'related_id' => $relatedId,
                'position' => $position,
            ])
            ->ignore();

        $this->db->query($insert);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $relatedId, func_get_args());
    }

    /*Удаление связанных товаров*/
    public function deleteRelatedProduct($productId, $relatedId = null)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__related_products')
            ->where('product_id=:product_id')
            ->bindValue('product_id', (int)$productId);

        if ($relatedId !== null) {
            $delete->where('related_id=:related_id')
                ->bindValue('related_id', (int)$relatedId);
        }
        $this->db->query($delete);

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function deleteRelatedProducts($productsIds, $relatedIds = null)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__related_products')
            ->where('product_id IN(:products_ids)')
            ->bindValue('products_ids', (array)$productsIds);

        if ($relatedIds === null) {
            $this->db->query($delete);
            ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $delete->where('related_id IN(:related_ids)')
            ->bindValue('related_ids', (int)$relatedIds);
        $this->db->query($delete);
        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function getNeighborsProducts($categoryId, $position)
    {
        $pIds = [];
        // предыдущий товар
        $select = $this->queryFactory->newSelect();
        $select->from('__products p')
            ->cols(['id'])
            ->join('left', '__products_categories pc', 'pc.product_id=p.id')
            ->where('p.position>:position')
            ->where('pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)')
            ->where('pc.category_id=:category_id')
            ->where('p.visible')
            ->orderBy(['p.position ASC'])
            ->limit(1)
            ->bindValues([
                'position' => $position,
                'category_id' => $categoryId,
            ]);

        $this->db->query($select);
        $pid = $this->db->result('id');
        if (is_scalar($pid) && $pid !== '') {
            $pIds[(string)$pid] = 'prev';
        }

        // следующий товар
        $select = $this->queryFactory->newSelect();
        $select->from('__products p')
            ->cols(['id'])
            ->join('left', '__products_categories pc', 'pc.product_id=p.id')
            ->where('p.position<:position')
            ->where('pc.position=(SELECT MIN(pc2.position) FROM __products_categories pc2 WHERE pc.product_id=pc2.product_id)')
            ->where('pc.category_id=:category_id')
            ->where('p.visible')
            ->orderBy(['p.position DESC'])
            ->limit(1)
            ->bindValues([
                'position' => $position,
                'category_id' => $categoryId,
            ]);

        $this->db->query($select);
        $pid = $this->db->result('id');
        if (is_scalar($pid) && $pid !== '') {
            $pIds[(string)$pid] = 'next';
        }

        $result = ['next' => '', 'prev' => ''];
        if (!empty($pIds)) {
            foreach ($this->find(array('id' => array_keys($pIds))) as $p) {
                /** @var object{id: int|string} $p */
                $productId = (string)$p->id;
                if (isset($pIds[$productId])) {
                    $result[$pIds[$productId]] = $p;
                }
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    public function duplicate($productId)
    {

        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entity->get(BrandsEntity::class);

        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->entity->get(CategoriesEntity::class);

        /** @var ImagesEntity $imagesEntity */
        $imagesEntity = $this->entity->get(ImagesEntity::class);

        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entity->get(FeaturesValuesEntity::class);

        $productId = (int)$productId;
        $product = $this->findOne(['id' => $productId]);
        if ($product === false) {
            return false;
        }

        /** @var object{position: int|string|float}&\stdClass $product */

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $product->position;

        $newProduct = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (!empty($field) && property_exists($product, $field)) {
                $newProduct->$field = $product->$field;
            }
        }

        $newProduct->id = null;
        $newProduct->url = '';
        $newProduct->meta_title = '';
        $newProduct->meta_keywords = '';
        $newProduct->meta_description = '';
        $newProduct->external_id = '';
        unset($newProduct->created);

        $newProductId = $this->add($newProduct);

        // Сдвигаем товары вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__products')
            ->set('position', 'position-1')
            ->where('position<=:position')
            ->bindValue('position', $product->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__products')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newProductId,
            ]);
        $this->db->query($update);

        //lastModify
        if (!empty($product->brand_id)) {
            $brandsEntity->update($product->brand_id, ['last_modify' => 'now()']);
        }

        // Дублируем категории
        $categories = $categoriesEntity->getProductCategories($productId);
        foreach ($categories as $i => $c) {
            $categoriesEntity->addProductCategory($newProductId, $c->category_id, $i);
        }

        // Дублируем изображения
        $imagesIds = [];
        $images = $imagesEntity->find(['product_id' => $productId]);
        foreach ($images as $image) {
            $image->id = null;
            $image->product_id = $newProductId;
            $imagesIds[] = $imagesEntity->add($image);
        }

        $mainInfo = [];
        if (!empty($imagesIds)) {
            $mainInfo['main_image_id'] = reset($imagesIds);
        }
        if (!empty($categories)) {
            $mainInfo['main_category_id'] = reset($categories)->category_id;
        }

        if (!empty($mainInfo)) {
            $this->update($newProductId, $mainInfo);
        }

        // Дублируем варианты
        $variants = $variantsEntity->find(['product_id' => $productId]);
        foreach ($variants as $variant) {
            /** @var object{product_id: int|string, stock: mixed, infinity?: mixed, sku?: mixed, id?: mixed, rate_from?: mixed, rate_to?: mixed, external_id: mixed}&\stdClass $variant */
            $variant->product_id = $newProductId;
            unset($variant->sku);
            unset($variant->id);
            if (isset($variant->infinity)) {
                $variant->stock = null;
            }
            unset($variant->infinity);
            unset($variant->rate_from);
            unset($variant->rate_to);
            $variant->external_id = '';
            $variantsEntity->add($variant);
        }

        // Дублируем значения свойств
        $values = $featuresValuesEntity->getProductValuesIds([$productId]);
        foreach ($values as $value) {
            /** @var object{value_id: int|string} $value */
            $featuresValuesEntity->addProductValue($newProductId, $value->value_id);
        }

        // Дублируем связанные товары
        $related = $this->getRelatedProducts(['product_id' => $productId]);
        foreach ($related as $r) {
            $this->addRelatedProduct($newProductId, $r->related_id, $r->position);
        }

        $this->multiDuplicateProduct($productId, $newProductId);
        ExtenderFacade::execute([static::class, __FUNCTION__], $newProductId, func_get_args());
    }

    private function multiDuplicateProduct($productId, $newProductId)
    {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            /** @var VariantsEntity $variantsEntity */
            $variantsEntity = $this->entity->get(VariantsEntity::class);

            $languages = $langEntity->find();
            $productLangFields = $this->getLangFields();
            $variantLangFields = $variantsEntity->getLangFields();
            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);
                    //Product
                    if (!empty($productLangFields)) {
                        $sourceProduct = $this->get($productId);
                        $destinationProduct = new \stdClass();
                        foreach ($productLangFields as $field) {
                            if (in_array($field, ['meta_title', 'meta_keywords', 'meta_description'])) {
                                continue;
                            }
                            $destinationProduct->{$field} = $sourceProduct->{$field};
                        }
                        $this->update($newProductId, $destinationProduct);
                    }

                    // Дублируем варианты
                    if (!empty($variantLangFields)) {
                        $variants = $variantsEntity->find(['product_id' => $newProductId]);
                        $sourceVariants = $variantsEntity->find(['product_id' => $productId]);
                        foreach ($sourceVariants as $i => $sourceVariant) {
                            $destinationVariant = new \stdClass();
                            foreach ($variantLangFields as $field) {
                                $destinationVariant->{$field} = $sourceVariant->{$field};
                            }
                            $variantsEntity->update($variants[$i]->id, $destinationVariant);
                        }
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }

    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {
        /** @var Money $money */
        $money = $this->serviceLocator->getService(Money::class);
        $coef = $money->getCoefMoney();

        switch ($order) {
            case 'price':
                $orderFields = [
                    "(SELECT -floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*$coef)
                    FROM __variants pv
                    LEFT JOIN __currencies c on c.id=pv.currency_id
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) DESC"
                ];
                break;
            case 'price_desc':
                $orderFields = [
                    "(SELECT -floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*$coef)
                    FROM __variants pv
                    LEFT JOIN __currencies c on c.id=pv.currency_id
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) ASC"
                ];
                break;
            case 'stock':
                $orderFields = [
                    "(SELECT CASE WHEN pv.stock > 0 THEN 0 WHEN pv.stock IS NULL THEN 1 ELSE 2 END
                    FROM __variants pv
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) ASC",
                    "(SELECT pv.stock
                    FROM __variants pv
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) ASC",
                ];
                break;
            case 'stock_desc':
                $orderFields = [
                    "(SELECT CASE WHEN pv.stock > 0 THEN 0 WHEN pv.stock IS NULL THEN 1 ELSE 2 END
                    FROM __variants pv
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) ASC",
                    "(SELECT pv.stock
                    FROM __variants pv
                    WHERE
                        p.id = pv.product_id
                        AND pv.position=(SELECT MIN(position)
                            FROM __variants
                            WHERE
                                product_id=p.id LIMIT 1
                        )
                    LIMIT 1) DESC",
                ];
                break;
            case 'rand':
                $orderFields = ['RAND()'];
                break;
            case 'position':
                $orderFields = ['p.position DESC'];
                break;
        }

        // Если передали флаг, что нужно сместить товары не в наличии в конец списка, добавим SQL запрос
        if (
            !empty($orderFields)
            && isset($additionalData['in_stock_first'])
            && $additionalData['in_stock_first'] === true
            && !$this->settings->get('is_preorder')
        ) {
            array_unshift($orderFields, '((SELECT count(pv.id) FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id)>0) DESC');
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }

    protected function filter__has_price($state, $filter)
    {
        if ($state == true) {
            if (isset($filter['price'])) {
                $this->select->where('pv.price > 0');
            } else {
                $this->select->join('INNER', '__variants AS pv', 'pv.product_id = p.id AND pv.price > 0');
            }
        }
    }

    protected function filter__features($features, $filter)
    {
        $subQuery = $this->queryFactory->newSelect();
        // Алиас для таблицы без языков
        $optionsPx = 'fv';

        if (!empty($this->lang->getLangId())) {
            $subQuery->where('lfv.lang_id=' . (int)$this->lang->getLangId())
                ->join('LEFT', '__lang_features_values AS lfv', 'pf.value_id=lfv.feature_value_id');
            // Алиас для таблицы с языками
            $optionsPx = 'lfv';
        }

        foreach ($features as $featureId => $value) {
            // ВАЖНО: Структура $features: [featureId => [valueId => translit, ...]]
            // Тобто $value - це асоціативний масив, де ключі - це ID значень (value_id), а значення - translit
            // Нам потрібні тільки значення (translit), а не ключі (ID значень)
            if (empty($value) || !is_array($value)) {
                continue;
            }

            // Отримуємо масив translit-значень зі значень асоціативного масиву
            $translitValues = array_values($value);

            // ВАЖНО: Використовуємо (int)$featureId для типізації індексів (згідно з docs/migration/aura-74-8.md)
            $placeholderTranslit = "translit_features_" . (int)$featureId;
            $placeholderFeatureId = "feature_id_features_" . (int)$featureId;

            // ВАЖНО: Використовуємо іменований плейсхолдер для IN, оскільки метод perform() в Database.php
            // автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів
            // Це працює для основних запитів, але для підзапитів потрібно вручну розгорнути масив
            // Але оскільки ми передаємо біндінги через bindValues(), perform() має розгорнути масив
            $featuresValues[] = "({$optionsPx}.translit IN (:{$placeholderTranslit}) AND fv.feature_id=:{$placeholderFeatureId})";

            // Прив'язуємо значення тільки до підзапиту
            // ВАЖНО: Примусове приведення до масиву для безпеки IN (?) (згідно з docs/migration/aura-74-8.md)
            $subQuery->bindValues([
                $placeholderTranslit => (array)$translitValues,
                $placeholderFeatureId => (int)$featureId,
            ]);
        }

        if (!empty($featuresValues)) {
            if (!empty($filter['visible'])) {
                $subQuery->join('LEFT', '__products AS p', 'p.id=pf.product_id')
                    ->where('p.visible = ' . (int)$filter['visible']); // TODO проверить, не может ли он удалиться ранее, когда применялся
            }

            $subQuery->from('__products_features_values AS pf')
                ->cols(['DISTINCT(pf.product_id)'])
                ->where('(' . implode(' OR ', $featuresValues) . ')')
                ->join('LEFT', '__features_values AS fv', 'fv.id=pf.value_id')
                ->having('COUNT(DISTINCT fv.feature_id) >=' . count($features))
                ->groupBy(['product_id']);

            // ВАЖНО: Передаємо bindValues ДО joinSubSelect (згідно з docs/migration/aura-74-8.md та FILTER_BUG_SUMMARY.md)
            // Це критично важливо для правильної обробки підзапитів в Aura SQL Query v3.0
            // Метод getStatement() повертає лише SQL-рядок, тому біндінги потрібно передавати вручну
            $subQueryBindValues = $subQuery->getBindValues();

            // ВАЖНО: Розгортаємо масиви в біндінгах для підзапиту, оскільки perform() може не розгорнути
            // масиви для плейсхолдерів підзапиту (підзапит вже є частиною SQL-рядка)
            // Метод perform() автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів,
            // але це працює тільки для основних запитів, а не для підзапитів
            $expandedBindValues = [];
            foreach ($subQueryBindValues as $key => $value) {
                if (is_array($value)) {
                    // Розгортаємо масив на окремі плейсхолдери
                    foreach ($value as $index => $item) {
                        $expandedBindValues[$key . '_' . $index] = $item;
                    }
                } else {
                    $expandedBindValues[$key] = $value;
                }
            }

            // Замінюємо IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL
            // ВАЖНО: Використовуємо str_ireplace для заміни всіх входжень (якщо є кілька однакових плейсхолдерів)
            $subQueryStatement = $subQuery->getStatement();

            foreach ($subQueryBindValues as $key => $value) {
                if (is_array($value)) {
                    $placeholders = [];
                    foreach ($value as $index => $item) {
                        $placeholders[] = ':' . $key . '_' . $index;
                    }
                    // Замінюємо всі входження, не тільки перше
                    $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
                }
            }

            // ВАЖНО: Об'єднуємо біндінги з попередніми, а не перезаписуємо їх
            // Це критично важливо, коли є кілька фільтрів по характеристикам
            $existingBindValues = $this->select->getBindValues();
            $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);

            // Передаємо об'єднані біндінги в основний запит
            $this->select->bindValues($mergedBindValues);

            $this->select->joinSubSelect(
                'INNER',
                $subQueryStatement,
                'products_features',
                'products_features.product_id=p.id'
            );
        }
    }

    /**
     * @param array<string, mixed> $priceRange min/max product price filter
     */
    protected function filter__price(array $priceRange)
    {
        /** @var Money $money */
        $money = $this->serviceLocator->getService(Money::class);
        $coef = $money->getCoefMoney();

        // Build WHERE conditions using EXISTS subquery to avoid NULL issues with LEFT JOIN
        $whereConditions = [];

        if (isset($priceRange['min']) && $priceRange['min'] !== '') {
            // Ensure value is a string before trim() - PHP 8.3+ requires string type
            $minValue = is_scalar($priceRange['min']) ? trim((string)$priceRange['min']) : '';
            if ($minValue !== '') {
                // Convert to float for proper SQL comparison
                $minValue = (float)$minValue;
                $whereConditions[] = "EXISTS (SELECT 1 FROM __variants pv2 LEFT JOIN __currencies c2 ON c2.id=pv2.currency_id WHERE pv2.product_id=p.id AND ROUND(IF(pv2.currency_id=0 OR c2.id is null,pv2.price, pv2.price*c2.rate_to/c2.rate_from)*{$coef}, 2)>=:price_min)";
                $this->select->bindValue('price_min', $minValue);
            }
        }

        if (isset($priceRange['max']) && $priceRange['max'] !== '') {
            // Ensure value is a string before trim() - PHP 8.3+ requires string type
            $maxValue = is_scalar($priceRange['max']) ? trim((string)$priceRange['max']) : '';
            if ($maxValue !== '') {
                // Convert to float for proper SQL comparison
                $maxValue = (float)$maxValue;
                $whereConditions[] = "EXISTS (SELECT 1 FROM __variants pv3 LEFT JOIN __currencies c3 ON c3.id=pv3.currency_id WHERE pv3.product_id=p.id AND ROUND(IF(pv3.currency_id=0 OR c3.id is null,pv3.price, pv3.price*c3.rate_to/c3.rate_from)*{$coef}, 2)<=:price_max)";
                $this->select->bindValue('price_max', $maxValue);
            }
        }

        if (!empty($whereConditions)) {
            $this->select->where('(' . implode(' AND ', $whereConditions) . ')');
        }
    }

    /**
     * @param $categoriesIds
     * @throws \Aura\SqlQuery\Exception
     */
    protected function filter__category_id($categoriesIds)
    {
        $this->select->join(
            'INNER',
            '__products_categories AS pc',
            'p.id = pc.product_id AND pc.category_id IN(:category_ids)'
        );

        $this->select->bindValue('category_ids', $categoriesIds);

        $this->select->groupBy(['p.id']);
    }

    protected function filter__without_category($categoriesIds)
    {
        $this->select->where("(SELECT count(*)=0 FROM __products_categories pc WHERE pc.product_id=p.id)=:without_category");
        $this->select->bindValue('without_category', $categoriesIds);
    }

    protected function filter__in_stock()
    {
        if ($this->settings->get('is_preorder')) {
            return;
        }

        $this->select->where("(SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1");
    }

    protected function filter__not_in_stock()
    {
        if ($this->settings->get('is_preorder')) {
            $this->select->where('1 = 0');
            return;
        }

        $this->select->where("(SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) <> 1");
    }

    protected function filter__has_images()
    {
        $this->select->where('(SELECT count(*)>0 FROM __images pi WHERE pi.product_id=p.id LIMIT 1) = 1');
    }

    protected function filter__has_no_images()
    {
        $this->select->where('(SELECT count(*)>0 FROM __images pi WHERE pi.product_id=p.id LIMIT 1) <> 1');
    }

    protected function filter__discounted($state)
    {
        $this->select->where('(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>pv.price LIMIT 1) = :discounted')
            ->bindValue('discounted', (int)$state);
    }

    protected function filter__other_filter($filters)
    {
        if (empty($filters)) {
            return;
        }

        if ($otherFilter = $this->executeOtherFilter($filters)) {
            $this->select->where("(" . implode(' OR ', $otherFilter) . ")");
        }
    }

    private function executeOtherFilter($filters)
    {
        $otherFilter = [];
        if (in_array("featured", $filters)) {
            $otherFilter[] = "p.featured=1";
        }

        if (in_array("discounted", $filters)) {
            $otherFilter[] = "(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>pv.price LIMIT 1) = 1";
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $otherFilter, func_get_args());
    }

    /**
     * @param $keywords
     */
    protected function filter__keyword($keywords)
    {
        $keywords = explode(' ', $keywords);

        $tableAlias = $this->getTableAlias();
        $langAlias = $this->lang->getLangAlias(
            $this->getTableAlias()
        );
        foreach ($keywords as $keyNum => $keyword) {
            $keywordFilter = [];
            $keywordFilter[] = "{$langAlias}.name LIKE :keyword_name_{$keyNum}";
            $keywordFilter[] = "{$langAlias}.meta_keywords LIKE :keyword_meta_keywords_{$keyNum}";
            $keywordFilter[] = "{$tableAlias}.id in (SELECT product_id FROM __variants WHERE sku LIKE :keyword_sku_{$keyNum})";

            $this->select->bindValues([
                "keyword_name_{$keyNum}" => '%' . $keyword . '%',
                "keyword_meta_keywords_{$keyNum}" => '%' . $keyword . '%',
                "keyword_sku_{$keyNum}" => '%' . $keyword . '%',
            ]);

            $this->select->where('(' . implode(' OR ', $keywordFilter) . ')');
        }
    }

    protected function filter__brand($value)
    {
        $this->select->where(($value ? '' : '!') . self::getTableAlias() . '.brand_id');
    }
}
