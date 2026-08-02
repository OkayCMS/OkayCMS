<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;

/**
 * @phpstan-type CurrencyRow object{id: int|string, code: string, rate_from: int|float, rate_to: int|float}
 * @phpstan-type YmlFeatureRow array{
 *     id: int|string,
 *     name: string,
 *     values: list<string>,
 *     values_string: string
 * }
 * @phpstan-type YmlProductRow object{
 *     product_id: int|string,
 *     variant_id: int|string,
 *     product_name: string,
 *     variant_name?: string|null,
 *     slug_url: string,
 *     url: string,
 *     description?: string|null,
 *     annotation?: string|null,
 *     weight?: int|float|string|null,
 *     sku?: string|null,
 *     price: int|float,
 *     compare_price: int|float,
 *     currency_id: int|string|null,
 *     stock: int|string|null,
 *     brand_name?: string|null,
 *     features?: array<int|string, YmlFeatureRow>,
 *     main_category_id: int|string|null,
 *     images_string?: string|null,
 *     images?: list<string>,
 *     total_variants: int|string
 * }&\stdClass
 */
class YmlAdapter extends AbstractPresetAdapter
{
    /** @var string */
    protected static $headerTemplate = 'presets/yml/header.tpl';

    /** @var string */
    protected static $footerTemplate = 'presets/yml/footer.tpl';

    public function getQuery(int|string $feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        if ($this->feed->settings['use_full_description']) {
            $sql->cols(['lp.description AS description']);
        } else {
            $sql->cols(['lp.annotation AS annotation']);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    protected function getSubSelect(int|string $feedId): Select
    {
        $sql = parent::getSubSelect(...func_get_args());

        $sql->cols([
            'v.compare_price',
            'v.weight'
        ]);

        if ($this->feed->settings['upload_only_products_in_stock'] && !$this->settings->get('is_preorder')) {
            $sql->where('(v.stock >0 OR v.stock is NULL)');
        }

        if (!$this->feed->settings['upload_without_images']) {
            $sql->where('p.main_image_id != \'\' AND p.main_image_id IS NOT NULL');
        }

        if ($this->feed->settings['no_export_without_price']) {
            $sql->where('v.price > 0');
        }

        if (($value = $this->feed->settings['filter_price']['value']) !== null) {
            $operator = $this->normalizeComparisonOperator($this->feed->settings['filter_price']['operator'] ?? null);

            $sql->join('left', CurrenciesEntity::getTable() . ' AS cur', 'cur.id = v.currency_id')
                ->where("(v.price*cur.rate_to/cur.rate_from) {$operator} :filter_price_value")
                ->bindValues(['filter_price_value' => $value]);
        }

        if (($value = $this->feed->settings['filter_stock']['value']) !== null) {
            $operator = $this->normalizeComparisonOperator($this->feed->settings['filter_stock']['operator'] ?? null);

            $sql->where("v.stock IS NOT NULL AND v.stock {$operator} :filter_stock_value")
                ->bindValues(['filter_stock_value' => $value]);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    public function getItem(object $product, bool $addVariantUrl = false): array
    {
        /** @var YmlProductRow $product */
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
        $currencyId = $product->currency_id;
        if ($currencyId !== null && isset($this->allCurrencies[$currencyId])) {
            // Переводим в основную валюту сайта
            /** @var CurrencyRow $variantCurrency */
            $variantCurrency = $this->allCurrencies[$currencyId];
            if ($variantCurrency->rate_from != $variantCurrency->rate_to) {
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

        /** @var CurrencyRow $mainCurrency */
        $mainCurrency = $this->mainCurrency;
        $result['price']['data'] = $this->money->convert($price, $mainCurrency->id, false);
        if ($product->compare_price > 0) {
            $comparePrice = $this->money->convert($comparePrice, $mainCurrency->id, false);
            $result['oldprice']['data'] = $comparePrice;
        }

        $result['currencyId']['data'] = $mainCurrency->code;
        $result['categoryId']['data'] = $product->main_category_id;

        if ($this->feed->settings['count'] && $product->stock !== null) {
            $result['count']['data'] = $product->stock;
        }

        if (!empty($product->images)) {
            $iNum = 0;
            foreach ($product->images as $imageFilename) {
                $i['tag'] = 'picture';
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
                if ($iNum++ == 10) {
                    break;
                }
            }
        }

        $result['manufacturer_warranty']['data'] = $this->feed->settings['has_manufacturer_warranty'] ? 'true' : 'false';

        if (!empty($product->brand_name)) {
            $result['vendor']['data'] = $this->xmlFeedHelper->escape($product->brand_name);
        }

        if (!empty($product->sku)) {
            $result['vendorCode']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        if (!empty($product->weight > 0)) {
            $result['weight']['data'] = $this->xmlFeedHelper->escape($product->weight);
        }

        //  добавляем описание
        if (!empty($product->description)) {
            if (!empty($this->feed->settings['description_in_html']) && $this->feed->settings['description_in_html'] == 1) {    //  передаем html полностью в CDATA
                $result['description']['data'] = '<![CDATA[' . $product->description . ']]>';
            } else {
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);
            }
        } elseif (!empty($product->annotation)) {
            if (!empty($this->feed->settings['description_in_html']) && $this->feed->settings['description_in_html'] == 1) {    //  передаем html полностью в CDATA
                $result['description']['data'] = '<![CDATA[' . $product->annotation . ']]>';
            } else {
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->annotation);
            }
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
            'tag' => 'offer',
            'attributes' => [
                'id' => $product->variant_id,
                'available' => ($this->feedVariantIsAvailable($product->stock) ? 'true' : 'false'),
            ],
            'data' => $result
        ];

        if ($product->total_variants > 1) {
            $item['attributes']['group_id'] = $product->product_id;
        }

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}
