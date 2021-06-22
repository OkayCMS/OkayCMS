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
    /** @var TranslationsHelper */
    private $translationsHelper;
    
    /** @var Settings */
    private $settings;
    
    public function __construct(
        TranslationsHelper $translationsHelper,
        Settings $settings
    ) {
        $this->translationsHelper = $translationsHelper;
        $this->settings = $settings;
    }

    public function initOneTranslation($translations, $langLabel)
    {
        return $this->translationsHelper->addLocalTranslations($translations, $langLabel);
    }

    public function getWriteLangFile($realFile, $langLabel, $theme)
    {
        if (!($channel = $this->settings->get('deploy_build_channel')) || $channel == 'local') {
            return $realFile;
        }
        return  __DIR__ . '/../tmp/' . $langLabel . '.php';
    }

    public function writeThemeTranslations($null, ...$args)
    {
        $this->translationsHelper->writeThemeTranslations(...$args);
    }

    public function writeModuleTranslation($null, ...$args)
    {
        $this->translationsHelper->writeModuleTranslation(...$args);
    }

    public function get($result, $id)
    {
        if ($newResult = $this->translationsHelper->getLocalVar($id, $result)) {
            $result = $newResult;
        }
        return $result;
    }
}