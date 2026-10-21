<?php

namespace Okay\Core;

use Okay\Core\Modules\Modules;
use Psr\Log\LoggerInterface;

class BackendTranslations
{
    /** @var array<string, string> */
    private $translations = [];

    /** @var array<string, string>|null */
    private $_langEn = null;

    private $_logger;
    private $_modules;
    private $_initializedLang;
    private $_debugTranslation;

    public function __construct(LoggerInterface $logger, Modules $modules, $debugTranslation = false)
    {
        $this->_logger = $logger;
        $this->_modules = $modules;
        $this->_debugTranslation = (bool)$debugTranslation;
    }
    
    public function getLangLabel()
    {
        return $this->_initializedLang;
    }
    
    public function initTranslations($langLabel = 'en')
    {
        if ($this->_initializedLang === $langLabel) {
            return;
        }

        $this->translations = [];

        // Загрузка базового перевода админки
        $file = "backend/lang/" . $langLabel . ".php";
        if (!file_exists($file)) {
            foreach (glob("backend/lang/??.php") as $f) {
                $file = "backend/lang/" . pathinfo($f, PATHINFO_FILENAME) . ".php";
                break;
            }
        }

        $lang = [];
        require_once($file);

        foreach ($lang as $var => $translation) {
            $this->addTranslation($var, $translation);
        }

        foreach ($this->_modules->getRunningModules() as $runningModule) {
            foreach ($this->_modules->getModuleBackendTranslations(
                $runningModule['vendor'], $runningModule['module_name'], $langLabel
            ) as $var => $translation) {
                $this->addTranslation($var, $translation);
            }
        }

        $this->_initializedLang = $langLabel;
    }

    public function getTranslation(string $var)
    {
        if (isset($this->translations[$var])) {
            return $this->translations[$var];
        }

        // Подгружаем английский перевод если нет в текущем языке
        if ($this->_langEn === null) {
            $lang = [];
            require_once("backend/lang/en.php");
            $this->_langEn = $lang;
        }

        if (isset($this->_langEn[$var])) {
            $translation = $this->_langEn[$var];
            if ($this->_debugTranslation) {
                $translation .= '<b style="color: red!important;">$btr->' . $var . ' from other language</b>';
            }
            return $translation;
        }

        if ($this->_debugTranslation) {
            return '<b style="color: red!important;">$btr->' . $var . ' not exists</b>';
        }

        return false;
    }

    /**
     * @param string $var
     * @param string $translation
     * добавление перевода к уже существующему набору
     */
    public function addTranslation(string $var, string $translation): void
    {
        $var = preg_replace('~[^\w]~', '', $var);
        $this->translations[$var] = $translation;
    }

    public function __get(string $var)
    {
        return $this->getTranslation($var);
    }
}