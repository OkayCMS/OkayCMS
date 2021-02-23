<?php


namespace Okay\Core\Modules;


use Okay\Core\OkayContainer\OkayContainer;
use Okay\Core\Router;
use Smarty;
use Okay\Core\Design;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\Config;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\ModulesEntity;
use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;

class Modules // TODO: подумать, мож сюда переедет CRUD Entity/Modules
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var array список контроллеров бекенда
     */
    private $backendControllersList = [];
    
    /**
     * @var array список запущенных модулей
     */
    private $runningModules = [];
    
    /**
     * @var array параметры модулей из файла module.json
     */
    private $modulesParams = [];
    private $modulesModifications = ['front' => [], 'backend' => []];
    private $modificationsInit = false;
    
    private $plugins;

    public function __construct(
        EntityFactory $entityFactory,
        Module        $module,
        QueryFactory  $queryFactory,
        Database      $database,
        Config        $config,
        Smarty        $smarty
    ) {
        $this->entityFactory = $entityFactory;
        $this->module        = $module;
        $this->queryFactory  = $queryFactory;
        $this->db            = $database;
        $this->config        = $config;
        $this->smarty        = $smarty;
    }
    
    /**
     * Метод возвращает список зарегистрированных контроллеров для бекенда
     * @return array
     */
    public function getBackendControllers()
    {
        return $this->backendControllersList;
    }

    public function startAllModules()
    {
        $this->startModules(false);
    }
    
    /**
     * Процедура запуска включенных подулей. Включает в себя загрузку конфигураций,
     * маршрутов и сервисов обявленных в рамках модулей
     *
     * @throws \Exception
     * @return void
     */
    public function startEnabledModules()
    {
        $this->startModules(true);
    }

    private function startModules($activeOnly = true)
    {

        $select = $this->queryFactory->newSelect()
            ->from(ModulesEntity::getTable())
            ->cols(['id', 'vendor', 'module_name', 'enabled'])
            ->orderBy(['position ASC']);

        $this->db->query($select);
        $modules = $this->db->results();

        foreach ($modules as $module) {
            // Запоминаем какие модули мы запустили, они понадобятся чтобы активировать их js и css
            $this->runningModules[$module->vendor . '/' . $module->module_name] = [
                'vendor' => $module->vendor,
                'module_name' => $module->module_name,
                'is_active' => $module->enabled,
            ];
        }

        $SL = ServiceLocator::getInstance();
        /** @var Design $design */
        $design = $SL->getService(Design::class);

        foreach ($modules as $module) {
            if ($this->module->moduleDirectoryNotExists($module->vendor, $module->module_name)) {
                continue;
            }

            // TODO: подумать над тем, чтобы перенести этот код отсюда
            if ($activeOnly === true && (int) $module->enabled !== 1) {
                $plugins = $this->module->getSmartyPlugins($module->vendor, $module->module_name);
                foreach ($plugins as $plugin) {
                    $reflector = new \ReflectionClass($plugin['class']);
                    $props     = (object) $reflector->getDefaultProperties();
                    $parentClass = $reflector->getParentClass();

                    if (!empty($props->tag)) {
                        $tag = $props->tag;
                    } else {
                        $tag = strtolower($reflector->getShortName());
                    }

                    $mock = function() {
                        return '';
                    };

                    if ($parentClass->name === \Okay\Core\SmartyPlugins\Func::class) {
                        $design->registerPlugin('function', $tag, $mock);
                    }
                    elseif ($parentClass->name === \Okay\Core\SmartyPlugins\Modifier::class) {
                        $design->registerPlugin('modifier', $tag, $mock);
                    }
                }

                continue;
            }

            $moduleConfigFile = __DIR__ . '/../../Modules/' . $module->vendor . '/' . $module->module_name . '/config/config.php';
            if (is_file($moduleConfigFile)) {
                $this->config->loadConfigsFrom($moduleConfigFile);
            }

            if ($moduleParams = $this->module->getModuleParams($module->vendor, $module->module_name)) {
                $this->modulesParams[$module->vendor . '/' . $module->module_name] = $moduleParams;
            }
            
            $this->backendControllersList = array_merge($this->backendControllersList, $this->startModule($module->id, $module->vendor, $module->module_name));
        }
    }

    public function getBackendModulesTplModifications()
    {
        $this->initModulesModifications();
        return $this->modulesModifications['backend'];
    }

    public function getFrontModulesTplModifications()
    {
        $this->initModulesModifications();
        return $this->modulesModifications['front'];
    }
    
    private function initModulesModifications()
    {
        if ($this->modificationsInit === true) {
            return;
        }

        $allowedModifiers = [
            'append',
            'appendBefore',
            'prepend',
            'appendAfter',
            'html',
            'text',
            'replace',
            'remove',
        ];
        
        $frontModifications = [];
        $backendModifications = [];
        if (!empty($this->modulesParams)) {
            $modulesParams = array_reverse($this->modulesParams);
            foreach ($modulesParams as $vendorModule => $params) {
                
                // Для выключенных модулей не нужно инициализировать модификаторы
                if (!isset($this->runningModules[$vendorModule]) || !$this->runningModules[$vendorModule]['is_active']) {
                    continue;
                }
                
                $moduleDir = __DIR__ . '/../../Modules/' . $vendorModule . '/';
                if (!empty($params->modifications->front)) {
                    foreach ($params->modifications->front as $modification) {
                        if (!empty($modification->changes)) {
                            foreach ($modification->changes as $change) {
                                
                                // Если не указали комментарий, добавим название модуля
                                if (empty($change->comment)) {
                                    $change->comment = $vendorModule;
                                }
                                
                                foreach ($allowedModifiers as $modifier) {
                                    // Если в значении модификатора указано имя файла - значение считаем с самого файла
                                    if (property_exists($change, $modifier)) {
                                        if (is_file($moduleDir . 'design/html/' . $change->{$modifier})) {
                                            $change->{$modifier} = file_get_contents($moduleDir . 'design/html/' . $change->{$modifier});
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $frontModifications = array_merge($frontModifications, $params->modifications->front);
                }
                
                if (!empty($params->modifications->backend)) {
                    foreach ($params->modifications->backend as $modification) {
                        if (!empty($modification->changes)) {
                            foreach ($modification->changes as $change) {

                                // Если не указали комментарий, добавим название модуля
                                if (empty($change->comment)) {
                                    $change->comment = $vendorModule;
                                }
                                
                                foreach ($allowedModifiers as $modifier) {
                                    // Если в занчении модификатора указано имя файла, значение считаем с самого файла
                                    if (property_exists($change, $modifier)) {
                                        if (is_file($moduleDir . 'Backend/design/html/' . $change->{$modifier})) {
                                            $change->{$modifier} = file_get_contents($moduleDir . 'Backend/design/html/' . $change->{$modifier});
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $backendModifications = array_merge($backendModifications, $params->modifications->backend);
                }
            }
        }
        
        $this->modulesModifications['front'] = $frontModifications;
        $this->modulesModifications['backend'] = $backendModifications;
        
        $this->modificationsInit = true;
    }
    
    /**
     * Возвращаем массив запущенных модулей в формате указанном ниже
     *
     *  [
            'vendor' => $module->vendor,
            'module_name' => $module->module_name,
        ];
     */
    public function getRunningModules()
    {
        return $this->runningModules;
    }
    
    /**
     * Метод проверяет активен ли модуль
     * @param $vendor
     * @param $moduleName
     * @return bool
     * @throws \Exception
     */
    public function isActiveModule($vendor, $moduleName)
    {
        $this->db->query(
            $this->queryFactory->newSelect()
                ->from(ModulesEntity::getTable())
                ->cols(['enabled'])
                ->where('vendor = ?', (string)$vendor)
                ->where('module_name = ?', (string)$moduleName)
        );
        
        return (bool)$this->db->result('enabled');
    }
    
    public function getPaymentModules($langLabel)
    {
        $modules = [];

        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        foreach ($modulesEntity->find(['enabled' => 1, 'type' => MODULE_TYPE_PAYMENT]) as $module) {
            $module->settings = $this->initModuleSettings($module->vendor, $module->module_name, $langLabel);
            $modules[$module->vendor . '/' . $module->module_name] = $module;
        }
        return $modules;
    }
    
    public function getDeliveryModules($langLabel)
    {
        $modules = [];
        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        foreach ($modulesEntity->find(['enabled' => 1, 'type' => MODULE_TYPE_DELIVERY]) as $module) {
            $module->settings = $this->initModuleSettings($module->vendor, $module->module_name, $langLabel);
            $modules[$module->vendor . '/' . $module->module_name] = $module;
        }
        return $modules;
    }

    public function indexingNotInstalledModules()
    {
        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $this->entityFactory->get(ModulesEntity::class);
        $notInstalledModules = $modulesEntity->findNotInstalled();
        foreach($notInstalledModules as $module) {
            $this->mockingSmartyPlugins($module);
        }
    }

    private function mockingSmartyPlugins($module)
    {
        $moduleDir     = $this->module->getModuleDirectory($module->vendor, $module->module_name);
        $smartyRegFile = $moduleDir."Init/SmartyPlugins.php";

        if (! file_exists($smartyRegFile)) {
            return;
        }

        $smartyPlugins = include $smartyRegFile;
        foreach($smartyPlugins as $plugin) {
            if (! class_exists($plugin['class'])) {
                continue;
            }

            $pluginClass       = new \ReflectionClass($plugin['class']);
            $defaultProperties = $pluginClass->getDefaultProperties();
            if (!empty($defaultProperties['tag'])) {
                $pluginName = $defaultProperties['tag'];
            } else {
                $classParts = explode('\\', $plugin['class']);
                $pluginName = strtolower(end($classParts));
            }

            $this->smarty->registerPlugin('function', $pluginName, function() {
                return null;
            });
        }
    }

    private function initModuleSettings($vendor, $moduleName, $langLabel)
    {
        $settings = [];
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);

        $moduleTranslations = $this->getModuleBackendTranslations($vendor, $moduleName, $langLabel);
        if (is_readable($moduleDir . '/settings.xml') && $xml = simplexml_load_file($moduleDir . '/settings.xml')) {

            foreach ($xml->settings as $setting) {
                $attributes = $setting->attributes();
                $settingName = (string)$setting->name;
                $translationName = preg_replace('~{\$lang->(.+)?}~', '$1', $settingName);
                $settingName = isset($moduleTranslations[$translationName]) ? $moduleTranslations[$translationName] : $settingName;
                $settings[(string)$setting->variable] = new \stdClass;
                $settings[(string)$setting->variable]->name = $settingName;
                $settings[(string)$setting->variable]->variable = (string)$setting->variable;
                
                if (empty((array)$setting->options)) {
                    $settings[(string)$setting->variable]->type = 'text';
                    if (!empty($attributes->type) && in_array(strtolower($attributes->type), ['hidden', 'text', 'date', 'checkbox'])) {
                        $settings[(string)$setting->variable]->type = strtolower($attributes->type);
                    }
                    
                    if (!empty((string)$setting->value) && $settings[(string)$setting->variable]->type == 'checkbox') {
                        $settings[(string)$setting->variable]->value = (string)$setting->value;
                    }
                    
                } else {
                    $settings[(string)$setting->variable]->options = [];
                    foreach ($setting->options as $option) {
                        $optionName = (string)$option->name;
                        $translationName = preg_replace('~{\$lang->(.+)?}~', '$1', $optionName);
                        $optionName = isset($moduleTranslations[$translationName]) ? $moduleTranslations[$translationName] : $optionName;
                        $settings[(string)$setting->variable]->options[(string)$option->value] = new \stdClass;
                        $settings[(string)$setting->variable]->options[(string)$option->value]->name = $optionName;
                        $settings[(string)$setting->variable]->options[(string)$option->value]->value = (string)$option->value;
                    }
                }
            }
        }
        
        return $settings;
    }
    
    /**
     * Метод возвращает массив переводов
     * @param string $vendor
     * @param string $moduleName
     * @param string $langLabel
     * @return array
     * @throws \Exception
     */
    public function getModuleBackendTranslations($vendor, $moduleName, $langLabel)
    {
        $langLabel = $this->getBackendLangLabel($vendor, $moduleName, $langLabel);
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);

        $lang = [];
        if (is_file($moduleDir . '/Backend/lang/' . $langLabel . '.php')) {
            include $moduleDir . 'Backend/lang/' . $langLabel . '.php';
        }
        return $lang;
    }
    
    /**
     * @param string $vendor
     * @param string $moduleName
     * @param string $langLabel
     * @return string
     * @throws \Exception
     */
    private function getBackendLangLabel($vendor, $moduleName, $langLabel)
    {
        $resultLabel = '';
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);
        
        if (is_file($moduleDir . 'Backend/lang/' . $langLabel . '.php')) {
            $resultLabel = $langLabel;
        } elseif (is_file($moduleDir . 'Backend/lang/en.php')) {
            $resultLabel = 'en';
        } elseif (is_dir($moduleDir . 'Backend/lang/') && ($langs = array_slice(scandir($moduleDir . 'Backend/lang/'), 2)) && count($langs) > 0) {
            $resultLabel = str_replace('.php', '', reset($langs));
        }
        
        return $resultLabel;
    }

    public function getModuleFrontTranslations($vendor, $moduleName, $langLabel)
    {
        $langLabel = $this->getFrontLangLabel($vendor, $moduleName, $langLabel);
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);

        // TODO: подумать что делать с локатором и циклической зависимостью из-за которой нельзя заинжектить сервис
        $serviceLocator = ServiceLocator::getInstance();
        
        /** @var FrontTemplateConfig $frontTemplateConfig */
        $frontTemplateConfig = $serviceLocator->getService(FrontTemplateConfig::class);
        $themeDir  = 'design/'.$frontTemplateConfig->getTheme().'/';

        $lang = [];
        if (is_file($themeDir .'modules/'.$vendor.'/'.$moduleName.'/lang/'. $langLabel.'.php')) {
            include $themeDir .'modules/'.$vendor.'/'.$moduleName.'/lang/'. $langLabel.'.php';
        }
        elseif (is_file($moduleDir . '/design/lang/' . $langLabel . '.php')) {
            include $moduleDir . 'design/lang/' . $langLabel . '.php';
        }
        return $lang;
    }

    /**
     * @param string $vendor
     * @param string $moduleName
     * @param string $langLabel
     * @return string
     * @throws \Exception
     */
    private function getFrontLangLabel($vendor, $moduleName, $langLabel)
    {
        $resultLabel = '';
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);

        if (is_file($moduleDir . 'design/lang/' . $langLabel . '.php')) {
            $resultLabel = $langLabel;
        } elseif (is_file($moduleDir . 'design/lang/en.php')) {
            $resultLabel = 'en';
        } elseif (is_dir($moduleDir . 'design/lang/') && ($langs = array_slice(scandir($moduleDir . 'design/lang/'), 2)) && count($langs) > 0) {
            $resultLabel = str_replace('.php', '', reset($langs));
        }

        return $resultLabel;
    }

    /**
     * @param $moduleId
     * @param $vendor
     * @param $moduleName
     * @param $design
     * @return array
     * @throws \Exception
     * Запуск определенного модуля, по большому счету сделано чтобы можно было контроллировать
     * какие модули запускать (для лайта), та и чтобы нельзя было просто так удалить лицензию
     */
    public function startModule($moduleId, $vendor, $moduleName)
    {

        if (empty($this->module)) {
            return [];
        }

        $container = OkayContainer::getInstance();

        $backendControllersList = [];
        $initClassName = $this->module->getInitClassName($vendor, $moduleName);
        if (!empty($initClassName)) {
            /** @var AbstractInit $initObject */
            $initObject = new $initClassName((int)$moduleId, $vendor, $moduleName);
            $initObject->init();
            foreach ($initObject->getBackendControllers() as $controllerName) {
                $controllerName = $vendor . '.' . $moduleName . '.' . $controllerName;
                if (!in_array($controllerName, $backendControllersList)) {
                    $backendControllersList[] = $controllerName;
                }
            }
        }

        $routes = $this->module->getRoutes($vendor, $moduleName);
        if (self::isActiveModule($vendor, $moduleName) === false) {
            foreach ($routes as &$route) {
                $route['mock'] = true;
            }
        }

        $services = $this->module->getServices($vendor, $moduleName);
        $container->bindServices($services);

        $plugins = $this->module->getSmartyPlugins($vendor, $moduleName);
        $container->bindServices($plugins);

        foreach($plugins as $name => $plugin) {
            $this->plugins[$name] = $plugin;
        }

        Router::bindRoutes($routes);

        return $backendControllersList;
    }

    public function registerSmartyPlugins()
    {
        if (!empty($this->plugins)) {
            $SL = ServiceLocator::getInstance();
            $design = $SL->getService(Design::class);
            $module = $SL->getService(Module::class);
            foreach ($this->plugins as $plugin) {
                $p = $SL->getService($plugin['class']);
                $p->register($design, $module);
            }
        }
    }
    
}