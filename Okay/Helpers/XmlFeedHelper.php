<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\QueryFactory\Select;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ImagesEntity;

/**
 * Class XmlFeedHelper
 * @package Okay\Helpers
 * 
 * Данный хелпер используется как вспомогательный для формирования XML выгрузок
 */

class XmlFeedHelper
{

    /** @var Languages */
    private $languages;
    
    private $siteName;
    private $defaultProductsSeoPattern;
    private $allCategories;
    private $mainCurrency;
    private $allCurrencies;
    
    public function __construct(
        Languages $languages,
        Settings $settings,
        EntityFactory $entityFactory
    ) {
        $this->languages = $languages;
        $this->siteName = $settings->get('site_name');
        $this->defaultProductsSeoPattern = (object)$settings->get('default_products_seo_pattern');
        
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        
        $this->allCategories = $categoriesEntity->find();

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $entityFactory->get(CurrenciesEntity::class);

        $this->mainCurrency  = $currenciesEntity->getMainCurrency();
        $this->allCurrencies = $currenciesEntity->mappedBy('id')->find();
        
    }
    
    /**
     * Метод формирует XML строку, на основе данных из массива.
     * Метод принимает массив, как описание офера. Ключ массива это название тега, а значение - массив с ключами:
     * data - значение, которое нужно вставить в тег (можно текст XML, если там должен быть вложенный тег)
     * tag - если много тегов с одинаковым названием, можно указывать его не в ключе массива, а указать здесь
     *       название тега
     * attributes - ассоциативный массив аттрибутов тега, ключ - название аттрибута, значение - значение аттрибута
     *
     * Например:
     * $result['id']['data'] = 1;
     * $result[] = [
     *      'data' => '2',
     *      'tag' => 'guarantee',
     *      'attributes' => [
     *          'type' => 'shop',
     *      ],
     *  ];
     * сформирует XML:
     * <id>1</id>
     * <guarantee type="shop">2</guarantee>
     *
     * @param array $item описание офера
     * @param string $itemTag название тега в который нужно обернуть сам офер
     * @param array $itemTagAttributes атрибуты для тега офера
     * @return string
     */
    public function compileItem(array $item, $itemTag = null, array $itemTagAttributes = [])
    {
        $xmlProduct = '';
        if (!empty($itemTag)) {

            $itemTagAttributesString = '';
            if (!empty($itemTagAttributes)) {
                foreach ($itemTagAttributes as $attrName => $attrValue) {
                    $itemTagAttributesString .= " {$attrName}=\"{$attrValue}\"";
                }
            }
            
            $xmlProduct .= "<{$itemTag}{$itemTagAttributesString}>" . PHP_EOL;
        }
        foreach ($item as $tag => $value) {
            if (!empty($value['tag'])) {
                $tag = $value['tag'];
            }

            $attributes = '';
            if (!empty($value['attributes'])) {
                foreach ($value['attributes'] as $attrName => $attrValue) {
                    $attributes .= " {$attrName}=\"{$attrValue}\"";
                }
            }

            $xmlProduct .= "<{$tag}{$attributes}>{$value['data']}</{$tag}>" . PHP_EOL;
        }

        if (!empty($itemTag)) {
            $xmlProduct .= "</{$itemTag}>" . PHP_EOL;
        }
        return $xmlProduct;// No ExtenderFacade
    }

    /**
     * Метод добавляет к sql запросу join таблиц свойств и значений, но таким образом, чтобы все значения были
     * склеены в одну колонку результата выборки. Далее через метод self::attachFeatures() можно их распарсить.
     *
     * @param Select $select
     * @return Select
     */
    public function joinFeatures(Select $select)
    {
        $select->cols([
            'GROUP_CONCAT(DISTINCT lf.feature_id, "!-", lf.name SEPARATOR "@|@") AS features_string',
            'GROUP_CONCAT(DISTINCT fv.feature_id, "!-", fv.value SEPARATOR "@|@") AS values_string',
            'GROUP_CONCAT(DISTINCT f.id, "!-", f.auto_name_id SEPARATOR "@|@") AS auto_name_id_string',
            'GROUP_CONCAT(DISTINCT f.id, "!-", f.auto_value_id SEPARATOR "@|@") AS auto_value_id_string',
        ])
            ->leftJoin('__products_features_values pv', 'pv.product_id = p.id')
            ->leftJoin(FeaturesValuesEntity::getTable().' AS  fv', 'pv.value_id = fv.id')
            ->leftJoin(FeaturesValuesEntity::getLangTable().' AS  lfv', 'fv.id = lfv.feature_value_id and lfv.lang_id=' . $this->languages->getLangId())
            ->leftJoin(FeaturesEntity::getTable().' AS  f', 'fv.feature_id = f.id')
            ->leftJoin(FeaturesEntity::getLangTable().' AS  lf', 'fv.feature_id = lf.feature_id and lf.lang_id=' . $this->languages->getLangId());
        
        return $select;// No ExtenderFacade
    }

    /**
     * Метод добавляет к sql запросу join таблицы изображений, но таким образом, чтобы все изображение товара были
     * склеены в одну колонку результата выборки. Далее через метод self::attachProductImages() можно их распарсить.
     * 
     * @param Select $select
     * @return Select
     */
    public function joinImages(Select $select)
    {
        $select->cols([
            'GROUP_CONCAT(DISTINCT i.filename ORDER BY i.position SEPARATOR "@|@") as images_string',
        ])->leftJoin(ImagesEntity::getTable().' AS  i', 'i.product_id = p.id');

        return $select;// No ExtenderFacade
    }

    /**
     * Метод парсит строку со свойствами и их значениями, и складывает в свойство $features объекта $product в виде 
     * массива, ключом которого является id свойства, значение массив с ключами id, name, 'values_string' и values.
     * Чтобы для данного метода были валидные данные нужно обязательно расширить sql запрос методом self::joinFeatures()
     * 
     * Пример результата:
     * $product->features[1] = [
     *      'id' => 1,
     *      'name' => 'Feature name',
     *      'values_string' => 'Val 1, Val 2, ..., Val N',
     *      'values' => [
     *          'Val 1',
     *          'Val 2',
     *          ...
     *          'Val N',
     *      ],
     * ]
     *
     * @param object $product строка выборки из базы данных
     * @return object
     */
    public function attachFeatures($product)
    {
        if (!empty($product->features_string) && !empty($product->values_string)) {
            $features = explode('@|@', $product->features_string);
            $values = [];
            foreach (explode('@|@', $product->values_string) as $value) {
                list($featureId, $val) = explode('!-', $value, 2);
                $values[$featureId][] = $val;
            }
            $autoNameIds = [];
            foreach (explode('@|@', $product->auto_name_id_string) as $autoNameId) {
                list($featureId, $val) = explode('!-', $autoNameId, 2);
                if (!empty($val)) {
                    $autoNameIds[$featureId] = $val;
                }
            }
            
            $autoValueIds = [];
            foreach (explode('@|@', $product->auto_value_id_string) as $autoValueId) {
                list($featureId, $val) = explode('!-', $autoValueId, 2);
                if (!empty($val)) {
                    $autoValueIds[$featureId] = $val;
                }
            }
            
            foreach ($features as $feature) {
                list($featureId, $featureName) = explode('!-', $feature, 2);
                
                if (isset($values[$featureId])) {
                    $product->features[$featureId] = [
                        'id' => $featureId,
                        'name' => $featureName,
                        'values' => $values[$featureId],
                        'values_string' => implode(', ', $values[$featureId]),
                    ];
                }

                if (isset($autoNameIds[$featureId])) {
                    $product->features[$featureId]['auto_name_id'] = $autoNameIds[$featureId];
                }

                if (isset($autoValueIds[$featureId])) {
                    $product->features[$featureId]['auto_value_id'] = $autoValueIds[$featureId];
                }
            }
        }

        return $product; // No ExtenderFacade
    }

    public function attachDescriptionByTemplate($product)
    {
        
        if (empty($product->description)) {
            
            if (!empty($product->main_category_id)) {

                $category = $this->allCategories[$product->main_category_id];
                
                if ($data = $this->getCategoryField($category, 'auto_description')) {
                    $descriptionTemplate = $data;
                } elseif (!empty($this->defaultProductsSeoPattern->auto_description)) {
                    $descriptionTemplate = $this->defaultProductsSeoPattern->auto_description;
                }

                if (!empty($descriptionTemplate)) {
                    $metaData = strtr($descriptionTemplate, $this->getMetadataParts($product));
                    $product->description = trim(preg_replace('/{\$[^$]*}/', '', $metaData));
                }
            }
        }
        
        return $product; // No ExtenderFacade
    }
    
    protected function getMetadataParts($product)
    {

        $price = round($product->price, 2);
        $comparePrice = null;
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
        
        $mataDataParts = [
            '{$brand}'         => $product->vendor,
            '{$product}'       => $product->product_name,
            '{$price}'         => $price . ' ' . $this->mainCurrency->sign,
            '{$compare_price}' => ($comparePrice != null ? $comparePrice . ' ' . $this->mainCurrency->sign : ''),
            '{$sku}'           => $product->sku,
            '{$sitename}'      => $this->siteName,
        ];

        if (!empty($product->main_category_id) && isset($this->allCategories[$product->main_category_id])) {
            $category = $this->allCategories[$product->main_category_id];
            $mataDataParts['{$category}'] = ($category->name ? $category->name : '');
            $mataDataParts['{$category_h1}'] = ($category->name_h1 ? $category->name_h1 : '');
        }
        
        if (!empty($product->features)) {
            foreach ($product->features as $feature) {
                
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

    protected function getCategoryField($category, $fieldName)
    {
        if (empty($category)) {
            return false;
        }

        $categoryPath = array_reverse($category->path);
        
        foreach ($categoryPath as $c) {
            if (!empty($c->{$fieldName})) {
                return $c->{$fieldName};
            }
        }
        return false;
    }
    
    /**
     * Метод парсит строку с изображениями, и складывает их в виде массива filename в свойстве images.
     * Чтобы для данного метода были валидные данные нужно обязательно расширить sql запрос методом self::joinImages()
     *
     * @param object $product строка выборки из базы данных
     * @return object
     */
    public function attachProductImages($product)
    {
        if (!empty($product->images_string)) {
            $images = explode('@|@', $product->images_string);
            $product->images = array_slice($images, 0, 10);
        }

        return $product; // No ExtenderFacade
    }

    public function escape($str)
    {
        return htmlspecialchars(strip_tags($str)); // no ExtenderFacade
    }

    /**
     * Метод возвращает один общий массив ID категорий включая дочерние, по ID родителей
     * 
     * @param array $categoriesIds массив ID категорий для которых нужно собрать всех деток
     * @return array
     * @throws \Exception
     */
    public function addAllChildrenToList(array $categoriesIds)
    {
        $SL = ServiceLocator::getInstance();
        /** @var EntityFactory $ef */
        $ef = $SL->getService(EntityFactory::class);

        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $ef->get(CategoriesEntity::class);
        
        $uploadCategories = [];
        foreach ($categoriesIds as $cId) {
            $category = $categoriesEntity->get((int)$cId);
            if (!empty($category)) {
                $uploadCategories = array_merge($uploadCategories, $category->children);
            }
        }
        return $uploadCategories; // no ExtenderFacade
    }
    
}