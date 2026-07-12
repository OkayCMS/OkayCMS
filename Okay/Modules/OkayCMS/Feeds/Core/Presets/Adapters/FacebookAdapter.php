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
 * @phpstan-type FacebookFeatureRow array{values_string: string}
 * @phpstan-type FacebookProductRow object{
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
 *     features?: array<int|string, FacebookFeatureRow>,
 *     main_category_id: int|string|null,
 *     images?: list<string>,
 *     total_variants: int|string
 * }&\stdClass
 */
class FacebookAdapter extends AbstractPresetAdapter
{
    /** @var string */
    protected static $headerTemplate = 'presets/facebook/header.tpl';

    /** @var string */
    protected static $footerTemplate = 'presets/facebook/footer.tpl';

    protected function buildCategories($feedId): array
    {
        return ExtenderFacade::execute(__METHOD__, [], func_get_args());
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
        /** @var FacebookProductRow $product */
        if ($this->feed->settings['use_variant_name_like_size']) {
            $result['title']['data'] = $this->xmlFeedHelper->escape($product->product_name);
            if (!empty($product->variant_name)) {
                $result['size']['data'] = $this->xmlFeedHelper->escape($product->variant_name);
            }
        } else {
            if (!empty($product->variant_name)) {
                $result['title']['data'] = $this->xmlFeedHelper->escape($product->product_name . ' ' . $product->variant_name);
            } else {
                $result['title']['data'] = $this->xmlFeedHelper->escape($product->product_name);
            }
        }

        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['link']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['link']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
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

        $result['id']['data'] = $this->xmlFeedHelper->escape($product->variant_id);

        if (!empty($product->weight > 0)) {
            $result['product_weight']['data'] = $this->xmlFeedHelper->escape($product->weight);
        }

        if (!empty($product->sku)) {
            $result['mpn']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        $result['condition']['data'] = 'new';

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
            $result['price']['data'] = $this->xmlFeedHelper->escape($comparePrice . ' ' . $mainCurrency->code);
            $result['sale_price']['data'] = $this->xmlFeedHelper->escape($price . ' ' . $mainCurrency->code);
        } else {
            $result['price']['data'] = $this->xmlFeedHelper->escape($price . ' ' . $mainCurrency->code);
        }

        $result['availability']['data'] = $this->feedVariantIsAvailable($product->stock) ? 'in stock' : 'out of stock';

        if (!empty($product->brand_name)) {
            $result['brand']['data'] = $this->xmlFeedHelper->escape($product->brand_name);
        }

        $result['adult']['data'] = $this->feed->settings['adult'] ? 'true' : 'false';

        if (($featureId = $this->feed->settings['color']) && isset($product->features[$featureId])) {
            $result['color']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['gtin']) && isset($product->features[$featureId])) {
            $result['gtin']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['gender']) && isset($product->features[$featureId])) {
            $result['gender']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        if (($featureId = $this->feed->settings['material']) && isset($product->features[$featureId])) {
            $result['material']['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
            unset($product->features[$featureId]);
        }

        foreach ($this->feed->settings['custom_labels'] as $key => $featureId) {
            if ($featureId && isset($product->features[$featureId])) {
                $result["custom_label_$key"]['data'] = $this->xmlFeedHelper->escape($product->features[$featureId]['values_string']);
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

            $result['product_type']['data'] = mb_substr($productType, 0, -3);
        }

        if (!empty($product->images)) {
            $iNum = 0;
            foreach ($product->images as $imageFilename) {
                if ($iNum == 0) {
                    $i['tag'] = 'image_link';
                } else {
                    $i['tag'] = 'additional_image_link';
                }
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
                if ($iNum++ == 10) {
                    break;
                }
            }
        }

        if ($categoryId !== null && ($categorySettings = $this->getCategorySettings($categoryId))) {
            if ($categorySettings['fb_product_category']) {
                $result['fb_product_category']['data'] = $categorySettings['fb_product_category'];
            }

            if ($categorySettings['google_product_category']) {
                $result['google_product_category']['data'] = $categorySettings['google_product_category'];
            }
        }

        if ($product->total_variants > 1) {
            $result['item_group_id']['data'] = $product->product_id;
        }

        $item = [
            'tag' => 'item',
            'data' => $result
        ];

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}
