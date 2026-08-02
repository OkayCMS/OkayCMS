# Експорт (Export)

Експорт виконується у файл CSV.
У базовому функціоналі можливий експорт усіх товарів, товарів певної категорії або бренда.
Щоб із модуля додати можливість експортувати товари за якоюсь власною ознакою, можна розширити метод `getCategoriesForExportFilter()` класу BackendExportHelper і в методі свого [extender](./modules/extenders.md) передати в дизайн потрібну змінну.
Далі можна розширити метод `setUp()` класу BackendExportHelper, прийнявши як аргумент масив. Нульовим елементом цього масиву буде фільтр, за яким вибираються товари для експорту.

Приклад:

```php
public function extendFilter($params)
{
    [$filter, $page] = $params;
    $supplierId = $this->request->get('supplier_id', 'integer');

    if ($supplierId) {
        $filter['supplier_id'] = $supplierId;
    }

    return [$filter, $page];
}
```


Щоб фільтр, доданий як у прикладі, спрацьовував, потрібно створити користувацький фільтр для [сутності](./entities.md) ProductsEntity.

Щоб додати колонки з модуля в експорт товарів, потрібно розширити метод `getColumnsNames()` класу BackendExportHelper.

Приклад:

```php
public function extendExportColumnsNames($columnsNames)
{
    $columnsNames['supplier'] = 'Supplier';

    return $columnsNames;
}
```
