<?php


namespace Okay\Admin\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;

class BackendDiscountsRequest
{
    /** @var Request */
    private $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function postCartSets()
    {
        $postSets = $this->request->post('cart_sets');
        $sets = [];
        if (!empty($postSets))
            foreach ($postSets['sets'] as $i => $set) {
                $sets[] = (object) [
                    'set' => $set,
                    'partial' => $postSets['partial'][$i] ? true : false
                ];
            }

        return ExtenderFacade::execute(__METHOD__, $sets, func_get_args());
    }

    public function postPurchaseSets()
    {
        $postSets = $this->request->post('purchase_sets');
        $sets = [];
        if (!empty($postSets))
            foreach ($postSets['sets'] as $i => $set) {
                $sets[] = (object) [
                    'set' => $set,
                    'partial' => $postSets['partial'][$i] ? true : false
                ];
            }

        return ExtenderFacade::execute(__METHOD__, $sets, func_get_args());
    }
}