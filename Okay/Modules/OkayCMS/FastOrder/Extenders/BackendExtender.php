<?php


namespace Okay\Modules\OkayCMS\FastOrder\Extenders;


use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Settings;

class BackendExtender implements ExtensionInterface
{

    private $settings;
    private $request;
    
    public function __construct(Settings $settings, Request $request)
    {
        $this->settings = $settings;
        $this->request = $request;
    }
    
    public function updateSettings()
    {
        $this->settings->update('captcha_fast_order', $this->request->post('captcha_fast_order'));
    }
    
}