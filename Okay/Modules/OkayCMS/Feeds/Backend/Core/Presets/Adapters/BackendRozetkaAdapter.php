<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\AbstractBackendPresetAdapter;

class BackendRozetkaAdapter extends AbstractBackendPresetAdapter
{
    /** @var string */
    protected static $settingsTemplate = 'presets/rozetka/settings.tpl';

    public function postCategorySettings(): array
    {
        $settings = array_merge_recursive(parent::postCategorySettings(), [
            'external_id' => $this->request->post('external_id', null, '')
        ]);

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function postSettings(): array
    {
        $postSettings = $this->request->post('settings', null, []);

        $settings = [
            'upload_without_images' => $postSettings['upload_without_images'] ?? 0,
            'upload_only_products_in_stock' => $postSettings['upload_only_products_in_stock'] ?? 0,
            'use_full_description' => $postSettings['use_full_description'] ?? 0,
            'company' => $postSettings['company'],
            'feed_name' => $postSettings['feed_name'],
            'price_change' => $postSettings['price_change'],
            'variant_name_param' => $postSettings['variant_name_param'],
            'filter_price' => [
                'operator' => $postSettings['filter_price']['operator'],
                'value' => $postSettings['filter_price']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_price']['value']),
            ],
            'filter_stock' => [
                'operator' => $postSettings['filter_stock']['operator'],
                'value' => $postSettings['filter_stock']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_stock']['value']),
            ],
        ];

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function registerCategorySettingsBlock(): void
    {
        $this->designBlocks->registerBlock(
            'okay_cms__feeds__feed__categories_settings__settings_custom_block',
            dirname(__DIR__, 3).'/design/html/presets/rozetka/category_settings.tpl'
        );

        parent::registerCategorySettingsBlock();

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}