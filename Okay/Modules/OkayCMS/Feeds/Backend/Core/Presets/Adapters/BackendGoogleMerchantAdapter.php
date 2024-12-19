<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters;

use Okay\Core\Design;
use Okay\Core\DesignBlocks;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Entities\FeaturesEntity;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\AbstractBackendPresetAdapter;

class BackendGoogleMerchantAdapter extends AbstractBackendPresetAdapter
{
    /** @var string */
    protected static $settingsTemplate = 'presets/google_merchant/settings.tpl';


    /** @var FeaturesEntity */
    protected $featuresEntity;

    public function __construct(
        Design         $design,
        Request        $request,
        DesignBlocks   $designBlocks,
        FeaturesEntity $featuresEntity
    ){
        parent::__construct(...func_get_args());

        $this->featuresEntity = $featuresEntity;
    }

    public function postSettings(): array
    {
        $postSettings = $this->request->post('settings', null, []);

        $settings = [
            'upload_only_products_in_stock' => $postSettings['upload_only_products_in_stock'] ?? 0,
            'use_full_description' => $postSettings['use_full_description'] ?? 0,
            'use_full_description_if_not_exist_annotation' => $postSettings['use_full_description_if_not_exist_annotation'] ?? 0,
            'description_in_html' => $postSettings['description_in_html'] ?? 0,
            'replace_description_by_name_if_empty' => $postSettings['replace_description_by_name_if_empty'] ?? 0,
            'no_export_without_price' => $postSettings['no_export_without_price'] ?? 0,
            'adult' => $postSettings['adult'] ?? 0,
            'use_variant_name_like_size' => $postSettings['use_variant_name_like_size'] ?? 0,
            'upload_without_images' => $postSettings['upload_without_images'] ?? 0,
            'company' => $postSettings['company'],
            'color' => $postSettings['color'],
            'gtin' => $postSettings['gtin'],
            'gender' => $postSettings['gender'],
            'material' => $postSettings['material'],
            'price_change' => $postSettings['price_change'],
            'filter_price' => [
                'operator' => $postSettings['filter_price']['operator'],
                'value' => $postSettings['filter_price']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_price']['value']),
            ],
            'filter_stock' => [
                'operator' => $postSettings['filter_stock']['operator'],
                'value' => $postSettings['filter_stock']['value'] === '' ? null : (float) str_replace(',', '.', $postSettings['filter_stock']['value']),
            ],
            'custom_labels' => $postSettings['custom_labels']
        ];

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }
}