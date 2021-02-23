<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Design;
use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\ServiceLocator;
use Okay\Core\SmartyPlugins\Func;

class CheckoutPaymentForm extends Func
{

    protected $tag = 'checkout_payment_form';
    
    private $design;
    private $module;
    private $modules;
    private $rootDir;
    
    public function __construct(Design $design, Module $module, Modules $modules, $rootDir)
    {
        $this->design = $design;
        $this->module = $module;
        $this->modules = $modules;
        $this->rootDir = $rootDir;
    }

    public function run($params)
    {
        $SL = ServiceLocator::getInstance();

        $moduleName = str_replace("/", "\\", $params['module']);
        $moduleName = preg_replace("/[^A-Za-z0-9\\\\]+/", "", $moduleName);

        $moduleClassName = 'Okay\\Modules\\' . $moduleName;
        if (!$this->module->isModuleClass($moduleClassName)) {
            return '';
        }
        
        $vendor = $this->module->getVendorName($moduleClassName);
        $name = $this->module->getModuleName($moduleClassName);
        
        if (!$this->modules->isActiveModule($vendor, $name)) {
            return '';
        }
        
        // Устанавливаем директорию HTML из модуля
        $moduleTemplateDir = $this->module->generateModuleTemplateDir(
            $vendor,
            $name
        );
        
        $this->design->setModuleTemplatesDir($moduleTemplateDir);
        $this->design->useModuleDir();
        
        /** @var PaymentFormInterface $paymentFormService */
        $paymentFormService = $SL->getService('Okay\\Modules\\' . $vendor . '\\' . $name . '\\PaymentForm');
        
        $paymentForm = $paymentFormService->checkoutForm($params['order_id']);
        
        // Возвращаем для смарти директорию как была.
        $this->design->useDefaultDir();
        return $paymentForm;
    }
}