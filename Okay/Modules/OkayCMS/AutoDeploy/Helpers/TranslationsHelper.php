<?php


namespace Okay\Modules\OkayCMS\AutoDeploy\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Modules;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\TranslationsEntity;

class TranslationsHelper
{
    const TRANS_T_LOCAL = 'local';


    /** @var FrontTemplateConfig */
    private $frontTemplateConfig;

    /** @var Settings */
    private $settings;

    /** @var Modules */
    private $modules;


    /** @var LanguagesEntity */
    private $languagesEntity;

    /** @var TranslationsEntity */
    private $translationsEntity;


    /** @var array all local translations */
    private $localVars = [];

    private $localLangDir;

    public function __construct(
        FrontTemplateConfig $frontTemplateConfig,
        Settings            $settings,
        EntityFactory       $entityFactory,
        Modules             $modules
    ) {
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->settings            = $settings;
        $this->modules             = $modules;

        $this->translationsEntity = $entityFactory->get(TranslationsEntity::class);
        $this->languagesEntity    = $entityFactory->get(LanguagesEntity::class);

        $this->localLangDir = __DIR__ . '/../../../../../design/' . $this->frontTemplateConfig->getTheme() . '/lang/';
    }

    public function initOneLocalTranslation($langLabel, $force = false)
    {
        if (empty($langLabel)) {
            return false;
        }

        if ($force === true) {
            unset($this->localVars[$langLabel]);
        }

        if (!isset($this->themeVars[$langLabel])) {
            $this->localVars[$langLabel] = [];
            $langFile = $this->localLangDir . 'local.' . $langLabel . '.php';
            if (file_exists($langFile)) {
                $lang = [];
                include $langFile;

                foreach ($lang as $id => $translation) {
                    $this->localVars[$langLabel][$id] = (object) [
                        'value' => $translation,
                        'type'  => self::TRANS_T_LOCAL
                    ];
                }
            }
        }

        return $this->localVars[$langLabel];
    }

    public function writeThemeTranslations($langLabel, $translations)
    {
        // На локалке не нужно записывать локальные переводы
        if (!($channel = $this->settings->get('deploy_build_channel')) || $channel == 'local') {
            return;
        }

        $langFile = $this->localLangDir . 'local.' . $langLabel . '.php';
        $this->translationsEntity->initOneTranslation($langLabel, true);
        $currentTranslations = $this->translationsEntity->find(['lang' => $langLabel]);

        $translationsToWrite = [];
        foreach ($this->localVars[$langLabel] as $label => $translation) {
            $translationsToWrite[$label] = $translation->value;
        }

        foreach($translations as $label => $translation) {
            if (
                isset($currentTranslations[$label]) &&
                $currentTranslations[$label]->value != $translation->value
            ) {
                $translationsToWrite[$label] = $translation->value;
            }
        }

        $this->translationsEntity->writeTranslationsToLangFile($langFile, $translationsToWrite);
        $this->translationsEntity->initTranslations(true);

        // Удалим временный файл
        unlink(__DIR__ . '/../tmp/' . $langLabel . '.php');
    }

    public function writeModuleTranslation(
        $langLabel,
        $oldId,
        $newId,
        $translation,
        $vendor,
        $name
    ) {
        // На локалке не нужно записывать локальные переводы
        if (!($channel = $this->settings->get('deploy_build_channel')) || $channel == 'local') {
            return;
        }

        $langFile = $this->localLangDir . 'local.' . $langLabel . '.php';
        $currentTranslations = $this->modules->getModuleFrontTranslations($vendor, $name, $langLabel);

        $translationsToWrite = [];
        foreach ($this->localVars[$langLabel] as $label => $localVar) {
            $translationsToWrite[$label] = $localVar->value;
        }

        if (
            (isset($currentTranslations[$oldId]) &&
            $translation != $currentTranslations[$oldId]) ||
            (isset($translationsToWrite[$oldId]) &&
            $translation != $translationsToWrite[$oldId])
        ) {
            $translationsToWrite[$oldId] = $translation;
        }

        $this->translationsEntity->writeTranslationsToLangFile($langFile, $translationsToWrite);
        $this->translationsEntity->initTranslations(true);

        // Удалим временный файл
        unlink(__DIR__ . '/../tmp/' . $langLabel . '.php');
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
        if ($result = $this->initOneLocalTranslation($langLabel)) {
            foreach ($result as $label => $translation) {
                if ($translations[$label]) {
                    $translations[$label]->value = $translation->value;
                    $translations[$label]->type  = self::TRANS_T_LOCAL;
                } else {
                    $translations[$label] = $translation;
                }
            }
        }
        
        return $translations;
    }

    public function getLocalVar($id, $translation)
    {
        if ($translation) {
            foreach ($this->languagesEntity->find() as $l) {
                $localResult = $this->initOneLocalTranslation($l->label);
                if (isset($localResult[$id])) {
                    $translation->{'lang_' . $l->label}->value = $localResult[$id]->value;
                    $translation->{'lang_' . $l->label}->type = self::TRANS_T_LOCAL;
                    $translation->{'values'}[$l->id] = $translation->{'lang_' . $l->label};
                }
            }
            return $translation;
        }

        return false;
    }
}