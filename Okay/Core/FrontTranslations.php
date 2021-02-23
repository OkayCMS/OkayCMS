<?php


namespace Okay\Core;


use Okay\Core\Modules\Modules;
use Okay\Entities\TranslationsEntity;

class FrontTranslations
{
    
    private $_debugTranslation;
    private $_entityFactory;
    private $_languages;
    private $_modules;
    
    public function __construct(EntityFactory $entityFactory, Languages $languages, Modules $modules, $debugTranslation = false)
    {
        $this->_debugTranslation = (bool)$debugTranslation;
        $this->_entityFactory = $entityFactory;
        $this->_languages = $languages;
        $this->_modules = $modules;
        
        $this->init();
    }

    public function init()
    {
        $langLabel = $this->_languages->getLangLabel();

        /** @var TranslationsEntity $translations */
        $translations = $this->_entityFactory->get(TranslationsEntity::class);
        foreach ($translations->find(['lang' => $langLabel]) as $var=>$value) {
            $this->$var = $value;
        }

        // Дополняем переводы из активных модулей 
        foreach ($this->_modules->getRunningModules() as $runningModule) {
            foreach ($this->_modules->getModuleFrontTranslations($runningModule['vendor'], $runningModule['module_name'], $langLabel) as $var=>$value) {
                $this->$var = $value;
            }
        }
    }
    
    public function __get($var)
    {
        // Если не нашли перевода на текущем языке, посмотрим может есть этот перевод на основном языке или уже на английском
        /** @var TranslationsEntity $translations */
        $translations = $this->_entityFactory->get(TranslationsEntity::class);
        $mainLanguage = $this->_languages->getMainLanguage();
        $res = $translations->get($var);
        if (isset($res->{'lang_' . $mainLanguage->label}) || isset($res->lang_en)) {
            if (isset($res->{'lang_' . $mainLanguage->label})) {
                $translation = $res->{'lang_' . $mainLanguage->label};
            } else {
                $translation = $res->lang_en;
            }
            
            $this->$var = $translation;
            
            // Если включили дебаг переводов, выведим соответствующее сообщение на неизвестный перевод
            if ($this->_debugTranslation === true) {
                $translation .= '<b style="color: red!important;">$lang->' . $var . ' from other language</b>';
            }
            return $translation;
        } elseif ($this->_debugTranslation === true) {
            return '<b style="color: red!important;">$lang->' . $var . ' not exists</b>';
        }
    }
    
    public function getTranslation($var)
    {
        return $this->$var;
    }
    
    public function addTranslation($var, $translation)
    {
        $var = preg_replace('~[^\w]~', '', $var);
        $this->$var = $translation;
    }
}
