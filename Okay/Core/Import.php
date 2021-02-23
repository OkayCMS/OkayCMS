<?php


namespace Okay\Core;


class Import
{

    /*Соответствие полей в базе и имён колонок в файле*/
    private $columnsNames = [
        'category'=>         ['category', 'категория'],
        'brand'=>            ['brand', 'бренд'],
        'name'=>             ['product', 'name', 'товар', 'название', 'наименование'],
        'variant'=>          ['variant', 'вариант'],
        'sku'=>              ['sku', 'артикул'],
        'price'=>            ['price', 'цена'],
        'compare_price'=>    ['compare price', 'old price', 'старая цена'],
        'currency'=>         ['currency_id', 'currency', 'currency id', 'ID валюты'],
        'weight'=>           ['weight', 'вес варианта'],
        'stock'=>            ['stock', 'склад', 'на складе'],
        'units'=>            ['units', 'ед. изм.'],
        'visible'=>          ['visible', 'published', 'видим'],
        'featured'=>         ['featured', 'hit', 'хит', 'рекомендуемый'],
        'meta_title'=>       ['meta title', 'заголовок страницы'],
        'meta_keywords'=>    ['meta keywords', 'ключевые слова'],
        'meta_description'=> ['meta description', 'описание страницы'],
        'annotation'=>       ['annotation', 'аннотация', 'краткое описание'],
        'description'=>      ['description', 'описание'],
        'images'=>           ['images', 'изображения'],
        'url'=>              ['url', 'адрес']

    ];

    // Соответствие имени колонки и поля в базе
    protected $internalColumnsNames;

    protected $importFilesDir       = 'backend/files/import/'; // Временная папка
    protected $import_file          = 'import.csv';            // Временный файл
    protected $categoryDelimiter    = ',,';                    // Разделитель каегорий в файле
    protected $subCategoryDelimiter = '/';                     // Разделитель подкаегорий в файле
    protected $valuesDelimiter      = ',,';                    // Разделитель значений свойства в товаре
    protected $columnDelimiter      = ';';
    protected $columns              = [];
    protected $locale               = 'ru_RU.UTF-8';

    // Заменяем имена колонок из файла на внутренние имена колонок
    public function initInternalColumns($fields = [])
    {
        if (isset($this->internalColumnsNames)) {
            return true;
        }
        if (empty($this->columns)) {
            return false;
        }
        if (!empty($fields)) {
            foreach ($fields as $csv=>$inner) {
                if (isset($this->columnsNames[$inner]) && !in_array(mb_strtolower($csv), array_map("mb_strtolower", $this->columnsNames[$inner]))) {
                    $this->columnsNames[$inner][] = $csv;
                }
            }
        }
        $this->internalColumnsNames = [];
        foreach ($this->columns as &$column) {
            if ($internal_name = $this->internalColumnName($column)) {
                $this->internalColumnsNames[$column] = $internal_name;
                $column = $internal_name;
            }
        }
        return true;
    }

    // Определяем колонки из первой строки файла
    public function initColumns()
    {
        $f = fopen($this->importFilesDir.$this->import_file, 'r');
        $this->columns = fgetcsv($f, null, $this->columnDelimiter);
        fclose($f);
    }

    // Возвращает внутренние название колонки по названию колонки в файле
    private function internalColumnName($name)
    {
        $name = trim($name);
        $name = str_replace('/', '', $name);
        $name = str_replace('\/', '', $name);
        foreach($this->columnsNames as $i=>$names) {
            foreach($names as $n) {
                if(!empty($name) && preg_match("/^".preg_quote($name)."$/ui", $n)) {
                    return $i;
                }
            }
        }
        return false;
    }

    public function getColumnDelimiter()
    {
        return $this->columnDelimiter;
    }

    /**
     * @param array
     * @return void
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getCategoryDelimiter()
    {
        return $this->categoryDelimiter;
    }

    /**
     * @return string
     */
    public function getSubCategoryDelimiter()
    {
        return $this->subCategoryDelimiter;
    }

    /**
     * @return string
     */
    public function getValuesDelimiter()
    {
        return $this->valuesDelimiter;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return array
     */
    public function getColumnsNames()
    {
        return $this->columnsNames;
    }

    /**
     * @return mixed
     */
    public function getInternalColumnsNames()
    {
        return $this->internalColumnsNames;
    }

    /**
     * @return string
     */
    public function getImportFilesDir()
    {
        return $this->importFilesDir;
    }

    /**
     * @return string
     */
    public function getImportFile()
    {
        return $this->import_file;
    }

}