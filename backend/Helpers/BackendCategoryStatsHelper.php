<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ReportStatEntity;

class BackendCategoryStatsHelper
{
    private $totalPrice;

    private $totalAmount;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var CategoriesEntity
     */
    private $categoriesEntity;

    /**
     * @var ReportStatEntity
     */
    private $reportStatEntity;

    public function __construct(EntityFactory $entityFactory, Request $request)
    {
        $this->request          = $request;
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        $this->reportStatEntity = $entityFactory->get(ReportStatEntity::class);
    }

    public function buildFilter()
    {
        $filter = [];

        $dateFrom = $this->request->get('date_from');
        $dateTo = $this->request->get('date_to');

        if (!empty($date_from)) {
            $filter['date_from'] = date("Y-m-d 00:00:01", strtotime($dateFrom));
        }

        if (!empty($date_to)) {
            $filter['date_to'] = date("Y-m-d 23:59:00", strtotime($dateTo));
        }

        $categoryId = $this->request->get('category', 'integer');
        if (!empty($categoryId)) {
            $category = $this->categoriesEntity->get($categoryId);
            $filter['category_id'] = $category->children;
        }

        $brandId = $this->request->get('brand', 'integer');
        if (!empty($brandId)) {
            $filter['brand_id'] = $brandId;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getStatistic($filter)
    {
        $this->totalPrice = 0;
        $this->totalAmount = 0;

        $categories = $this->categoriesEntity->getCategoriesTree();

        $purchases = $this->reportStatEntity->getCategorizedStat($filter);
        if (!empty($category)) {
            $categories_list = $this->catTree([$category], $purchases);
        } else {
            $categories_list = $this->catTree($categories, $purchases);
        }

        return ExtenderFacade::execute(__METHOD__, $categories_list, func_get_args());
    }

    private function catTree($categories, $purchases = [])
    {
        foreach ($categories as $k => $v) {
            if (isset($v->subcategories)) {
                $this->catTree($v->subcategories, $purchases);
            }

            if (isset($purchases[$v->id])) {
                $price = floatval($purchases[$v->id]->price);
                $amount = intval($purchases[$v->id]->amount);
            } else {
                $price = 0;
                $amount = 0;
            }

            $categories[$k]->price  = $price;
            $categories[$k]->amount = $amount;

            $this->totalPrice  += $price;
            $this->totalAmount += $amount;
        }

        return $categories;
    }
}