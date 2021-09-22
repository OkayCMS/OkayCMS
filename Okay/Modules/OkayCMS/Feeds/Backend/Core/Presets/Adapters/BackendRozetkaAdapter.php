<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\AbstractBackendPresetAdapter;

class BackendRozetkaAdapter extends AbstractBackendPresetAdapter
{
    /** @var string */
    protected static $settingsTemplate = 'preset_settings/rozetka.tpl';


    public function postSettings(): string
    {
        $postSettings = $this->request->post('settings', null, []);

        $settings = [
            'upload_without_images' => $postSettings['upload_without_images'] ?? 0,
            'upload_only_products_in_stock' => $postSettings['upload_only_products_in_stock'] ?? 0,
            'use_full_description' => $postSettings['use_full_description'] ?? 0,
            'company' => $postSettings['company'],
            'filter_price' => [
                'operator' => $postSettings['filter_price']['operator'],
                'value' => $postSettings['filter_price']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_price']['value']),
            ],
            'filter_stock' => [
                'operator' => $postSettings['filter_stock']['operator'],
                'value' => $postSettings['filter_stock']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_stock']['value']),
            ],
        ];

        $dbSettings = serialize($settings);

        return ExtenderFacade::execute(__METHOD__, $dbSettings, func_get_args());
    }
}