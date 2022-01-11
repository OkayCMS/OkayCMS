<?php

namespace Seo;

use Okay\Helpers\MetaRobotsHelper;
use PHPUnit\Framework\TestCase;
use Exception;

class MetaRobotsHelperTest extends TestCase
{

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        require_once 'Okay/Core/config/constants.php';
    }

    /**
     * @param array $robotsSettings
     * @param array $otherFilters
     * @param $expectedResult
     * @dataProvider getBaseCatalogOtherFiltersDataProvider
     *
     */
    public function testGetBaseCatalogRobots(array $robotsSettings, array $otherFilters, $expectedResult)
    {
        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new \ReflectionClass(MetaRobotsHelper::class);
        $method = $reflector->getMethod('getBaseCatalogRobots');
        $method->setAccessible(true);

        $metaRobotsHelper = new MetaRobotsHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $robotsSettings);

        $actualResult = $method->invokeArgs($metaRobotsHelper, [$otherFilters]);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param array $features
     * @param string $expectedExceptionMessage
     * @dataProvider setAvailableFeaturesDataProvider
     */
    public function testSetAvailableFeatures(array $features, string $expectedExceptionMessage)
    {
        $metaRobotsHelper = new MetaRobotsHelper;

        $actualResult = '';
        try {
            $metaRobotsHelper->setAvailableFeatures($features);
        } catch (Exception $e) {
            $actualResult = $e->getMessage();
        }
        $this->assertEquals($actualResult, $expectedExceptionMessage);
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
     * @throws Exception
     * @dataProvider getCatalogPaginationFullFiltersDataProvider
     */
    public function testGetCatalogRobots(array $robotsSettings, $page, array $otherFilters, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        $metaRobotsHelper = new MetaRobotsHelper;

        $metaRobotsHelper->setAvailableFeatures([
            (object)[
                'id' => 1,
                'features_values' => [
                    (object)[
                        'value' => 'val_11',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_12',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_111',
                        'to_index' => 0,
                    ],
                    (object)[
                        'value' => 'val_112',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_113',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_114',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1151',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1152',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1153',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_117',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1161',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1162',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1163',
                        'to_index' => 1,
                    ],
                ],
            ],
            (object)[
                'id' => 2,
                'features_values' => [
                    (object)[
                        'value' => 'val_2122',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_22',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_211',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_212',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_213',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_214',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_215',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_1161',
                        'to_index' => 1,
                    ],
                    (object)[
                        'value' => 'val_21',
                        'to_index' => 1,
                    ],
                ],
            ],
        ]);

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $robotsSettings);

        $actualResult = $metaRobotsHelper->getCatalogRobots($page, $otherFilters, $featuresFilter, $brandsFilter);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param array $robotsSettings
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @param array|false $expectedResult
     * @throws \ReflectionException
     * @dataProvider getCatalogFeaturesFilterDataProvider
     */
    public function testGetCatalogRobotsExecutor(array $robotsSettings, array $featuresFilter, array $brandsFilter, $expectedResult)
    {
        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new \ReflectionClass(MetaRobotsHelper::class);
        $method = $reflector->getMethod('getCatalogRobotsExecutor');
        $method->setAccessible(true);

        $metaRobotsHelper = new MetaRobotsHelper;

        // Передаем нужные настройки в наш класс
        call_user_func_array([$metaRobotsHelper, 'setParams'], $robotsSettings);

        $actualResult = $method->invokeArgs($metaRobotsHelper, [$featuresFilter, $brandsFilter]);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getCatalogFeaturesFilterDataProvider() : array
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
                [
                    1 => [
                        150 => 'val_11',
                    ],
                    2 => [
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
                [
                    1 => [
                        150 => 'val_12',
                    ],
                    2 => [
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
                [
                    1 => [
                        150 => 'val_13',
                    ],
                    2 => [
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
                [
                    1 => [
                        150 => 'val_14',
                    ],
                    2 => [
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
                [
                    1 => [
                        150 => 'val_15',
                        151 => 'val_25',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }
    
    public function getBaseCatalogOtherFiltersDataProvider() : array
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
    public function getCatalogPaginationFullFiltersDataProvider() : array
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
                    1 => [
                        150 => 'val_112',
                    ],
                    2 => [
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
                    3, // Общая максимальная вложенность фильтра
                ],
                null,
                [
                    'featured',
                ],
                [
                    1 => [
                        150 => 'val_111',
                    ],
                    2 => [
                        250 => 'val_211',
                    ],
                ],
                [],
                ROBOTS_NOINDEX_NOFOLLOW, // Значение свойства не иднексируемое
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
                    1 => [
                        150 => 'val_112',
                    ],
                    2 => [
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
                    1 => [
                        150 => 'val_113',
                    ],
                    2 => [
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
                    1 => [
                        150 => 'val_114',
                    ],
                    2 => [
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
                    1 => [
                        150 => 'val_1151',
                        151 => 'val_1161',
                    ],
                    2 => [
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
                    1 => [
                        150 => 'val_1152',
                        151 => 'val_1162',
                    ],
                    2 => [
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
                    1 => [
                        150 => 'val_1153',
                        151 => 'val_1163',
                    ],
                    2 => [
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
                    1 => [
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
                [],
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
                [],
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
                [],
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
                [],
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
                [],
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
                [],
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
                2,
                [
                    'featured',
                ],
                [],
                [],
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
                [],
                [],
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
                [],
                [],
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
                [],
                [],
                ROBOTS_NOINDEX_NOFOLLOW,
            ],
        ];
    }
    
    public function setAvailableFeaturesDataProvider() : array
    {
        return [
            [
                [
                    (object)[
                        'name' => 'test',
                    ],
                ],
                'Param $features must have id property',
            ],
            [
                [
                    (object)[
                        'id' => 1,
                    ],
                ],
                'Param $features must have features_values property',
            ],
            [
                [
                    (object)[
                        'id' => 1,
                        'features_values' => [
                            (object)[
                                'name' => 'test'
                            ],
                        ],
                    ],
                ],
                'Param $features[]->features_values must have value property',
            ],
            [
                [
                    (object)[
                        'id' => 1,
                        'features_values' => [
                            (object)[
                                'value' => 'test'
                            ],
                        ],
                    ],
                ],
                'Param $features[]->features_values must have to_index property',
            ],
            [
                [
                    (object)[
                        'id' => 1,
                        'features_values' => [
                            (object)[
                                'value' => 'test',
                                'to_index' => 1
                            ],
                        ],
                    ],
                ],
                '',
            ],
        ];
    }
    
}
