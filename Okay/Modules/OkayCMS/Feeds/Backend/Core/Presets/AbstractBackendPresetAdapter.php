<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets;

use Okay\Core\Design;
use Okay\Core\DesignBlocks;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Feeds\Core\InheritedExtenderTrait;

abstract class AbstractBackendPresetAdapter implements BackendPresetAdapterInterface
{
    use InheritedExtenderTrait;

    /** @var string|null */
    protected static $settingsTemplate;


    /** @var Design */
    protected $design;

    /** @var Request */
    protected $request;

    /** @var DesignBlocks */
    protected $designBlocks;

    public function __construct(
        Design $design,
        Request $request,
        DesignBlocks $designBlocks
    ) {
        $this->design       = $design;
        $this->request      = $request;
        $this->designBlocks = $designBlocks;
    }

    /**
     * @return array<string, mixed>
     */
    public function postSettings(): array
    {
        return $this->inheritedExtender(__FUNCTION__, [], func_get_args());
    }

    protected function normalizeComparisonOperator(mixed $operator): string
    {
        return in_array($operator, ['<', '>', '='], true) ? $operator : '=';
    }

    /**
     * @return array<string, mixed>
     */
    public function postCategorySettings(): array
    {
        $settings = [
            'name_in_feed' => $this->request->post('name_in_feed', null, '')
        ];

        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    /**
     * @return array<string, mixed>
     */
    public function postFeatureSettings(): array
    {
        $settings = [
            'entity_id' => $this->request->post('entity_id'),
            'to_feed' => $this->request->post('to_feed', 'int', 0),
            'name_in_feed' => $this->request->post('name_in_feed', null, '')
        ];

        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    /**
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public function loadSettings(array $settings): array
    {
        return $this->inheritedExtender(__FUNCTION__, $settings, func_get_args());
    }

    protected function getSettingsTemplate(): ?string
    {
        return $this->inheritedExtender(__FUNCTION__, static::$settingsTemplate, func_get_args());
    }

    public function fetchSettingsTemplate(): string
    {
        $settingsTemplate = $this->getSettingsTemplate();

        if (is_null($settingsTemplate)) {
            throw new \Exception('Backend adapter must contain valid settings template');
        }

        $settingsTemplate = $this->design->fetch($settingsTemplate);

        return $this->inheritedExtender(__FUNCTION__, $settingsTemplate, func_get_args());
    }

    public function registerCategorySettingsBlock(): void
    {
        $this->designBlocks->registerBlock(
            'okay_cms__feeds__feed__categories_settings__settings_custom_block',
            dirname(__DIR__, 2) . '/design/html/presets/common/category_settings.tpl'
        );

        $this->inheritedExtender(__FUNCTION__, null, func_get_args());
    }

    public function registerFeatureSettingsBlock(): void
    {
        $this->designBlocks->registerBlock(
            'okay_cms__feeds__feed__features_settings__settings_custom_block',
            dirname(__DIR__, 2) . '/design/html/presets/common/feature_settings.tpl'
        );

        $this->inheritedExtender(__FUNCTION__, null, func_get_args());
    }
}
