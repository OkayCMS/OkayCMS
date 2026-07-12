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
    private const COMPILE_VERSION = 'css-config-block-comment-v2';

    private $templateCss = [];
    private $individualCss = [];

    /** @var array<string, string> */
    private $cssVariables = [];
    private $preloadFiles = [];
    private $filesAttributes = [];

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

            $this->filesAttributes[$fullPath] = $css->getAttributes();
        } else {
            $this->templateCss[$css->getPosition()][$fileId] = $fullPath;
        }
    }

    public function isPreload($filename)
    {
        return isset($this->preloadFiles[$filename]);
    }

    public function getAttributes($filename)
    {
        return $this->filesAttributes[$filename] ?? null;
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

        $settingsContent = file_get_contents($this->settingsFile);
        if ($settingsContent === false) {
            return false;
        }

        $oCssParser = new Parser($settingsContent);
        $oCssDocument = $oCssParser->parse();
        foreach ($oCssDocument->getAllRuleSets() as $oBlock) {
            foreach ($oBlock->getRules() as $r) {
                $rule = $r->getRule();
                if (isset($variables[$rule])) {
                    $value = (string) $variables[$rule];
                    $r->setValue($value);
                    $this->cssVariables[$rule] = $value;
                }
            }
        }

        $renderedCss = trim($oCssDocument->render(OutputFormat::createPretty()));
        $renderedCss = $this->removeGeneratedSettingsHeader($renderedCss);

        $resultFile = $this->getSettingsFileHeader() . $renderedCss . PHP_EOL;
        file_put_contents($this->settingsFile, $resultFile);
    }

    private function getSettingsFileHeader(): string
    {
        $resultFile = '/**' . PHP_EOL;
        $resultFile .= '* Файл стилей для настройки шаблона.' . PHP_EOL;
        $resultFile .= '* Регистрировать этот файл для подключения в шаблоне не нужно' . PHP_EOL;
        $resultFile .= '*/' . PHP_EOL . PHP_EOL;

        return $resultFile;
    }

    private function removeGeneratedSettingsHeader(string $content): string
    {
        $pattern = '~\s*/\*\*?\s*\R\*\s*Файл стилей для настройки шаблона\.\s*\R'
            . '\*\s*Регистрировать этот файл для подключения в шаблоне не нужно\s*\R\*/\s*~u';
        $content = preg_replace($pattern, '', $content);

        return trim(is_string($content) ? $content : '');
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
            $compiledFilename .= self::COMPILE_VERSION;

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
            foreach ($this->templateCss[$position] as $k => $fullFilePath) {
                $inputFileName = pathinfo($fullFilePath, PATHINFO_BASENAME);
                $tmpMapFile = $inputFileName . '.map';

                $tmpCompiledFilename = $compileCssDir . $inputFileName;
                $this->compileFile($fullFilePath, $tmpCompiledFilename);

                $content = file_get_contents($tmpCompiledFilename);
                if ($content === false) {
                    $content = '';
                }

                $content = preg_replace('~/\*# sourceMappingURL.*\*/$~s', '', $content);
                $content = is_string($content) ? $content : '';
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
            $resultFile[] = "\n/*# sourceMappingURL=" . $mapFile . " */\n";
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
     *
     * @return array<string, string>
     */
    public function compileRegisteredIndividual($position, $compileCssDir, $compiledFilenamePrefix = null)
    {
        $result = [];
        if (!empty($this->individualCss[$position])) {
            foreach ($this->individualCss[$position] as $k => $fullFilePath) {
                $hash = md5(self::COMPILE_VERSION . md5_file($fullFilePath) . (file_exists($this->settingsFile) ? md5_file($this->settingsFile) : ''));
                $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . $hash . '.css';

                if (isset($this->filesAttributes[$fullFilePath])) {
                    $this->filesAttributes[$compiledFilename] = $this->filesAttributes[$fullFilePath];
                    unset($this->filesAttributes[$fullFilePath]);
                }

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
        $hash = md5(self::COMPILE_VERSION . md5_file($fullFilePath) . (file_exists($this->settingsFile) ? md5_file($this->settingsFile) : ''));
        $compiledFilename = $compileCssDir . (!empty($compiledFilenamePrefix) ? $compiledFilenamePrefix . '.' : '') . pathinfo($fullFilePath, PATHINFO_BASENAME) . '.' . $hash . '.css';

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
        // SourceMap uses magic methods for properties, property $file is documented in stub file
        $map->file = $compiledFilename;
        $generated = $position->generated;
        $source = $position->source;

        $generated->line = 0;
        $generated->column = 0;

        $source->fileName = Request::getRootUrl() . '/' . str_replace($this->rootDir, '', $fullFilePath);
        $sourceLine = 0;
        $generatedLine = 0;
        $blockComment = false;

        foreach (file($fullFilePath) ?: [] as $line) {
            if ($line === '') {
                continue;
            }

            $line = $this->stripCssBlockComments($line, $blockComment);
            $line = rtrim($line);

            if (strtolower(pathinfo($fullFilePath, PATHINFO_EXTENSION)) == 'css') {
                $line = $this->setCssVariables($line, $fullFilePath);
            }

            if ($line !== '') {
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

        $resultFile[] = "\n/*# sourceMappingURL=" . pathinfo($mapFile, PATHINFO_BASENAME) . " */\n";
        $map->save($mapFile);
        $this->saveCompileFile(implode("", $resultFile), $compiledFilename);
    }

    private function stripCssBlockComments(string $line, bool &$blockComment): string
    {
        $result = '';
        $offset = 0;

        while ($offset < strlen($line)) {
            if ($blockComment === true) {
                $commentEnd = strpos($line, '*/', $offset);
                if ($commentEnd === false) {
                    return $result;
                }

                $offset = $commentEnd + 2;
                $blockComment = false;
                continue;
            }

            $commentStart = strpos($line, '/*', $offset);
            if ($commentStart === false) {
                $result .= substr($line, $offset);
                break;
            }

            $result .= substr($line, $offset, $commentStart - $offset);
            $commentEnd = strpos($line, '*/', $commentStart + 2);
            if ($commentEnd === false) {
                $blockComment = true;
                break;
            }

            $offset = $commentEnd + 2;
        }

        return $result;
    }

    private function setCssVariables($cssLine, $file)
    {

        if (empty($this->cssVariables)) {
            $this->initCssVariables();
        }

        // Вычисляем директорию, для подключения ресурсов из css файла (background-image: url() etc.)
        $subDir = trim(str_replace($this->rootDir, '', pathinfo($file, PATHINFO_DIRNAME)), "/\\");
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
        //        if (strpos($cssLine, 'url') !== false) {
        //            //$cssLine = preg_replace('~url\s*\(\s*(\'|")(?!data)~', 'url($1../../' . $subDir . '/', $cssLine);
        //            $cssLine = preg_replace('~url\s*\(\s*(\'|")?(?!data)(.*?)\1?\)~', 'url($1../../' . $subDir . '/$2$1)', $cssLine);
        //        }

        return $cssLine;
    }

    private function initCssVariables()
    {
        if (empty($this->cssVariables) && file_exists($this->settingsFile)) {
            $settingsContent = file_get_contents($this->settingsFile);
            if ($settingsContent === false) {
                return;
            }

            $oCssParser = new Parser($settingsContent);
            $oCssDocument = $oCssParser->parse();
            foreach ($oCssDocument->getAllRuleSets() as $oBlock) {
                foreach ($oBlock->getRules() as $r) {
                    $value = $r->getValue();
                    // In PHP 8.3+, Color and other Value objects cannot be cast to string directly
                    // Use render() method to convert to string
                    if (is_object($value)) {
                        if (!method_exists($value, 'render')) {
                            continue;
                        }

                        $css_value = $value->render(OutputFormat::createCompact());
                    } else {
                        $css_value = (string)$value;
                    }

                    $rule = $r->getRule();
                    if (strpos($rule, '--') === 0) {
                        $this->cssVariables[$rule] = $css_value;
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
