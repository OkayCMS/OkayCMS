<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;

class PriceUaAdapter extends AbstractPresetAdapter
{
    /** @var string */
    static protected $headerTemplate = 'presets/price_ua/header.tpl';

    /** @var string */
    static protected $footerTemplate = 'presets/price_ua/footer.tpl';

    protected function buildCategories($feedId): array
    {
        $result = parent::buildCategories($feedId);
        $result[0]['tag'] = 'catalog';

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function getQuery($feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        if ($this->feed->settings['use_full_description']) {
            $sql->cols(['lp.description AS description']);
        } else {
            $sql->cols(['lp.annotation AS annotation']);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    protected function getSubSelect($feedId): Select
    {
        $sql = parent::getSubSelect(...func_get_args());

        $sql->cols([
            'v.compare_price',
            'v.weight'
        ]);

        if ($this->feed->settings['upload_only_products_in_stock']) {
            $sql->where('(v.stock >0 OR v.stock is NULL)');
        }

        if (!$this->feed->settings['upload_without_images']) {
            $sql->where('p.main_image_id != \'\' AND p.main_image_id IS NOT NULL');
        }

        if ($this->feed->settings['no_export_without_price']) {
            $sql->where('v.price > 0');
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
        // Указываем связку урла товара и его slug
        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        $result['name']['data'] = $this->xmlFeedHelper->escape($product->product_name . (!empty($product->variant_name) ? ' ' . $product->variant_name : ''));

        $price = $product->price;
        $comparePrice = $product->compare_price;
        if (isset($this->allCurrencies[$product->currency_id])) {
            // Переводим в основную валюту сайта
            $variantCurrency = $this->allCurrencies[$product->currency_id];
            if (!empty($product->currency_id) && $variantCurrency->rate_from != $variantCurrency->rate_to) {
                $price = round($product->price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
                if (!empty($product->compare_price)) {
                    $comparePrice = round($product->compare_price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
                }
            }
        }

        if ($this->feed->settings['price_change']) {
            $price = $price + $price / 100 * $this->feed->settings['price_change'];
            $comparePrice = $comparePrice + $comparePrice / 100 * $this->feed->settings['price_change'];
        }

        $result['price']['data'] = $this->money->convert($price, $this->mainCurrency->id, false);
        if ($product->compare_price > 0) {
            $comparePrice = $this->money->convert($comparePrice, $this->mainCurrency->id, false);
            $result['oldprice']['data'] = $comparePrice;
        }

        $result['currencyId']['data'] = $this->mainCurrency->code;
        $result['categoryId']['data'] = $product->main_category_id;

        if (!empty($product->images)) {
            $iNum = 0;
            foreach ($product->images as $imageFilename) {
                $i['tag'] = 'image';
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
                if ($iNum++ == 10) {
                    break;
                }
            }
        }

        $guaranteeId = $this->feed->settings['guarantee_manufacturer'];
        $guaranteeShopId = $this->feed->settings['guarantee_shop'];

        if (isset($product->features[$guaranteeId])) {
            $result[] = [
                'tag' => 'guarantee',
                'data' => $this->xmlFeedHelper->escape($product->features[$guaranteeId]['values_string']),
                'attributes' => [
                    'type' => 'manufacturer',
                ],
            ];
            unset($product->features[$guaranteeId]);
        }

        if (isset($product->features[$guaranteeShopId])) {
            $result[] = [
                'tag' => 'guarantee',
                'data' => $this->xmlFeedHelper->escape($product->features[$guaranteeShopId]['values_string']),
                'attributes' => [
                    'type' => 'shop',
                ],
            ];
            unset($product->features[$guaranteeShopId]);
        }

        if (!empty($product->brand_name)) {
            $result['vendor']['data'] = $this->xmlFeedHelper->escape($product->brand_name);
        }

        if (!empty($product->sku)) {
            $result['code']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        if (!empty($product->weight > 0)) {
            $result['weight']['data'] = $this->xmlFeedHelper->escape($product->weight);
        }

        if (!empty($product->description)) {
            $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);
        } else if (!empty($product->annotation)) {
            $result['description']['data'] = $this->xmlFeedHelper->escape($product->annotation);
        }

        $countryOfOriginParamId = $this->feed->settings['country_of_origin'];

        if (isset($product->features[$countryOfOriginParamId])) {
            $result[] = [
                'tag' => 'country_of_origin',
                'data' => $this->xmlFeedHelper->escape($product->features[$countryOfOriginParamId]['values_string']),
            ];
            unset($product->features[$countryOfOriginParamId]);
        }

        if (!empty($product->features)) {
            foreach ($product->features as $feature) {
                $featureSettings = $this->getFeatureSettings($feature['id']);

                if (!$featureSettings || $featureSettings['to_feed']) {
                    if (!$featureSettings || !($name = $featureSettings['name_in_feed'])) {
                        $name = $feature['name'];
                    }

                    foreach ($feature['values'] as $value) {
                        $result[] = [
                            'tag' => 'param',
                            'data' => $this->xmlFeedHelper->escape($value),
                            'attributes' => [
                                'name' => $this->xmlFeedHelper->escape($name),
                            ],
                        ];
                    }
                }
            }
        }

        $item = [
            'tag' => 'item',
            'attributes' => [
                'id' => $product->variant_id,
                'available' => ($product->stock > 0 || $product->stock === null ? 'true' : 'false'),
            ],
            'data' => $result
        ];

        if ($product->total_variants > 1) {
            $item['attributes']['group_id'] = $product->product_id;
        }

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}