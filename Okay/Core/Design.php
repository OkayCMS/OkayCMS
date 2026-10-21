<?php


namespace Okay\Core;


use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\TplMod\TplMod;
use Smarty;
use Mobile_Detect;

class Design
{
    
    const TEMPLATES_DEFAULT = 'default';
    const TEMPLATES_MODULE = 'module';
    
    /**
     * @var Smarty
     */
    public $smarty;

    /** @var Mobile_Detect */
    public $detect;

    /** @var FrontTemplateConfig */
    private $frontTemplateConfig;

    /** @var Module */
    private $module;

    /** @var Modules */
    private $modules;

    /** @var TplMod */
    private $tplMod;

    /** @var array */
    private $smartyFunctions = [];
    
    /** @var array */
    private $smartyModifiers = [];

    /** @var string */
    private $moduleTemplateDir;

    /** @var string */
    private $defaultTemplateDir;

    private $moduleChangeDir = [];

    private $rootDir;

    /** @var string */
    private $useTemplateDir = self::TEMPLATES_DEFAULT;
    
    private $smartyHtmlMinify;
    
    /**
     * @var array
     */
    private $allowedPhpFunctions = [
        'escape',
        'cat',
        'count',
        'in_array',
        'nl2br',
        'str_replace',
        'reset',
        'floor',
        'round',
        'ceil',
        'max',
        'min',
        'number_format',
        'print_r',
        'var_dump',
        'printa',
        'file_exists',
        'stristr',
        'strtotime',
        'empty',
        'urlencode',
        'intval',
        'isset',
        'sizeof',
        'is_array',
        'array_intersect',
        'time',
        'array',
        'base64_encode',
        'implode',
        'explode',
        'preg_replace',
        'preg_match',
        'key',
        'json_encode',
        'json_decode',
        'is_file',
        'date',
        'strip_tags',
        'trim',
        'ltrim',
        'rtrim',
        'array_keys',
        'pathinfo',
        'strtolower',
        'strpos',
        'sprintf',
        'vsprintf',
        'preg_match'
    ];

    private $smartyStaticClasses = [];


    public function __construct(
        Smarty $smarty,
        Mobile_Detect $mobileDetect,
        FrontTemplateConfig $frontTemplateConfig,
        Module $module,
        Modules $modules,
        TplMod $tplMod,
        $smartyCacheLifetime,
        $smartyCompileCheck,
        $smartyHtmlMinify,
        $smartyDebugging,
        $smartySecurity,
        $smartyCaching,
        $smartyForceCompile,
        $smartyStaticClasses,
        $rootDir
    ) {
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->detect         = $mobileDetect;
        $this->module         = $module;
        $this->modules        = $modules;
        $this->tplMod         = $tplMod;
        $this->rootDir        = $rootDir;

        $this->smarty = $smarty;
        $this->smarty->compile_check   = $smartyCompileCheck;
        $this->smarty->caching         = $smartyCaching;
        $this->smarty->cache_lifetime  = $smartyCacheLifetime;
        $this->smarty->debugging       = $smartyDebugging;
        $this->smarty->error_reporting = E_ALL & ~E_NOTICE & ~E_WARNING;

        $theme = $this->frontTemplateConfig->getTheme();

        if ($smartySecurity == true) {
            $this->smarty->enableSecurity();
            $this->smarty->security_policy->php_modifiers = $this->allowedPhpFunctions;
            $this->smarty->security_policy->php_functions = $this->allowedPhpFunctions;
            $this->smarty->security_policy->secure_dir = array(
                $rootDir . 'design/' . $theme,
                $rootDir . 'backend/design',
                $rootDir . 'Okay/Modules',
            );
        }

        $this->defaultTemplateDir = $rootDir.'design/'.$theme.'/html';
        $this->smarty->setCompileDir($rootDir.'compiled/'.$theme);
        $this->smarty->setTemplateDir($this->defaultTemplateDir);

        $compileDir = $this->smarty->getCompileDir();
        if (!is_dir($compileDir) && !@mkdir($compileDir, 0777, true) && !is_dir($compileDir)) {
            throw new \RuntimeException(sprintf(
                'Cannot create the Smarty compile directory "%s". Without it no page can be rendered.',
                $compileDir
            ));
        }
        
        $this->smarty->setCacheDir('cache');
        
        $this->smartyHtmlMinify = $smartyHtmlMinify;
        if ($smartyHtmlMinify) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }

        if ($smartyForceCompile) {
            $smarty->setForceCompile(true);
        }
        if (!empty($smartyStaticClasses)) {
            $this->smartyStaticClasses = $smartyStaticClasses;
        }
        
        $this->smarty->registerFilter('pre', [$this, 'applyTplModifiers']);
    }
    
    public function applyTplModifiers($content, $s)
    {
        
        $currentFile = $s->_current_file;
        
        // Определяем модификации чего сейчас нам нужны, фронта или бека
        if (strpos((string) $currentFile, $this->rootDir.'backend'.DIRECTORY_SEPARATOR.'design'.DIRECTORY_SEPARATOR.'html') !== false) {
            $modifications = $this->modules->getBackendModulesTplModifications();
        } else {
            $modifications = $this->modules->getFrontModulesTplModifications();
        }
        $fileModifications = [];
        if (!empty($modifications)) {
            foreach ($modifications as $modificationDTO) {
                if (DIRECTORY_SEPARATOR.ltrim($modificationDTO->getFile(), DIRECTORY_SEPARATOR) == substr((string) $currentFile, -strlen(DIRECTORY_SEPARATOR.$modificationDTO->getFile()))) {
                    $fileModifications = array_merge($fileModifications, $modificationDTO->getChanges());
                }
            }
        }
        
        if (!empty($fileModifications)) {
            $content = $this->tplMod->buildFile($content, $fileModifications);
        }
        
        return $content;
    }

    /**
     * Метод нужен для модулей, если в каком-то экстендере или еще где нужно обработать tpl файл
     * нужно предварительно вызвать этот метод, чтобы переключить директорию tpl файлов.
     * После вызова fetch() нужно обязательно вернуть стандартную директорию методом rollbackTemplatesDir()
     * 
     * @param $moduleClassName
     * @throws \Exception
     */
    public function setModuleDir($moduleClassName)
    {
        
        $vendor = $this->module->getVendorName($moduleClassName);
        $name = $this->module->getModuleName($moduleClassName);

        $moduleTemplateDir = $this->module->generateModuleTemplateDir(
            $vendor,
            $name
        );

        $this->moduleChangeDir[] = [
            'prev_module_dir' => $this->getModuleTemplatesDir(),
            'is_use_prev_module_dir' => $this->isUseModuleDir(),
        ];
        
        $this->setModuleTemplatesDir($moduleTemplateDir);
        $this->useModuleDir();
    }

    /**
     * Метод возвращает стандартную директорию tpl файлов.
     * Применяется если в модуле сменили директорию tpl файлов посредством метода setModuleDir()
     */
    public function rollbackTemplatesDir()
    {
        
        if ($moduleChangeDir = array_pop($this->moduleChangeDir)) {
            if (!empty($moduleChangeDir['prev_module_dir'])) {
                $this->setModuleTemplatesDir($moduleChangeDir['prev_module_dir']);
            }
            if (!$moduleChangeDir['is_use_prev_module_dir']) {
                $this->useDefaultDir();
            }
        } else {
            $this->useDefaultDir();
        }
    }
    
    /**
     * Проверка существует ли данный файл шаблона
     * 
     * @param $tplFile
     * @return bool
     * @throws \SmartyException
     */
    public function templateExists($tplFile)
    {
        $tplFile = mb_strcut((string) $tplFile, 0, 250);

        $this->setSmartyTemplatesDir();

        return $this->smarty->templateExists(trim((string) preg_replace('~[\n\r]*~', '', $tplFile)));
    }
    
    public function registerPlugin($type, $tag, $callback)
    {
        switch ($type) {
            case 'modifier':
                $this->smartyModifiers[$tag] = $callback;
                break;
            case 'function':
                $this->smartyFunctions[$tag] = $callback;
                break;
        }
    }

    /**
     * @param string $var
     * @param mixed $value
     * @param bool $dynamicJs Если установить в true, переменная будет доступна в файле scripts.tpl клиентского шаблона,
     * как обычная Smarty переменная
     * @return \Smarty_Internal_Data
     */
    public function assign($var, $value, $dynamicJs = false)
    {
        
        if ($dynamicJs === true) {
            $_SESSION['dynamic_js']['vars'][$var] = $value;
        }
        
        return $this->smarty->assign($var, $value);
    }

    /**
     * @param $var
     * @param $value
     * 
     * Метод позволяет передать переменную с PHP непосредственно в JS код
     * Считать переменную можно будет как okay.var_name
     */
    public function assignJsVar($var, $value)
    {
        $_SESSION['common_js']['vars'][$var] = $value;
    }

    /*Отображение конкретного шаблона*/
    public function fetch($template, $forceMinify = false)
    {
        if (!$this->smartyHtmlMinify && $forceMinify === true) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }
        
        $this->registerSmartyPlugins();
        $this->registerAllowedPhpFunctions();
        $this->registerStaticClasses();

        $this->setSmartyTemplatesDir();

        $html = $this->smarty->fetch($template);
        
        if (!$this->smartyHtmlMinify && $forceMinify === true) {
            $this->smarty->unloadFilter('output', 'trimwhitespace');
        }
        return $html;
    }

    public function useDefaultDir()
    {
        $this->useTemplateDir = self::TEMPLATES_DEFAULT;
        $this->setSmartyTemplatesDir();
    }

    public function useModuleDir()
    {
        $this->useTemplateDir = self::TEMPLATES_MODULE;
        $this->setSmartyTemplatesDir();
    }

    public function isUseModuleDir()
    {
        if ($this->useTemplateDir === self::TEMPLATES_MODULE) {
            return true;
        }
        return false;
    }
    
    private function registerSmartyPlugins()
    {
   
        foreach ($this->smartyModifiers as $tag => $callback) {
            if (!isset($this->smarty->registered_plugins['modifier'][$tag])) {
                $this->smarty->registerPlugin('modifier', $tag, $callback);
            }
            unset($this->smartyModifiers[$tag]);
        }

        foreach ($this->smartyFunctions as $tag => $callback) {
            if (!isset($this->smarty->registered_plugins['function'][$tag])) {
                $this->smarty->registerPlugin('function', $tag, $callback);
            }
            unset($this->smartyFunctions[$tag]);
        }
    }

    private function registerAllowedPhpFunctions()
    {
        foreach ($this->allowedPhpFunctions as $func) {
            if (!function_exists($func)) {
                continue;
            }

            $callback = $this->wrapIfPassedByReference($func);

            foreach (['modifier', 'function'] as $type) {
                if (!isset($this->smarty->registered_plugins[$type][$func])) {
                    $this->smarty->registerPlugin($type, $func, $callback);
                }
            }
        }
    }

    /**
     * Функции, первый параметр которых принимается по ссылке (reset и подобные),
     * нельзя регистрировать плагином напрямую: Smarty вызывает плагины через
     * call_user_func_array(), то есть по значению, и PHP 8 бросает
     * "Argument #1 must be passed by reference, value given", а вместо результата
     * в шаблон попадает сам массив. Оборачиваем такие функции, чтобы ссылка
     * бралась от локальной копии внутри обёртки.
     *
     * @param string $func
     * @return string|\Closure
     */
    private function wrapIfPassedByReference($func)
    {
        try {
            $parameters = (new \ReflectionFunction($func))->getParameters();
        } catch (\ReflectionException $e) {
            return $func;
        }

        if (empty($parameters) || !$parameters[0]->isPassedByReference()) {
            return $func;
        }

        return static function ($value, ...$args) use ($func) {
            return $func($value, ...$args);
        };
    }

    private function registerStaticClasses()
    {
        foreach ($this->smartyStaticClasses as $staticClass) {
            $className = ltrim($staticClass, '\\');
            if (!class_exists($className)) {
                continue;
            }

            // Шаблоны обращаются к статическому классу и по полному имени
            // (Okay\Core\UserReferer\UserReferer::CHANNEL_EMAIL), и по короткому
            // (BannerImageSettingsDTO::SHOW_DEFAULT). Регистрируем обе формы:
            // короткое имя иначе резолвится в глобальное пространство имён
            // и падает с "Class not found".
            $names = [$staticClass];

            $separatorPosition = strrpos($className, '\\');
            $shortName = $separatorPosition === false
                ? $className
                : substr($className, $separatorPosition + 1);
            if (!in_array($shortName, $names, true)) {
                $names[] = $shortName;
            }

            foreach ($names as $name) {
                if (!isset($this->smarty->registered_classes[$name])) {
                    $this->smarty->registerClass($name, $className);
                }
            }
        }
    }

    public function addStaticClass($class)
    {
        if (!empty($class)) {
            $this->smartyStaticClasses[] = $class;
        } else {
            throw new \InvalidArgumentException('Class name cannot be empty');
        }
    }

    public function getDefaultTemplatesDir()
    {
        return rtrim($this->defaultTemplateDir , '/');
    }

    public function setModuleTemplatesDir($moduleTemplateDir)
    {
        $this->moduleTemplateDir = $moduleTemplateDir;
        $this->setSmartyTemplatesDir();
    }

    public function getModuleTemplatesDir()
    {
        return rtrim((string)$this->moduleTemplateDir , '/');
    }

    /*Установка директории файлов шаблона(отображения)*/
    public function setTemplatesDir($dir)
    {
        $dir = rtrim((string) $dir, '/') . '/';
        if (!is_string($dir)) {
            throw new \Exception("Param \$dir must be string");
        }
        
        $this->defaultTemplateDir = $dir;
        $this->smarty->setTemplateDir($dir);
    }

    /*Установка директории для готовых файлов для отображения*/
    public function setCompiledDir($dir)
    {
        $this->smarty->setCompileDir($dir);
    }

    /*Получение директории файлов шаблона(отображения)*/
    public function getTemplatesDir()
    {
        $dirs = $this->smarty->getTemplateDir();
        return reset($dirs);
    }

    /*Получение директории для готовых файлов для отображения*/
    public function getCompiledDir()
    {
        return $this->smarty->getCompileDir();
    }

    /*Выборка переменой*/
    public function getVar($name)
    {
        return $this->smarty->getTemplateVars($name);
    }
    
    public function get_var($name)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getVar', E_USER_DEPRECATED);
        return $this->getVar($name);
    }

    /*Очитска кэша Smarty*/
    public function clearCache()
    {
        $this->smarty->clearAllCache();
    }

    /*Определение мобильного устройства*/
    public function isMobile()
    {
        return $this->detect->isMobile();
    }

    /*Определение планшетного устройства*/
    public function isTablet()
    {
        return $this->detect->isTablet();
    }

    public function setSmartyTemplatesDir()
    {
        if ($this->isUseModuleDir() === false) {
            $this->smarty->setTemplateDir($this->getDefaultTemplatesDir());
        } else {
            $namespace = str_replace($this->rootDir, '', $this->getModuleTemplatesDir());
            $namespace = str_replace('/', '\\', $namespace);

            $vendor = $this->module->getVendorName($namespace);
            $moduleName = $this->module->getModuleName($namespace);
            /**
             * Устанавливаем директории поиска файлов шаблона как:
             * Директория модуля в дизайне (если модуль кастомизируют)
             * Директория модуля
             * Стандартная директория дизайна
             */
            $this->smarty->setTemplateDir([
                dirname((string) $this->getDefaultTemplatesDir()) . "/modules/{$vendor}/{$moduleName}/html",
                $this->getModuleTemplatesDir(),
                $this->getDefaultTemplatesDir(),
            ]);
        }
    }
    
    public function clearCompiled()
    {
        $theme = $this->frontTemplateConfig->getTheme();
        $dir = $this->rootDir.'compiled/'.$theme;
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    @unlink($dir."/".$file);
                }
            }
            closedir($handle);
        }

        $dir = $this->rootDir.'backend/design/compiled/';
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != '.keep_folder') {
                    @unlink($dir."/".$file);
                }
            }
            closedir($handle);
        }
    }

    private function getModuleVendorByPath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        return preg_replace('~.*/?Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$1', $path);
    }

    private function getModuleNameByPath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        return preg_replace('~.*/?Okay/Modules/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/?.*~', '$2', $path);
    }

}
