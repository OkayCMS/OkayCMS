<?php


namespace Okay\Modules\OkayCMS\FastOrder\Extenders;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Modules\OkayCMS\FastOrder\Helpers\ValidateHelper;

class BackendExtender implements ExtensionInterface
{

    private $settings;
    private $request;
    
    public function __construct(
        Settings          $settings,
        Request           $request,
        ValidateHelper    $validateHelper
    )
    {
        $this->settings = $settings;
        $this->request = $request;
        $this->validateHelper = $validateHelper;
    }
    
    public function updateSettings()
    {
        $this->settings->update('captcha_fast_order', $this->request->post('captcha_fast_order'));
    }

    public function  ValidateFastOrder($order,$variantId)
    {

        $errors = $this->validateHelper->ValidateFastOrderHeler($order,$variantId);

        return ExtenderFacade::execute(__METHOD__, $errors, func_get_args());
    }
}