# Импорт (Import)

Из csv файлов можно призводить импорт товаров, категорий и свойств товаров.
Также можно импортировать данные для [модуля](./modules/README.md). 

### Расширение импорта из модуля
Для того чтобы дополнить список импортируемх полей, которые выводятся перечнем при запуске импорта, полем из модуля необходимо использовать шортблок import_fields_association.
Для того чтобы считать из импортируемого файла необходимую информацию можно в [модуле](./modules/README.md) реализовать [экстендер](./modules/extenders.md), который будет расширять метод parseProductData() класса BackendImportHelper.
В методе экстендера принять вторым аргументом $itemFromCsv и считать необходимую информацию.

Пример:

```php
public function extendParseProductData($product, $itemFromCsv)
{
    if (!empty($itemFromCsv['supplier'])) {
        //...abstract
    }
}
```


Для того чтобы поля модуля при импорте не добавлялись в качестве новых свойств необходимо расширить метод getModulesColumnsNames() класса BackendImportHelper.
Метод экстендера принимает в качестве аргумента массив полей из модулей и добавляет свои поля.

Пример:

```php
public function extendModulesColumnsNames($modulesColumnsNames)
{
    $modulesColumnsNames['supplier'] = 'supplier';
    return $modulesColumnsNames;
}
```
 
Для того чтобы из модуля внести изменения после импорта, необходимо расширить метод afterImportProductProcedure() класса BackendImportHelper. 