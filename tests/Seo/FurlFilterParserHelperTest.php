<?php

namespace Seo;

use Okay\Core\EntityFactory;
use Okay\Entities\BrandsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\FurlFilterParserHelper;
use PHPUnit\Framework\TestCase;
use Exception;

class FurlFilterParserHelperTest extends TestCase
{
    private $furlFilterParserHelper;
    
    public function setUp() : void
    {
        $filterHelperStub = $this->getMockBuilder(FilterHelper::class)->disableOriginalConstructor()->getMock();
        $filterHelperStub->method('getUrlPattern')->willReturn('{$brand}/{$otherFilter}/{$features}/{$page}/{$sort}');
        $filterHelperStub->method('getNotFeaturesParts')->willReturn(['brand', 'filter', 'page', 'sort']);

        // настраиваем BrandsEntityStub
        $brandsEntityStub = $this->getMockBuilder(BrandsEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $brandsEntityStub->method('col')->willReturnSelf();
        
        $brandsEntityStub->method('find')
            ->will($this->returnValueMap([
                [
                    ['url' => ['foo']],
                    [
                        'foo',
                    ],
                ],
                [
                    ['url' => ['foo', 'bar']],
                    [
                        'foo',
                        'bar',
                    ],
                ],
                [ // запрашивали бренды в другой последовательности
                    ['url' => ['bar', 'foo']],
                    [
                        'foo',
                        'bar',
                    ],
                ],
                [
                    ['url' => ['foo', 'foo', 'bar']], // дубль бренда
                    [
                        'foo',
                        'bar',
                    ],
                ],
                [
                    ['url' => ['foo', 'bar', 'baz']], // нет бренда baz
                    [
                        'foo',
                        'bar',
                    ],
                ],
            ]));
        
        // настраиваем FeaturesEntityStub
        $featuresEntityStub = $this->getMockBuilder(FeaturesEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $featuresEntityStub->method('col')->willReturnSelf();

        $featuresEntityStub->method('find')
            ->will($this->returnValueMap([
                [
                    ['url' => ['color']],
                    [
                        'color',
                    ],
                ],
                [
                    ['url' => ['color2']],
                    [],
                ],
                [
                    ['url' => ['color', 'size']],
                    [
                        'color',
                        'size',
                    ],
                ],
                [ // запрашивали свойства в другой последовательности
                    ['url' => ['size', 'color']],
                    [
                        'color',
                        'size',
                    ],
                ],
                [
                    ['url' => ['color', 'size', 'color']], // дубль бренда
                    [
                        'color',
                        'size',
                    ],
                ],
                [
                    ['url' => ['color', 'size', 'baz']], // нет свойства baz
                    [
                        'color',
                        'size',
                    ],
                ],
            ]));

        // настраиваем FeaturesValuesEntityStub
        $featuresValuesEntityStub = $this->getMockBuilder(FeaturesValuesEntity::class)
            ->disableOriginalConstructor()
            ->getMock();

        $featuresValuesEntityStub->method('cols')->willReturnSelf();

        $featuresValuesEntityStub->method('find')
            ->will($this->returnValueMap([
                [
                    ['selected_features' => ['color' => ['red'], 'size' => ['small']]],
                    [
                        (object)[
                            'feature_url' => 'size',
                            'translit' => 'small',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                    ],
                ],
                [
                    ['selected_features' => ['color' => ['red', 'blue'], 'size' => ['small']]],
                    [
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'blue',
                        ],
                        (object)[
                            'feature_url' => 'size',
                            'translit' => 'small',
                        ],
                    ],
                ],
                [
                    ['selected_features' => ['color' => ['red', 'blue', 'yellow'], 'size' => ['small']]],
                    [
                        (object)[
                            'feature_url' => 'size',
                            'translit' => 'small',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'blue',
                        ],
                    ],
                ],
                [ // Запрашивают значения в другой последовательности
                    ['selected_features' => ['color' => ['blue', 'red']]],
                    [
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'blue',
                        ],
                    ],
                ],
                [ // Запрашивают значения в другой последовательности
                    ['selected_features' => ['color' => ['blue', 'red'], 'size' => ['small']]],
                    [
                        (object)[
                            'feature_url' => 'size',
                            'translit' => 'small',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'blue',
                        ],
                    ],
                ],
                [ // Запрашивают свойства в другой последовательности
                    ['selected_features' => ['size' => ['small'], 'color' => ['red']]],
                    [
                        (object)[
                            'feature_url' => 'size',
                            'translit' => 'small',
                        ],
                        (object)[
                            'feature_url' => 'color',
                            'translit' => 'red',
                        ],
                    ],
                ],
                [ // Запрашивают несуществующее свойство
                    ['selected_features' => ['color2' => ['red']]],
                    [],
                ],
            ]));
        
        $entityFactoryStub = $this->getMockBuilder(EntityFactory::class)->disableOriginalConstructor()->getMock();
        $entityFactoryMap = [
            [BrandsEntity::class, $brandsEntityStub],
            [FeaturesEntity::class, $featuresEntityStub],
            [FeaturesValuesEntity::class, $featuresValuesEntityStub],
        ];

        $entityFactoryStub->method('get')
            ->will($this->returnValueMap($entityFactoryMap));
        
        $this->furlFilterParserHelper = new FurlFilterParserHelper($filterHelperStub, $entityFactoryStub);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider getCurrentPageDataProvider
     */
    public function testGetCurrentPage($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->getCurrentPage();

        $this->assertEquals($expectedResult, $actualResult);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider getCurrentSortDataProvider
     */
    public function testGetCurrentSort($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->getCurrentSort();

        $this->assertEquals($expectedResult, $actualResult);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider getCurrentOtherFiltersDataProvider
     */
    public function testGetCurrentOtherFilters($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->getCurrentOtherFilters();

        $this->assertEquals($expectedResult, $actualResult);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider getCurrentBrandsUrlsDataProvider
     */
    public function testGetCurrentBrandsUrls($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->getCurrentBrandsUrls();

        $this->assertEquals($expectedResult, $actualResult);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider getCurrentFeaturesUrlsDataProvider
     */
    public function testGetCurrentFeaturesUrls($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->getCurrentFeaturesUrls();
        
        $this->assertEquals($expectedResult, $actualResult);
    }
    
    /**
     * @param $filterUrl
     * @param $expectedResult
     * @throws Exception
     * @dataProvider validateUrlDataProvider
     */
    public function testValidateUrl($filterUrl, $expectedResult)
    {
        $this->furlFilterParserHelper->setFilterUrl($filterUrl);
        $actualResult = $this->furlFilterParserHelper->validateUrl();

        if ($expectedResult === true) {
            $this->assertTrue($actualResult);
        } else {
            $this->assertEquals($expectedResult, $actualResult);
        }
    }
    
    public function getCurrentPageDataProvider() : array
    {
        return [
            [
                'param1-val1/page-3',
                3,
            ],
            [
                'param1-val1/page-all',
                'all',
            ],
            [ // Пагинация не может начинаться на 0
                'param1-val1/page-03',
                false,
            ],
            [
                'param1-val1',
                '',
            ],
        ];
    }

    public function getCurrentSortDataProvider() : array
    {
        return [
            [
                'param1-val1/sort-name',
                'name',
            ],
            [
                'param1-val1/sort-position',
                'position',
            ],
            [ // Нет такой сортировки
                'param1-val1/sort-some_sort',
                false,
            ],
            [
                'param1-val1',
                '',
            ],
            [
                'param1-val1/sort-nameкириллица',
                false,
            ],
        ];
    }

    public function getCurrentOtherFiltersDataProvider() : array
    {
        return [
            [
                'filter-discounted_featured/param1-val1/sort-name',
                [
                    'discounted',
                    'featured',
                ],
            ],
            [
                'filter-discounted/param1-val1/sort-position',
                [
                    'discounted',
                ],
            ],
            [
                'filter-featured/param1-val1/sort-some_sort',
                [
                    'featured',
                ],
            ],
            [ // Дубль значения
                'filter-featured_featured/param1-val1/sort-some_sort',
                false,
            ],
            [
                'filter-myfilter/param1-val1/sort-some_sort',
                false,
            ],
            [
                'param1-val1/sort-some_sort',
                [],
            ],
            [
                'param1-val1/filter-featuredкириллица',
                false,
            ],
        ];
    }
    
    public function validateUrlDataProvider() : array
    {
        return [
            [
                'filter-discounted_featured/page-1/sort-name',
                true,
            ],
            [
                'filter-discounted_featured/page-all',
                true,
            ],
            [
                'filter-discounted',
                true,
            ],
            [
                'sort-name',
                true,
            ],
            [
                'brand-foo_bar/filter-discounted_featured',
                true,
            ],
            [
                'filter-featured_discounted/sort-name',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_VALUES,
            ],
            [ // сортировка не на своём месте
                'sort-name/filter-discounted_featured',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS,
            ],
            [ // пагинация не на своём месте
                'page-2/filter-discounted_featured',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS,
            ],
            [ // неизвестная часть foo-bar/
                'filter-discounted_featured/foo-bar/page-1/sort-name',
                FILTER_ERROR_WRONG_PARAMS,
            ],
            [ // дубль параметров
                'filter-discounted_featured/page-1/page-2/sort-name',
                FILTER_ERROR_WRONG_PARAMS,
            ],
            [ // Перепутаны местами бренд и доп. фильтр
                'filter-discounted_featured/brand-foo_bar',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS,
            ],
            [ // перепутаны местами значения свойства
                'color-blue_red/size-small/page-1/sort-name',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_VALUES,
            ],
            [ // перепутаны местами свойства
                'size-small/color-red/page-1/sort-name',
                FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS,
            ],
            [ // перепутаны местами свойства
                'color2-red/page-1/sort-name',
                FILTER_ERROR_WRONG_PARAMS,
            ],
        ];
    }
    
    public function getCurrentBrandsUrlsDataProvider() : array
    {
        return [
            [
                'brand-foo/page-1/sort-name',
                [
                    'foo',
                ],
            ],
            [
                'brand-foo_bar/page-1/sort-name',
                [
                    'foo',
                    'bar',
                ],
            ],
            [
                'brand-foo_bar_baz/page-1/sort-name',
                false,
            ],
            [ // Дубль бренда
                'brand-foo_foo_bar/page-1/sort-name',
                false,
            ],
        ];
    }
    
    public function getCurrentFeaturesUrlsDataProvider() : array
    {
        return [
            [
                'color-red/size-small/page-1/sort-name',
                [
                    'color' => [
                        'red',
                    ],
                    'size' => [
                        'small',
                    ],
                ],
            ],
            [
                'color-red_blue/size-small/page-1/sort-name',
                [
                    'color' => [
                        'red',
                        'blue',
                    ],
                    'size' => [
                        'small',
                    ],
                ],
            ],
            [ // цвета yellow нет
                'color-red_blue_yellow/size-small/page-1/sort-name',
                false,
            ],
            [ // свойства нет нет
                'color2-red/page-1/sort-name',
                false,
            ],
            [ // вообще не передали свойства
                'page-1/sort-name',
                [],
            ],
        ];
    }
}
