<?php


namespace Seo;


use Okay\Helpers\FilterHelper;
use Okay\Helpers\FurlFilterBuilderHelper;
use Okay\Helpers\FurlFilterParserHelper;
use PHPUnit\Framework\TestCase;
use Exception;
use ReflectionClass;

class FurlFilterBuilderHelperTest extends TestCase
{

    /**
     * @param array $urlParts
     * @param array $newParams
     * @param array $expectedResult
     * @dataProvider replaceUrlParamsPageDataProvider
     * @dataProvider replaceUrlParamsSortDataProvider
     * @dataProvider replaceUrlParamsOtherFilterDataProvider
     * @dataProvider replaceUrlParamsBrandDataProvider
     * @dataProvider replaceUrlParamsFeaturesDataProvider
     * @throws Exception
     */
    public function testReplaceUrlParamsFilterUrl(array $urlParts, array $newParams, array $expectedResult)
    {
        $filterHelperStub = $this->getMockBuilder(FilterHelper::class)->disableOriginalConstructor()->getMock();

        $parserHelperStub = $this->getMockBuilder(FurlFilterParserHelper::class)->disableOriginalConstructor()->getMock();

        $parserHelperStub->method('getUrlParts')
            ->willReturn($urlParts);
        
        $parserHelperStub->method('getOtherFiltersTypes')
            ->willReturn(['discounted', 'featured']);
        
        $parserHelperStub->method('getSortTypes')
            ->willReturn(['position', 'price', 'price_desc', 'name', 'name_desc', 'rating', 'rating_desc']);

        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new ReflectionClass(FurlFilterBuilderHelper::class);
        $method = $reflector->getMethod('replaceUrlParamsFilterUrl');
        $method->setAccessible(true);

        $filterBuilder = new FurlFilterBuilderHelper($parserHelperStub, $filterHelperStub);
        $filterBuilder->setAvailableBrands([
            'foo',
            'bar',
        ]);
        $filterBuilder->setAvailableFeatures([
            (object)[
                'url' => 'color',
                'features_values' => [
                    (object)[
                        'translit' => 'red',
                    ],
                    (object)[
                        'translit' => 'blue',
                    ],
                ],
            ],
            (object)[
                'url' => 'size',
                'features_values' => [
                    (object)[
                        'translit' => 'small',
                    ],
                ],
            ],
        ]);
        
        $actualResult = $method->invokeArgs($filterBuilder, [$newParams]);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string $urlPattern
     * @param array $urlParams
     * @param string $expectedResult
     * @dataProvider buildFilterDataProvider
     */
    public function testBuildFilterUrl(string $urlPattern, array $urlParams, string $expectedResult)
    {
        $filterHelperStub = $this->getMockBuilder(FilterHelper::class)->disableOriginalConstructor()->getMock();
        $filterHelperStub->method('getUrlPattern')
            ->willReturn($urlPattern);

        $parserHelperStub = $this->getMockBuilder(FurlFilterParserHelper::class)->disableOriginalConstructor()->getMock();

        $filterBuilder = new FurlFilterBuilderHelper($parserHelperStub, $filterHelperStub);

        $actualResult = $filterBuilder->buildFilterUrl($urlParams);

        $this->assertEquals($expectedResult, $actualResult);
    }
    
    public function replaceUrlParamsPageDataProvider(): array
    {
        return [
            'смена страницы пагинации' => [
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 2,
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
                [
                    'page' => 3,
                ],
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 3,
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
            ],
            'смена страницы пагинации на первую страницу. Её не должно быть' => [
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 2,
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
                [
                    'page' => 1,
                ],
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
            ],
            'добавление страницы пагинации' => [
                [],
                [
                    'page' => 'all',
                ],
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 'all',
                    ],
                ],
            ],
            'удаление страницы пагинации' => [
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 2,
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
                [
                    'page' => null,
                ],
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
            ],
        ];
    }

    public function replaceUrlParamsSortDataProvider(): array
    {
        return [
            'смена сортировки' => [
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                            'featured',
                        ],
                    ]
                ],
                [
                    'sort' => 'price',
                ],
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'price',
                    ],
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                            'featured',
                        ],
                    ]
                ],
            ],
            'удаление сортировки' => [
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 2,
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
                [
                    'sort' => null,
                ],
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => 2,
                    ],
                ],
            ],
            'добавление сортировки' => [
                [],
                [
                    'sort' => 'price',
                ],
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'price',
                    ],
                ],
            ],
        ];
    }

    public function replaceUrlParamsOtherFilterDataProvider(): array
    {
        return [
            'удаление одного из значений доп. фильтра' => [
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                            'featured',
                        ],
                    ]
                ],
                [
                    'filter' => 'discounted',
                ],
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'featured',
                        ],
                    ]
                ],
            ],
            'добавление значения в не правильной последовательности' => [
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'featured',
                        ],
                    ]
                ],
                [
                    'filter' => 'discounted',
                ],
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                            'featured',
                        ],
                    ]
                ],
            ],
            'добавление значения' => [
                [],
                [
                    'filter' => 'discounted',
                ],
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                        ],
                    ]
                ],
            ],
            'удалили вообще фильтр' => [
                [
                    '{$otherFilter}' => [
                        'key' => 'filter',
                        'value' => [
                            'discounted',
                            'featured',
                        ],
                    ],
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
                [
                    'filter' => null,
                ],
                [
                    '{$sort}' => [
                        'key' => 'sort',
                        'value' => 'name_desc',
                    ],
                ],
            ],
        ];
    }

    public function replaceUrlParamsBrandDataProvider(): array
    {
        return [
            'Возврат текущего результата' => [
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
                [],
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
            ],
            'удаление одного из брендов' => [
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
                [
                    'brand' => 'bar',
                ],
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                        ],
                    ]
                ],
            ],
            'удаление вообще брендов' => [
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
                [
                    'brand' => null,
                ],
                [],
            ],
            // В базе бренды расположены foo, bar
            'добавление бренда в не правильной последовательности' => [
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'bar',
                        ],
                    ]
                ],
                [
                    'brand' => 'foo',
                ],
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
            ],
            'добавление фильтра по бренду' => [
                [],
                [
                    'brand' => 'foo',
                ],
                [
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                        ],
                    ]
                ],
            ],
        ];
    }

    public function replaceUrlParamsFeaturesDataProvider(): array
    {
        return [
            'добавление нового значения свойства' => [
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'blue',
                            ],
                        ],
                    ]
                ],
                [
                    'color' => 'red',
                ],
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                    ],
                ],
            ],
            'удаление значения свойства' => [
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                    ]
                ],
                [
                    'color' => 'red',
                ],
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'blue',
                            ],
                        ],
                    ],
                ],
            ],
            'удаление свойства' => [
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ]
                ],
                [
                    'color' => null,
                ],
                [
                    '{$features}' => [
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ],
                ],
            ],
            'удаление всех свойств' => [
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ]
                ],
                [
                    'color' => null,
                    'size' => null,
                ],
                [],
            ],
            // свойства в базе расположены color, size
            'добавление нового свойства' => [
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                    ]
                ],
                [
                    'size' => 'small',
                ],
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                                'blue',
                            ],
                        ],
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ],
                ],
            ],
            // свойства в базе расположены color, size
            'добавление нового свойства обратная последовательность' => [
                [
                    '{$features}' => [
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ]
                ],
                [
                    'color' => 'red',
                ],
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'red',
                            ],
                        ],
                        'size' => [
                            'key' => 'size',
                            'value' => [
                                'small',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function buildFilterDataProvider(): array
    {
        return [
            [
                '{$brand}/{$otherFilter}/{$features}/{$page}/{$sort}',
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'blue',
                            ],
                        ],
                    ],
                ],
                'color-blue',
            ],
            [
                '{$features}/{$page}',
                [
                    '{$features}' => [
                        'color' => [
                            'key' => 'color',
                            'value' => [
                                'blue',
                            ],
                        ],
                    ],
                    '{$page}' => [
                        'key' => 'page',
                        'value' => [
                            '2',
                        ],
                    ],
                ],
                'color-blue/page-2',
            ],
            [
                '{$brand}/{$page}',
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => [
                            'all',
                        ],
                    ],
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
                'brand-foo_bar/page-all',
            ],
            [
                '{$page}/{$brand}',
                [
                    '{$page}' => [
                        'key' => 'page',
                        'value' => [
                            'all',
                        ],
                    ],
                    '{$brand}' => [
                        'key' => 'brand',
                        'value' => [
                            'foo',
                            'bar',
                        ],
                    ]
                ],
                'page-all/brand-foo_bar',
            ],
        ];
    }
}