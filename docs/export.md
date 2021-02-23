# Экспорт (Export)

Экспорт производится в файл csv. 
В дефолном функционале возможен экспорт всех товаров, товаров определенной категории или бренда.
Для того, чтобы из модуля добавить возможность экспортировать товары по какому-то своему признаку, можно расширить метод getCategoriesForExportFilter() класса BackendExportHelper и в методе своего [экстендера](./modules/extenders.md) передать в дизайн необходимую переменную.
Далее можно расширить метод setUp() класса BackendExportHelper, приняв в качестве аргумента массив. Нулевым элементом данного массива будет фильтр, по которому выбираются товары для экспорта.

 Пример:

```php
     public function extendSetUp($array)
    {
        $supplier_id = //...abstract

        $array[0] = $array[0] + ['supplier_id' => $supplier_id];
        return $array;
    }
```


Для того, чтобы отрабатывал фильтр, допленный как указано в примере в методе extendSetUp необходимо создать пользовательский фильтр для [сущности](./entities.md) ProductsEntity.  

Чтобы добвить колонки из модуля в экспорт товаров, необходимо расширить метод getColumnsNames() класса BackendExportHelper.

Пример:

```php
    public function extendExportColumnsNames($columnsNames)
    {
        $columnsNames['supplier'] = 'Supplier';
        return $columnsNames;
    }
```
