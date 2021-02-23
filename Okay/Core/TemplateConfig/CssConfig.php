<?php


namespace Okay\Core\TemplateConfig;


use axy\sourcemap\PosMap;
use axy\sourcemap\SourceMap;
use Okay\Core\Request;
use Okay\Core\TemplateConfig\Css as TemplateCss;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;

class CssConfig
{

    private $templateCss = [];
    private $individualCss = [];
    private $cssVariables = [];
    private $preloadFiles = [];

    private $rootDir;
    private $settingsFile;

    public function __construct($rootDir, $settingsFile)
    {
        $this->rootDir = $rootDir;
        $this->settingsFile = $settingsFile;
    }

    /**
     * @param Css $css
     * @param string $fullPath полный путь к файлу
     */
    public function register(TemplateCss $css, $fullPath)
    {
        $fileId = md5($fullPath);
        if ($css->getIndividual() === true) {
            $this->individualCss[$css->getPosition()][$fileId] = $fullPath;

            if ($css->getPreload() === true) {
                $this->preloadFiles[$fullPath] = $fullPath;
            }
            
        } else {
            $this->templateCss[$css->getPosition()][$fileId] = $fullPath;
        }
    }

    public function isPreload($filename)
    {
        return isset($this->preloadFiles[$filename]);
    }
    
    public function getCssVariables()
    {

        if (empty($this->cssVariables)) {
            $this->initCssVariables();
        }

        return $this->cssVariables;
    }

    public function updateCssVariables($variables)
    {

        if (empty($variables)) {
            return false;
        }

        if (empty($this->cssVariables)) {
            $this->initCssVariables();
        }

        $oCssParser = new Parser(file_get_contents($this->settingsFile));
        $oCssDocument = $oCssParser->parse();
        foreach ($oCssDocument->getAllRuleSets() as $oBlock) {
            foreach ($oBlock->getRules() as $r) {
                if (isset($variables[$r->getRule()])) {
                    $r->setValue($variables[$r->getRule()]);
                    $this->cssVariables[$r->getRule()] = $variables[$r->getRule()];
                }
            }
        }

        $resultFile = '/**' . PHP_EOL;
        $resultFile .= '* Файл стилей для настройки шаблона.' . PHP_EOL;
        $resultFile .= '* Регистрировать этот файл для подключения в шаблоне не нужно' . PHP_EOL;
        $resultFile .= '*/' . PHP_EOL . PHP_EOL;

        $resultFile .= trim($oCssDocument->render(OutputFormat::createPretty())) . PHP_EOL;
        file_put_contents($this->settingsFile, $resultFile);
    }
    
    /**
     * @param string $position head|footer указание куда файл генерируется
     * @param string $compileCssDir путь к директории, в которой нужно сохранить скомпилированные css файлы
     * @param string $compiledFilenamePrefix префикс имени скомпилированного файла. Может понадобиться для компиляции 
     * файлов для разных тем.
     * Метод компилирует все зарегистрированные, через метод registerCss(), CSS файлы
     * Собираются они в одном общем выходном файле, в кеше
     * Также здесь подставляются значения переменных CSS.
     * @return string|null
     */
    public function compileRegistered($position, $compileCssDir, $compiledFilenamePrefix = null)
    {

        $resultFile = [];
        $compiledFilename = '';
        if (!empty($this->templateCss[$position])) {

            // Определяем название выходного файла, на основании хешей всех входящих файлов
            foreach ($this->templateCss[$position] as $file) {
                $compiledFilename .= md5_file($file) . (file_exists($this->settingsFile) ? md5_file($this->settingsFile) : '');
            }

            $filenameHash = md5($compiledFilename);
            $mapFile = (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . $position . '.' . $filenameHash . '.css.map';

            $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . $position . '.' . $filenameHash . '.css';
            // Если файл уже скомпилирован, отдаем его.
            if (file_exists($compiledFilename)) {
                // Обновляем дату редактирования файла, чтобы он не инвалидировался
                touch($compiledFilename);
                return $compiledFilename;
            }

            $map = new SourceMap();
            $lineNum = 0;
            foreach ($this->templateCss[$position] as $k=>$fullFilePath) {
                $inputFileName = pathinfo($fullFilePath, PATHINFO_BASENAME);
                $tmpMapFile = $inputFileName . '.map';

                $tmpCompiledFilename = $compileCssDir . $inputFileName;
                $this->compileFile($fullFilePath, $tmpCompiledFilename);

                $content = file_get_contents($tmpCompiledFilename);
                $content = preg_replace('~/\*# sourceMappingURL.*\*/$~s', '', $content);
                $content = rtrim($content);
                $resultFile[] = $content;

                $tmpMap = SourceMap::loadFromFile($compileCssDir . $tmpMapFile);
                $map->concat($tmpMap, $lineNum);
                unset($tmpMap);
                $lineNum += 1;
                $resultFile[] = PHP_EOL;
                unlink($compileCssDir . $inputFileName);
                unlink($compileCssDir . $tmpMapFile);
            }
            $resultFile[] = "\n/*# sourceMappingURL=".$mapFile." */\n";
            $map->save($compileCssDir . $mapFile);
        }

        $this->saveCompileFile(implode("", $resultFile), $compiledFilename);

        return $compiledFilename;
    }

    /**
     * @param string $position head|footer указание куда файл генерируется
     * @param string $compileCssDir путь к директории, в которой нужно сохранить скомпилированные css файлы
     * @param string $compiledFilenamePrefix префикс имени скомпилированного файла. Может понадобиться для компиляции
     * файлов для разных тем.
     * Метод компилирует зарегистрированные, через метод registerCss(), CSS индивидуальные файлы
     * Также здесь подставляются значения переменных CSS.
     * @return array
     */
    public function compileRegisteredIndividual($position, $compileCssDir, $compiledFilenamePrefix = null)
    {
        $result = [];
        if (!empty($this->individualCss[$position])) {

            foreach ($this->individualCss[$position] as $k=>$fullFilePath) {
                $hash = md5(md5_file($fullFilePath) . (file_exists($this->settingsFile) ? md5_file($this->settingsFile) : ''));
                $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . $hash . '.css';
                
                $result[$fullFilePath] = $compiledFilename;

                if (file_exists($compiledFilename)) {
                    // Обновляем дату редактирования файла, чтобы он не инвалидировался
                    touch($compiledFilename);
                } else {
                    $this->compileFile($fullFilePath, $compiledFilename);
                }
                // Удаляем скомпилированный файл из зарегистрированных, чтобы он повторно не компилировался
                unset($this->individualCss[$position][$k]);
            }
        }
        return $result;
    }

    public function compileIndividual($fullFilePath, $compileCssDir, $compiledFilenamePrefix = null)
    {
        $hash = md5(md5_file($fullFilePath) . (file_exists($this->settingsFile) ? md5_file($this->settingsFile) : ''));
        $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . '.' . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . $hash . '.css';

        if (file_exists($compiledFilename)) {
            // Обновляем дату редактирования файла, чтобы он не инвалидировался
            touch($compiledFilename);
        } else {
            $this->compileFile($fullFilePath, $compiledFilename);
        }

        return $compiledFilename;
    }
    
    /**
     * @param string $fullFilePath абсолютный путь к файлу в ФС
     * @param string $compiledFilename относительный путь к файлу
     */
    private function compileFile($fullFilePath, $compiledFilename)
    {
        $map = new SourceMap();
        $position = new PosMap(null);
        $mapFile = $compiledFilename . '.map';
        $map->file = $compiledFilename;
        $generated = $position->generated;
        $source = $position->source;

        $generated->line = 0;
        $generated->column = 0;

        $source->fileName = Request::getRootUrl() . '/' . str_replace($this->rootDir, '', $fullFilePath);
        $sourceLine = 0;
        $generatedLine = 0;
        $blockComment = false;

        foreach (file($fullFilePath) as $line) {
            if ($line === '') {
                continue;
            }

            // Проверяем что мы не в блоке комментариев
            $clearLine = $line;
            if (($posComment = strpos($line, '/*')) !== false) {
                $blockComment = true;
                $clearLine = substr($line, 0, $posComment);
            }

            if ($blockComment === true && ($posComment = strpos($line, '*/')) !== false) {
                $blockComment = false;
                $clearLine = substr($line, $posComment+2);
            }

            $line = $clearLine;
            $line = rtrim($line);

            if (strtolower(pathinfo($fullFilePath, PATHINFO_EXTENSION)) == 'css') {
                $line = $this->setCssVariables($line, $fullFilePath);
            }

            if ($blockComment === false) {

                $lenPre = strlen($line);
                $line = ltrim($line);
                $generatedStrLen = strlen($line);
                $sourceLenLine = $lenPre - $generatedStrLen;

                $source->line = $sourceLine;
                $source->column = $sourceLenLine;

                if (strtolower(pathinfo($fullFilePath, PATHINFO_EXTENSION)) == 'css') {
                    $resultFile[] = $line;
                }

                $map->addPosition(clone $position);
                $generated->column += $generatedStrLen;
                $generated->line = $generatedLine;

            }
            $sourceLine++;
        }

        $resultFile[] = "\n/*# sourceMappingURL=".pathinfo($mapFile, PATHINFO_BASENAME)." */\n";
        $map->save($mapFile);
        $this->saveCompileFile(implode("", $resultFile), $compiledFilename);
    }

    private function setCssVariables($cssLine, $file)
    {

        if (empty($this->cssVariables)) {
            $this->initCssVariables();
        }

        // Вычисляем директорию, для подключения ресурсов из css файла (background-image: url() etc.)
        $subDir = trim(substr(pathinfo($file, PATHINFO_DIRNAME), strlen($this->rootDir)), "/\\");
        $subDir = dirname($subDir);

        // Переназначаем переменные из файла настроек шаблона
        $var = trim(preg_replace('~^.+?\s*:\s*var\((.+)?\).*$~', '$1', $cssLine));

        if (isset($this->cssVariables[trim($var)])) {
            $cssLine = str_replace("var({$var})", $this->cssVariables[trim($var)], $cssLine);
        }

        // Перебиваем в файле все относительные пути
        if (strpos($cssLine, 'url') !== false && strpos($cssLine, '..') !== false) {
            $cssLine = strtr($cssLine, ['../' => '../../' . $subDir . '/']);
        }

        return $cssLine;
    }
    
    private function initCssVariables()
    {
        if (empty($this->cssVariables) && file_exists($this->settingsFile)) {
            $oCssParser = new Parser(file_get_contents($this->settingsFile));
            $oCssDocument = $oCssParser->parse();
            foreach ($oCssDocument->getAllRuleSets() as $oBlock) {
                foreach ($oBlock->getRules() as $r) {
                    $css_value = (string)$r->getValue();
                    if (strpos($r->getRule(), '--') === 0) {
                        $this->cssVariables[$r->getRule()] = $css_value;
                    }
                }
            }
        }
    }
    
    /**
     * @param $content
     * @param $file
     * Метод сохраняет скомпилированный css в кеш
     */
    private function saveCompileFile($content, $file)
    {
        if (!empty($content)) {
            // Сохраняем скомпилированный CSS
            file_put_contents($file, $content);
        }
    }
    
}