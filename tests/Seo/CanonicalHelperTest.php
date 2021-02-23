<?php

namespace Seo;

use Okay\Helpers\CanonicalHelper;
use PHPUnit\Framework\TestCase;

class CanonicalHelperTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        require_once 'Okay/Core/config/constants.php';
    }

    /**
     * @param array $canonicalSettings
     * @param $page
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @param array|false $expectedResult
     * @throws \ReflectionException
     * @dataProvider getCategoryFeaturesFilterDataProvider
     * @dataProvider getCategoryBrandsFilterDataProvider
     * @dataProvider getCategoryFeaturesBrandsFilterDataProvider
     * @dataProvider getCategoryPaginationFeaturesBrandsFilterDataProvider
     */
    public function testGetCategoryCanonicalDataExecutor(array $canonicalSettings, $page, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new \ReflectionClass(CanonicalHelper::class);
        $method = $reflector->getMethod('getCategoryCanonicalDataExecutor');
        $method->setAccessible(true);

        $canonicalHelper = new CanonicalHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$canonicalHelper, 'setParams'], $canonicalSettings);

        $actualResult = $method->invokeArgs($canonicalHelper, [(string)$page, $featuresFilter, $brandsFilter]);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param array $canonicalSettings
     * @param $page
     * @param array $otherFilters
     * @param array|false $expectedResult
     * @dataProvider getCatalogPaginationDataProvider
     * @dataProvider getCatalogOtherFiltersDataProvider
     * @dataProvider getCatalogOtherFiltersPaginationDataProvider
     */
    public function testGetCatalogCanonical(array $canonicalSettings, $page, array $otherFilters, $expectedResult)
    {
        $canonicalHelper = new CanonicalHelper;
        
        // Передаем нужные настройки в наш класс
        call_user_func_array([$canonicalHelper, 'setParams'], $canonicalSettings);

        $actualResult = $canonicalHelper->getCatalogCanonicalData((string)$page, $otherFilters);
        
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Интеграционный тест, проверяет как сработает полное определение каноникла для категории
     * 
     * @param array $canonicalSettings
     * @param $page
     * @param array $otherFilters
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @param array|false $expectedResult
     * @dataProvider getCategoryPaginationFullFiltersDataProvider
     */
    public function testGetCategoryCanonicalData(array $canonicalSettings, $page, array $otherFilters, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        $canonicalHelper = new CanonicalHelper;
        
        // Передаем нужные настройки в наш класс
        call_user_func_array([$canonicalHelper, 'setParams'], $canonicalSettings);
        
        $actualResult = $canonicalHelper->getCategoryCanonicalData($page, $otherFilters, $featuresFilter, $brandsFilter);
        
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getCategoryFeaturesFilterDataProvider() : array
    {
        return [
            [ // Страница фильтров, в настройках ведет на страницу без фильтра, canonical на страницу без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [],
                [
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [ // Страница фильтров, в настройках ведет на страницу фильтра, canonical на страницу фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                ],
                [],
                [],
            ],
            [ // Страница фильтров, в настройках "отсутствует", canonical на странице отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_ABSENT, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                ],
                [],
                false,
            ],
        ];
    }
    
    public function getCategoryBrandsFilterDataProvider() : array
    {
        return [
            [ // Страница фильтров, в настройках ведет на страницу без фильтра, canonical на страницу без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'brand_1',
                ],
                [
                    'brand' => null,
                ],
            ],
            [ // Страница фильтров, в настройках ведет на страницу фильтра, canonical на страницу фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'brand_1',
                ],
                [],
            ],
            [ // Страница фильтров, в настройках "отсутствует", canonical на странице отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_ABSENT, // Category brand filter
                    CANONICAL_ABSENT, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'brand_1',
                ],
                false,
            ],
        ];
    }
    
    public function getCategoryFeaturesBrandsFilterDataProvider() : array
    {
        return [
            [ // Страница фильтров, в настройках ведет на страницу без фильтра и без бренда, canonical на страницу без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'brand' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [ // Страница фильтров, в настройках ведет на страницу фильтра и бренда, canonical на страницу фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [],
            ],
            [ // Страница фильтров, в настройках свойств "отсутствует" на страницу бренда, canonical на странице отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_ABSENT, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                false,
            ],
            [ // Страница фильтров, в настройках без бренда со свойством, canonical на свойства без бренда
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'brand' => null,
                ],
            ],
        ];
    }
    
    public function getCategoryPaginationFeaturesBrandsFilterDataProvider() : array
    {
        return [
            [ // Страница фильтров и пагинации, в настройках ведет на первую страницу без фильтра, canonical на первую страницу без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                1,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'page' => null,
                    'brand' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [ // Страница фильтров и пагинации, в настройках ведет на первую страницу с фильтром, canonical на первую страницу с фильтром
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                1,
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'page' => null,
                ],
            ],
            [ // Страница фильтров и пагинации, в настройках ведет на эту страницу с фильтром, canonical на эту страницу с фильтром
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'page' => 'all',
                ],
            ],
            [ // Страница фильтров и пагинации, в настройках "отсутствует", canonical отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand_1',
                ],
                false,
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    1, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'feature_1' => [
                        150 => 'val_11',
                    ],
                    'feature_2' => [
                        250 => 'val_22',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'page' => null,
                    'brand' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'feature_1' => [
                        150 => 'val_11',
                        151 => 'val_12',
                    ],
                    'feature_2' => [
                        250 => 'val_22',
                    ],
                ],
                [
                    'brand_1',
                ],
                [
                    'page' => null,
                    'brand' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'feature_1' => [
                        150 => 'val_11',
                        151 => 'val_12',
                    ],
                    'feature_2' => [
                        250 => 'val_21',
                        251 => 'val_22',
                        252 => 'val_23',
                    ],
                ],
                [],
                [
                    'page' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                [
                    'brand_1',
                    'brand_2',
                    'brand_3',
                ],
                [
                    'page' => null,
                    'brand' => null,
                ],
            ],
        ];
    }

    public function getCatalogPaginationDataProvider() : array
    {
        return [
            [ // Страница пагинации, canonical на первую
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                [
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница без пагинации, canonical на каталог
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'sort' => null,
                ],
            ],
            [ // Страница без пагинации, в настройках ведет на страницу пагинации, canonical на страницу без пагинации
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации, в настройках ведет на страницу пагинации, canonical на страницу пагинации
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                [
                    'page' => 2,
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации, в настройках ведет на page-all, canonical на страницу пагинации
                [
                    CANONICAL_PAGE_ALL, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                [
                    'page' => 'all',
                    'sort' => null,
                ],
            ],
            [ // Страница без пагинации, в настройках ведет на page-all, canonical на страницу без пагинации
                [
                    CANONICAL_PAGE_ALL, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации, в настройках "отсутствует", canonical отсутствует
                [
                    CANONICAL_ABSENT, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                3,
                [],
                false,
            ],
            [ // Страница page-all, в настройках ведет на первую страницу, canonical на первую страницу
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                [
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница page-all, в настройках ведет на page-all, canonical на page-all
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_CURRENT_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                [
                    'page' => 'all',
                    'sort' => null,
                ],
            ],
            [ // Страница page-all, в настройках "отсутствует", canonical отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_ABSENT, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                false,
            ],
        ];
    }
    
    public function getCatalogOtherFiltersDataProvider() : array
    {
        return [
            [ // Страница фильтров, в настройках ведет на страницу без фильтра, canonical на страницу без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'discounted',
                ],
                [
                    'filter' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница фильтров, в настройках ведет на страницу c фильтром, canonical на страницу с фильтром
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'discounted',
                ],
                [
                    'sort' => null,
                ],
            ],
            [ // Страница фильтров, в настройках "отсутствует", canonical отсутствует
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'discounted',
                ],
                false,
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    1, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                    'discounted',
                ],
                [
                    'sort' => null,
                    'filter' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    0, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                    'discounted',
                ],
                [
                    'sort' => null,
                    'filter' => null,
                ],
            ],
        ];
    }

    /**
     * Тест страницы пагинации результатов фильтрации
     * @return array[]
     */
    public function getCatalogOtherFiltersPaginationDataProvider() : array
    {
        return [
            [ // Страница пагинации и доп. фильтра, настройках без пагинации без доп. фильтра canonical на первую без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [
                    'featured',
                ],
                [
                    'filter' => null,
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации и доп. фильтром, в настройках без пагинации с доп. фильтром canonical на без пагинации с доп. фильтром
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                5,
                [
                    'featured',
                ],
                [
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации и доп. фильтром, в настройках с пагинацией с доп. фильтром canonical на пагинацию с доп. фильтром
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                5,
                [
                    'featured',
                ],
                [
                    'page' => 5,
                    'sort' => null,
                ],
            ],
            [ // Страница пагинации и доп. фильтра, в настройках "отсутствует" canonical отсутствует
                [
                    CANONICAL_ABSENT, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                5,
                [
                    'featured',
                ],
                false,
            ],
            [ // Страница page-all и доп. фильтра, настройках без пагинации без доп. фильтра canonical на первую без фильтра
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'featured',
                ],
                [
                    'filter' => null,
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница page-all и доп. фильтром, в настройках без пагинации с доп. фильтром canonical на без пагинации с доп. фильтром
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITH_FILTER, // Catalog other filter
                    CANONICAL_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'featured',
                ],
                [
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [ // Страница page-all и доп. фильтром, в настройках с пагинацией с доп. фильтром canonical на page-all с доп. фильтром
                [
                    CANONICAL_CURRENT_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'featured',
                ],
                [
                    'page' => 'all',
                    'sort' => null,
                ],
            ],
            [ // Страница page-all и доп. фильтра, в настройках "отсутствует" canonical отсутствует
                [
                    CANONICAL_ABSENT, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITHOUT_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'featured',
                ],
                false,
            ],
        ];
    }
    
    /**
     * Немного кейсов определения каноникла в категории при разных условиях
     * @return array[]
     */
    public function getCategoryPaginationFullFiltersDataProvider() : array
    {
        return [
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_WITHOUT_FILTER_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    4, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand',
                ],
                [
                    'filter' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_FIRST_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    4, // Общая максимальная вложенность фильтра
                ],
                'all',
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand',
                ],
                [
                    'page' => null,
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    4, // Общая максимальная вложенность фильтра
                ],
                3,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand',
                ],
                [
                    'page' => 3,
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    4, // Общая максимальная вложенность фильтра
                ],
                3,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [
                    'brand',
                ],
                false,
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_WITHOUT_FILTER, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        250 => 'val_2',
                    ],
                ],
                [],
                [
                    'feature_1' => null,
                    'feature_2' => null,
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITHOUT_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [],
                [],
                false,
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_ABSENT, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                ],
                [],
                [
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                ],
                [],
                [
                    'page' => 2,
                    'sort' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    1, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                [
                    'feature_1' => [
                        150 => 'val_1',
                    ],
                    'feature_2' => [
                        350 => 'val_133',
                    ],
                ],
                [],
                [
                    'page' => null,
                    'sort' => null,
                    'feature_1' => null,
                    'feature_2' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [],
                [
                    'feature_11' => [
                        150 => 'val_1',
                        154 => 'val_1',
                    ],
                ],
                [],
                [
                    'sort' => null,
                    'feature_11' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                3,
                [
                    'discounted'
                ],
                [
                    'feature_11' => [
                        150 => 'val_1',
                    ],
                ],
                [
                    'brand_123'
                ],
                [
                    'page' => null,
                    'sort' => null,
                    'feature_11' => null,
                    'brand' => null,
                    'filter' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'discounted'
                ],
                [
                    'feature_11' => [
                        150 => 'val_1',
                    ],
                    'feature_45345' => [
                        150 => 'val_1',
                    ],
                ],
                [
                    'brand_123'
                ],
                [
                    'sort' => null,
                    'feature_11' => null,
                    'feature_45345' => null,
                    'brand' => null,
                    'filter' => null,
                ],
            ],
            [
                [
                    CANONICAL_FIRST_PAGE, // Catalog pagination
                    CANONICAL_FIRST_PAGE, // page-all
                    CANONICAL_WITH_FILTER, // Category brand filter
                    CANONICAL_WITH_FILTER, // Category features filter
                    CANONICAL_ABSENT, // Catalog other filter
                    CANONICAL_CURRENT_PAGE, // Catalog filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    1, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'discounted',
                    'featured',
                ],
                [
                    'feature_11' => [
                        150 => 'val_1',
                    ],
                ],
                [
                    'brand_123'
                ],
                [
                    'sort' => null,
                    'feature_11' => null,
                    'brand' => null,
                    'filter' => null,
                ],
            ],
        ];
    }
    
}
