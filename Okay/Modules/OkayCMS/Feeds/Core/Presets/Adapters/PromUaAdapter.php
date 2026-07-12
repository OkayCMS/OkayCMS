<?php

namespace Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters;

use Aura\Sql\ExtendedPdo;
use Okay\Core\Database;
use Okay\Core\Design;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\QueryFactory;
use Okay\Core\QueryFactory\Select;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\XmlFeedHelper;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\AbstractPresetAdapter;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;
use Okay\Modules\OkayCMS\Feeds\Helpers\FeedsHelper;

/**
 * @phpstan-type CurrencyRow object{id: int|string, code: string, rate_from: int|float, rate_to: int|float}
 * @phpstan-type UaLanguageRow object{id: int|string}
 * @phpstan-type CategoryUaRow object{name: string|null, name_h1: string|null, path?: list<object>}
 * @phpstan-type PromUaFeatureRow array{
 *     id: int|string,
 *     name: string,
 *     values: list<string>,
 *     values_string: string,
 *     auto_name_id?: string|null,
 *     auto_value_id?: string|null
 * }
 * @phpstan-type PromUaProductRow object{
 *     product_id: int|string,
 *     variant_id: int|string,
 *     product_name: string,
 *     variant_name?: string|null,
 *     slug_url: string,
 *     url: string,
 *     description?: string|null,
 *     annotation?: string|null,
 *     weight?: int|float|string|null,
 *     sku: string,
 *     price: int|float,
 *     compare_price: int|float,
 *     currency_id: int|string|null,
 *     stock: int|string|null,
 *     brand_name: string,
 *     features?: array<int|string, PromUaFeatureRow>,
 *     main_category_id: int|string|null,
 *     images_string?: string|null,
 *     images?: list<string>,
 *     total_variants: int|string,
 *     product_name_ua?: string|null,
 *     variant_name_ua?: string|null,
 *     description_ua?: string|null,
 *     annotation_ua?: string|null,
 *     brand_name_ua?: string|null,
 *     features_string_ua?: string|null,
 *     values_string_ua?: string|null,
 *     features_ua?: array<int|string, PromUaFeatureRow>
 * }&\stdClass
 */
class PromUaAdapter extends AbstractPresetAdapter
{
    /** @var string */
    protected static $headerTemplate = 'presets/prom_ua/header.tpl';

    /** @var string */
    protected static $footerTemplate = 'presets/prom_ua/footer.tpl';

    /** @var UaLanguageRow|false|null */
    private $uaLang;

    private string $siteNameUa;
    /** @var array<int|string, CategoryUaRow> */
    private array $allCategoriesUa;
    private object $defaultProductsSeoPatternUa;

    public function __construct(
        Money $money,
        Design $design,
        QueryFactory $queryFactory,
        Database $database,
        XmlFeedHelper $xmlFeedHelper,
        Response $response,
        ExtendedPdo $pdo,
        Settings $settings,
        Languages $languages,
        Image $image,
        CurrenciesEntity $currenciesEntity,
        FeedsEntity $feedsEntity,
        CategoriesEntity $categoriesEntity,
        FeedsHelper $feedHelper
    ) {
        parent::__construct(
            $money,
            $design,
            $queryFactory,
            $database,
            $xmlFeedHelper,
            $response,
            $pdo,
            $settings,
            $languages,
            $image,
            $currenciesEntity,
            $feedsEntity,
            $categoriesEntity,
            $feedHelper
        );

        $this->uaLang = $this->feedHelper->checkIfUaMainLanguageIs();

        // Отримуємо дані для української версії
        if (!empty($this->uaLang) && !empty($uaLangId = $this->uaLang->id)) {
            $currentLangId = $this->languages->getLangId();
            $this->languages->setLangId($uaLangId);
            $this->settings->initSettings();

            $this->siteNameUa = $this->settings->get('site_name');
            $this->defaultProductsSeoPatternUa = (object)$settings->get('default_products_seo_pattern');
            $categoriesEntity->initCategories();
            /** @var array<int|string, CategoryUaRow> $allCategoriesUa */
            $allCategoriesUa = $categoriesEntity->find();
            $this->allCategoriesUa = $allCategoriesUa;

            $this->languages->setLangId($currentLangId);
            $this->settings->initSettings();
            $categoriesEntity->initCategories();
        }
    }

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

    public function getQuery(int|string $feedId): Select
    {
        $sql = parent::getQuery(...func_get_args());

        //  получаем украинские данные
        if (!empty($this->uaLang) && !empty($uaLangId = $this->uaLang->id)) {
            if ($this->feed->settings['use_full_description']) {
                $sql->cols([
                    'lp.description AS description',
                    'lp_ua.name as product_name_ua',
                    'lp_ua.description as description_ua',
                    'lv_ua.name as variant_name_ua',
                    'lb_ua.name as brand_name_ua',
                ]);
            } else {
                $sql->cols([
                    'lp.annotation AS annotation',
                    'lp_ua.name as product_name_ua',
                    'lp_ua.annotation as annotation_ua',
                    'lv_ua.name as variant_name_ua',
                    'lb_ua.name as brand_name_ua',
                ]);
            }

            $sql->leftJoin(ProductsEntity::getLangTable() . ' AS lp_ua', 'lp_ua.product_id = t.product_id and lp_ua.lang_id=' . $uaLangId);
            $sql->leftJoin(VariantsEntity::getLangTable() . ' AS lv_ua', 'lv_ua.variant_id = t.variant_id and lv_ua.lang_id=' . $uaLangId);
            $sql->leftJoin(BrandsEntity::getLangTable() . ' AS lb_ua', 'lb_ua.brand_id = t.brand_id and lb_ua.lang_id=' . $uaLangId);
        } else {
            if ($this->feed->settings['use_full_description']) {
                $sql->cols(['lp.description AS description']);
            } else {
                $sql->cols(['lp.annotation AS annotation']);
            }
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

        //  получаем украинские данные
        if (!empty($this->uaLang) && !empty($uaLangId = $this->uaLang->id)) {
            $sql->cols([
                'GROUP_CONCAT(DISTINCT lf_ua.feature_id, "!-", lf_ua.name SEPARATOR "@|@") AS features_string_ua',
                'GROUP_CONCAT(DISTINCT fv.feature_id, "!-", lfv_ua.value SEPARATOR "@|@") AS values_string_ua',
            ])
                ->leftJoin(FeaturesValuesEntity::getLangTable() . ' AS  lfv_ua', 'fv.id = lfv_ua.feature_value_id and lfv_ua.lang_id=' . $uaLangId)
                ->leftJoin(FeaturesEntity::getLangTable() . ' AS  lf_ua', 'f.id = lf_ua.feature_id and lf_ua.lang_id=' . $uaLangId);
        }

        return ExtenderFacade::execute(__METHOD__, $sql, func_get_args());
    }

    public function modifyItem(object $item): object
    {
        $item = parent::modifyItem($item);
        /** @var PromUaProductRow $item */

        //  получаем украинские данные
        if (!empty($this->uaLang) && !empty($this->uaLang->id)) {
            $item = $this->xmlFeedHelper->attachFeatures(
                $item,
                'features_string_ua',
                'values_string_ua',
                'features_ua'
            );
            /** @var PromUaProductRow $item */

            // Застосовуємо шаблон опису товару на українській мові
            $metaParts = $this->getMetadataPartsUa($item);

            $item = $this->xmlFeedHelper->attachDescriptionByTemplate(
                $item,
                $metaParts,
                $this->getDescriptionTemplateUa($item),
                'description_ua'
            );
            /** @var PromUaProductRow $item */
            $annotationProduct = $item;
            /** @var PromUaProductRow $annotationProduct */
            $item = $this->xmlFeedHelper->attachDescriptionByTemplate(
                $item,
                $metaParts,
                $this->getAnnotationTemplateUa($annotationProduct),
                'annotation_ua'
            );
            /** @var PromUaProductRow $item */
        }
        return $item;
    }

    /**
     * @param PromUaProductRow $product
     */
    protected function getDescriptionTemplateUa($product): string
    {
        $categoryId = $product->main_category_id;
        $category = $categoryId !== null && isset($this->allCategoriesUa[$categoryId]) ? $this->allCategoriesUa[$categoryId] : null;
        $descriptionTemplate = '';
        if ($category !== null && ($data = $this->xmlFeedHelper->getCategoryField($category, 'auto_description'))) {
            $descriptionTemplate = $data;
        } elseif (!empty($this->defaultProductsSeoPatternUa->auto_description)) {
            $descriptionTemplate = $this->defaultProductsSeoPatternUa->auto_description;
        }
        return $descriptionTemplate;
    }
    /**
     * @param PromUaProductRow $product
     */
    protected function getAnnotationTemplateUa($product): string
    {
        $categoryId = $product->main_category_id;
        $category = $categoryId !== null && isset($this->allCategoriesUa[$categoryId]) ? $this->allCategoriesUa[$categoryId] : null;
        $annotationTemplate = '';
        if ($category !== null && ($data = $this->xmlFeedHelper->getCategoryField($category, 'auto_annotation'))) {
            $annotationTemplate = $data;
        } elseif (!empty($this->defaultProductsSeoPatternUa->auto_annotation)) {
            $annotationTemplate = $this->defaultProductsSeoPatternUa->auto_annotation;
        }
        return $annotationTemplate;
    }

    /**
     * @return array<string, string>
     * @param PromUaProductRow $product
     */
    protected function getMetadataPartsUa($product): array
    {
        $mataDataParts = $this->xmlFeedHelper->getMetadataParts($product);

        if (!empty($product->brand_name_ua)) {
            $mataDataParts['{$brand}'] = $product->brand_name_ua;
        }

        if (!empty($product->product_name_ua)) {
            $mataDataParts['{$product}'] = $product->product_name_ua;
        }

        $mataDataParts['{$sitename}'] = $this->siteNameUa;

        if (!empty($product->main_category_id) && isset($this->allCategoriesUa[$product->main_category_id])) {
            $category = $this->allCategoriesUa[$product->main_category_id];
            $mataDataParts['{$category}'] = ($category->name ?: '');
            $mataDataParts['{$category_h1}'] = ($category->name_h1 ?: '');
        }

        if (!empty($product->features_ua)) {
            foreach ($product->features_ua as $feature) {
                if (!empty($feature['auto_name_id'])) {
                    $mataDataParts['{$' . $feature['auto_name_id'] . '}'] = $feature['name'];
                }
                if (!empty($feature['auto_value_id'])) {
                    $mataDataParts['{$' . $feature['auto_value_id'] . '}'] = $feature['values_string'];
                }
            }
        }

        return $mataDataParts; // No ExtenderFacade
    }

    public function getItem(object $product, bool $addVariantUrl = false): array
    {
        /** @var PromUaProductRow $product */
        // Указываем связку урла товара и его slug
        ProductRoute::setUrlSlugAlias($product->url, $product->slug_url);
        if ($addVariantUrl) {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url, 'variantId' => $product->variant_id], true);
        } else {
            $result['url']['data'] = Router::generateUrl('product', ['url' => $product->url], true);
        }

        $result['name']['data'] = $this->xmlFeedHelper->escape($product->product_name . (!empty($product->variant_name) ? ' ' . $product->variant_name : ''));

        if (!empty($product->product_name_ua)) {
            $result['name_ua']['data'] = $this->xmlFeedHelper->escape($product->product_name_ua . (!empty($product->variant_name_ua) ? ' ' . $product->variant_name_ua : '') . (!empty($product->sku) ? ' (' . ($product->sku) . ')' : ''));
        }

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
                $result['description']['data'] = '<![CDATA[' . $product->description . ']]>';
            } elseif (!empty($product->annotation)) {
                $result['description']['data'] = '<![CDATA[' . $product->annotation . ']]>';
            }

            if (!empty($product->description_ua)) {
                $result['description_ua']['data'] = '<![CDATA[' . $product->description_ua . ']]>';
            } elseif (!empty($product->annotation_ua)) {
                $result['description_ua']['data'] = '<![CDATA[' . $product->annotation_ua . ']]>';
            }
        } else {
            if (!empty($product->description)) {    //  передаем описание без верстки
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->description);
            } elseif (!empty($product->annotation)) {
                $result['description']['data'] = $this->xmlFeedHelper->escape($product->annotation);
            }

            if (!empty($product->description_ua)) {
                $result['description_ua']['data'] = $this->xmlFeedHelper->escape($product->description_ua);
            } elseif (!empty($product->annotation_ua)) {
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
