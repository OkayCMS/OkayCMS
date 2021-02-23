<?php


namespace Okay\Core\Modules;


use Okay\Core\Config;
use Okay\Core\TemplateConfig\FrontTemplateConfig;

class ModuleDesign
{
    /**
     * @var Module
     */
    private $module;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FrontTemplateConfig
     */
    private $frontTemplateConfig;

    public function __construct(Module $module, FrontTemplateConfig $frontTemplateConfig, Config $config)
    {
        $this->module         = $module;
        $this->config         = $config;
        $this->frontTemplateConfig = $frontTemplateConfig;
    }

    public function getAllFiles($vendor, $moduleName)
    {
        $templateFiles = $this->getTemplateFiles($vendor, $moduleName);
        $jsFiles       = $this->getJsFiles($vendor, $moduleName);
        $cssFiles      = $this->getCssFiles($vendor, $moduleName);
        $langFiles     = $this->getLangFiles($vendor, $moduleName);

        return array_merge($templateFiles, $jsFiles, $cssFiles, $langFiles);
    }

    public function getTemplateFiles($vendor, $moduleName)
    {
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);
        $baseDir   = $this->config->get('root_dir') . $moduleDir . '/design/html/';

        $files = $this->getFiles($baseDir);
        foreach($files as $file) {
            $file->directory = "html/{$file->directory}";
        }

        return $this->markClonedToThemeFiles($files, $vendor, $moduleName);
    }

    public function getJsFiles($vendor, $moduleName)
    {
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);
        $baseDir   = $this->config->get('root_dir') . $moduleDir . '/design/js/';

        $files = $this->getFiles($baseDir);
        foreach($files as $file) {
            $file->directory = "js/{$file->directory}";
        }

        return $this->markClonedToThemeFiles($files, $vendor, $moduleName);
    }

    public function getCssFiles($vendor, $moduleName)
    {
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);
        $baseDir   = $this->config->get('root_dir') . $moduleDir . '/design/css/';

        $files = $this->getFiles($baseDir);
        foreach($files as $file) {
            $file->directory = "css/{$file->directory}";
        }

        return $this->markClonedToThemeFiles($files, $vendor, $moduleName);
    }

    public function getLangFiles($vendor, $moduleName)
    {
        $moduleDir = $this->module->getModuleDirectory($vendor, $moduleName);
        $baseDir   = $this->config->get('root_dir') . $moduleDir . '/design/lang/';

        $files = $this->getFiles($baseDir);
        foreach($files as $file) {
            $file->directory = "lang/{$file->directory}";
        }

        return $this->markClonedToThemeFiles($files, $vendor, $moduleName);
    }

    public function cloneFileToTheme($file, $vendor, $moduleName)
    {
        $moduleFile      = $this->config->get('root_dir').$this->module->getModuleDirectory($vendor, $moduleName)."design/{$file}";
        $themeModuleFile = $this->config->get('root_dir').$this->getThemeModuleDir($vendor, $moduleName)."/{$file}";

        if (file_exists($themeModuleFile)) {
            return false;
        }

        if (! file_exists($moduleFile)) {
            throw new \Exception("File not exists {$moduleFile}");
        }

        $this->createPathDirToFileIfNeeded($themeModuleFile);
        $result = (bool) copy($moduleFile, $themeModuleFile);
        chmod($themeModuleFile, 0644);
        return $result;
    }

    public function cloneFileSetToTheme(array $files, $vendor, $moduleName)
    {
        if (empty($files)) {
            return;
        }

        foreach($files as $file) {
            $this->cloneFileToTheme($file, $vendor, $moduleName);
        }
    }

    private function markClonedToThemeFiles($files, $vendor, $moduleName)
    {
        foreach($files as $file) {
            $file->cloned_to_theme = (int) $this->fileClonedToTheme($file, $vendor, $moduleName);
        }

        return $files;
    }

    private function createPathDirToFileIfNeeded($filePath)
    {
        $themeRootDir          = $this->config->get('root_dir')."design/".$this->frontTemplateConfig->getTheme()."/";
        $validateRootThemePath = substr($filePath, 0, strlen($themeRootDir));

        if ($themeRootDir !== $validateRootThemePath) {
            throw new \Exception("Incorrect absolute path to file {$filePath}");
        }

        $themeFilePath = substr($filePath, strlen($themeRootDir));
        $dirNames      = $this->getDirNames($themeFilePath);

        $fullPathDir = substr($themeRootDir, 0, -1);
        foreach($dirNames as $dirName) {
            $fullPathDir .= "/{$dirName}";

            if (is_dir($fullPathDir)) {
                continue;
            }

            mkdir($fullPathDir);
            chmod($fullPathDir, 0755);
        }
    }

    private function getDirNames($filePath)
    {
        $parts = explode('/', $filePath);

        end($parts);
        $fileIndex = key($parts);
        unset($parts[$fileIndex]);

        return $parts;
    }

    private function getFiles($baseDir)
    {
        if (!is_dir($baseDir)) {
            return [];
        }

        $files = [];
        $traversalTpl = function ($dir = '') use (&$traversalTpl, &$files, $baseDir) {
            if (!is_dir($baseDir . $dir)) {
                return;
            }

            $fileNames = $this->getDirContent($baseDir . $dir);
            foreach ($fileNames as $fileName) {
                if (is_dir($fileName)) {
                    $traversalTpl($dir . $fileName . '/');
                }

                $file = new \stdClass();
                $file->filename        = $fileName;
                $file->directory       = $dir;

                $files[] = $file;
            }
        };
        $traversalTpl();

        return $files;
    }

    private function getThemeModuleDir($vendor, $moduleName)
    {
        $themeName = $this->frontTemplateConfig->getTheme();
        return "design/{$themeName}/modules/{$vendor}/{$moduleName}";
    }

    private function getDirContent($dir)
    {
        $fileNames = scandir($dir);
        return array_splice($fileNames, 2);
    }

    private function fileClonedToTheme($file, $vendor, $moduleName)
    {
        return file_exists($this->config->get('root_dir').$this->getThemeModuleDir($vendor, $moduleName)."/{$file->directory}/{$file->filename}");
    }
}