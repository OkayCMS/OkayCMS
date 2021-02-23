<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Import;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory;
use Okay\Core\Translit;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;

class BackendImportHelper
{
    
    private $import;
    private $queryFactory;
    private $languages;
    private $entityFactory;
    private $imageCore;
    
    public function __construct(
        Import $import,
        QueryFactory $queryFactory,
        Languages $languages,
        EntityFactory $entityFactory,
        Image $imageCore
    ) {
        $this->import = $import;
        $this->queryFactory = $queryFactory;
        $this->languages = $languages;
        $this->entityFactory = $entityFactory;
        $this->imageCore = $imageCore;
    }
    
    // Импорт одного товара $item[column_name] = value;
    public function importItem($item)
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entityFactory->get(VariantsEntity::class);

        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        
        $importedItem = new \stdClass();

        // Проверим не пустое ли название и артинкул (должно быть хоть что-то из них)
        if (empty($item['sku']) && empty($item['name'])) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        // Подготовим товар для добавления в базу
        $product = $this->parseProductData($item);
        
        // Если задан бренд
        if (!empty($item['brand'])) {
            $item['brand'] = trim($item['brand']);
            // Найдем его по имени
            $brandId = $this->searchBrand($item['brand']);

            if (!$product['brand_id'] = $brandId) {

                /** @var BrandsEntity $brandsEntity */
                $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
                
                // Создадим, если не найден
                $brand = $this->prepareAddBrand([
                    'name'             => $item['brand'],
                    'meta_title'       => $item['brand'],
                    'meta_keywords'    => $item['brand'],
                    'meta_description' => $item['brand'],
                ]);
                $product['brand_id'] = $brandsEntity->add($brand);
            }
        }

        // Если задана категория
        $categoryId = null;
        $categoriesIds = [];
        if (!empty($item['category'])) {
            foreach (explode($this->import->getCategoryDelimiter(), $item['category']) as $c) {
                $categoriesIds[] = $this->importCategory($c);
            }
            $categoryId = reset($categoriesIds);
        }

        // Подготовим вариант товара
        $variant = $this->parseVariantData($item);
        
        // Сразу позволим модулям определить товар по своей логике
        $importItemData = $this->preSearchImportProductData($product, $variant);
        
        // Если же товар не был найден модулями, ищем по стандартной логике. Так же этот метод можно расширить экстендером
        $importItemData = $this->searchImportProductData($product, $variant, $importItemData);

        if ($importItemData['productId']) {
            $productId = (int)$importItemData['productId'];
        }
        if ($importItemData['variantId']) {
            $variantId = (int)$importItemData['variantId'];
        }

        $importedItem->status = $importItemData['status'];

        if (isset($importedItem->status)) {
            if (!empty($product)) {
                $current_url = '';
                if (!empty($productId)){
                    $select = $this->queryFactory->newSelect();
                    $current_url = $select->cols(['url'])
                        ->from('__products')
                        ->where('id=:id')
                        ->limit(1)
                        ->bindValue('id', $productId)
                        ->result('url');
                }
                if (!isset($product['url']) && !empty($product['name']) && empty($current_url)) {
                    $product['url'] = Translit::translit($product['name']);
                }
                
                if (empty($productId)) {
                    $preparedProduct = $this->prepareAddProduct($product);
                    $productId = $productsEntity->add($preparedProduct);
                } else {
                    $preparedProduct = $this->prepareUpdateProduct($product);
                    $productsEntity->update($productId, $preparedProduct);
                }
            }
            
            if (empty($variantId) && !empty($productId)) {
                $select = $this->queryFactory->newSelect();
                $pos = $select->cols(['MAX(v.position) as pos'])
                    ->from('__variants v')
                    ->where('v.product_id=:product_id')
                    ->limit(1)
                    ->bindValue('product_id', $productId)
                    ->result('pos');

                $variant['position'] = $pos+1;
                $variant['product_id'] = $productId;
                if (!isset($variant['currency_id'])) {
                    /** @var CurrenciesEntity $currenciesEntity */
                    $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
                    
                    $currency = $currenciesEntity->getMainCurrency();
                    $variant['currency_id'] = $currency->id;
                }

                $preparedVariant = $this->prepareAddVariant($variant);
                $variantId = $variantsEntity->add($preparedVariant);
            } elseif (!empty($variantId) && !empty($variant)) {
                $preparedVariant = $this->prepareUpdateVariant($variant);
                $variantsEntity->update($variantId, $preparedVariant);
            }
        }

        if(!empty($variantId) && !empty($productId)) {
            // Нужно вернуть обновленный товар
            $importedItem->variant = $variantsEntity->findOne(['id' => $variantId]);
            $importedItem->product = $productsEntity->findOne(['id' => $productId]);

            // Добавляем категории к товару
            $select = $this->queryFactory->newSelect();
            $pos = $select->cols(['MAX(position) as pos'])
                ->from('__products_categories')
                ->where('product_id=:product_id')
                ->bindValue('product_id', $productId)
                ->result('pos');

            $pos = $pos === false ? 0 : $pos + 1;
            if (!empty($categoriesIds)) {
                foreach ($categoriesIds as $cId) {
                    $categoriesEntity->addProductCategory($productId, $cId, $pos++);
                }
            }

            // Изображения товаров
            $imagesIds = [];
            if (isset($item['images'])) {
                $imagesIds = $this->importImages($productId, $item['images']);
            }

            $mainInfo = [];
            $mainImage = reset($imagesIds);
            if (!empty($mainImage)) {
                $mainInfo['main_image_id'] = $mainImage;
            }

            if (!empty($categoryId)) {
                $mainInfo['main_category_id'] = $categoryId;
            }

            if (!empty($mainInfo)) {
                $productsEntity->update($productId, $mainInfo);
            }

            // Характеристики товара
            $features = [];
            foreach ($item as $featureName => $featureValue) {
                if ($this->isFeature($featureName)) {
                    $features[$featureName] = $featureValue;
                }
            }

            if (!empty($features)) {
                $this->addFeatures($features, $productId, $categoryId);
            }

            $this->afterImportProductProcedure($importedItem->product, $importedItem->variant, $categoriesIds);
            
            return ExtenderFacade::execute(__METHOD__, $importedItem, func_get_args());
        }
        
        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }

    /**
     * Метод нужен чтобы модули могли вносить свои изменения после импорта
     * 
     * @param $product
     * @param $variant
     * @param $categoriesIds
     */
    private function afterImportProductProcedure($product, $variant, $categoriesIds)
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    private function parseVariantData($itemFromCsv)
    {
        $variant = [];

        if (isset($itemFromCsv['variant'])) {
            $variant['name'] = trim($itemFromCsv['variant']);
        }

        if (isset($itemFromCsv['price']) && !empty($itemFromCsv['price'])) {
            $variant['price'] = str_replace(',', '.', str_replace(' ', '', trim($itemFromCsv['price'])));
        }

        if (isset($itemFromCsv['compare_price']) && !empty($itemFromCsv['compare_price'])) {
            $variant['compare_price'] = str_replace(',', '.', str_replace(' ', '', trim($itemFromCsv['compare_price'])));
        }

        if (isset($itemFromCsv['stock'])) {
            if ($itemFromCsv['stock'] == '') {
                $variant['stock'] = null;
            } else {
                $variant['stock'] = trim($itemFromCsv['stock']);
            }
        }

        if (isset($itemFromCsv['sku'])) {
            $variant['sku'] = trim($itemFromCsv['sku']);
        }

        if (isset($itemFromCsv['currency'])) {
            $variant['currency_id'] = intval($itemFromCsv['currency']);
        }
        if (isset($itemFromCsv['weight'])) {
            $variant['weight'] = (float)str_replace(',', '.', str_replace(' ', '', trim($itemFromCsv['weight'])));
        }

        if (isset($itemFromCsv['units'])) {
            $variant['units'] = $itemFromCsv['units'];
        }
        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }

    private function parseProductData($itemFromCsv)
    {
        $product = [];

        if (isset($itemFromCsv['name'])) {
            $product['name'] = trim($itemFromCsv['name']);
        }

        if (isset($itemFromCsv['meta_title'])) {
            $product['meta_title'] = trim($itemFromCsv['meta_title']);
        }

        if (isset($itemFromCsv['meta_keywords'])) {
            $product['meta_keywords'] = trim($itemFromCsv['meta_keywords']);
        }

        if (isset($itemFromCsv['meta_description'])) {
            $product['meta_description'] = trim($itemFromCsv['meta_description']);
        }

        if (isset($itemFromCsv['annotation'])) {
            $product['annotation'] = trim($itemFromCsv['annotation']);
        }

        if (isset($itemFromCsv['description'])) {
            $product['description'] = trim($itemFromCsv['description']);
        }

        if (isset($itemFromCsv['visible'])) {
            $product['visible'] = intval($itemFromCsv['visible']);
        }

        if (isset($itemFromCsv['featured'])) {
            $product['featured'] = intval($itemFromCsv['featured']);
        }

        if (!empty($itemFromCsv['url'])) {
            $product['url'] = Translit::translit(trim($itemFromCsv['url']));
        } elseif (!empty($itemFromCsv['name'])) {
            $product['url'] = Translit::translit(trim($itemFromCsv['name']));
        }
        if (!empty($product['url'])) {
            $product['url'] = str_replace('.', '', $product['url']);
        }

        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }
    
    private function addFeatures($features, $productId, $categoryId)
    {
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);
        
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
        
        $featuresNames   = [];
        $featuresValues  = [];
        $valuesTranslits = [];
        $valuesIds       = [];

        foreach ($features as $featureName => $featureValue) {
            if ($featureValue === '' || empty($categoryId)) {
                continue;
            }

            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("SELECT f.id FROM __features f WHERE f.name=:feature_name LIMIT 1");
            $sql->bindValue('feature_name', $featureName);
            $featureId = $sql->result('id');

            if (empty($featureId)) {
                $featureId = $featuresEntity->add(['name' => $featureName]);
            }

            $featuresNames[$featureId]  = $featureName;
            $featuresValues[$featureId] = explode($this->import->getValuesDelimiter(), $featureValue);

            foreach ($featuresValues[$featureId] as $value) {
                $valuesTranslits[] = Translit::translitAlpha($value);
            }
        }

        if (empty($featuresNames)) {
            return;
        }

        foreach ($featuresValuesEntity->find(['feature_id' => array_keys($featuresNames), 'translit' => $valuesTranslits]) as $value) {
            $valuesIds[$value->feature_id][$value->translit] = $value->id;
        }

        $featuresValuesEntity->deleteProductValue($productId, null, array_keys($featuresNames));

        $valuesTransaction = "INSERT IGNORE INTO `__products_features_values` (`product_id`, `value_id`) VALUES ";
        $sqlValues = [];

        foreach ($featuresNames as $featureId => $featureName) {
            $featuresEntity->addFeatureCategory($featureId, $categoryId);

            $values = $featuresValues[$featureId];

            foreach ($values as $value) {
                $valueId = null;
                $translit = Translit::translitAlpha($value);

                // Ищем значение с таким транслитом
                if (isset($valuesIds[$featureId][$translit])) {
                    $valueId = $valuesIds[$featureId][$translit];
                }

                // Если нет, тогда добавим значение
                if (empty($valueId)) {
                    $featureValue = new \stdClass();
                    $featureValue->value = trim($value);
                    $featureValue->feature_id = $featureId;
                    $featureValue->translit = Translit::translitAlpha($value);

                    $valueId = $featuresValuesEntity->add($featureValue);
                }

                if (!empty($valueId)) {
                    $sqlValues[] = "('$productId', '$valueId')";
                }
            }
        }

        if (empty($sqlValues)) {
            return;
        }

        $valuesTransaction .= implode(", ", $sqlValues);

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement($valuesTransaction)->execute();
    }
    
    private function importImages($productId, $itemImages)
    {
        /** @var ImagesEntity $imagesEntity */
        $imagesEntity = $this->entityFactory->get(ImagesEntity::class);
        $imagesIds = [];
        if (!empty($itemImages)) {
            // Изображений может быть несколько, через запятую
            $images = explode(',', $itemImages);
            foreach ($images as $image) {
                $image = trim($image);
                if (!empty($image)) {
                    // Имя файла
                    $imageFilename = pathinfo($image, PATHINFO_BASENAME);

                    if (preg_match("~^https?://~", $image)) {
                        $imageFilename = $this->imageCore->correctFilename($imageFilename);
                        $image = rawurlencode($image);
                    }

                    // Добавляем изображение только если такого еще нет в этом товаре
                    $select = $this->queryFactory->newSelect();
                    $result = $select->cols(['id', 'filename'])
                        ->from('__images')
                        ->where('product_id=:product_id')
                        ->where('(filename=:image_filename OR filename=:image)')
                        ->limit(1)
                        ->bindValue('product_id', $productId)
                        ->bindValue('image_filename', $imageFilename)
                        ->bindValue('image', $image)
                        ->result();

                    if (empty($result->filename)) {
                        $newImage = new \stdClass();
                        $newImage->product_id = $productId;
                        $newImage->filename = $image;
                        $imagesIds[] = $imagesEntity->add($newImage);
                    } else {
                        $imagesIds[] = $result->id;
                    }
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, $imagesIds, func_get_args());
    }
    
    private function isFeature($importColumnName)
    {
        if (!in_array($importColumnName, $this->import->getInternalColumnsNames()) && !in_array($importColumnName, $this->getModulesColumnsNames())) {
            return true;
        }

        return false;
    }

    // Отдельная функция для импорта категории
    private function importCategory($category)
    {
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);
        
        // Поле "категория" может состоять из нескольких имен, разделенных subcategory_delimiter-ом
        // Только неэкранированный subcategory_delimiter может разделять категории
        $delimiter = $this->import->getSubcategoryDelimiter();
        $regex = "/\\DELIMITER((?:[^\\\\\DELIMITER]|\\\\.)*)/";
        $regex = str_replace('DELIMITER', $delimiter, $regex);
        $names = preg_split($regex, $category, 0, PREG_SPLIT_DELIM_CAPTURE);
        $id = null;
        $parent = 0;

        // Для каждой категории
        foreach ($names as $name) {
            // Заменяем \/ на /
            $name = trim(str_replace("\\$delimiter", $delimiter, $name));
            if(!empty($name)) {
                // Найдем категорию по имени
                $select = $this->queryFactory->newSelect();
                $id = $select->cols(['id'])
                    ->from(CategoriesEntity::getTable())
                    ->where('name=:name')
                    ->where('parent_id=:parent')
                    ->bindValue('name', $name)
                    ->bindValue('parent', $parent)->result('id');

                // Если не найдена - добавим ее
                if (empty($id)) {
                    $preparedCategory = $this->prepareAddCategory([
                        'name'             => $name,
                        'parent_id'        => $parent,
                        'meta_title'       => $name,
                        'meta_keywords'    => $name,
                        'meta_description' => $name,
                        'url'              => Translit::translit($name),
                    ]);
                    $id = $categoriesEntity->add($preparedCategory);
                }

                $parent = $id;
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $id, func_get_args());
    }
    
    private function prepareAddVariant($variant)
    {
        $variant = (array)$variant;
        // нужно хотяб одно поле из переводов
        $fields = VariantsEntity::getFields();
        $tm = array_intersect(array_keys($variant), $fields);
        if (empty($tm) && !empty($fields)) {
            $variant[$fields[0]] = "";
        }

        // Чтобы не ругалось что поле не может быть NULL
        if (!isset($variant['price'])) {
            $variant['price'] = 0;
        }
        if (!isset($variant['compare_price'])) {
            $variant['compare_price'] = 0;
        }
        
        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }
    
    private function prepareUpdateVariant($variant)
    {
        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }
    
    private function prepareUpdateProduct($variant)
    {
        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }
    
    private function prepareAddProduct($product)
    {
        // Если для нового товара не заданы метаданные, запишем туда название товара
        if (!isset($product['meta_title']) || empty($product['meta_title'])) {
            $product['meta_title'] = $product['name'];
        }

        if (!isset($product['meta_keywords']) || empty($product['meta_keywords'])) {
            $product['meta_keywords'] = $product['name'];
        }

        if (!isset($product['meta_description']) || empty($product['meta_description'])) {
            $product['meta_description'] = $product['name'];
        }
        
        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }
    
    private function prepareAddBrand($brand)
    {
        return ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
    }

    private function searchBrand($brandName)
    {
        if ($this->languages->getLangId()) {
            $select = $this->queryFactory->newSelect();
            $brandId = $select->cols(['brand_id as id'])
                ->from('__lang_brands')
                ->where('name=:name')
                ->where('lang_id=:lang_id')
                ->bindValue('name', $brandName)
                ->bindValue('lang_id', $this->languages->getLangId())
                ->result('id');
        } else {
            $select = $this->queryFactory->newSelect();
            $brandId = $select->cols(['id'])
                ->from('__brands')
                ->where('name=:name')
                ->bindValue('name', $brandName)
                ->result('id');
        }
        return ExtenderFacade::execute(__METHOD__, $brandId, func_get_args());
    }

    /**
     * Данный метод может использоваться для определения товара ПЕРЕД стандартной логикой определения товара.
     * Может быть полезно, если нужно изменить принцип определения товара при импорте.
     * Чтобы определить товар, нужно вернуть результат в виде массива $itemData. См. описание параметров массива
     * в методе self::searchImportProductData().
     * 
     * @param $product
     * @param $variant
     * @return array
     */
    private function preSearchImportProductData($product, $variant)
    {
        $itemData = [
            'productId' => null,
            'variantId' => null,
            'status' => 'added',
            'determinedBy' => null,
        ];
        
        return ExtenderFacade::execute(__METHOD__, $itemData, func_get_args());
    }

    /**
     * Метод определяет ищет товар по входящим данным. По умолчанию это sku, productName или variantName 
     * (могут быть в сочетаниях). Если метод определил товар, он возвращает массив результата.
     * 
     * Параметры результата:
     * productId - id товара, который будем обновлять
     * variantId - id варианта, который будем обновлять. Если не указан, а есть только productId, вариант добавится этому товару
     * status - статус поиска (added|updated)
     * determinedBy - описание, по каким критериям найден товар. Данный параметр может использоваться модулями,
     * которые расширяют импорт, и им важно знать по каким критериям был найден товар/вариант.
     * Возможные значения по умолчанию: skuAndVariantName, sku, productName, productNameAndVariantName
     * 
     * @param $product
     * @param $variant
     * @param array $itemData
     * @return array
     */
    private function searchImportProductData($product, $variant, $itemData = [])
    {
        
        if (!empty($itemData['productId']) || !empty($itemData['variantId'])) {
            return ExtenderFacade::execute(__METHOD__, $itemData, func_get_args());
        }

        $status = 'added';
        $determinedBy = null;
        
        // Если задан артикул варианта, найдем этот вариант и соответствующий товар
        if (!empty($variant['sku'])) {
            $select = $this->queryFactory->newSelect();
            $select->cols(['v.id as variant_id', 'v.product_id'])
                ->from('__variants v')
                ->where('v.sku=:sku')
                ->bindValue('sku', $variant['sku']);

            if (!empty($variant['name'])) {
                $select->where('v.name=:name')
                    ->bindValue('name', $variant['name']);
            }

            if ($result = $select->result()) {
                $productId = (int)$result->product_id;
                $variantId = (int)$result->variant_id;

                if (!empty($variant['name'])) {
                    $determinedBy = 'skuAndVariantName';
                } else {
                    $determinedBy = 'sku';
                }
                
                $status = 'updated';
            } elseif (!empty($product['name'])) {
                $select = $this->queryFactory->newSelect();
                $select->cols(['p.id as product_id'])
                    ->from('__products p')
                    ->where('p.name=:name')
                    ->limit(1)
                    ->bindValue('name', $product['name']);
                
                if ($result = $select->result()) {
                    $productId = (int)$result->product_id;
                    $status = 'added';
                    $determinedBy = 'productName';
                } else {
                    $status = 'added';
                }
            }
        } else {
            // если нет артикула попробуем по названию
            $select = $this->queryFactory->newSelect();
            $select->cols(['v.id as variant_id', 'p.id as product_id'])
                ->from('__products p')
                ->join('LEFT', '__variants v', 'v.product_id=p.id')
                ->where('p.name=:p_name')
                ->bindValue('p_name', $product['name']);

            if (!empty($variant['name'])) {
                $select->where('v.name=:v_name')
                    ->bindValue('v_name', $variant['name']);
            }

            $result = $select->result();

            if ($result) {
                $productId = (int)$result->product_id;
                $variantId = (int)$result->variant_id;
                if (empty($variantId)) {
                    $status = 'added';
                } else {
                    $status = 'updated';
                    if (!empty($variant['name'])) {
                        $determinedBy = 'productNameAndVariantName';
                    } else {
                        $determinedBy = 'productName';
                    }
                    
                    //unset($variant['sku']);
                }
            } else {
                $status = 'added';
            }
        }
        $itemData = [];
        if (isset($productId)){
            $itemData['productId'] =  $productId;
        }
        if (isset($variantId)){
            $itemData['variantId'] =  $variantId;
        }
        $itemData['status'] =  $status;
        $itemData['determinedBy'] =  $determinedBy;
        
        return ExtenderFacade::execute(__METHOD__, $itemData, func_get_args());
    }
    
    private function prepareAddCategory($category)
    {
        return ExtenderFacade::execute(__METHOD__, $category, func_get_args());
    }

    public function getModulesColumnsNames()
    {
        $modulesColumnsNames = [];
        return ExtenderFacade::execute(__METHOD__, $modulesColumnsNames, func_get_args());
    }
}