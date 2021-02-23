<?php


namespace Okay\Core\TemplateConfig;


use MatthiasMullie\Minify\JS as JsMinifier;

class JsConfig
{

    private $templateJs = [];
    private $individualJs = [];
    private $deferJsFiles = [];
    private $preloadFiles = [];

    /**
     * @param Js $js
     * @param string $fullPath полный путь к файлу
     */
    public function register(Js $js, $fullPath)
    {
        $fileId = md5($fullPath);
        if ($js->getIndividual() === true) {
            if ($js->getDefer() === true) {
                $this->deferJsFiles[$fullPath] = $fullPath;
            }
            if ($js->getPreload() === true) {
                $this->preloadFiles[$fullPath] = $fullPath;
            }
            $this->individualJs[$js->getPosition()][$fileId] = $fullPath;
        } else {
            $this->templateJs[$js->getPosition()][$fileId] = $fullPath;
        }
    }

    public function hasDefer($filename)
    {
        return isset($this->deferJsFiles[$filename]);
    }

    public function isPreload($filename)
    {
        return isset($this->preloadFiles[$filename]);
    }
    
    /**
     * Метод компилирует все зарегистрированные JS файлы
     * @param string $position head|footer указание куда файл генерируется
     * @param string $compileJsDir путь к директории, в которой нужно сохранить скомпилированные css файлы
     * @param string $compiledFilenamePrefix префикс имени скомпилированного файла. Может понадобиться для компиляции
     * @return string compiled filename
     */
    public function compileRegistered($position, $compileJsDir, $compiledFilenamePrefix = null)
    {

        $resultFile = '';
        $compiledFilename = '';
        if (!empty($this->templateJs[$position])) {

            // Определяем название выходного файла, на основании хешей всех входящих файлов
            foreach ($this->templateJs[$position] as $file) {
                $compiledFilename .= md5_file($file);
            }

            $compiledFilename = $compileJsDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . $position . '.' . md5($compiledFilename) . '.js';

            // Если файл уже скомпилирован, отдаем его.
            if (file_exists($compiledFilename)) {
                // Обновляем дату редактирования файла, чтобы он не инвалидировался
                touch($compiledFilename);
                return $compiledFilename;
            }

            foreach ($this->templateJs[$position] as $k=>$file) {
                $filename = pathinfo($file, PATHINFO_BASENAME);

                $resultFile .= '/*! #File ' . $filename . ' */' . PHP_EOL;
                $resultFile .= file_get_contents($file) . PHP_EOL . PHP_EOL;

                // Удаляем скомпилированный файл из зарегистрированных, чтобы он повторно не компилировался
                unset($this->templateJs[$position][$k]);
            }
        }

        $minifier = new JsMinifier();
        $minifier->add($resultFile);
        $resultFile = $minifier->minify();

        $this->saveCompileFile($resultFile, $compiledFilename);

        return $compiledFilename;
    }

    /**
     * Метод компилирует зарегистрированные индивидуальные JS файлы
     * @param string $position head|footer указание куда файл генерируется
     * @param string $compileJsDir путь к директории, в которой нужно сохранить скомпилированные css файлы
     * @param string $compiledFilenamePrefix префикс имени скомпилированного файла. Может понадобиться для компиляции
     * @return array
     */
    public function compileRegisteredIndividual($position, $compileJsDir, $compiledFilenamePrefix = null)
    {
        $result = [];
        if (!empty($this->individualJs[$position])) {

            foreach ($this->individualJs[$position] as $k=>$fullFilePath) {

                $compiledFilename = $compileJsDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . md5_file($fullFilePath) . '.js';
                $result[$fullFilePath] = $compiledFilename;

                if (isset($this->deferJsFiles[$fullFilePath])) {
                    $this->deferJsFiles[$compiledFilename] = $compiledFilename;
                    unset($this->deferJsFiles[$fullFilePath]);
                }

                if (file_exists($compiledFilename)) {
                    // Обновляем дату редактирования файла, чтобы он не инвалидировался
                    touch($compiledFilename);
                } else {
                    $result_file = file_get_contents($fullFilePath) . PHP_EOL . PHP_EOL;

                    $minifier = new JsMinifier();
                    $minifier->add($result_file);
                    $result_file = $minifier->minify();

                    $this->saveCompileFile($result_file, $compiledFilename);
                }
                // Удаляем скомпилированный файл из зарегистрированных, чтобы он повторно не компилировался
                unset($this->individualJs[$position][$k]);
            }
        }
        return $result;
    }

    public function compileIndividual($fullFilePath, $compileCssDir, $compiledFilenamePrefix = null)
    {
        $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . md5_file($fullFilePath) . '.js';

        if (file_exists($compiledFilename)) {
            // Обновляем дату редактирования файла, чтобы он не инвалидировался
            touch($compiledFilename);
        } else {
            $result_file = file_get_contents($fullFilePath) . PHP_EOL . PHP_EOL;

            $minifier = new JsMinifier();
            $minifier->add($result_file);
            $result_file = $minifier->minify();

            $this->saveCompileFile($result_file, $compiledFilename);
        }

        return $compiledFilename;
    }

    public static function minifyJs($jsString)
    {
        $minifier = new JsMinifier();
        $minifier->add($jsString);

        return $minifier->minify();
    }

    /**
     * @param $content
     * @param $file
     * Метод сохраняет скомпилированный js в кеш
     */
    private function saveCompileFile($content, $file)
    {
        if (!empty($content)) {
            // Сохраняем скомпилированный CSS
            file_put_contents($file, $content);
        }
    }
}