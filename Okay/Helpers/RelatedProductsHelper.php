<?php


namespace Okay\Helpers;


use Okay\Core\Entity\RelatedProductsInterface;
use Okay\Core\Modules\Extender\ExtenderFacade;

class RelatedProductsHelper
{
    private $productsHelper;
    
    public function __construct(ProductsHelper $productsHelper)
    {
        $this->productsHelper = $productsHelper;
    }

    /**
     * @param RelatedProductsInterface $relatedObjectsEntity экземпляр класса, в котором стоит вызвать метод getRelatedProducts()
     * @param array $filter аргумент метода getRelatedProducts()
     * @return mixed|void|null
     * @throws \Exception
     */
    public function getRelatedProductsList(RelatedProductsInterface $relatedObjectsEntity, array $filter)
    {

        $relatedProducts = [];
        foreach ($relatedObjectsEntity->getRelatedProducts($filter) as $p) {
            $relatedProducts[$p->related_id] = null;
        }

        if (!empty($relatedProducts)) {
            $relatedIds = array_keys($relatedProducts);
            $relatedFilter = [
                'id' => $relatedIds,
                'limit' => count($relatedIds),
                'in_stock' => true,
                'visible' => 1,
            ];
            foreach ($this->productsHelper->getList($relatedFilter) as $p) {
                $relatedProducts[$p->id] = $p;
            }
            foreach ($relatedProducts as $id=>$r) {
                if ($r === null) {
                    unset($relatedProducts[$id]);
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }
}