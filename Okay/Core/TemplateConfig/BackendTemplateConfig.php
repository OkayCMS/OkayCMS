<?php


namespace Okay\Core\TemplateConfig;


use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\Request;

class BackendTemplateConfig
{
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';
    
    private $rootDir;
    private $scriptsDefer;
    
    private $themeSettingsFileName;
    private $compileCssDir;
    private $compileJsDir;
    private $registeredTemplateFiles = false;

    private $modules;
    private $module;
    private $jsConfig;
    private $cssConfig;

    public function __construct(
        Modules $modules,
        Module $module,
        $rootDir,
        $scriptsDefer,
        $themeSettingsFileName,
        $compileCssDir,
        $compileJsDir
    ) {

        $this->modules = $modules;
        $this->module = $module;
        $this->jsConfig = new JsConfig();
        $this->cssConfig = new CssConfig($rootDir, '');
        
        $this->rootDir = $rootDir;
        $this->scriptsDefer = $scriptsDefer;
        $this->themeSettingsFileName = $themeSettingsFileName;
        $this->compileCssDir = $compileCssDir;
        $this->compileJsDir = $compileJsDir;

        if (!is_dir($this->compileCssDir)) {
            mkdir($this->compileCssDir, 0777, true);
        }

        if (!is_dir($this->compileJsDir)) {
            mkdir($this->compileJsDir, 0777, true);
        }
        
    }


    public function __destruct()
    {
        // Инвалидация компилированных js и css файлов
        $css = glob($this->rootDir . $this->compileCssDir . "backend.*.css");
        $cssMaps = glob($this->rootDir . $this->compileCssDir . "backend.*.css.map");
        $js = glob($this->rootDir . $this->compileJsDir . "backend.*.js");

        $cacheFiles = array_merge($css, $cssMaps, $js);
        if (is_array($cacheFiles)) {
            foreach ($cacheFiles as $f) {
                $fileTime = filemtime($f);
                // Если файл редактировался более недели назад, удалим его, вероятнее всего он уже не нужен
                if ($fileTime !== false && time() - $fileTime > 604800) {
                    @unlink($f);
                }
            }
        }
    }
    
    /**
     * Метод возвращает теги на подключение всех зарегистрированных js и css для блока head
     * @return string
     * @throws \Exception
     */
    public function head()
    {
        $head = '';
        $head .= $this->getIncludeHtml(TC_POSITION_HEAD);

        return $head;

    }
    
    /**
     * Метод возвращает теги на подключение всех зарегистрированных js и css для футера
     * @return string
     * @throws \Exception
     */
    public function footer()
    {
        return $this->getIncludeHtml(TC_POSITION_FOOTER);
    }

    /**
     * метод компилирует индивидуальный CSS файл, который подключили через смарти плагин
     * @param $filename
     * @param string $dir
     * @return string
     */
    public function compileIndividualCss($filename, $dir = null)
    {
        if ($filename != $this->themeSettingsFileName && $this->checkFile($filename, self::TYPE_CSS, $dir) === true) {
            $fullFilePath = $this->getFullPath($filename, self::TYPE_CSS, $dir);
            $compiledFilename =  $this->cssConfig->compileIndividual($fullFilePath, $this->compileCssDir, 'backend');
            return !empty($compiledFilename) ? "<link href=\"" . Request::getRootUrl() . "/{$compiledFilename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL : '';
        }
        return '';
    }

    /**
     * метод компилирует индивидуальный JS файл, который подключили через смарти плагин
     * @param $filename
     * @param string $dir
     * @param bool $defer
     * @return string
     */
    public function compileIndividualJs($filename, $dir = null, $defer = false)
    {
        
        if ($this->checkFile($filename, self::TYPE_JS, $dir) === true) {
            $fullFilePath = $this->getFullPath($filename, self::TYPE_JS, $dir);

            $compiledFilename = $this->jsConfig->compileIndividual($fullFilePath, $this->compileCssDir, 'backend');
            return !empty($compiledFilename) ? "<script src=\"" . Request::getRootUrl() . "/{$compiledFilename}\"" . ($defer === true ? " defer" : '') . "></script>" . PHP_EOL : '';
        }
        return '';
    }
    
    /**
     * @param string $position
     * @return string html для подключения js и css шаблона
     */
    private function getIncludeHtml($position = null)
    {
        if (empty($position)) {
            $position = TC_POSITION_HEAD;
        }
        
        $this->registerTemplateFiles();
        $includeHtml = '';

        // Подключаем основной файл стилей
        if (($css_filename = $this->cssConfig->compileRegistered($position, $this->compileCssDir, 'backend')) !== '') {
            $includeHtml .= "<link href=\"".Request::getRootUrl()."/{$css_filename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
        }

        // Подключаем дополнительные индивидуальные файлы стилей
        if (($individualCss_filenames = $this->cssConfig->compileRegisteredIndividual($position, $this->compileCssDir, 'backend')) !== []) {
            foreach ($individualCss_filenames as $filename) {
                $includeHtml .= "<link href=\"".Request::getRootUrl()."/{$filename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
            }
        }

        // Подключаем основной JS файл
        if (($js_filename = $this->jsConfig->compileRegistered($position, $this->compileJsDir, 'backend')) !== '') {
            $includeHtml .= "<script src=\"".Request::getRootUrl()."/{$js_filename}\"></script>" . PHP_EOL;
        }

        // Подключаем дополнительные индивидуальные JS файлы
        if (($individualJs_filenames = $this->jsConfig->compileRegisteredIndividual($position, $this->compileJsDir, 'backend')) !== []) {
            foreach ($individualJs_filenames as $filename) {
                $includeHtml .= "<script src=\"".Request::getRootUrl()."/{$filename}\"></script>" . PHP_EOL;
            }
        }

        return $includeHtml;
    }
    
    private function checkFile($filename, $type, $dir = null)
    {
        // файлы по http регистрировать нельзя
        if (preg_match('~^(https?:)?//~', $filename)) {
            return false;
        }

        $file = $this->getFullPath($filename, $type, $dir);
        return (bool)file_exists($file);
    }
    
    private function getFullPath($filename, $type, $dir = null)
    {
        $directory =  $this->rootDir;
        if ($dir !== null) {
            $directory .= trim($dir, ' \t\n\r\0\x0B/') . '/';
        } else {
            $directory .= 'backend/design/' . $type . '/';
        }
        return $directory . $filename;
    }
    
    private function registerTemplateFiles()
    {
        if ($this->registeredTemplateFiles === true) {
            return;
        }

        if (file_exists('backend/design/js.php') && ($themeJs = include 'backend/design/js.php') && is_array($themeJs)) {
            /** @var Js $jsItem */
            foreach ($themeJs as $jsItem) {
                if ($this->checkFile($jsItem->getFilename(), self::TYPE_JS, $jsItem->getDir()) === true) {
                    $fullPath = $this->getFullPath($jsItem->getFilename(), self::TYPE_JS, $jsItem->getDir());
                    $this->jsConfig->register($jsItem, $fullPath);
                }
            }
        }

        if (file_exists('backend/design/css.php') && ($themeCss = include 'backend/design/css.php') && is_array($themeCss)) {
            /** @var Css $cssItem */
            foreach ($themeCss as $cssItem) {
                // Файл настроек шаблона регистрировать не нужно
                if ($cssItem->getFilename() != $this->themeSettingsFileName && $this->checkFile($cssItem->getFilename(), self::TYPE_CSS, $cssItem->getDir()) === true) {
                    $fullPath = $this->getFullPath($cssItem->getFilename(), self::TYPE_CSS, $cssItem->getDir());
                    $this->cssConfig->register($cssItem, $fullPath);
                }
            }
        }

        $runningModules = $this->modules->getRunningModules();
        foreach ($runningModules as $runningModule) {

            $moduleThemesDir = $this->module->getModuleDirectory($runningModule['vendor'], $runningModule['module_name']) . 'Backend/design/';

            if (file_exists($moduleThemesDir . 'css.php') && ($moduleCss = include $moduleThemesDir . 'css.php') && is_array($moduleCss)) {
                /** @var Css $cssItem */
                foreach ($moduleCss as $cssItem) {
                    if ($cssItem->getDir() === null) {
                        $cssDir = $this->module->getModuleDirectory(
                            $runningModule['vendor'],
                            $runningModule['module_name']
                            ) 
                            . 'Backend/design/css/';
                        
                        $cssItem->setDir($cssDir);
                    }
                    $this->cssConfig->register($cssItem, $cssItem->getDir() . $cssItem->getFilename());
                }
            }

            if (file_exists($moduleThemesDir . 'js.php') && ($moduleJs = include $moduleThemesDir . 'js.php') && is_array($moduleJs)) {

                /** @var Js $jsItem */
                foreach ($moduleJs as $jsItem) {
                    if ($jsItem->getDir() === null) {
                        $jsDir = $this->module->getModuleDirectory(
                                $runningModule['vendor'],
                                $runningModule['module_name']
                            )
                            . 'Backend/design/js/';
                        $jsItem->setDir($jsDir);
                    }

                    $this->jsConfig->register($jsItem, $jsItem->getDir() . $jsItem->getFilename());
                }
            }
        }
        $this->registeredTemplateFiles = true;
    }
}