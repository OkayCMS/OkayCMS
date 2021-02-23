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
        if (empty($product->url)) {
            $product->url = Translit::translit($product->name);
            $product->url = str_replace('.', '', $product->url);
            
            while ($url = $this->cols(['url'])->findOne(['url' => $product->url])) {
                if (preg_match('/(.+)?_([0-9]+)$/', $url, $parts)) {
                    $product->url = $parts[1].'_'.($parts[2]+1);
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
        $select = $this->queryFactory->newSelect();
        $select->cols(['filename'])
            ->from('__images')
            ->where('id IN(:images_ids)')
            ->where('product_id IN(:products_ids)')
            ->bindValue('images_ids', $imagesIds)
            ->bindValue('products_ids', $productsIds);
        $this->db->query($select);
        $candidatesToDelete = $this->getResults('filename');

        $filesUsesInOtherProducts = [];
        if (!empty($candidatesToDelete)) {
            $select = $this->queryFactory->newSelect();
            $select->cols(['filename'])
                ->from('__images')
                ->where('filename IN(:images_filenames)')
                ->where('product_id NOT IN(:products_ids)')
                ->bindValue('images_filenames', $candidatesToDelete)
                ->bindValue('products_ids', $productsIds);
            $this->db->query($select);
            $filesUsesInOtherProducts = $this->getResults('filename');
        }

        $toDeleteFiles = array_diff($candidatesToDelete, $filesUsesInOtherProducts);
        foreach($toDeleteFiles as $file) {
            @unlink($this->config->root_dir . $this->config->original_images_dir . $file);
            $this->removeAllResizes($file);
        }
    }

    private function removeAllResizes($file)
    {
        $parts = explode('.', $file);
        $ext = end($parts);

        array_pop($parts);
        $filenameWithoutExt = implode($parts, '.');

        $pattern = $this->config->root_dir . $this->config->resized_images_dir . $filenameWithoutExt . ".*x*." . $ext;
        $rezisedImages = glob($pattern);

        if (!is_array($rezisedImages)) {
            return;
        }

        foreach ($rezisedImages as $rezisedImage) {
            @unlink($rezisedImage);
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

    public function getPriceRange(array $filter = [])
    {
        $coef = $this->serviceLocator->getService(Money::class)->getCoefMoney();

        $this->setUp();

        $this->buildFilter($filter);
        $this->select->cols([
            "floor(min(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})) as min",
            "floor(max(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})) as max",
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
    public function addRelatedProduct($productId, $relatedId, $position = 0) {
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
        if ($pid) {
            $pIds[$pid] = 'prev';
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
        if ($pid) {
            $pIds[$pid] = 'next';
        }

        $result = ['next'=>'', 'prev'=>''];
        if (!empty($pIds)) {
            foreach ($this->find(array('id'=>array_keys($pIds))) as $p) {
                $result[$pIds[$p->id]] = $p;
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

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $product->position;
        
        $newProduct = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($product, $field)) {
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
        foreach($categories as $i=>$c) {
            $categoriesEntity->addProductCategory($newProductId, $c->category_id, $i);
        }

        // Дублируем изображения
        $imagesIds = [];
        $images = $imagesEntity->find(['product_id'=>$productId]);
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
        $variants = $variantsEntity->find(['product_id'=>$productId]);
        foreach($variants as $variant) {
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
        foreach($values as $value) {
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

    private function multiDuplicateProduct($productId, $newProductId) {
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
                        foreach($productLangFields as $field) {
                            if (in_array($field, ['meta_title', 'meta_keywords', 'meta_description'])) {
                                continue;
                            }
                            $destinationProduct->{$field} = $sourceProduct->{$field};
                        }
                        $this->update($newProductId, $destinationProduct);
                    }

                    // Дублируем варианты
                    if (!empty($variantLangFields)) {
                        $variants = $variantsEntity->find(['product_id'=>$newProductId]);
                        $sourceVariants = $variantsEntity->find(['product_id'=>$productId]);
                        foreach($sourceVariants as $i=>$sourceVariant) {
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
        $coef = $this->serviceLocator->getService(Money::class)->getCoefMoney();

        switch ($order) {
            case 'price' :
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
            case 'price_desc' :
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
                $maxOrderAmount = $this->settings->get('max_order_amount');
                $orderFields = [
                    "(SELECT IFNULL(pv.stock, {$maxOrderAmount})
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
                $maxOrderAmount = $this->settings->get('max_order_amount');
                $orderFields = [
                    "(SELECT IFNULL(pv.stock, {$maxOrderAmount})
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
        if (!empty($orderFields) && isset($additionalData['in_stock_first']) && $additionalData['in_stock_first'] === true) {
            array_unshift($orderFields, '((SELECT count(pv.id) FROM __variants pv WHERE (pv.stock IS NULL OR pv.stock>0) AND p.id = pv.product_id)>0) DESC');
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }

    protected function filter__has_price($state)
    {
        if ($state == true) {
            $this->select->join('INNER', '__variants AS pv', 'pv.product_id = p.id AND v.price > 0');
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

        foreach ($features as $featureId=>$value) {
            $featuresValues[] = "({$optionsPx}.translit IN (:translit_features_{$featureId}) AND fv.feature_id=:feature_id_features_{$featureId})";
            $this->select->bindValues([
                "translit_features_{$featureId}" => (array)$value,
                "feature_id_features_{$featureId}" => $featureId,
            ]);

            $subQuery->bindValues([
                "translit_features_{$featureId}" => (array)$value,
                "feature_id_features_{$featureId}" => $featureId,
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
                ->having('COUNT(*) >=' . count($features))
                ->groupBy(['product_id']);

            $this->select->joinSubSelect(
                'INNER',
                $subQuery->getStatement(),
                'products_features',
                'products_features.product_id=p.id'
            );
        }
    }

    protected function filter__price(array $priceRange)
    {
        $coef = $this->serviceLocator->getService(Money::class)->getCoefMoney();

        if (isset($priceRange['min'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})>=?", trim($priceRange['min']));
        }
        if (isset($priceRange['max'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})<=?", trim($priceRange['max']));
        }

        $this->select->join('LEFT', '__variants AS pv', 'pv.product_id = p.id');
        $this->select->join('LEFT', '__currencies AS c', 'c.id=pv.currency_id');

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
        $this->select->where("(SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1");
    }

    protected function filter__not_in_stock()
    {
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
        $this->select->where('(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = :discounted')
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
            $otherFilter[] = "(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = 1";
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
        foreach ($keywords as $keyNum=>$keyword) {
            
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
}