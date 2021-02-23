<?php

namespace Seo;

use Okay\Helpers\MetaRobotsHelper;
use PHPUnit\Framework\TestCase;

class MetaRobotsHelperTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        require_once 'Okay/Core/config/constants.php';
    }

    /**
     * @param array $robotsSettings
     * @param $page
     * @param array $otherFilters
     * @param $expectedResult
     * @dataProvider getCatalogPaginationDataProvider
     * @dataProvider getCatalogOtherFiltersDataProvider
     * @dataProvider getCatalogOtherFiltersPaginationDataProvider
     */
    public function testGetCatalogRobots(array $robotsSettings, $page, array $otherFilters, $expectedResult)
    {
        $metaRobotsHelper = new MetaRobotsHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $robotsSettings);

        $actualResult = $metaRobotsHelper->getCatalogRobots($page, $otherFilters);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Интеграционный тест, проверяет как сработает полное определение robots для категории
     * 
     * @param array $robotsSettings
     * @param $page
     * @param array $otherFilters
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @param $expectedResult
     * @dataProvider getCategoryPaginationFullFiltersDataProvider
     */
    public function testGetCategoryRobots(array $robotsSettings, $page, array $otherFilters, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        $metaRobotsHelper = new MetaRobotsHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $robotsSettings);

        $actualResult = $metaRobotsHelper->getCategoryRobots($page, $otherFilters, $featuresFilter, $brandsFilter);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param array $canonicalSettings
     * @param $page
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @param array|false $expectedResult
     * @throws \ReflectionException
     * @dataProvider getCategoryFeaturesFilterDataProvider
     */
    public function testGetCategoryCanonicalDataExecutor(array $canonicalSettings, $page, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new \ReflectionClass(MetaRobotsHelper::class);
        $method = $reflector->getMethod('getCategoryRobotsExecutor');
        $method->setAccessible(true);

        $metaRobotsHelper = new MetaRobotsHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $canonicalSettings);

        $actualResult = $method->invokeArgs($metaRobotsHelper, [(string)$page, $featuresFilter, $brandsFilter]);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getCategoryFeaturesFilterDataProvider() : array
    {
        return [
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_11',
                    ],
                    'feature_2' => [
                        250 => 'val_2122',
                    ],
                ],
                [],
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_NOINDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_12',
                    ],
                    'feature_2' => [
                        250 => 'val_22',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_NOINDEX_NOFOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_13',
                    ],
                    'feature_2' => [
                        250 => 'val_23',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    1, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_14',
                    ],
                    'feature_2' => [
                        250 => 'val_24',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'feature_1' => [
                        150 => 'val_15',
                        151 => 'val_25',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }
    
    public function getCatalogOtherFiltersDataProvider() : array
    {
        return [
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_NOINDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_NOINDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_NOINDEX_NOFOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [// В настройках указано index,follow, но превышена максимальное кол-во доп. фильтров, выводим noindex,nofollow
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [// В настройках указано index,follow, но превышена максимальная вложенность фильтров, выводим noindex,nofollow
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }
    
    public function getCatalogPaginationDataProvider() : array
    {
        return [
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    1, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                2,
                [],
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_NOINDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    0, // Общая максимальная вложенность фильтра
                ],
                3,
                [],
                ROBOTS_NOINDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_NOINDEX_NOFOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    0, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                4,
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    1, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_NOINDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                ROBOTS_NOINDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_NOINDEX_NOFOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                'all',
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }

    public function getCatalogOtherFiltersPaginationDataProvider() : array
    {
        return [
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_NOINDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                3,
                [
                    'featured',
                ],
                ROBOTS_NOINDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_NOINDEX_NOFOLLOW, // filter pagination
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
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [// В настройках указано index,follow, но превышена максимальное кол-во доп. фильтров, выводим noindex,nofollow
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    1, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    2, // Общая максимальная вложенность фильтра
                ],
                3,
                [
                    'featured',
                    'discounted',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }

    /**
     * Немного кейсов определения robots в категории при разных условиях
     * @return array[]
     */
    public function getCategoryPaginationFullFiltersDataProvider() : array
    {
        return [
            [
                [
                    ROBOTS_NOINDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_NOINDEX_NOFOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_111',
                    ],
                    'feature_2' => [
                        250 => 'val_211',
                    ],
                ],
                [],
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_NOINDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_NOINDEX_NOFOLLOW, // filter pagination
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
                    'feature_1' => [
                        150 => 'val_112',
                    ],
                    'feature_2' => [
                        250 => 'val_212',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
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
                [
                    'feature_1' => [
                        150 => 'val_113',
                    ],
                    'feature_2' => [
                        250 => 'val_213',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    1, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_114',
                    ],
                    'feature_2' => [
                        250 => 'val_214',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    1, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1151',
                        151 => 'val_1161',
                    ],
                    'feature_2' => [
                        250 => 'val_21',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    2, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1152',
                        151 => 'val_1162',
                    ],
                    'feature_2' => [
                        250 => 'val_215',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    3, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                    'discounted',
                    'all-products',
                ],
                [
                    'feature_1' => [
                        150 => 'val_1153',
                        151 => 'val_1163',
                    ],
                    'feature_2' => [
                        250 => 'val_21',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
            [
                [
                    ROBOTS_NOINDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_INDEX_FOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    3, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                5,
                [],
                [
                    'feature_1' => [
                        150 => 'val_117',
                    ],
                ],
                [
                    'brand_1',
                ],
                ROBOTS_INDEX_FOLLOW,
            ],
            [
                [
                    ROBOTS_INDEX_FOLLOW, // pagination
                    ROBOTS_INDEX_FOLLOW, // page-all
                    ROBOTS_INDEX_FOLLOW, // brand filter
                    ROBOTS_INDEX_FOLLOW, // features filter
                    ROBOTS_INDEX_FOLLOW, // other filters
                    ROBOTS_NOINDEX_NOFOLLOW, // filter pagination
                    2, // Максимальное кол-во брендов
                    2, // Максимальное кол-во доп. фильтров
                    2, // Максимальное кол-во разных свойств
                    3, // Максимальное кол-во значений одного свойства
                    3, // Общая максимальная вложенность фильтра
                ],
                5,
                [
                    'discounted'
                ],
                [],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }
    
}
