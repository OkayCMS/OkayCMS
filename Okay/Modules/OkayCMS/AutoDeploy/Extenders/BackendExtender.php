<?php


namespace Okay\Modules\OkayCMS\AutoDeploy\Extenders;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig;
use Okay\Entities\TranslationsEntity;
use Okay\Modules\OkayCMS\AutoDeploy\Helpers\TranslationsHelper;

class BackendExtender implements ExtensionInterface
{
    
    /** @var TranslationsEntity */
    private $translationsEntity;
    
    /** @var TemplateConfig */
    private $templateConfig;
    
    /** @var TranslationsHelper */
    private $translationsHelper;
    
    /** @var Settings */
    private $settings;
    
    public function __construct(
        EntityFactory $entityFactory,
        TemplateConfig $templateConfig,
        TranslationsHelper $translationsHelper,
        Settings $settings
    ) {
        $this->translationsEntity = $entityFactory->get(TranslationsEntity::class);
        $this->templateConfig = $templateConfig;
        $this->translationsHelper = $translationsHelper;
        $this->settings = $settings;
    }

    public function getWriteLangFile($realFile, $langLabel, $theme)
    {
        if (!($channel = $this->settings->get('deploy_build_channel')) || $channel == 'local') {
            return $realFile;
        }
        return  __DIR__ . '/../tmp/' . $langLabel . '.php';
    }

    public function initOneTranslation($translations, $langLabel)
    {
        return $this->translationsHelper->addLocalTranslations($translations, $langLabel);
    }

    public function writeTranslations($result, $langLabel, $translations)
    {
        $this->translationsHelper->writeTranslations($langLabel, $translations);
    }

    
}