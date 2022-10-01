<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters;

use Okay\Core\Design;
use Okay\Core\DesignBlocks;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Entities\FeaturesEntity;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\AbstractBackendPresetAdapter;

class BackendYmlAdapter extends AbstractBackendPresetAdapter
{
    /** @var string */
    protected static $settingsTemplate = 'presets/yml/settings.tpl';


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
            'upload_without_images' => $postSettings['upload_without_images'] ?? 0,
            'upload_only_products_in_stock' => $postSettings['upload_only_products_in_stock'] ?? 0,
            'store' => $postSettings['store'] ?? 0,
            'pickup' => $postSettings['pickup'] ?? 0,
            'use_full_description' => $postSettings['use_full_description'] ?? 0,
            'has_manufacturer_warranty' => $postSettings['has_manufacturer_warranty'] ?? 0,
            'no_export_without_price' => $postSettings['no_export_without_price'] ?? 0,
            'delivery' => $postSettings['delivery'] ?? 0,
            'adult' => $postSettings['adult'] ?? 0,
            'count' => $postSettings['count'] ?? 0,
            'enable_auto_discounts' => $postSettings['enable_auto_discounts'] ?? 0,
            'company' => $postSettings['company'],
            'country_of_origin' => $postSettings['country_of_origin'],
            'price_change' => $postSettings['price_change'],
            'feed_name' => $postSettings['feed_name'],
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
}