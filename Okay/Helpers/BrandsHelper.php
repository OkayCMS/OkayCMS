<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\BrandsEntity;

class BrandsHelper implements GetListInterface
{
    
    private $entityFactory;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function getBrandsFilter(array $filter = [])
    {
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function getCurrentSort()
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {

        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }
        
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $brandsEntity->cols(BrandsEntity::getDifferentFields($excludedFields));
        }
        
        if ($sortName !== null) {
            $brandsEntity->order($sortName, $this->getOrderBrandsAdditionalData());
        }
        $brands = $brandsEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function getExcludeFields()
    {
        $excludedFields = [
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
        return ExtenderFacade::execute(__METHOD__, $excludedFields, func_get_args());
    }
    
    // Данный метод остаётся для обратной совместимости, но объявлен как deprecated, и будет удалён в будущих версиях
    public function getBrandsList($filter = [], $sort = null)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getList', E_USER_DEPRECATED);
        $brands = $this->getList($filter, $sort, false);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    private function getOrderBrandsAdditionalData()
    {
        $orderAdditionalData = [];
        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }
}