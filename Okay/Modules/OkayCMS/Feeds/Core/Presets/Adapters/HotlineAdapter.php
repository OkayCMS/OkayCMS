<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;

class HotlineAdapter extends AbstractPresetAdapter
{
    /** @var string */
    static protected $headerTemplate = 'preset_headers/hotline.tpl';

    /** @var string */
    static protected $footerTemplate = 'preset_footers/hotline.tpl';


    /** @var object */
    protected $UAH_currency;

    /** @var object */
    protected $USD_currency;

    protected function init(): void
    {
        parent::init();

        foreach ($this->allCurrencies as $currency) {
            if ($currency->code === "UAH") {
                $this->UAH_currency = $currency;
            } elseif ($currency->code === "USD") {
                $this->USD_currency = $currency;
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    protected function buildCategories(array $dbCategories): array
    {
        $result = [];

        foreach ($dbCategories as $dbCategory) {
            $xmlCategory = [
                'tag' => 'category',
                'data' => [
                    'id' => [
                        'data' => $dbCategory->id
                    ],
                    'name' => [
                        'data' => $dbCategory->name
                    ]
                ]
            ];

            if (!empty($dbCategory->parent_id)) {
                $xmlCategory['data']['parentId'] = ['data' => $dbCategory->parent_id];
            }

            $result[] = $xmlCategory;

            if (!empty($dbCategory->subcategories) && $dbCategory->count_children_visible) {
                $result = array_merge($result, $this->buildCategories($dbCategory->subcategories));
            }
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function getQuery($feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        if ($this->feed->settings['use_full_description']) {
            $descriptionField = 'lp.description';
        } else {
            $descriptionField = 'lp.annotation';
        }

        $sql->cols([$descriptionField . ' AS description']);

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    protected function getSubSelect($feedId): Select
    {
        $sql = parent::getSubSelect(...func_get_args());

        if (!$this->feed->settings['upload_without_images']) {
            $sql->where('p.main_image_id != \'\' AND p.main_image_id IS NOT NULL');
        }

        if ($this->feed->settings['upload_only_products_in_stock']) {
            $sql->where('(v.stock >0 OR v.stock is NULL)');
        }

        if (($value = $this->feed->settings['filter_price']['value']) !== null) {
            $operator = $this->feed->settings['filter_price']['operator'];

            $sql->join('left', CurrenciesEntity::getTable().' AS cur', 'cur.id = v.currency_id')
                ->where("(v.price*cur.rate_to/cur.rate_from) {$operator} :filter_price_value")
                ->bindValues(['filter_price_value' => $value]);
        }

        if (($value = $this->feed->settings['filter_stock']['value']) !== null) {
            $operator = $this->feed->settings['filter_stock']['operator'];

            $sql->where("IF(v.stock IS NULL, IF ('{$operator}' = '<' OR '{$operator}' = '=', false, true), v.stock {$operator} :filter_stock_value)")
                ->bindValues(['filter_stock_value' => $value]);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    public function getItem(object $product, bool $addVariantUrl = false): array
    {
        $result['id']['data'] = $product->variant_id;
        $result['group_id']['data'] = $product->product_id;
        $result['categoryId']['data'] = $product->main_category_id;
        if (!empty($product->sku)) {
            $result['code']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }
        $result['name']['data'] = $this->xmlFeedHelper->escape($product->product_name . (!empty($product->variant_name) ? ' ' . $product->variant_name : ''));
        if (!empty($product->vendor)) {
            $result['vendor']['data'] = $this->xmlFeedHelper->escape($product->vendor);
        }
        $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);

        // Указываем связку урла товара и его slug
        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        if ($product->stock || $product->stock === null) {
            $result['stock']['data'] = 'В наличии';
        } else {
            $result['stock']['data'] = 'Под заказ';
        }

        if (isset($this->allCurrencies[$product->currency_id])) {

            // Переводим в основную валюту сайта
            $variantCurrency = $this->allCurrencies[$product->currency_id];
            if (!empty($product->currency_id) && $variantCurrency->rate_from != $variantCurrency->rate_to) {
                $product->price = round($product->price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
            }

            // Приводим цены в гривнах
            if ($this->UAH_currency) {
                $result['priceRUAH']['data'] = $this->money->convert($product->price, $this->UAH_currency->id, false);
            } else {
                $result['priceRUAH']['data'] = $this->money->convert($product->price, $this->mainCurrency->id, false);
            }

            // Приводим цены в долларах
            if ($this->USD_currency) {
                $result['priceRUSD']['data'] = $this->money->convert($product->price, $this->USD_currency->id, false);
            }
        }

        if (!empty($product->images)) {
            foreach ($product->images as $imageFilename) {
                $i['tag'] = 'image';
                $i['data'] = $this->image->getResizeModifier($imageFilename, 800, 600);
                $result[] = $i;
            }
        }

        $guaranteeId = $this->feed->settings['guarantee_manufacturer'];
        $guaranteeShopId = $this->feed->settings['guarantee_shop'];
        $countryOfOriginParamId = $this->feed->settings['country_of_origin'];

        if (isset($product->features[$guaranteeId])) {
            $result[] = [
                'data' => $this->xmlFeedHelper->escape($product->features[$guaranteeId]['values_string']),
                'tag' => 'guarantee',
                'attributes' => [
                    'type' => 'manufacturer',
                ],
            ];
            unset($product->features[$guaranteeId]);
        }

        if (isset($product->features[$guaranteeShopId])) {
            $result[] = [
                'data' => $this->xmlFeedHelper->escape($product->features[$guaranteeShopId]['values_string']),
                'tag' => 'guarantee',
                'attributes' => [
                    'type' => 'shop',
                ],
            ];
            unset($product->features[$guaranteeShopId]);
        }

        if (isset($product->features[$countryOfOriginParamId])) {
            $result[] = [
                'data' => $this->xmlFeedHelper->escape($product->features[$countryOfOriginParamId]['values_string']),
                'tag' => 'param',
                'attributes' => [
                    'name' => 'Країна виготовлення',
                ],
            ];
            unset($product->features[$countryOfOriginParamId]);
        }

        if (!empty($product->features)) {
            foreach ($product->features as $feature) {
                if ($this->isFeatureToFeed($feature['id'])) {
                    foreach ($feature['values'] as $value) {
                        $result[] = [
                            'data' => $this->xmlFeedHelper->escape($value),
                            'tag' => 'param',
                            'attributes' => [
                                'name' => $this->xmlFeedHelper->escape(($name = $this->getFeatureMappingName($feature['id'])) ? $name : $feature['name']),
                            ],
                        ];
                    }
                }
            }
        }

        $item = [
            'tag' => 'item',
            'data' => $result
        ];

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}