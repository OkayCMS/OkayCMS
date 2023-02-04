<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;

class PromUaAdapter extends AbstractPresetAdapter
{
    /** @var string */
    static protected $headerTemplate = 'presets/prom_ua/header.tpl';

    /** @var string */
    static protected $footerTemplate = 'presets/prom_ua/footer.tpl';

    protected function buildCategories($feedId): array
    {
        $result = parent::buildCategories($feedId);
        $result[0]['tag'] = 'catalog';

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    protected function buildCategory(object $dbCategory): array
    {
        $categorySettings = $this->getCategorySettings($dbCategory->id);

        if (!$categorySettings || !($name = $categorySettings['name_in_feed'])) {
            $name = $dbCategory->name;
        }

        $xmlCategory = [
            'tag' => 'category',
            'data' => $this->xmlFeedHelper->escape($name),
            'attributes' => [
                'id' => $dbCategory->id
            ],
        ];

        if (!empty($dbCategory->parent_id)) {
            $xmlCategory['attributes']['parentId'] = $dbCategory->parent_id;
        }

        if ($categorySettings && ($externalId = $categorySettings['external_id'])) {
            $xmlCategory['attributes']['portal_id'] = $externalId;
        }

        return ExtenderFacade::execute(__METHOD__, $xmlCategory, func_get_args());
    }

    public function getQuery($feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        $uaLang = $this->feedHelper->checkIfUaMainLanguageIs();

        //  получаем украинские данные
        if (!empty($uaLang)
            && ($uaLang != false)
            && !empty($uaLangId = $uaLang->id)
        ) {
            if ($this->feed->settings['use_full_description']) {
                $sql->cols([
                    'lp.description AS description',
                    'lp_ua.name as product_name_ua',
                    'lp_ua.description as description_ua',
                    'lv_ua.name as variant_name_ua',
                ]);
            } else {
                $sql->cols(['lp.annotation AS annotation']);
                $sql->cols([
                    'lp.annotation AS annotation',
                    'lp_ua.name as product_name_ua',
                    'lp_ua.annotation as annotation_ua',
                    'lv_ua.name as variant_name_ua',
                ]);
            }

            $sql->leftJoin(ProductsEntity::getLangTable().' AS lp_ua', 'lp_ua.product_id = t.product_id and lp_ua.lang_id=' . $uaLangId);
            $sql->leftJoin(VariantsEntity::getLangTable().' AS lv_ua', 'lv_ua.variant_id = t.variant_id and lv_ua.lang_id=' . $uaLangId);

        } else {
            if ($this->feed->settings['use_full_description']) {
                $sql->cols(['lp.description AS description']);
            } else {
                $sql->cols(['lp.annotation AS annotation']);
            }
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

        if (!empty($product->product_name_ua)) {
            $result['name_ua']['data'] = $this->xmlFeedHelper->escape($product->product_name_ua . (!empty($product->variant_name_ua) ? ' ' . $product->variant_name_ua : '') . (!empty($product->sku) ? ' (' . ($product->sku) . ')' : ''));
        }

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
        if (!empty($this->feed->settings['description_in_html']) && $this->feed->settings['description_in_html'] == 1) {    //  передаем html текст полностью в CDATA
            if (!empty($product->description)) {
                $result['description']['data'] = '<![CDATA['.$product->description.']]>';
            } else if (!empty($product->annotation)) {
                $result['description']['data'] = '<![CDATA['.$product->annotation.']]>';
            }

            if (!empty($product->description_ua)) {
                $result['description_ua']['data'] = '<![CDATA['.$product->description_ua.']]>';
            } else if (!empty($product->annotation_ua)) {
                $result['description_ua']['data'] = '<![CDATA['.$product->annotation_ua.']]>';
            }
        } else {
            if (!empty($product->description)) {    //  передаем описание без верстки
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);
            } else if (!empty($product->annotation)) {
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->annotation);
            }

            if (!empty($product->description_ua)) {
                $result['description_ua']['data'] = $this->xmlFeedHelper->escape($product->description_ua);
            } else if (!empty($product->annotation_ua)) {
                $result['description_ua']['data'] = $this->xmlFeedHelper->escape($product->annotation_ua);
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