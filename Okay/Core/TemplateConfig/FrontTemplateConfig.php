<?php


namespace Okay\Core\TemplateConfig;


use Okay\Core\BackendTranslations;
use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\Router;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\ManagersEntity;
use Psr\Log\LoggerInterface;

class FrontTemplateConfig
{
    const TYPE_JS = 'js';
    const TYPE_CSS = 'css';
    
    private $rootDir;
    private $scriptsDefer;
    
    private $themeSettingsFileName;
    private $theme;
    private $adminTheme;
    private $adminThemeManagers;
    private $compileCssDir;
    private $compileJsDir;
    private $registeredTemplateFiles = false;

    private $modules;
    private $module;
    private $config;
    private $jsConfig;
    private $cssConfig;
    
    private $headCssFilename;
    private $headIndividualCssFilenames;
    private $headJsFilename;
    private $headIndividualJsFilenames;
    private $footerCssFilename;
    private $footerIndividualCssFilenames;
    private $footerJsFilename;
    private $footerIndividualJsFilenames;

    public function __construct(
        Modules $modules,
        Module $module,
        Settings $settings,
        Config $config,
        $rootDir,
        $scriptsDefer,
        $themeSettingsFileName,
        $compileCssDir,
        $compileJsDir
    ) {

        $this->theme = $settings->get('theme');
        $this->adminTheme = $settings->get('admin_theme');
        $this->adminThemeManagers = $settings->get('admin_theme_managers');
        $settingsFile = 'design/' . $this->getTheme() . '/css/' . $themeSettingsFileName;
        
        $this->modules = $modules;
        $this->module = $module;
        $this->config = $config;
        $this->jsConfig = new JsConfig();
        $this->cssConfig = new CssConfig($rootDir, $settingsFile);
        
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
        $css = glob($this->rootDir . $this->compileCssDir . $this->getTheme() . ".*.css");
        $cssMaps = glob($this->rootDir . $this->compileCssDir . $this->getTheme() . ".*.css.map");
        $js = glob($this->rootDir . $this->compileJsDir . $this->getTheme() . ".*.js");

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
    
    public function getTheme()
    {
        // Если тема уже удалена, выключим её для админа
        if (!empty($this->adminTheme) && !is_dir(__DIR__ . '/../../../design/' . $this->adminTheme . '/html')) {
            $SL = ServiceLocator::getInstance();

            /** @var Settings $settings */
            $settings = $SL->getService(Settings::class);

            $settings->set('admin_theme', '');
            $this->adminTheme = '';
        }

        $adminTheme = $this->adminTheme;
        $adminThemeManagers = $this->adminThemeManagers;
        if (!empty($_COOKIE['admin_login']) && !empty($adminTheme) && $this->theme != $this->adminTheme) {
            if (empty($adminThemeManagers) || in_array($_COOKIE['admin_login'], $this->adminThemeManagers)) {
                return $this->adminTheme;
            }
        }

        return $this->theme;
    }

    /**
     * Метод возвращает все зарегистрированные css из активного шаблона, нужно чтобы в админке в редакторе их подставить
     *
     * @return array
     */
    public function getRegisteredCss()
    {
        $this->registerTemplateFiles();

        $css = [];

        // Подключаем основной файл стилей
        if (($cssFilename = $this->cssConfig->compileRegistered(TC_POSITION_HEAD, $this->compileCssDir, $this->getTheme())) !== '') {
            $css[] = $cssFilename;
        }

        // Подключаем дополнительные индивидуальные файлы стилей
        if (($individualCss_filenames = $this->cssConfig->compileRegisteredIndividual(TC_POSITION_HEAD, $this->compileCssDir, $this->getTheme())) !== []) {
            foreach ($individualCss_filenames as $cssFilename) {
                $css[] = $cssFilename;
            }
        }

        // Подключаем основной файл стилей
        if (($cssFilename = $this->cssConfig->compileRegistered(TC_POSITION_FOOTER, $this->compileCssDir, $this->getTheme())) !== '') {
            $css[] = $cssFilename;
        }

        // Подключаем дополнительные индивидуальные файлы стилей
        if (($individualCss_filenames = $this->cssConfig->compileRegisteredIndividual(TC_POSITION_FOOTER, $this->compileCssDir, $this->getTheme())) !== []) {
            foreach ($individualCss_filenames as $cssFilename) {
                $css[] = $cssFilename;
            }
        }

        return $css;
    }
    
    /**
     * Метод возвращает теги на подключение всех зарегистрированных js и css для блока head
     * @return string
     * @throws \Exception
     */
    public function head()
    {

        $SL = ServiceLocator::getInstance();

        /** @var LoggerInterface $logger */
        $logger = $SL->getService(LoggerInterface::class);

        /** @var Config $config */
        $config = $SL->getService(Config::class);

        $head = '';

        // Добавляем в шапку предзагрузчики
        if ($this->config->get('preload_head_css')) {
            if ($this->headCssFilename !== '') {
                $head .= "<link href=\"{$this->headCssFilename}\" as=\"style\" rel=\"preload\">" . PHP_EOL;
            }
        }
        if ($this->config->get('preload_footer_css')) {
            if ($this->footerCssFilename !== '') {
                $head .= "<link href=\"{$this->footerCssFilename}\" as=\"style\" rel=\"preload\">" . PHP_EOL;
            }
        }
        if ($this->config->get('preload_head_js')) {
            if ($this->headJsFilename !== '') {
                $head .= "<link href=\"{$this->headJsFilename}\" as=\"script\" rel=\"preload\">" . PHP_EOL;
            }
        }
        if ($this->config->get('preload_footer_js')) {
            if ($this->footerJsFilename !== '') {
                $head .= "<link href=\"{$this->footerJsFilename}\" as=\"script\" rel=\"preload\">" . PHP_EOL;
            }
        }

        if ($this->headIndividualCssFilenames !== []) {
            foreach ($this->headIndividualCssFilenames as $originalFullFilePath => $filename) {
                if ($this->cssConfig->isPreload($originalFullFilePath)) {
                    $head .= "<link href=\"{$filename}\" as=\"style\" rel=\"preload\">" . PHP_EOL;
                }
            }
        }

        if ($this->headIndividualJsFilenames !== []) {
            foreach ($this->headIndividualJsFilenames as $originalFullFilePath => $filename) {
                if ($this->jsConfig->isPreload($originalFullFilePath)) {
                    $head .= "<link href=\"{$filename}\" as=\"script\" rel=\"preload\">" . PHP_EOL;
                }
            }
        }

        if ($this->footerIndividualCssFilenames !== []) {
            foreach ($this->footerIndividualCssFilenames as $originalFullFilePath => $filename) {
                if ($this->cssConfig->isPreload($originalFullFilePath)) {
                    $head .= "<link href=\"{$filename}\" as=\"style\" rel=\"preload\">" . PHP_EOL;
                }
            }
        }

        if ($this->footerIndividualJsFilenames !== []) {
            foreach ($this->footerIndividualJsFilenames as $originalFullFilePath => $filename) {
                if ($this->jsConfig->isPreload($originalFullFilePath)) {
                    $head .= "<link href=\"{$filename}\" as=\"script\" rel=\"preload\">" . PHP_EOL;
                }
            }
        }
        
        // Подключаем динамический JS (scripts.tpl)
        $commonJsFile = "design/" . $this->getTheme() . "/html/common_js.tpl";
        if (is_file($commonJsFile)) {
            $filename = md5_file($commonJsFile) . json_encode($_GET);
            if (isset($_SESSION['common_js'])) {
                $filename .= json_encode($_SESSION['common_js']);
            }

            $filename = md5($filename);

            $getParams = (!empty($_GET) ? "?" . http_build_query($_GET) : '');
            $head .= "<script src=\"" . Router::generateUrl('common_js', ['fileId' => $filename]) . $getParams . "\"" . ($this->scriptsDefer == true ? " defer" : '') . "></script>" . PHP_EOL;
        } else {
            $logger->error("File \"$commonJsFile\" not found");
        }

        $head .= $this->getIncludeHtml(TC_POSITION_HEAD);

        if ($config->get('dev_mode') == true) {
            $head .= '<style>
                .design_block_parent_element {
                    position: relative;
                    border: 1px solid transparent;
                    min-height: 15px;
                }
                .design_block_parent_element.focus {
                    border: 1px solid red;
                }
                .fn_design_block_name {
                    position: absolute;
                    top: -9px;
                    left: 15px;
                    background-color: #fff;
                    padding: 0 10px;
                    box-sizing: border-box;
                    font-size: 14px;
                    line-height: 14px;
                    font-weight: 700;
                    color: red;
                    cursor: pointer;
                    z-index: 1000;
                }
                .fn_design_block_name:hover {
                    z-index: 1100;
                }
            </style>';
        }

        return $head;

    }
    
    /**
     * Метод возвращает теги на подключение всех зарегистрированных js и css для футера
     * @return string
     * @throws \Exception
     */
    public function footer()
    {
        $SL = ServiceLocator::getInstance();

        /** @var Design $design */
        $design = $SL->getService(Design::class);

        /** @var EntityFactory $entityFactory */
        $entityFactory = $SL->getService(EntityFactory::class);

        /** @var LoggerInterface $logger */
        $logger = $SL->getService(LoggerInterface::class);

        /** @var ManagersEntity $managersEntity */
        $managersEntity = $entityFactory->get(ManagersEntity::class);

        $footer = $this->getIncludeHtml(TC_POSITION_FOOTER);

        if (!empty($_SESSION['admin']) && ($manager = $managersEntity->get($_SESSION['admin']))) {

            $templatesDir = $design->getTemplatesDir();
            $compiledDir = $design->getCompiledDir();

            $design->setTemplatesDir('backend/design/html');
            $design->setCompiledDir('backend/design/compiled');

            // Перевод админки
            /** @var BackendTranslations $backendTranslations */
            $backendTranslations = $SL->getService(BackendTranslations::class);
            $backendTranslations->initTranslations($manager->lang);
            $design->assign('scripts_defer', $this->scriptsDefer);
            $design->assign('btr', $backendTranslations);
            $footer .= $design->fetch('admintooltip.tpl');

            // Возвращаем настройки компилирования файлов smarty
            $design->setTemplatesDir($templatesDir);
            $design->setCompiledDir($compiledDir);

        }

        // Подключаем динамический JS (scripts.tpl)
        $dynamicJsFile = "design/" . $this->getTheme() . "/html/scripts.tpl";
        if (is_file($dynamicJsFile)) {
            $filename = md5_file($dynamicJsFile) . json_encode($_GET);
            if (isset($_SESSION['dynamic_js'])) {
                $filename .= json_encode($_SESSION['dynamic_js']);
            }

            $filename = md5($filename);

            $getParams = (!empty($_GET) ? "?" . http_build_query($_GET) : '');
            $footer .= "<script src=\"" . Router::generateUrl('dynamic_js', ['fileId' => $filename]) . $getParams . "\"" . ($this->scriptsDefer == true ? " defer" : '') . "></script>" . PHP_EOL;
        } else {
            $logger->error("File \"$dynamicJsFile\" not found");
        }

        return $footer;
    }

    public function clearCompiled()
    {
        $cache_directories = [
            $this->compileCssDir,
            $this->compileJsDir,
        ];

        foreach ($cache_directories as $dir) {
            if (is_dir($dir)) {
                foreach (scandir($dir) as $file) {
                    if (!in_array($file, array(".", ".."))) {
                        @unlink($dir . $file);
                    }
                }
            }
        }
    }

    public function getCssVariables()
    {
        return $this->cssConfig->getCssVariables();
    }

    public function updateCssVariables($variables)
    {
        $this->cssConfig->updateCssVariables($variables);
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
            $compiledFilename =   $this->cssConfig->compileIndividual($fullFilePath, $this->compileCssDir, $this->getTheme());
            return !empty($compiledFilename) ? "<link href=\"{$compiledFilename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL : '';
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
            $compiledFilename =  $this->jsConfig->compileIndividual($fullFilePath, $this->compileCssDir, $this->getTheme());
            return !empty($compiledFilename) ? "<script src=\"{$compiledFilename}\"" . ($defer === true ? " defer" : '') . "></script>" . PHP_EOL : '';
        }
        return '';
    }
    
    
    public function compileFiles()
    {
        $this->registerTemplateFiles();

        // Подключаем основной файл стилей
        $this->headCssFilename = $this->cssConfig->compileRegistered(TC_POSITION_HEAD, $this->compileCssDir, $this->getTheme());

        // Подключаем дополнительные индивидуальные файлы стилей
        $this->headIndividualCssFilenames = $this->cssConfig->compileRegisteredIndividual(TC_POSITION_HEAD, $this->compileCssDir, $this->getTheme());

        // Подключаем основной JS файл
        $this->headJsFilename = $this->jsConfig->compileRegistered(TC_POSITION_HEAD, $this->compileJsDir, $this->getTheme());

        // Подключаем дополнительные индивидуальные JS файлы
        $this->headIndividualJsFilenames = $this->jsConfig->compileRegisteredIndividual(TC_POSITION_HEAD, $this->compileJsDir, $this->getTheme());
        
        // footer
        // Подключаем основной файл стилей
        $this->footerCssFilename = $this->cssConfig->compileRegistered(TC_POSITION_FOOTER, $this->compileCssDir, $this->getTheme());

        // Подключаем дополнительные индивидуальные файлы стилей
        $this->footerIndividualCssFilenames = $this->cssConfig->compileRegisteredIndividual(TC_POSITION_FOOTER, $this->compileCssDir, $this->getTheme());

        // Подключаем основной JS файл
        $this->footerJsFilename = $this->jsConfig->compileRegistered(TC_POSITION_FOOTER, $this->compileJsDir, $this->getTheme());

        // Подключаем дополнительные индивидуальные JS файлы
        $this->footerIndividualJsFilenames = $this->jsConfig->compileRegisteredIndividual(TC_POSITION_FOOTER, $this->compileJsDir, $this->getTheme());
        
    }
    
    /**
     * @param string $position
     * @return string html для подключения js и css шаблона
     * @throws \Exception
     */
    private function getIncludeHtml($position = null)
    {
        $includeHtml = '';
        if (empty($position) || $position == TC_POSITION_HEAD) {
            
            // Подключаем основной файл стилей
            if ($this->headCssFilename !== '') {
                $includeHtml .= "<link href=\"{$this->headCssFilename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
            }

            // Подключаем дополнительные индивидуальные файлы стилей
            if ($this->headIndividualCssFilenames !== []) {
                foreach ($this->headIndividualCssFilenames as $filename) {
                    $includeHtml .= "<link href=\"{$filename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
                }
            }

            // Подключаем основной JS файл
            if ($this->headJsFilename !== '') {
                $includeHtml .= "<script src=\"{$this->headJsFilename}\"" . ($this->scriptsDefer == true ? " defer" : '') . "></script>" . PHP_EOL;
            }

            // Подключаем дополнительные индивидуальные JS файлы
            if ($this->headIndividualJsFilenames !== []) {
                foreach ($this->headIndividualJsFilenames as $filename) {
                    $includeHtml .= "<script src=\"{$filename}\"" . ($this->jsConfig->hasDefer($filename) ? " defer" : '') . "></script>" . PHP_EOL;
                }
            }
        } else {
            // Подключаем основной файл стилей
            if ($this->footerCssFilename !== '') {
                $includeHtml .= "<link href=\"{$this->footerCssFilename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
            }

            // Подключаем дополнительные индивидуальные файлы стилей
            if ($this->footerIndividualCssFilenames !== []) {
                foreach ($this->footerIndividualCssFilenames as $filename) {
                    $includeHtml .= "<link href=\"{$filename}\" type=\"text/css\" rel=\"stylesheet\">" . PHP_EOL;
                }
            }

            // Подключаем основной JS файл
            if ($this->footerJsFilename !== '') {
                $includeHtml .= "<script src=\"{$this->footerJsFilename}\"" . ($this->scriptsDefer == true ? " defer" : '') . "></script>" . PHP_EOL;
            }

            // Подключаем дополнительные индивидуальные JS файлы
            if ($this->footerIndividualJsFilenames !== []) {
                foreach ($this->footerIndividualJsFilenames as $filename) {
                    $includeHtml .= "<script src=\"{$filename}\"" . ($this->jsConfig->hasDefer($filename) ? " defer" : '') . "></script>" . PHP_EOL;
                }
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
            $directory .= 'design/' . $this->getTheme() . '/' . $type . '/';
        }
        return $directory . $filename;
    }
    
    private function registerTemplateFiles()
    {
        if ($this->registeredTemplateFiles === true) {
            return;
        }

        if (($themeJs = include 'design/' . $this->getTheme() . '/js.php') && is_array($themeJs)) {
            /** @var Js $jsItem */
            foreach ($themeJs as $jsItem) {
                if ($this->checkFile($jsItem->getFilename(), self::TYPE_JS, $jsItem->getDir()) === true) {
                    $fullPath = $this->getFullPath($jsItem->getFilename(), self::TYPE_JS, $jsItem->getDir());
                    $this->jsConfig->register($jsItem, $fullPath);
                }
            }
        }

        if (($themeCss = include 'design/' . $this->getTheme() . '/css.php') && is_array($themeCss)) {
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

            $moduleThemesDir = $this->module->getModuleDirectory($runningModule['vendor'], $runningModule['module_name']) . 'design/';

            if (file_exists($moduleThemesDir . 'css.php') && ($moduleCss = include $moduleThemesDir . 'css.php') && is_array($moduleCss)) {
                /** @var Css $cssItem */
                foreach ($moduleCss as $cssItem) {
                    if ($cssItem->getDir() === null) {
                        $cssDir = $this->getModuleCssDirGivenPriority($cssItem, $runningModule);
                        $cssItem->setDir($cssDir);
                    }
                    $this->cssConfig->register($cssItem, $cssItem->getDir() . $cssItem->getFilename());
                }
            }

            if (file_exists($moduleThemesDir . 'js.php') && ($moduleJs = include $moduleThemesDir . 'js.php') && is_array($moduleJs)) {

                /** @var Js $jsItem */
                foreach ($moduleJs as $jsItem) {
                    if ($jsItem->getDir() === null) {
                        $jsDir = $this->getModuleJsDirGivenPriority($jsItem, $runningModule);
                        $jsItem->setDir($jsDir);
                    }

                    $this->jsConfig->register($jsItem, $jsItem->getDir() . $jsItem->getFilename());
                }
            }
        }
        $this->registeredTemplateFiles = true;
    }

    /**
     * Метод возвращает путь к css файлу, с учетом того, что его моги переопределить в дизайне для кастомизации
     * 
     * @param Css $cssItem
     * @param $module
     * @return string
     * @throws \Exception
     */
    private function getModuleCssDirGivenPriority(Css $cssItem, $module)
    {
        $moduleThemesDir = $this->module->getModuleDirectory($module['vendor'], $module['module_name']) . 'design/';

        $moduleInnerThemeCssDir = './design/'.$this->getTheme().'/modules/'.$module['vendor'].'/'.$module['module_name'].'/css';
        if (file_exists($moduleInnerThemeCssDir.'/'.$cssItem->getFilename())) {
            return $moduleInnerThemeCssDir;
        }

        return $moduleThemesDir . 'css/';
    }

    /**
     * Метод возвращает путь к js файлу, с учетом того, что его моги переопределить в дизайне для кастомизации
     * 
     * @param Js $jsItem
     * @param $module
     * @return string
     * @throws \Exception
     */
    private function getModuleJsDirGivenPriority(Js $jsItem, $module)
    {
        $moduleThemesDir = $this->module->getModuleDirectory($module['vendor'], $module['module_name']) . 'design/';

        $moduleInnerThemeJsDir = './design/'.$this->getTheme().'/modules/'.$module['vendor'].'/'.$module['module_name'].'/js/';
        if (file_exists($moduleInnerThemeJsDir.$jsItem->getFilename())) {
            return $moduleInnerThemeJsDir;
        }

        return $moduleThemesDir . 'js/';
    }
    
}