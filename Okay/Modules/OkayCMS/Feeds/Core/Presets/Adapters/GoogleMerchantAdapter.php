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
 * @phpstan-type CategoryPathRow object{name: string}
 * @phpstan-type CategoryRow object{path: list<CategoryPathRow>}
 * @phpstan-type GoogleMerchantFeatureRow array{id: int|string, name: string, values_string: string}
 * @phpstan-type GoogleMerchantProductRow object{
 *     product_id: int|string,
 *     id: int|string,
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
 *     features?: array<int|string, GoogleMerchantFeatureRow>,
 *     main_category_id: int|string|null,
 *     images?: list<string>,
 *     total_variants: int|string
 * }&\stdClass
 */
class GoogleMerchantAdapter extends AbstractPresetAdapter
{
    /** @var string */
    protected static $headerTemplate = 'presets/google_merchant/header.tpl';

    /** @var string */
    protected static $footerTemplate = 'presets/google_merchant/footer.tpl';

    protected function buildCategories($feedId): array
    {
        return ExtenderFacade::execute(__METHOD__, [], func_get_args());
    }

    public function getQuery(int|string $feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        if ($this->isSettingParamTrue('use_full_description')) {
            $sql->cols(['lp.description AS description']);
        } elseif ($this->isSettingParamTrue('use_full_description_if_not_exist_annotation')) {
            $sql->cols(['lp.description AS description']);
            $sql->cols(['lp.annotation AS annotation']);
        } else {
            $sql->cols(['lp.annotation AS annotation']);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    protected function isSettingParamTrue(string $param): bool
    {
        return isset($this->feed->settings[$param]) && $this->feed->settings[$param];
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

    protected function getItem(object $product, bool $addVariantUrl = false): array
    {
        /** @var GoogleMerchantProductRow $product */
        if ($this->feed->settings['use_variant_name_like_size']) {
            $result['g:title']['data'] = $this->xmlFeedHelper->escape($product->product_name);
            if (!empty($product->variant_name)) {
                $result['g:size']['data'] = $this->xmlFeedHelper->escape($product->variant_name);
            }
        } else {
            if (!empty($product->variant_name)) {
                $result['g:title']['data'] = $this->xmlFeedHelper->escape($product->product_name . ' ' . $product->variant_name);
            } else {
                $result['g:title']['data'] = $this->xmlFeedHelper->escape($product->product_name);
            }
        }

        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['g:link']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['g:link']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        $description = '';
        $canUseCdata = false;
        if (
            ($this->isSettingParamTrue('use_full_description_if_not_exist_annotation') && !empty($product->description) && empty($product->annotation))
            || ($this->isSettingParamTrue('use_full_description') && !empty($product->description))
        ) {
            $description = $product->description;
            $canUseCdata = true;
        } elseif (!empty($product->annotation)) {
            $description = $product->annotation;
            $canUseCdata = true;
        } elseif ($this->isSettingParamTrue('replace_description_by_name_if_empty')) {
            $description = $result['g:title']['data'];
        }

        if ($canUseCdata && $this->isSettingParamTrue('description_in_html')) {
            $description = '<![CDATA[' . $description . ']]>';
        } else {
            $description = $this->xmlFeedHelper->escape($description);
        }

        $result['g:description']['data'] = $description;

        if (!empty($product->weight > 0)) {
            $result['g:product_weight']['data'] = $this->xmlFeedHelper->escape($product->weight . ' kg');
        }

        if (!empty($product->sku)) {
            $result['g:id']['data'] = $this->xmlFeedHelper->escape($product->sku);
        } else {
            $result['g:id']['data'] = $this->xmlFeedHelper->escape($product->variant_id);
        }

        $result['g:condition']['data'] = 'new';

        $price = round($product->price, 2);
        $comparePrice = round($product->compare_price, 2);
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
        $price = $this->money->convert($price, $mainCurrency->id, false);
        $comparePrice = $this->money->convert($comparePrice, $mainCurrency->id, false);

        if ($product->compare_price > $product->price) {
            $result['g:price']['data'] = $this->xmlFeedHelper->escape($comparePrice . ' ' . $mainCurrency->code);
            $result['g:sale_price']['data'] = $this->xmlFeedHelper->escape($price . ' ' . $mainCurrency->code);
        } else {
            $result['g:price']['data'] = $this->xmlFeedHelper->escape($price . ' ' . $mainCurrency->code);
        }

        $availability = $this->googleMerchantAvailability($product->stock);
        $result['g:availability']['data'] = $availability;
        if ($availability === 'backorder') {
            $result['g:availability_date']['data'] = (new \DateTimeImmutable('+1 month'))->format(DATE_ATOM);
        }

        if (!empty($product->brand_name)) {
            $result['g:brand']['data'] = $this->xmlFeedHelper->escape($product->brand_name);
        }

        $result['g:adult']['data'] = $this->feed->settings['adult'] ? 'true' : 'false';

        //добавляем атрибуты в product_detail, согласно настройкам в ok_okay_cms__feeds__feeds
        if (isset($product->features)) {
            foreach ($product->features as $keyFeature => $feature) {
                $attributeName = $this->xmlFeedHelper->escape($feature['name']);
                $attributeValue = $this->xmlFeedHelper->escape($feature['values_string']);
                if (isset($this->feed->features_settings[$feature['id']]['name_in_feed']) && $this->feed->features_settings[$feature['id']]['name_in_feed']) {
                    $attributeName = $this->xmlFeedHelper->escape($this->feed->features_settings[$feature['id']]['name_in_feed']);
                }
                if (
                    isset($attributeName) && $attributeName
                    && isset($attributeValue) && $attributeValue
                    && (!isset($this->feed->features_settings[$feature['id']])  //показываем свойство, если под него нет вообще настроек
                        || $this->feed->features_settings[$feature['id']]['to_feed']
                    )
                ) {
                    $result[] = [
                        'tag' => 'g:product_detail',
                        'data' => [
                            'g:attribute_name' => [
                                'data' => $attributeName
                            ],
                            'g:attribute_value' => [
                                'data' => $attributeValue
                            ],
                        ]
                    ];
                }
            }
        }
        ///добавляем атрибуты в product_detail, согласно настройкам в ok_okay_cms__feeds__feeds

        if (($featureId = $this->feed->settings['color']) && isset($product->features[$featureId])) {
            $result['g:color']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['gtin']) && isset($product->features[$featureId])) {
            $result['g:gtin']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['mpn']) && isset($product->features[$featureId])) {
            $result['g:mpn']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        } elseif (!empty($product->sku)) {
            $result['g:mpn']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        if (($featureId = $this->feed->settings['gender']) && isset($product->features[$featureId])) {
            $result['g:gender']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['material']) && isset($product->features[$featureId])) {
            $result['g:material']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        foreach ($this->feed->settings['custom_labels'] as $key => $featureId) {
            if ($featureId && isset($product->features[$featureId])) {
                $result["g:custom_label_$key"]['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
                unset($product->features[$featureId]);
            }
        }

        $categoryId = $product->main_category_id;
        if ($categoryId !== null && !empty($this->allCategories[$categoryId])) {
            /** @var CategoryRow $category */
            $category = $this->allCategories[$categoryId];
            $categoryPath = $category->path;

            $productType = '';

            foreach ($categoryPath as $category) {
                $productType .= $category->name . ' > ';
            }

            $result['g:product_type']['data'] = mb_substr($productType, 0, -3);
        }

        if (!empty($product->images)) {
            $iNum = 0;
            foreach ($product->images as $imageFilename) {
                if ($iNum == 0) {
                    $i['tag'] = 'g:image_link';
                } else {
                    $i['tag'] = 'g:additional_image_link';
                }
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
                if ($iNum++ == 10) {
                    break;
                }
            }
        }

        if ($categoryId !== null && ($categorySettings = $this->getCategorySettings($categoryId)) && $categorySettings['name_in_feed']) {
            $result['g:google_product_category']['data'] = $categorySettings['name_in_feed'];
        }

        if ($product->total_variants > 1) {
            $result['g:item_group_id']['data'] = $product->product_id;
        }

        $item = [
            'tag' => 'item',
            'data' => $result
        ];

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}
