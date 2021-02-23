<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\Translit;
use Okay\Core\Image;

class BrandsEntity extends Entity
{
    
    protected static $fields = [
        'id',
        'url',
        'image',
        'last_modify',
        'visible',
        'position',
    ];

    protected static $langFields = [
        'name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'annotation',
        'description'
    ];

    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__brands';
    protected static $langObject = 'brand';
    protected static $langTable = 'brands';
    protected static $tableAlias = 'b';
    protected static $alternativeIdField = 'url';
    
    public function find(array $filter = [])
    {
        $this->select->distinct(true);
        $this->select->join('left', '__products AS p', 'p.brand_id=b.id');
        return parent::find($filter);
    }
    
    public function count(array $filter = [])
    {
        $this->select->join('left', '__products AS p', 'p.brand_id=b.id');
        return parent::count($filter);
    }

    protected function filter__product_visible($productVisible)
    {
        $this->select->where('p.visible = ' . (int)$productVisible);
    }
    
    protected function filter__product_id($productsIds)
    {
        $this->select->where('p.id IN (:products_ids)');
        $this->select->bindValue('products_ids', (array)$productsIds);
    }
    
    protected function filter__category_id($categoryId)
    {
        $this->select->join('LEFT', '__products_categories pc', 'p.id = pc.product_id');
        $this->select->where('pc.category_id IN (:categories_ids)')
            ->bindValue('categories_ids', (array)$categoryId);
    }
    
    protected function filter__selected_brands($brandsIds)
    {
        $this->select->orWhere('b.id IN (:selected_brands)')
            ->bindValue('selected_brands', (array)$brandsIds);
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
            $otherFilter[] = 'p.featured=1';
        }

        if (in_array("discounted", $filters)) {
            $otherFilter[] = '(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>0 LIMIT 1) = 1';
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $otherFilter, func_get_args());
    }
    
    protected function filter__price(array $priceRange)
    {
        $coef = $this->serviceLocator->getService(Money::class)->getCoefMoney();

        if (isset($priceRange['min'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})>=:price_min")
                ->bindValue('price_min', trim($priceRange['min']));
        }
        if (isset($priceRange['max'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})<=:price_max")
                ->bindValue('price_max', trim($priceRange['max']));
        }

        $this->select->join('LEFT', '__variants AS pv', 'pv.product_id = p.id');
        $this->select->join('LEFT', '__currencies AS c', 'c.id=pv.currency_id');

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
            $subQuery->bindValues([
                "translit_features_{$featureId}" => (array)$value,
                "feature_id_features_{$featureId}" => $featureId,
            ]);
        }

        if (empty($featuresValues)) {
            return;
        }

        if (!empty($filter['product_visible'])) {
            $subQuery->join('LEFT', '__products AS p', 'p.id=pf.product_id')
                ->where('p.visible = ' . (int)$filter['product_visible']);
        }

        $subQuery->from('__products_features_values AS pf')
            ->cols(['pf.product_id'])
            ->where('(' . implode(' OR ', $featuresValues) . ')')
            ->join('LEFT', '__features_values AS fv', 'fv.id=pf.value_id')
            ->groupBy(['pf.product_id'])
            ->having('COUNT(*) >=' . count($features));
        
        $this->select->where('p.id IN (?)', $subQuery);
    }

    public function add($brand)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);
        
        $brand = (object)$brand;
        if (empty($brand->url)) {
            $brand->url = $translit->translit($brand->name);
            $brand->url = str_replace('.', '', $brand->url);
        }

        $brand->url = preg_replace("/[\s]+/ui", '', $brand->url);

        while ($this->get((string)$brand->url)) {
            if(preg_match('/(.+)([0-9]+)$/', $brand->url, $parts)) {
                $brand->url = $parts[1].''.($parts[2]+1);
            } else {
                $brand->url = $brand->url.'2';
            }
        }

        return parent::add($brand);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (empty($ids)) {
            parent::delete($ids);
        }

        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);
        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_brands_dir,
                $this->config->resized_brands_dir
            );
        }

        $update = $this->queryFactory->newUpdate();
        $update->table(ProductsEntity::getTable())
            ->set('brand_id', 0)
            ->where('brand_id IN (:brands_ids)')
            ->bindValue('brands_ids', $ids);
        $this->db->query($update);

        parent::delete($ids);
    }

    public function duplicate($brandId)
    {
        $brand = $this->findOne(['id' => $brandId]);

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $brand->position;

        $newBrand = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($brand, $field)) {
                $newBrand->$field = $brand->$field;
            }
        }

        $newBrand->id = null;
        $newBrand->url = '';

        //Добавляем новую запись в бд
        $newBrandId = $this->add($newBrand);

        // Сдвигаем страницы вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__brands')
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $brand->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__brands')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newBrandId,
            ]);
        $this->db->query($update);

        $this->multiDuplicateBrand($brandId, $newBrandId);
        return $newBrandId;
    }

    private function multiDuplicateBrand($brandId, $newBrandId) {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $brandLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($brandLangFields)) {
                        $sourceBrand = $this->findOne(['id' => $brandId]);
                        $destinationBrand = new \stdClass();
                        foreach($brandLangFields as $field) {
                            $destinationBrand->{$field} = $sourceBrand->{$field};
                        }
                        $this->update($newBrandId, $destinationBrand);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }

}
