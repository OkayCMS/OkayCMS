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
 * @phpstan-type CategoryRow object{id: int|string, name: string, parent_id?: int|string|null}
 * @phpstan-type HotlineFeatureRow array{id: int|string, name: string, values: list<string>, values_string: string}
 * @phpstan-type HotlineProductRow object{
 *     product_id: int|string,
 *     variant_id: int|string,
 *     main_category_id: int|string|null,
 *     product_name: string,
 *     variant_name?: string|null,
 *     slug_url: string,
 *     url: string,
 *     description?: string|null,
 *     annotation?: string|null,
 *     sku?: string|null,
 *     price: int|float,
 *     currency_id: int|string|null,
 *     stock: int|string|null,
 *     brand_name?: string|null,
 *     features?: array<int|string, HotlineFeatureRow>,
 *     images?: list<string>
 * }&\stdClass
 */
class HotlineAdapter extends AbstractPresetAdapter
{
    /** @var string */
    protected static $headerTemplate = 'presets/hotline/header.tpl';

    /** @var string */
    protected static $footerTemplate = 'presets/hotline/footer.tpl';


    /** @var CurrencyRow|null */
    protected $UAH_currency = null;

    /** @var CurrencyRow|null */
    protected $USD_currency = null;

    protected function init(): void
    {
        parent::init();

        foreach ($this->allCurrencies as $currency) {
            /** @var CurrencyRow $currency */
            if ($currency->code === "UAH") {
                $this->UAH_currency = $currency;
            } elseif ($currency->code === "USD") {
                $this->USD_currency = $currency;
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param CategoryRow $dbCategory
     */
    protected function buildCategory(object $dbCategory): array
    {
        $categorySettings = $this->getCategorySettings($dbCategory->id);

        if (!$categorySettings || !($name = $categorySettings['name_in_feed'])) {
            $name = $dbCategory->name;
        }

        $xmlCategory = [
            'tag' => 'category',
            'data' => [
                'id' => [
                    'data' => $dbCategory->id
                ],
                'name' => [
                    'data' => $this->xmlFeedHelper->escape($name)
                ]
            ]
        ];

        if (!empty($dbCategory->parent_id)) {
            $xmlCategory['data']['parentId'] = ['data' => $dbCategory->parent_id];
        }

        return ExtenderFacade::execute(__METHOD__, $xmlCategory, func_get_args());
    }

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

        if (!$this->feed->settings['upload_without_images']) {
            $sql->where('p.main_image_id != \'\' AND p.main_image_id IS NOT NULL');
        }

        if ($this->feed->settings['upload_only_products_in_stock'] && !$this->settings->get('is_preorder')) {
            $sql->where('(v.stock >0 OR v.stock is NULL)');
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
        /** @var HotlineProductRow $product */
        $result['id']['data'] = $product->variant_id;

        $result['group_id']['data'] = $product->product_id;

        $result['categoryId']['data'] = $product->main_category_id;

        if (!empty($product->sku)) {
            $result['code']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        $result['name']['data'] = $this->xmlFeedHelper->escape($product->product_name . (!empty($product->variant_name) ? ' ' . $product->variant_name : ''));

        if (!empty($product->brand_name)) {
            $result['vendor']['data'] = $this->xmlFeedHelper->escape($product->brand_name);
        }

        //  добавляем описание
        if (!empty($this->feed->settings['description_in_html']) && $this->feed->settings['description_in_html'] == 1) {    //  передаем html текст полностью в CDATA
            if (empty($product->description) && empty($product->annotation)) {
                $result['description']['data'] = '';
            } else {
                $result['description']['data'] = '<![CDATA[' . ($product->description ?? $product->annotation) . ']]>';
            }
        } else {
            $result['description']['data'] = $this->xmlFeedHelper->escape($product->description ?? $product->annotation);
        }

        // Указываем связку урла товара и его slug
        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        if (
            $this->settings->get('is_preorder')
            || (int) $product->stock > 0
            || ($product->stock === null && !$this->settings->get('use_backorder_status'))
        ) {
            $result['stock']['data'] = 'В наличии';
        } elseif ($product->stock === null) {
            $result['stock']['data'] = 'Под заказ';
        } else {
            $result['stock']['data'] = 'Нет в наличии';
        }

        $price = $product->price;

        if ($this->feed->settings['price_change']) {
            $price = $price + $price / 100 * $this->feed->settings['price_change'];
        }

        $currencyId = $product->currency_id;
        if ($currencyId !== null && isset($this->allCurrencies[$currencyId])) {
            // Переводим в основную валюту сайта
            /** @var CurrencyRow $variantCurrency */
            $variantCurrency = $this->allCurrencies[$currencyId];
            if ($variantCurrency->rate_from != $variantCurrency->rate_to) {
                $price = round($price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
            }

            // Приводим цены в гривнах
            if ($this->UAH_currency) {
                $result['priceRUAH']['data'] = $this->money->convert($price, $this->UAH_currency->id, false);
            } else {
                /** @var CurrencyRow $mainCurrency */
                $mainCurrency = $this->mainCurrency;
                $result['priceRUAH']['data'] = $this->money->convert($price, $mainCurrency->id, false);
            }

            // Приводим цены в долларах
            if ($this->USD_currency) {
                $result['priceRUSD']['data'] = $this->money->convert($price, $this->USD_currency->id, false);
            }
        }

        if (!empty($product->images)) {
            foreach ($product->images as $imageFilename) {
                $i['tag'] = 'image';
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
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
            'data' => $result
        ];

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}
