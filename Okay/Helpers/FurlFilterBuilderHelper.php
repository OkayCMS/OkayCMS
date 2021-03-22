<?php


namespace Okay\Helpers;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Exception;

class FurlFilterBuilderHelper
{

    private $parserHelper;
    private $filterHelper;
    
    private $sortTypes;
    private $otherFilterTypes;
    private $brandsUrls;
    private $featuresUrls;

    public function __construct(FurlFilterParserHelper $parserHelper, FilterHelper $filterHelper)
    {
        $this->parserHelper = $parserHelper;
        $this->filterHelper = $filterHelper;

        $this->sortTypes = $this->parserHelper->getSortTypes();
        $this->otherFilterTypes = $this->parserHelper->getOtherFiltersTypes();
    }

    /**
     * Метод генерирует ЧПУ строку для фильтра. Если вызвать без параметров, вернется текущий фильтр.
     * Можно передать в $params массив в виде ключ-значение для изменения параметров относительно текущего урла фильтра
     * Доступные ключи:
     *  - brand - фильтр по бренду
     *  - filter - "дополнительный" фильтр
     *  - page - страница пагинации
     *  - sort - сортировка
     *  - <feature_url> - урл любого свойства, участвующего в фильтре
     * 
     * Примеры параметров:
     * 
     * [ // Если бренда foo в строке не было, он добавиться, если он был - он удалиться.
     *  'brand' => 'foo'
     * ]
     * [ // В любом случае удалит весь фильтр по бренду
     *  'brand' => null
     * ]
     * 
     * За подробностями см. тесты Seo\FurlFilterBuilderHelperTest
     * 
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function filterChpuUrl(array $params) : string
    {
        return $this->buildFilterUrl(
            $this->replaceUrlParamsFilterUrl($params)
        );
    }

    /**
     * @param object[]|string[] $brands Массив доступных брендов для этой страницы.
     * В качестве бренда может быть объект бренда или массив урлов брендов.
     * Отсортированы они должны быть в последовательности как как их нужно добавлять в урл
     * @return $this
     * @throws Exception
     */
    public function setAvailableBrands(array $brands) : self
    {
        if (!empty($brands)) {
            $firstItem = reset($brands);
            if (is_object($firstItem)) {
                if (!property_exists($firstItem, 'url')) {
                    throw new Exception('Param $brands must have url property');
                }
                foreach ($brands as $brand) {
                    $this->brandsUrls[$brand->url] = $brand->url;
                }
            } elseif (is_string($firstItem)) {
                foreach ($brands as $brand) {
                    $this->brandsUrls[$brand] = $brand;
                }
            }
        }
        
        return $this;
    }

    /**
     * @param object[] $features Массив доступных свойств для этой страницы.
     * В качестве свойства должен быть объект свойства содержащий в свойстве features_values массив объектов значений.
     * В качестве значения свойства должен быть объект значения содержащий (свойство объекта) translit 
     * Отсортированы они должны быть в последовательности как как их нужно добавлять в урл
     * 
     * Пример данных:
     * [
     *  (object)[
     *      'url' => 'color',
     *      'features_values' => [
     *          (object)[
     *              'translit' => 'red',
     *          ],
     *          (object)[
     *              'translit' => 'blue',
     *          ],
     *      ],
     *  ],
     *  (object)[
     *      'url' => 'size',
     *      'features_values' => [
     *          (object)[
     *              'translit' => 'small',
     *          ],
     *      ],
     *  ],
     * ]
     * 
     * @return $this
     * @throws Exception
     */
    public function setAvailableFeatures(array $features) : self
    {
        if (!empty($features)) {
            $firstItem = reset($features);
            if (is_object($firstItem)) {
                if (!property_exists($firstItem, 'url')) {
                    throw new Exception('Param $features must have url property');
                }
                if (!property_exists($firstItem, 'features_values')) {
                    throw new Exception('Param $features must have features_values property');
                }
                $firstFeatureValue = reset($firstItem->features_values);

                if (!property_exists($firstFeatureValue, 'translit')) {
                    throw new Exception('Param $features[]->features_values must have translit property');
                }
                foreach ($features as $feature) {
                    foreach ($feature->features_values as $value) {
                        $this->featuresUrls[$feature->url][$value->translit] = $value->translit;
                    }
                }
            }
        }
        
        return $this;
    }
    
    public function replaceUrlParamsFilterUrl(array $params): array
    {
        $currentUrlParts = $this->parserHelper->getUrlParts();
        
        foreach ($params as $paramName => $paramValue) {
            switch ($paramName) {
                case 'filter': {
                    if (is_null($paramValue)) { // Если нужно удалить весь фильтр
                        unset($currentUrlParts['{$otherFilter}']);

                    // Если передали значение, которое уже есть. Удалим это значение
                    } elseif (!empty($currentUrlParts['{$otherFilter}']['value']) 
                        && in_array($paramValue, $currentUrlParts['{$otherFilter}']['value'])) {
                        
                        unset($currentUrlParts['{$otherFilter}']['value'][
                            array_search($paramValue, $currentUrlParts['{$otherFilter}']['value'])
                            ]);
                    } else {
                        if (!in_array($paramValue, $this->otherFilterTypes)) {
                            throw new Exception('Wrong other filter. Must be extended via '
                                .FurlFilterParserHelper::class
                                .'::getOtherFiltersTypes() method');
                        }
                        $currentUrlParts['{$otherFilter}']['value'][] = $paramValue;
                        $currentUrlParts['{$otherFilter}']['key'] = (string)$paramName;
                    }
                    if (!empty($currentUrlParts['{$otherFilter}']['value'])) {
                        // сортируем значения в соответствии с возможной их последовательностью
                        $currentUrlParts['{$otherFilter}']['value'] = array_values(
                            array_intersect(
                                $this->otherFilterTypes, 
                                $currentUrlParts['{$otherFilter}']['value']
                            )
                        );
                    } else {
                        // Если удалили все значения, удалим и информацию об фильтре
                        unset($currentUrlParts['{$otherFilter}']);
                    }
                    break;
                }
                case 'page': {
                    if (is_null($paramValue) || ($paramValue != 'all' && $paramValue <= 1)) {
                        unset($currentUrlParts['{$page}']);
                    } else {
                        $currentUrlParts['{$page}']['value'] = $paramValue;
                        $currentUrlParts['{$page}']['key'] = $paramName;
                    }
                    break;
                }
                case 'sort': {
                    if (is_null($paramValue)) {
                        unset($currentUrlParts['{$sort}']);
                    } else {
                        if (!in_array($paramValue, $this->sortTypes)) {
                            throw new Exception('Wrong sort type. Must be extended via '.FurlFilterParserHelper::class.'::getSortTypes() method');
                        }
                        $currentUrlParts['{$sort}']['value'] = $paramValue;
                        $currentUrlParts['{$sort}']['key'] = $paramName;
                    }
                    break;
                }
                case 'brand': {
                    if (is_null($paramValue)) {
                        unset($currentUrlParts['{$brand}']);
                        
                    // Если передали значение, которое уже есть. Удалим это значение
                    } elseif (!empty($currentUrlParts['{$brand}']['value']) 
                        && in_array($paramValue, $currentUrlParts['{$brand}']['value'])) {
                        
                        unset($currentUrlParts['{$brand}']['value'][
                            array_search($paramValue, $currentUrlParts['{$brand}']['value'])
                            ]);
                    } else {

                        if (!isset($this->brandsUrls[$paramValue])) {
                            throw new Exception('Wrong brand url. Need set available brands urls via '
                                .self::class
                                .'::setAvailableBrands() method');
                        }
                        
                        $currentUrlParts['{$brand}']['value'][] = $paramValue;
                        $currentUrlParts['{$brand}']['key'] = $paramName;
                    }

                    if (!empty($currentUrlParts['{$brand}']['value'])) {
                        // сортируем значения в соответствии с возможной их последовательностью
                        $currentUrlParts['{$brand}']['value'] = array_values(
                            array_intersect(
                                $this->brandsUrls,
                                $currentUrlParts['{$brand}']['value']
                            )
                        );
                    } else {
                        // Если удалили все значения, удалим и информацию об фильтре
                        unset($currentUrlParts['{$brand}']);
                    }
                    break;
                }
                default: {
                    if (is_null($paramValue)) {
                        unset($currentUrlParts['{$features}'][$paramName]);

                    // Если передали значение, которое уже есть. Удалим это значение
                    } elseif (!empty($currentUrlParts['{$features}'][$paramName]['value']) 
                        && in_array($paramValue, $currentUrlParts['{$features}'][$paramName]['value'], true) ) {

                        unset($currentUrlParts['{$features}'][$paramName]['value'][
                            array_search($paramValue, $currentUrlParts['{$features}'][$paramName]['value'])
                            ]);
                    } else {
                        
                        if (!isset($this->featuresUrls[$paramName][$paramValue])) {
                            throw new Exception('Wrong feature url. Need set available features urls via '
                                .self::class
                                .'::setAvailableFeatures() method');
                        }
                        
                        $currentUrlParts['{$features}'][$paramName]['value'][] = $paramValue;
                        $currentUrlParts['{$features}'][$paramName]['key'] = $paramName;
                        
                    }

                    if (!empty($currentUrlParts['{$features}'][$paramName]['value'])) {
                        // сортируем сами свойства в нужной последовательности
                        $featuresPositions =  array_intersect_key(
                            $this->featuresUrls,
                            $currentUrlParts['{$features}']
                        );
                        
                        $tmpFeatures = $currentUrlParts['{$features}']; // todo разобраться, тест это не отловил
                        unset($currentUrlParts['{$features}']);
                        foreach ($featuresPositions as $position => $featureName) {
                            $currentUrlParts['{$features}'][$position] = $tmpFeatures[$position];
                        }
                        
                        // сортируем значения в соответствии с возможной их последовательностью
                        $currentUrlParts['{$features}'][$paramName]['value'] = array_values(
                            array_intersect(
                                $this->featuresUrls[$paramName],
                                $currentUrlParts['{$features}'][$paramName]['value']
                            )
                        );
                        
                    } else {
                        // Если удалили все значения, удалим и информацию об фильтре
                        unset($currentUrlParts['{$features}'][$paramName]);
                        if (empty($currentUrlParts['{$features}'])) {
                            unset($currentUrlParts['{$features}']);
                        }
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentUrlParts, func_get_args());
    }

    public function buildFilterUrl(array $params) : string
    {
        $urlParts = [];
        foreach ($params as $partKey => $part) {
            if (empty($part)) {
                continue;
            }
            if ($partKey == '{$features}') {
                $featuresParts = [];
                foreach ($part as $featurePart) {
                    $featuresParts[] = $featurePart['key'] . '-' . implode('_', (array)$featurePart['value']);
                }
                $urlParts[$partKey] = implode('/', $featuresParts);
            } else {
                $urlParts[$partKey] = $part['key'] . '-' . implode('_', (array)$part['value']);
            }
        }

        $actualString = strtr($this->filterHelper->getUrlPattern(), $urlParts);
        $actualString = preg_replace('~{\$[^$]*}~', '', $actualString);
        return trim(preg_replace('~/{2,}~', '/', $actualString), '/'); // no ExtenderFacade
    }
    
}