# Імпорт (Import)

З CSV-файлів можна виконувати імпорт товарів, категорій і властивостей товарів.
Також можна імпортувати дані для [модуля](./modules/README.md).

### Розширення імпорту з модуля
Щоб доповнити список імпортованих полів, який показується переліком під час запуску імпорту, полем із модуля потрібно використати шорт-блок `import_fields_association`.
Щоб зчитати з імпортованого файла потрібну інформацію, у [модулі](./modules/README.md) можна реалізувати [extender](./modules/extenders.md), який розширюватиме метод `parseProductData()` класу BackendImportHelper.
У методі extender прийміть другим аргументом `$itemFromCsv` і зчитайте потрібну інформацію.

Приклад:

```php
public function extendParseProductData($product, $itemFromCsv)
{
    if (!empty($itemFromCsv['supplier'])) {
        //...abstract
    }
}
```


Щоб поля модуля під час імпорту не додавалися як нові властивості, потрібно розширити метод `getModulesColumnsNames()` класу BackendImportHelper.
Метод extender приймає як аргумент масив полів із модулів і додає свої поля.

Приклад:

```php
public function extendModulesColumnsNames($modulesColumnsNames)
{
    $modulesColumnsNames['supplier'] = 'supplier';
    return $modulesColumnsNames;
}
```
 
Щоб із модуля внести зміни після імпорту, потрібно розширити метод `afterImportProductProcedure()` класу BackendImportHelper.
