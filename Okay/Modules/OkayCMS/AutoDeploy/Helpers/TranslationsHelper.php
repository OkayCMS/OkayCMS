<?php


namespace Okay\Modules\OkayCMS\AutoDeploy\Helpers;


use Okay\Core\Settings;
use Okay\Core\TemplateConfig;

class TranslationsHelper
{
    
    /** @var TemplateConfig */
    private $templateConfig;
    
    /** @var Settings */
    private $settings;
    
    private $vars;
    private $localLangDir;

    public function __construct(TemplateConfig $templateConfig, Settings $settings)
    {
        $this->templateConfig = $templateConfig;
        $this->settings = $settings;
        
        $this->localLangDir = __DIR__ . '/../../../../../design/' . $this->templateConfig->getTheme() . '/lang/';
    }

    public function initOneTranslation($label = "")
    {
        if (empty($label)) {
            return false;
        }

        if (!isset($this->vars[$label])) {

            $langFile = __DIR__ . '/../../../../../design/' . $this->templateConfig->getTheme() . '/lang/' . $label . '.php';

            if (file_exists($langFile)) {
                $lang = [];
                require $langFile;

                // Подключаем файл переводов по умолчанию, но с возможностью переопределить в самом шаблоне
                $fileLangGeneral = __DIR__ . '/../../../../lang_general/' . $label . '.php';
                if (file_exists($fileLangGeneral)) {
                    $lang_general = [];
                    require $fileLangGeneral;
                    $lang = $lang + $lang_general;
                }

                $this->vars[$label] = $lang;
            } else {
                $this->vars[$label] = [];
            }
        }

        return $this->vars[$label];
    }

    /**
     * Метод дополняет переводы локальными
     * 
     * @param $translations
     * @param $langLabel
     * @return array
     */
    public function addLocalTranslations($translations, $langLabel)
    {
        $langFile = $this->localLangDir . 'local.' . $langLabel . '.php';
        if (file_exists($langFile)) {
            $lang = [];
            include $langFile;
            
            // Перезаписываем локальные переводы
            $translations = $lang + $translations;
        }
        
        return $translations;
    }
    
    public function writeTranslations($langLabel, $translations)
    {

        // На локалке не нужно записывать локальные переводы
        if (!($channel = $this->settings->get('deploy_build_channel')) || $channel == 'local') {
            return;
        }
        
        $currentTranslations = $this->initOneTranslation($langLabel);
        $content = "<?php\n\n";
        $content .= "\$lang = array();\n";
        foreach($translations as $label=>$value) {
            if ((isset($currentTranslations[$label]) && $currentTranslations[$label] != $value)) {
                
                $content .= "\$lang['" . $label . "'] = \"" . addcslashes($value, "\n\r\\\"") . "\";\n";
            }
        }

        $langFile = $this->localLangDir . 'local.' . $langLabel . '.php';

        $file = fopen($langFile, 'w');
        fwrite($file, $content);
        fclose($file);
        
        // Удалим временный файл
        unlink(__DIR__ . '/../tmp/' . $langLabel . '.php');
    }
}