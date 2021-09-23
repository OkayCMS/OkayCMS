<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;

class RozetkaAdapter extends AbstractPresetAdapter
{
    /** @var string */
    static protected $headerTemplate = 'preset_headers/rozetka.tpl';

    /** @var string */
    static protected $footerTemplate = 'preset_footers/rozetka.tpl';

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

        $sql->cols(['v.compare_price']);

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

    protected function getItem(object $product, bool $addVariantUrl = false): array
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
                $i['tag'] = 'picture';
                $i['data'] = $this->image->getResizeModifier($imageFilename, 1200, 1200);
                $result[] = $i;
                if ($iNum++ == 15) {
                    break;
                }
            }
        }

        $result['stock_quantity']['data'] = $product->stock;
        $result['delivery']['data'] = 'true';

        if (!empty($product->vendor)) {
            $result['vendor']['data'] = $this->xmlFeedHelper->escape($product->vendor);
        }

        if (!empty($product->sku)) {
            $result['vendorCode']['data'] = $this->xmlFeedHelper->escape($product->sku);
        }

        if (!empty($product->description)) {
            $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);
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
            'tag' => 'offer',
            'attributes' => [
                'id' => $product->variant_id,
                'available' => ($product->stock > 0 || $product->stock === null ? 'true' : 'false'),
            ],
            'data' => $result
        ];

        return ExtenderFacade::execute(__METHOD__, [$item], func_get_args());
    }
}