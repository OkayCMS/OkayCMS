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
    ];


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
        $this->smarty->error_reporting = E_ALL & ~E_NOTICE;

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

        // Создаем папку для скомпилированных шаблонов текущей темы
        if (!is_dir($this->smarty->getCompileDir())) {
            mkdir($this->smarty->getCompileDir(), 0777);
        }
        
        $this->smarty->setCacheDir('cache');
        
        $this->smartyHtmlMinify = $smartyHtmlMinify;
        if ($smartyHtmlMinify) {
            $this->smarty->loadFilter('output', 'trimwhitespace');
        }

        if ($smartyForceCompile) {
            $smarty->setForceCompile(true);
        }
        
        $this->smarty->registerFilter('pre', [$this, 'applyTplModifiers']);
    }
    
    public function applyTplModifiers($content, $s)
    {
        
        $currentFile = $s->_current_file;
        
        // Определяем модификации чего сейчас нам нужны, фронта или бека
        if (strpos($currentFile, $this->rootDir.'backend'.DIRECTORY_SEPARATOR.'design'.DIRECTORY_SEPARATOR.'html') !== false) {
            $modifications = $this->modules->getBackendModulesTplModifications();
        } else {
            $modifications = $this->modules->getFrontModulesTplModifications();
        }
        $fileModifications = [];
        if (!empty($modifications)) {
            foreach ($modifications as $modification) {
                if (DIRECTORY_SEPARATOR.ltrim($modification->file, DIRECTORY_SEPARATOR) == substr($currentFile, -strlen(DIRECTORY_SEPARATOR.$modification->file))) {
                    $fileModifications = array_merge($fileModifications, $modification->changes);
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
        $tplFile = mb_strcut($tplFile, 0, 250);
        
        if ($this->isUseModuleDir() === false) {
            $this->setSmartyTemplatesDir($this->getDefaultTemplatesDir());
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
            $this->setSmartyTemplatesDir([
                dirname($this->getDefaultTemplatesDir()) . "/modules/{$vendor}/{$moduleName}/html",
                $this->getModuleTemplatesDir(),
                $this->getDefaultTemplatesDir(),
            ]);
        }
        
        return $this->smarty->templateExists(trim(preg_replace('~[\n\r]*~', '', $tplFile)));
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

        if ($this->isUseModuleDir() === false) {
            $this->setSmartyTemplatesDir($this->getDefaultTemplatesDir());
        } else {
            $vendor = $this->getModuleVendorByPath($this->getModuleTemplatesDir());
            $moduleName = $this->getModuleNameByPath($this->getModuleTemplatesDir());

            /**
             * Устанавливаем директории поиска файлов шаблона как:
             * Директория модуля в дизайне (если модуль кастомизируют)
             * Директория модуля
             * Стандартная директория дизайна
             */
            $this->setSmartyTemplatesDir([
                rtrim($this->getDefaultTemplatesDir(), '/') . "/../modules/{$vendor}/{$moduleName}/html",
                $this->getModuleTemplatesDir(),
                $this->getDefaultTemplatesDir(),
            ]);
        }

        $html = $this->smarty->fetch($template);
        
        if (!$this->smartyHtmlMinify && $forceMinify === true) {
            $this->smarty->unloadFilter('output', 'trimwhitespace');
        }
        return $html;
    }

    public function useDefaultDir()
    {
        $this->useTemplateDir = self::TEMPLATES_DEFAULT;
    }

    public function useModuleDir()
    {
        $this->useTemplateDir = self::TEMPLATES_MODULE;
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
            $this->smarty->registerPlugin('modifier', $tag, $callback);
            unset($this->smartyModifiers[$tag]);
        }
        
        foreach ($this->smartyFunctions as $tag => $callback) {
            $this->smarty->registerPlugin('function', $tag, $callback);
            unset($this->smartyFunctions[$tag]);
        }
    }

    public function getDefaultTemplatesDir()
    {
        return rtrim($this->defaultTemplateDir , '/');
    }

    public function setModuleTemplatesDir($moduleTemplateDir)
    {
        $this->moduleTemplateDir = $moduleTemplateDir;
    }

    public function getModuleTemplatesDir()
    {
        return rtrim($this->moduleTemplateDir , '/');
    }

    /*Установка директории файлов шаблона(отображения)*/
    public function setTemplatesDir($dir)
    {
        $dir = rtrim($dir, '/') . '/';
        if (!is_string($dir)) {
            throw new \Exception("Param \$dir must be string");
        }
        
        $this->defaultTemplateDir = $dir;
        $this->setSmartyTemplatesDir($dir);
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

    public function setSmartyTemplatesDir($dir)
    {
        $this->smarty->setTemplateDir($dir);
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
