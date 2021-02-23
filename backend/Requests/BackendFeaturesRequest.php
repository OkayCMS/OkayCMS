<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeaturesRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translit
     */
    private $translit;

    public function __construct(
        Request  $request,
        Translit $translit
    ){
        $this->request  = $request;
        $this->translit = $translit;
    }

    public function postFeature()
    {
        $feature = new \stdClass();
        $feature->id                 = $this->request->post('id', 'integer');
        $feature->name               = $this->request->post('name');
        $feature->in_filter          = intval($this->request->post('in_filter'));
        $feature->auto_name_id       = $this->request->post('auto_name_id');
        $feature->auto_value_id      = $this->request->post('auto_value_id');
        $feature->url                = $this->request->post('url', 'string');
        $feature->url_in_product     = $this->request->post('url_in_product');
        $feature->to_index_new_value = $this->request->post('to_index_new_value');
        $feature->description        = $this->request->post('description');

        $feature->url = preg_replace("/[\s]+/ui", '', $feature->url);
        $feature->url = strtolower(preg_replace("/[^0-9a-z]+/ui", '', $feature->url));

        if (empty($feature->url)) {
            $feature->url = $this->translit->translitAlpha($feature->name);
        }

        return ExtenderFacade::execute(__METHOD__, $feature, func_get_args());
    }

    public function postFeatureCategories()
    {
        $featureCategories = $this->request->post('feature_categories', null, []);
        return ExtenderFacade::execute(__METHOD__, $featureCategories, func_get_args());
    }

    public function postFeaturesValues()
    {
        $featuresValues = [];
        if ($this->request->post('feature_values')) {
            foreach ($this->request->post('feature_values') as $n=>$fv) {
                foreach ($fv as $i=>$v) {
                    if (empty($featuresValues[$i])) {
                        $featuresValues[$i] = new \stdClass;
                    }
                    $featuresValues[$i]->$n = $v;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function postValuesToDelete()
    {
        $valuesToDelete = $this->request->post('values_to_delete');
        return ExtenderFacade::execute(__METHOD__, $valuesToDelete, func_get_args());
    }

    public function postUnionMainValueId()
    {
        $unionMainValueId = $this->request->post('union_main_value_id', 'integer');
        return ExtenderFacade::execute(__METHOD__, $unionMainValueId, func_get_args());
    }

    public function postUnionSecondValueId()
    {
        $unionSecondValueId = $this->request->post('union_second_value_id', 'integer');
        return ExtenderFacade::execute(__METHOD__, $unionSecondValueId, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }

    public function postTargetPage()
    {
        $targetPage = $this->request->post('target_page', 'integer');
        return ExtenderFacade::execute(__METHOD__, $targetPage, func_get_args());
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }
}