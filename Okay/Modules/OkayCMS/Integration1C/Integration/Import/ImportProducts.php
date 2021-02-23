<?php

namespace Okay\Modules\OkayCMS\Integration1C\Integration\Import;


use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;

class ImportProducts extends AbstractImport
{

    /**
     * @param string $xmlFile Full path to xml file
     * @return string
     */
    public function import($xmlFile) {

        // Категории и свойства (только в первом запросе пакетной передачи)
        if (empty($this->integration1C->getFromStorage('imported_product_num'))) {
            $z = new \XMLReader;
            $z->open($xmlFile);
            while ($z->read() && $z->name !== 'Классификатор');
            if ($z->name == 'Классификатор') {
                $xml = new \SimpleXMLElement($z->readOuterXML());
                $z->close();
                $this->importCategories($xml);
                $this->importFeatures($xml);
                $this->importUnits($xml);
            }
        }

        // Товары
        $z = new \XMLReader;
        $z->open($xmlFile);

        while ($z->read() && $z->name !== 'Товар');

        // Последний товар, на котором остановились
        $lastProductNum = 0;
        if (!empty($this->integration1C->getFromStorage('imported_product_num'))) {
            $lastProductNum = $this->integration1C->getFromStorage('imported_product_num');
        }

        // Номер текущего товара
        $currentProductNum = 0;

        while ($z->name === 'Товар') {
            if ($currentProductNum >= $lastProductNum) {
                $xml = new \SimpleXMLElement($z->readOuterXML());

                // Товары
                $this->importProduct($xml);

                $execTime = microtime(true) - $this->integration1C->startTime;
                if ($execTime+1 >= $this->integration1C->maxExecTime) {

                    // Запоминаем на каком товаре остановились
                    $this->integration1C->setToStorage('imported_product_num', $currentProductNum);
                    
                    $result =  "progress\n";
                    $result .=  "Выгружено товаров: $currentProductNum\n";
                    return $result;
                }
            }
            $currentProductNum ++;
            $z->next('Товар');
        }
        $z->close();
        
        $this->integration1C->setToStorage('imported_product_num', '');
        return "success\n";
    }

    /**
     * @param $xml \SimpleXMLElement()
     */
    protected function importUnits($xml)
    {
        if (isset($xml->ЕдиницыИзмерения->ЕдиницаИзмерения)) {
            foreach ($xml->ЕдиницыИзмерения->ЕдиницаИзмерения as $xmlGroup) {
                $param = "units_".strval($xmlGroup->Код);
                
                if (!$unit = (string)$xmlGroup->НаименованиеКраткое) {
                    $unit = (string)$xmlGroup->НаименованиеПолное;
                }
                $this->integration1C->setToStorage($param, $unit);
            }
        }
    }

    /**
     * @param $xml \SimpleXMLElement()
     * @param int $parentId
     */
    protected function importCategories($xml, $parentId = 0)
    {
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->integration1C->entityFactory->get(CategoriesEntity::class);
        
        if (isset($xml->Группы->Группа)) {
            foreach ($xml->Группы->Группа as $xmlGroup) {
                $select = $this->integration1C->queryFactory->newSelect();
                $select->cols(['id'])
                    ->from('__categories')
                    ->where('external_id=:external_id')
                    ->bindValue('external_id', (string)$xmlGroup->Ид);
                
                $this->integration1C->db->query($select);
                $categoryId = $this->integration1C->db->result('id');
                $name = (string)$xmlGroup->Наименование;
                if (empty($categoryId)) {
                    $url = $this->integration1C->translit->translit($name);
                    $url = str_replace('.', '', $url);
                    
                    $categoryId = $categoriesEntity->add([
                        'parent_id' => $parentId,
                        'external_id' => (string)$xmlGroup->Ид,
                        'url' => $url,
                        'name' => $name,
                        'meta_title' => $name,
                        'meta_keywords' => $name,
                        'meta_description' => $name,
                    ]);
                } else {
                    //Постоянное обновление категорий, проверка на предмет переименования родителськой категории            
                    $categoriesEntity->update($categoryId, [
                        'parent_id' => $parentId,
                        'name' => $name,
                    ]);
                }
                
                $param = "categories_".strval($xmlGroup->Ид);
                $this->integration1C->setToStorage($param, $categoryId);
                $this->importCategories($xmlGroup, $categoryId);
            }
        }
    }

    /**
     * @param $xml \SimpleXMLElement()
     * Метод импортирует свойства со справочника
     */
    protected function importFeatures($xml)
    {

        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->integration1C->entityFactory->get(FeaturesEntity::class);
        
        $property = [];
        if (isset($xml->Свойства->СвойствоНоменклатуры)) {
            $property = $xml->Свойства->СвойствоНоменклатуры;
        }

        if (isset($xml->Свойства->Свойство)) {
            $property = $xml->Свойства->Свойство;
        }

        foreach ($property as $xmlFeature) {
            // Если свойство содержит производителя товаров
            if ((string)$xmlFeature->Наименование == $this->integration1C->brandOptionName) {
                // Запомним в сессии Ид свойства с производителем
                $this->integration1C->setToStorage('brand_option_id', strval($xmlFeature->Ид));
               
                if ($xmlFeature->ТипЗначений == 'Справочник') {
                    foreach ($xmlFeature->ВариантыЗначений->Справочник as $val) {
                        $param = "brand_option_id_".strval($xmlFeature->Ид)."_".strval($val->ИдЗначения);
                        $this->integration1C->setToStorage($param, strval($val->Значение));
                    }
                } else {
                    $this->integration1C->setToStorage('brand_option_id', strval($xmlFeature->Ид));
                }
            } else {
                // Иначе обрабатываем как обычной свойство товара
                
                // Проверяем существует ли свойство не по наименованию, а по коду 1С
                $select = $this->integration1C->queryFactory->newSelect();
                $select->cols(['id'])
                    ->from('__features')
                    ->where('external_id=:external_id')
                    ->bindValue('external_id', (string)$xmlFeature->Ид);

                $this->integration1C->db->query($select);
                $featureId = $this->integration1C->db->result('id');
                // По умолчанию свойство АКТИВИРУЕМ для фильтра
                if (empty($featureId)) {
                    // Добавляем свойство и Код 1С
                    $featureId = $featuresEntity->add([
                        'name' => strval($xmlFeature->Наименование),
                        'external_id' => strval($xmlFeature->Ид),
                        'in_filter' => 1,
                    ]);
                } else {
                    $featuresEntity->update($featureId, [
                        'name' => strval($xmlFeature->Наименование),
                    ]);
                }

                $param = "features_".strval($xmlFeature->Ид);
                $this->integration1C->setToStorage($param, $featureId);
                
                // Разбираем значения свойств
                if ($xmlFeature->ТипЗначений == 'Справочник') {
                    foreach ($xmlFeature->ВариантыЗначений->Справочник as $val) {
                        $value = (string)$val->Значение;
                        $valueId = $this->getFeatureValueId($featureId, $value);
                        
                        $param = "features_values_".$featureId."_".strval($val->ИдЗначения);
                        $this->integration1C->setToStorage($param, $valueId);
                    }
                }
            }
        }
    }
    
    /**
     * @param $xmlProduct \SimpleXMLElement()
     */
    protected function importProduct($xmlProduct)
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->integration1C->entityFactory->get(ProductsEntity::class);
        
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->integration1C->entityFactory->get(VariantsEntity::class);
        
        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $this->integration1C->entityFactory->get(CategoriesEntity::class);
        
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->integration1C->entityFactory->get(BrandsEntity::class);
        
        $xmlProduct->Наименование = trim($xmlProduct->Наименование);

        //  Id товара и варианта (если есть) по 1С
        @list($product1cId, $variant1cId) = explode('#', $xmlProduct->Ид);
        if (empty($variant1cId)) {
            $variant1cId = '';
        }

        $properties = [];
        if ($xmlProduct->ЗначенияРеквизитов->ЗначениеРеквизита) {
            foreach ($xmlProduct->ЗначенияРеквизитов->ЗначениеРеквизита as $property) {
                $properties[(string)$property->Наименование] = (string)$property->Значение;
            }
        }
        
        // Не будем парсить все, что не товар (чтобы исключить услуги типа "Доставка" и подобные...)
        if ($this->integration1C->importProductsOnly === true && isset($properties['ТипНоменклатуры']) && $properties['ТипНоменклатуры'] != 'Товар') {
            return;
        }
        
        $productsCategoriesIds = [];
        if (isset($xmlProduct->Группы->Ид)) {
            foreach ($xmlProduct->Группы->Ид as $cat_id) {
                $param = "categories_".strval($cat_id);
                $productsCategoriesIds[] = $this->integration1C->getFromStorage($param);
            }
        }

        // Подгатавливаем вариант
        $variantId = null;
        $variant = new \stdClass;
        $values = [];
        if (isset($xmlProduct->ХарактеристикиТовара->ХарактеристикаТовара)) {
            foreach ($xmlProduct->ХарактеристикиТовара->ХарактеристикаТовара as $xml_property) {
                $values[] = $xml_property->Значение;
            }
        }
        
        if (!empty($values)) {
            $variant->name = implode(', ', $values);
        } else {
            // Нет вариантов товара поэтому сделаем пустым
            $variant->name = '';
        }
        $variant->sku = (string)$xmlProduct->Артикул;
        $variant->external_id = $variant1cId;

        // Ищем товар по внешнему id
        $select = $this->integration1C->queryFactory->newSelect();
        $select->cols(['id'])
            ->from('__products')
            ->where('external_id=:external_id')
            ->bindValue('external_id', $product1cId);

        $this->integration1C->db->query($select);
        $productId = $this->integration1C->db->result('id');
        
        // если не нашли, ищем по артикулу
        if (empty($productId) && !empty($variant->sku)) {
            $select = $this->integration1C->queryFactory->newSelect();
            $select->cols([
                'id',
                'product_id',
            ])->from('__variants')
                ->where('sku=:sku')
                ->bindValue('sku', $variant->sku);

            $this->integration1C->db->query($select);
            $res = $this->integration1C->db->result();
            if (!empty($res)) {
                $productId = $res->product_id;
                $variantId = $res->id;
            }
        } elseif (!empty($productId)) {
            $select = $this->integration1C->queryFactory->newSelect();
            $select->cols(['id'])
                ->from('__variants')
                ->where('product_id=:product_id')
                ->bindValue('product_id', $productId);
            
            if (!empty($variant1cId)) {
                $select->where('external_id=:external_id')
                    ->bindValue('external_id', $variant1cId);
            }

            $this->integration1C->db->query($select);
            $variantId = $this->integration1C->db->result('id');
        }
        
        // Если нужно - удаляем вариант или весь товар
        $attributes = $xmlProduct->attributes();
        if ((string)$xmlProduct->Статус == 'Удален' || (string)$attributes['Статус'] == 'Удален') {
            if ($productId !== null && $variantId !== null) {
                $variantsEntity->delete($variantId);
                if ($variantsEntity->count(['product_id'=>$productId]) == 0) {
                    $productsEntity->delete($productId);
                }
            }
            return;
        }
        
        // Если такого товара не нашлось
        if (empty($productId)) {
            // Добавляем товар
            $description = '';
            if (!empty($xmlProduct->Описание)) {
                $description = (string)$xmlProduct->Описание;
            }

            $url = $this->integration1C->translit->translit((string)$xmlProduct->Наименование);
            $url = str_replace('.', '', $url);

            // Делаем урлы уникальными
            while ($url = $productsEntity->col('url')->findOne(['url' => $url])) {
                if (preg_match('/(.+)?_([0-9]+)$/', $url, $parts)) {
                    $url = $parts[1].'_'.($parts[2]+1);
                } else {
                    $url .= '_1';
                }
            }
            
            $productId = $productsEntity->add([
                'external_id' => $product1cId,
                'url' => $url,
                'name' => (string)$xmlProduct->Наименование,
                'meta_title' => (string)$xmlProduct->Наименование,
                'meta_keywords' => (string)$xmlProduct->Наименование,
                'meta_description' => (string)$xmlProduct->Наименование,
                'annotation' => $description,
                'description' => $description,
                'visible' => 1,
            ]);

            // Добавляем товар в категории
            if (!empty($productsCategoriesIds)) {
                foreach ($productsCategoriesIds as $categoryId) {
                    $categoriesEntity->addProductCategory($productId, $categoryId);
                }
            }

            // Импортируем изображения
            $this->importImages($xmlProduct, $productId);

        } else {

            // Обновляем товар
            if ($this->integration1C->fullUpdate === true) {
                $p = new \stdClass();
                if (!empty($xmlProduct->Описание)) {
                    $description = strval($xmlProduct->Описание);
                    $p->annotation = $description;
                    $p->description = $description;
                }
                $p->external_id = $product1cId;
                //$p->url = $this->integration1C->translit->translit((string)$xmlProduct->Наименование);
                $p->name = (string)$xmlProduct->Наименование;
                $p->meta_title = (string)$xmlProduct->Наименование;
                $p->meta_keywords = (string)$xmlProduct->Наименование;
                $p->meta_description = (string)$xmlProduct->Наименование;

                $productsEntity->update($productId, $p);

                // Обновляем категории товара
                if (!empty($productsCategoriesIds) && !empty($productId)) {
                    $categoriesEntity->deleteProductCategory($productId);
                    foreach ($productsCategoriesIds as $categoryId) {
                        $categoriesEntity->addProductCategory($productId, $categoryId);
                    }
                }
            }

            // Импортируем изображения
            $this->importImages($xmlProduct, $productId);
        }

        // Определяем откуда читать единицы измерения
        if (!$variant->units = (string)$xmlProduct->БазоваяЕдиница) {
            $attributes = $xmlProduct->БазоваяЕдиница->attributes();
            $param = "units_" . strval($attributes['Код']);
            $variant->units = $this->integration1C->getFromStorage($param);
        }
        
        // Если не найден вариант, добавляем вариант один к товару
        if (empty($variantId)) {
            $variant->product_id = $productId;
            $variant->stock = 0;
            $variantId = $variantsEntity->add($variant);
        } elseif (!empty($variantId)) {
            $variantsEntity->update($variantId, $variant);
        }

        // Определяем основную категорию товара
        $mainCategoryId = reset($productsCategoriesIds);
        
        // Свойства товара
        if (isset($xmlProduct->ЗначенияСвойств->ЗначенияСвойства)) {
            // Импортируем значения свойств товара
            $this->importProductFeatures($productId, $mainCategoryId, $xmlProduct->ЗначенияСвойств->ЗначенияСвойства);
        }
        
        $mainInfo = [];
        // Указываем бренд товара
        if (isset($xmlProduct->Изготовитель->Наименование)) {

            $brandName = strval($xmlProduct->Изготовитель->Наименование);
            // Добавим бренд
            // Найдем его по имени
            $select = $this->integration1C->queryFactory->newSelect();
            $select->cols(['id'])
                ->from('__brands')
                ->where('name=:name')
                ->bindValue('name', $brandName);
            $this->integration1C->db->query($select);
            if (!$brandId = $this->integration1C->db->result('id')) {

                $url = $this->integration1C->translit->translitAlpha($brandName);
                $url = str_replace('.', '', $url);

                // Делаем урлы уникальными
                while ($url = $brandsEntity->col('url')->findOne(['url' => $url])) {
                    if (preg_match('/(.+)?_([0-9]+)$/', $url, $parts)) {
                        $url = $parts[1].'_'.($parts[2]+1);
                    } else {
                        $url .= '_1';
                    }
                }
                
                // Создадим, если не найден
                $brandId = $brandsEntity->add([
                    'name' => $brandName,
                    'meta_title' => $brandName,
                    'meta_keywords' => $brandName,
                    'meta_description' => $brandName,
                    'url' => $url,
                    'visible' => 1,
                ]);
            }
            if (!empty($brandId)) {
                $mainInfo['brand_id'] = $brandId;
            }
        }
        
        if (!empty($mainCategoryId)) {
            $mainInfo['main_category_id'] = $mainCategoryId;
        }
        
        if (!empty($mainInfo)) {
            $productsEntity->update($productId, $mainInfo);
        }
    }


    /**
     * @param $productId int
     * @param $mainCategoryId int
     * @param $xmlFeatures \SimpleXMLElement()
     */
    protected function importProductFeatures($productId, $mainCategoryId, $xmlFeatures)
    {
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->integration1C->entityFactory->get(FeaturesEntity::class);
        
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->integration1C->entityFactory->get(BrandsEntity::class);
        
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->integration1C->entityFactory->get(ProductsEntity::class);
        
        foreach ($xmlFeatures as $xml_option) {
            $param = "features_" . strval($xml_option->Ид);
            if ($this->integration1C->getFromStorage($param) !== null) {
                $featureId = $this->integration1C->getFromStorage($param);

                if (isset($mainCategoryId) && !empty($featureId)) {
                    $featuresEntity->addFeatureCategory($featureId, $mainCategoryId);

                    $insert = $this->integration1C->queryFactory->newInsert();
                    $insert->into('__products_features_values')
                        ->ignore(true);
                    
                    foreach ($xml_option->Значение as $xml_value) {
                        $param = "features_values_".$featureId."_".strval($xml_value);
                        if ($this->integration1C->getFromStorage($param) !== null) {
                            $valueId = $this->integration1C->getFromStorage($param);
                            $insert->addRow(['product_id' => $productId, 'value_id' => $valueId]);
                        } else {
                            $valueId = $this->getFeatureValueId($featureId, strval($xml_value));
                            $insert->addRow(['product_id' => $productId, 'value_id' => $valueId]);
                        }
                    }

                    if (!empty($insert->getBindValues())) {
                        $this->integration1C->db->query($insert);
                    }
                }
            }
            // Если свойство оказалось названием бренда
            elseif ($this->integration1C->getFromStorage('brand_option_id') !== null && !empty($xml_option->Значение) && $this->integration1C->getFromStorage('brand_option_id') == strval($xml_option->Ид)) {
                
                $param = "brand_option_id_".strval($xml_option->Ид)."_".strval($xml_option->Значение);
                if ($this->integration1C->getFromStorage($param) !== null) {
                    $brandName = $this->integration1C->getFromStorage($param) ;
                } else {
                    $brandName = strval($xml_option->Значение);
                }
                
                // Если мы не запомнили такого бренда ранее, проверим его в базе
                if (($brandId = $this->integration1C->getFromStorage('brands' . $brandName)) === null) {
                    // Найдем его по имени
                    $select = $this->integration1C->queryFactory->newSelect();
                    $select->cols(['id'])
                        ->from('__brands')
                        ->where('name=:name')
                        ->bindValue('name', $brandName);
                    $this->integration1C->db->query($select);
                    if (!$brandId = $this->integration1C->db->result('id')) {

                        $url = $this->integration1C->translit->translitAlpha($brandName);
                        $url = str_replace('.', '', $url);

                        // Делаем урлы уникальными
                        while ($url = $brandsEntity->col('url')->findOne(['url' => $url])) {
                            if (preg_match('/(.+)?_([0-9]+)$/', $url, $parts)) {
                                $url = $parts[1].'_'.($parts[2]+1);
                            } else {
                                $url .= '_1';
                            }
                        }
                        
                        // Создадим, если не найден
                        $brandId = $brandsEntity->add([
                            'name' => $brandName,
                            'meta_title' => $brandName,
                            'meta_keywords' => $brandName,
                            'meta_description' => $brandName,
                            'url' => $url,
                            'visible' => 1,
                        ]);
                    }

                    // Запомним бренд для следующих товаров
                    $this->integration1C->setToStorage('brands' . $brandName, $brandId);
                }
                if (!empty($brandId)) {
                    $productsEntity->update($productId, ['brand_id'=>$brandId]);
                }
            }
        }
    }
    
    /**
     * @param $featureId int
     * @param $value string
     * @return int|null
     * Метод проверяет существование значения свойства, и возвращает его id или создает новое значение
     */
    protected function getFeatureValueId($featureId, $value)
    {
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->integration1C->entityFactory->get(FeaturesEntity::class);
        
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->integration1C->entityFactory->get(FeaturesValuesEntity::class);
        
        if (empty($featureId) || empty($value)) {
            return null;
        }
        
        $valueId = null;
        $translit = $this->integration1C->translit->translitAlpha($value);

        // Ищем значение с таким транслитом
        if ($fv = $featuresValuesEntity->find(['feature_id'=>$featureId, 'translit'=>$translit])) {
            $fv = reset($fv);
            $valueId = $fv->id;
        }

        // Если нет, тогда добавим значение
        if (empty($valueId)) {
            
            // Определяем нужно ли делать занчение индексируемым
            $toIndex = $featuresEntity->cols(['to_index_new_value'])->get((int)$featureId)->to_index_new_value;
            
            $featureValue = new \stdClass();
            $featureValue->value = trim($value);
            $featureValue->feature_id = $featureId;
            $featureValue->to_index = $toIndex;
            $valueId = $featuresValuesEntity->add($featureValue);
        }
        return $valueId;
    }
    
    /**
     * @param $xmlProduct \SimpleXMLElement()
     * @param $productId integer
     * @throws \Exception
     */
    protected function importImages($xmlProduct, $productId)
    {

        /** @var ImagesEntity $imagesEntity */
        $imagesEntity = $this->integration1C->entityFactory->get(ImagesEntity::class);

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->integration1C->entityFactory->get(ProductsEntity::class);
        
        $position = 0;
        $imagesIds = [];
        // Обновляем основное изображение товара
        if (isset($xmlProduct->ОсновнаяКартинка)) {
            $image = basename($xmlProduct->ОсновнаяКартинка);
            if (!empty($image) && is_file($this->integration1C->getTmpDir() . $image) && is_writable($this->integration1C->config->original_images_dir)) {

                $filename = basename((string)$image);
                
                $imgId = $imagesEntity->cols(['id'])->find([
                    'limit' => 1,
                    'product_id' => $productId,
                    'filename' => $filename,
                ]);
                if (!empty($imgId)) {
                    $imagesEntity->delete($imgId);
                }
                rename($this->integration1C->getTmpDir() . $image, $this->integration1C->config->original_images_dir. $filename);
                $imagesIds[] = $imagesEntity->add([
                    'product_id' => $productId,
                    'filename' => $filename,
                    'position' => $position++,
                ]);
            }
        }

        // Обновляем изображение товара
        if (isset($xmlProduct->Картинка)) {
            foreach ($xmlProduct->Картинка as $img) {
                $image = (string)$img;
                $filename = basename($image);
                
                $originalImagesDir = $this->integration1C->config->root_dir . $this->integration1C->config->original_images_dir;
                if (!empty($filename) && is_file($this->integration1C->getTmpDir(). $image) && is_writable($originalImagesDir)) {
                    $imgId = $imagesEntity->cols(['id'])->find([
                        'limit' => 1,
                        'product_id' => $productId,
                        'filename' => $filename,
                    ]);

                    if (!empty($imgId)) {
                        $imagesEntity->delete($imgId);
                    }
                    
                    rename($this->integration1C->getTmpDir(). $image, $originalImagesDir . $filename);
                    $imagesIds[] = $imagesEntity->add([
                        'product_id' => $productId,
                        'filename' => $filename,
                        'position' => $position++,
                    ]);
                }
            }
        }
        
        if (!empty($imagesIds)) {
            $mainImageId = reset($imagesIds);
            $productsEntity->update($productId, ['main_image_id' => $mainImageId]);
        }
    }
    
}
