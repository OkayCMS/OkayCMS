<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Entities\CouponsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCouponsHelper
{
    /**
     * @var CouponsEntity
     */
    private $couponsEntity;

    /**
     * @var Request
     */
    private $request;

    public function __construct(EntityFactory $entityFactory, Request $request)
    {
        $this->couponsEntity = $entityFactory->get(CouponsEntity::class);
        $this->request = $request;
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        $keyword = $this->request->get('keyword', 'string');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function delete($ids)
    {
        $this->couponsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findCoupons($filter)
    {
        $coupons = $this->couponsEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $coupons, func_get_args());
    }

    public function count($filter)
    {
        $coupons = $this->couponsEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $coupons, func_get_args());
    }

    public function prepareAdd($coupon)
    {
        return ExtenderFacade::execute(__METHOD__, $coupon, func_get_args());
    }

    public function add($coupon)
    {
        $insertId = $this->couponsEntity->add($coupon);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }
}