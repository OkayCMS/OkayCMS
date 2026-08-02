<?php

namespace Okay\Helpers;

use Okay\Core\FrontTranslations;
use Okay\Core\Money as MoneyCore;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\TranslationsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\FeaturesEntity;

class CatalogHelper
{
    /** @var MoneyCore */
    private $money;

    /** @var Settings */
    private $settings;

    /** @var Request */
    private $request;

    /** @var FilterHelper */
    private $filterHelper;

    /** @var MetaRobotsHelper */
    private $metaRobotsHelper;

    /** @var Design */
    private $design;


    /** @var FeaturesEntity */
    private $featuresEntity;

    /** @var ProductsEntity */
    private $productsEntity;


    /** @var string[] */
    private $otherFilters = [
        'discounted',
        'featured',
    ];

    public function __construct(
        EntityFactory $entityFactory,
        MoneyCore $money,
        Settings $settings,
        Request $request,
        FilterHelper $filterHelper,
        MetaRobotsHelper $metaRobotsHelper,
        Design $design
    ) {
        $this->money            = $money;
        $this->settings         = $settings;
        $this->request          = $request;
        $this->filterHelper     = $filterHelper;
        $this->metaRobotsHelper = $metaRobotsHelper;
        $this->design           = $design;

        $this->featuresEntity = $entityFactory->get(FeaturesEntity::class);
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
    }

    /**
     * @param array<string, mixed> $productsFilter
     * @param array<int|string, object{id: int|string, features_values?: mixed}&\stdClass> $catalogFeatures
     * @param array<int|string, object> $catalogCategories
     * @param array<int|string, object>|null $catalogBrands
     */
    public function assignCatalogDataProcedure(
        array $productsFilter,
        array $catalogFeatures,
        array $catalogCategories = [],
        ?array $catalogBrands = null,
        ?int $featuresLimit = null
    ): void {
        if ($catalogBrands === null) {
            $brandsFilter = $this->filterHelper->prepareFilterGetBrands($productsFilter);
            $catalogBrands = $this->filterHelper->getBrands($brandsFilter);
        }

        $otherFiltersFilter = $this->getOtherFiltersFilter($productsFilter);

        if (!empty($catalogFeatures)) {
            /**
             * Получаем значения свойств для каталога, чтобы на страницах фильтров убрать фильтры
             * у которых изначально был только один вариант выбора
             */
            $baseFeaturesValues = $this->getBaseFeaturesValues(null, $this->settings->get('missing_products'));

            // Дополняем массив $catalogFilterFeatures значениями, которые в данный момент выбраны
            // и были изначально, но их фильтрация (по бренду или цене) отсекла.
            if (!empty($baseFeaturesValues)) {
                foreach ($baseFeaturesValues as $values) {
                    foreach ($values as $value) {
                        if (isset($productsFilter['features'][$value->feature_id][$value->id]) && isset($catalogFeatures[$value->feature_id])) {
                            if (!isset($catalogFeatures[$value->feature_id]->features_values) || !is_array($catalogFeatures[$value->feature_id]->features_values)) {
                                $catalogFeatures[$value->feature_id]->features_values = [];
                            }
                            $catalogFeatures[$value->feature_id]->features_values[$value->id] = $value;
                        }
                    }
                }
            }

            // Достаём значения свойств текущей категории
            // Для каждой свойства вычисляем доступные значения отдельно, исключая саму эту свойство из фильтра
            // ВАЖНО: для каждой свойства мы показываем значения, которые есть в товарах с ВСЕМИ другими выбранными свойствами
            // Например, если выбрана свойство 13, то для свойства 14 мы показываем значения свойства 14,
            // которые есть в товарах с свойством 13 (и другими выбранными свойствами, если есть)
            // Это позволяет показать все доступные значения для каждой свойства в товарах, которые соответствуют другим фильтрам
            // ВАЖНО: после миграции на PHP 8.3+ и Smarty 5, использование &$feature в foreach может вызывать проблемы
            // с сохранением изменений после unset($feature). Поэтому мы используем прямой доступ через $catalogFeatures[$featureId]
            foreach ($catalogFeatures as $featureId => $feature) {
                // Создаём глубокую копию фильтра БЕЗ текущей свойства для вычисления доступных значений
                // ВАЖНО: для каждой свойства мы показываем значения, которые есть в товарах с ВСЕМИ другими выбранными свойствами
                // Например, если выбрана свойство 13, то для свойства 14 мы показываем значения свойства 14,
                // которые есть в товарах с свойством 13 (и другими выбранными свойствами, если есть)
                // Используем serialize/unserialize для глубокого копирования
                $filterForFeature = unserialize(serialize($productsFilter));
                // Удаляем только текущую свойство из фильтра, оставляя все остальные выбранные свойства
                // Это позволяет показать значения текущей свойства в товарах, которые соответствуют другим фильтрам
                if (isset($filterForFeature['features'][$featureId])) {
                    unset($filterForFeature['features'][$featureId]);
                    // Если после удаления текущей свойства массив features стал пустым, удаляем его полностью
                    if (empty($filterForFeature['features'])) {
                        unset($filterForFeature['features']);
                    }
                }

                $featuresValuesFilter = $this->filterHelper->prepareFilterGetFeaturesValues($filterForFeature, null, $this->settings->get('missing_products'));

                // Важно: если мы исключили текущую свойство из фильтра, нужно убедиться, что она не передается в featuresValuesFilter
                // Это позволяет показать все значения текущей свойства в товарах, которые соответствуют другим фильтрам
                // Удаляем текущую свойство из features фильтра ПЕРЕД обмеженням по feature_id
                if (isset($featuresValuesFilter['features'][$featureId])) {
                    unset($featuresValuesFilter['features'][$featureId]);
                    if (empty($featuresValuesFilter['features'])) {
                        unset($featuresValuesFilter['features']);
                    }
                }

                // Ограничиваем поиск только значениями текущей свойства
                // Это важно делать ПОСЛЕ удаления текущей свойства из features фильтра
                // ЗАМЕНЯЕМ массив всех свойств на массив только с текущей свойством
                // (не добавляем к существующему массиву, так как prepareFilterGetFeaturesValues
                // уже установил feature_id как массив всех свойств)
                $featuresValuesFilter['feature_id'] = [$featureId];

                // Инициализируем features_values как массив, если он еще не инициализирован
                if (!isset($catalogFeatures[$featureId]->features_values) || !is_array($catalogFeatures[$featureId]->features_values)) {
                    $catalogFeatures[$featureId]->features_values = [];
                }

                $valuesForFeature = 0;
                $allValuesForFeature = [];
                $foundValues = [];
                foreach ($this->filterHelper->getFeaturesValues($featuresValuesFilter) as $featureValue) {
                    if ($featureValue->feature_id == $featureId) {
                        $this->filterHelper->setFeatureValue($featureValue);
                        // features_values уже инициализирован как массив на строке 152
                        // Используем прямое присвоение через $catalogFeatures[$featureId], а не через $feature
                        // чтобы гарантировать, что изменения сохраняются даже после миграции на PHP 8.3+ и Smarty 5
                        $catalogFeatures[$featureId]->features_values[$featureValue->id] = $featureValue;
                        // ВАЖНО: после миграции на PHP 8.3+ и Smarty 5, мы не используем $feature->features_values,
                        // так как $feature не передается по ссылке в foreach, и изменения не сохраняются
                        // Поэтому мы используем только прямое присвоение через $catalogFeatures[$featureId]
                        $allValuesForFeature[] = $featureValue->id;
                        $valuesForFeature++;
                        $foundValues[] = $featureValue->id;
                    }
                }
            }

            $unusedFeatures = 0;
            foreach ($catalogFeatures as $featureId => $feature) {
                // Если хоть одно значение свойства выбрано, его убирать нельзя
                if (empty($productsFilter['features'][$feature->id])) {
                    // ВАЖНО: после миграции на PHP 8.3+ и Smarty 5, $feature может не содержать features_values
                    // из-за проблем с посиланнями после unset($feature) в предыдущем цикле
                    // Поэтому мы проверяем features_values напрямую через $catalogFeatures[$featureId]
                    $featureValuesCount = isset($catalogFeatures[$featureId]->features_values) ? count($catalogFeatures[$featureId]->features_values) : 0;
                    // На странице фильтра убираем свойства у которых вообще нет значений (отфильтровались)
                    // или они изначально имели только один вариант выбора
                    if (
                        ($featuresLimit !== null && $unusedFeatures >= $featuresLimit)
                        || !isset($baseFeaturesValues[$feature->id])
                        || ($this->settings->get('hide_single_filters')
                            && ((count($baseFeaturesValues[$feature->id]) <= 1)
                                || !isset($catalogFeatures[$featureId]->features_values)
                                || count($catalogFeatures[$featureId]->features_values) <= 1))
                    ) {
                        unset($catalogFeatures[$featureId]);
                    } else {
                        $unusedFeatures++;
                    }
                }
            }
            foreach ($catalogFeatures as $k => $feature) {
                // ВАЖНО: после миграции на PHP 8.3+ и Smarty 5, $feature может не содержать features_values
                // из-за проблем с посиланнями после unset($feature) в предыдущем цикле
                // Поэтому мы проверяем features_values напрямую через $catalogFeatures[$k]
                $hasValues = isset($catalogFeatures[$k]->features_values) && is_array($catalogFeatures[$k]->features_values) && !empty($catalogFeatures[$k]->features_values);
                if (!$hasValues) {
                    unset($catalogFeatures[$k]);
                }
            }
        }

        $rangeFilter = $productsFilter;
        unset($rangeFilter['price']);

        $catalogPrices = $this->productsEntity->getPriceRange($rangeFilter);

        // Установим возможные значения свойств
        $this->metaRobotsHelper->setAvailableFeatures($catalogFeatures);

        $this->design->assign('catalog_categories', $catalogCategories);
        $this->design->assign('catalog_brands', $catalogBrands);
        $this->design->assign('catalog_other_filters', $this->getOtherFilters($otherFiltersFilter));
        $this->design->assign('catalog_features', $catalogFeatures);
        $this->design->assign('catalog_prices', $catalogPrices);

        $this->design->assign('selected_catalog_features', $productsFilter['features'] ?? []);
        $this->design->assign('selected_catalog_brands_ids', $productsFilter['brand_id'] ?? []);
        $this->design->assign('selected_catalog_other_filters', $productsFilter['other_filter'] ?? []);
        $this->design->assign('selected_catalog_prices', $productsFilter['price'] ?? []);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     * @return array<string, mixed>
     */
    public function getOtherFiltersFilter(array $filter)
    {
        if (!empty($filter['price']) && $filter['price']['min'] != '' && $filter['price']['max'] != '') {
            if (isset($filter['price']['min'])) {
                $filter['price']['min'] = round((float)$this->money->convert($filter['price']['min'], null, false));
            }

            if (isset($filter['price']['max'])) {
                $filter['price']['max'] = round((float)$this->money->convert($filter['price']['max'], null, false));
            }
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     * @return list<object>
     */
    public function getOtherFilters(array $filter)
    {
        $SL = ServiceLocator::getInstance();
        /** @var FrontTranslations $translations */
        $translations = $SL->getService(FrontTranslations::class);

        $otherFilters = [];
        foreach ($this->otherFilters as $f) {
            $label = 'features_filter_' . $f;
            $item = (object)[
                'url' => $f,
                'name' => $translations->{$label},
                'translation' => $label,
            ];
            if (empty($filter['other_filter']) || !in_array($f, $filter['other_filter'])) {
                $tmFilter = $filter;
                $tmFilter['other_filter'] = [$f];
                $cnt = $this->productsEntity->count($tmFilter);
                if ($cnt > 0) {
                    $otherFilters[] = $item;
                }
            } else {
                $otherFilters[] = $item;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $otherFilters, func_get_args());
    }

    /**
     * Метод возвращает данные для Ajax ответа при фильтрации
     *
     * @return object
     */
    public function getAjaxFilterData()
    {
        $result = new \stdClass();
        $result->products_content = $this->design->fetch('products_content.tpl');
        $result->products_pagination = $this->design->fetch('chpu_pagination.tpl');
        $result->products_sort = $this->design->fetch('products_sort.tpl');
        $result->features = $this->design->fetch('features.tpl');
        $result->selected_features = $this->design->fetch('selected_features.tpl');
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function paginate($itemsPerPage, $currentPage, array &$filter, Design $design)
    {
        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }

        // Вычисляем количество страниц
        $productsCount = $this->productsEntity->count($filter);

        // Показать все страницы сразу
        $allPages = false;
        if ($currentPage == 'all') {
            $allPages = true;
            $itemsPerPage = $productsCount;
        }

        // Если не задана, то равна 1
        $currentPage = max(1, (int)$currentPage);
        $design->assign('current_page_num', $currentPage);
        $design->assign('is_all_pages', $allPages);

        $pagesNum = !empty($itemsPerPage) ? ceil($productsCount / $itemsPerPage) : 0;
        $design->assign('total_pages_num', $pagesNum);
        $design->assign('total_products_num', $productsCount);

        $filter['page'] = $currentPage;
        $filter['limit'] = $itemsPerPage;

        $result = true;
        if ($allPages === false && $currentPage > 1 && $currentPage > $pagesNum) {
            $result = false;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * Метод возвращает базовые значения свойств (без учёта фильтрации)
     * Используется на странице фильтра, и нужно, чтобы определить у фильтра один вариант значения (который нужно скрыть)
     * или изначально было много значений, тогда такой фильтр остаётся
     */
    /**
     * @param array<string, mixed>|null $filter
     * @return array<int|string, array<int|string, object{feature_id: int|string, id: int|string}&\stdClass>>
     */
    public function getBaseFeaturesValues(?array $filter = null, ?string $missingProducts = null): array
    {
        if ($filter === null) {
            $filter = $this->filterHelper->getFeaturesValuesFilter();
        }

        // Если скрываем из каталога товары не в наличии, значит и в фильтре их значения тоже не нужны будут
        if ($missingProducts === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }

        if (($keyword = $this->filterHelper->getKeyword()) !== null) {
            $filter['product_keyword'] = $keyword;
        }

        if (!empty($this->features)) {
            $featuresIds = array_keys($this->features);
            if (!empty($featuresIds)) {
                $filter['feature_id'] = $featuresIds;
            }
        }

        /**
         * Получаем значения свойств для категории, чтобы на страницах фильтров убрать фильтры
         * у которых изначально был только один вариант выбора
         */
        $baseFeaturesValues = [];
        foreach ($this->filterHelper->getFeaturesValues($filter) as $fv) {
            $baseFeaturesValues[$fv->feature_id][$fv->id] = $fv;
        }

        return ExtenderFacade::execute(__METHOD__, $baseFeaturesValues, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     * @return array<string, mixed>|null
     */
    public function getProductsFilter(?string $filtersUrl = null, array $filter = []): ?array
    {
        if (($currentFeatures = $this->filterHelper->getCurrentFeatures($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } else {
            foreach ($this->filterHelper->getFeatures() as $feature) {
                if (isset($currentFeatures[$feature->id])) {
                    $filter['features'][$feature->id] = $currentFeatures[$feature->id];
                }
            }
        }

        if (($currentBrandsIds = $this->filterHelper->getCurrentBrands($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } elseif (!empty($currentBrandsIds)) {
            $filter['brand_id'] = $currentBrandsIds;
        }

        if (($currentOtherFilters = $this->filterHelper->getCurrentOtherFilters($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } elseif (!empty($currentOtherFilters)) {
            $filter['other_filter'] = $currentOtherFilters;
        }

        if (($currentPrices = $this->filterHelper->getCurrentPrices($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } elseif (!empty($currentPrices)) {
            $filter['price'] = $currentPrices;
        }

        // Handle GET parameters p[min] and p[max] if URL doesn't contain price
        if (empty($currentPrices)) {
            $pParam = $this->request->get('p', null, null, false);
            if (is_array($pParam)) {
                $pMin = $pParam['min'] ?? null;
                $pMax = $pParam['max'] ?? null;
                if ($pMin !== null || $pMax !== null) {
                    $filter['price'] = ['min' => $pMin !== null && $pMin !== '' ? $pMin : null, 'max' => $pMax !== null && $pMax !== '' ? $pMax : null];
                }
            }
        }

        $filter['visible'] = 1;

        $keyword = $this->request->get('keyword', null, null, false);
        if ($keyword !== null && ($keyword = strip_tags((string)$keyword))) {
            $filter['keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * @return array<string, mixed>
     */
    public function getCatalogFeaturesFilter(): array
    {
        $filter = [
            'in_filter' => 1,
            'visible' => 1,
        ];

        if (($keyword = $this->filterHelper->getKeyword()) !== null) {
            $filter['product_keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * @param array<string, mixed>|null $filter
     * @return array<int, object{id: int|string, url: string, features_values?: array<int|string, object>}&\stdClass>
     */
    public function getCatalogFeatures(?array $filter = null): array
    {
        if ($filter === null) {
            $filter = $this->getCatalogFeaturesFilter();
        }

        $features = $this->featuresEntity->mappedBy('id')->find($filter);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getProductsSort(?string $filtersUrl = null)
    {
        if (($currentSort = $this->filterHelper->getCurrentSort($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        // Сортировка товаров, сохраняем в сессии, чтобы текущая сортировка оставалась для всего сайта
        if (!empty($currentSort)) {
            $_SESSION['sort'] = $currentSort;
        }
        if (!empty($_SESSION['sort'])) {
            $sortProducts = $_SESSION['sort'];
        } else {
            $sortProducts = 'position';
        }

        return ExtenderFacade::execute(__METHOD__, $sortProducts, func_get_args());
    }
}
