# Миграции БД модулей

Миграции для модулей могут быть трёх видов:
* [Добавление поля к существующему классу Entity](#migrateEntityField)
* [Создание новой таблицы для Entity модуля](#migrateEntityTable)
* [Создание новой таблицы связи](#migrateCustomTable)

#### Добавление поля к существующему классу Entity <a name="migrateEntityField"></a>
Чтобы добавить новое поле к существующему классу [Entity](./../entities.md),
нужно в методе [install() класса Init](./README.md#configuratinFiles) вызвать метод migrateEntityField(),
который принимает два параметра:
* Имя класса Entity, к которому нужно добавить поле
* Экземпляр класса [Okay\Core\Modules\EntityField](#EntityField)

Пример:
```php
$this->migrateEntityField(VariantsEntity::class, (new EntityField('field_name'))->setTypeVarchar(255)->setIndex());
```

Также при добавлении поля к уже существующим сущностям, нужно его зарегистрировать в системе, чтобы оно учавствовало
в SELECT и фильтрации ([подробнее об Entities](./../entities.md)).

Пример:
```php
$this->registerEntityField(VariantsEntity::class, 'field_name');
```
Это тоже самое, если бы это поле было прописано в одно из свойств класса VariantsEntity
```php
use Okay\Core\Entity\Entity;

class VariantsEntity extends Entity
{
    protected static $fields = [
        'id',
        'product_id',
        'sku',
        'price',
    ];
    
    protected static $additionalFields = [
        '(v.stock IS NULL) as infinity',
        'c.rate_from',
        'c.rate_to',
    ];
    
    protected static $langFields = [
        'name',
        'units',
    ];
    //...abstract
}
```

#### Создание новой таблицы для Entity <a name="migrateEntityTable"></a>
Чтобы создать таблицу для нового Entity (который добавляет модуль),
нужно в методе [install() класса Init](./README.md#configuratinFiles) вызвать метод migrateEntityTable(),
который принимает два параметра:
* Имя класса Entity, к которому нужно добавить поле
* Массив экземпляров класса [Okay\Core\Modules\EntityField](#EntityField)

В массиве полей, нужно описать каждое поле, которое объявлено в Entity модуля.

Пример:
```php
$this->migrateEntityTable(NPCostDeliveryDataEntity::class, [
    (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
    (new EntityField('order_id'))->setTypeInt(11)->setIndex(),
    (new EntityField('city_id'))->setTypeVarchar(255, true),
    (new EntityField('warehouse_id'))->setTypeVarchar(255, true),
    (new EntityField('delivery_term'))->setTypeVarchar(8, true),
    (new EntityField('redelivery'))->setTypeTinyInt(1, true),
    (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
]);
```

<a name="compositeIndex"></a>

Чтобы создать составной индекс, нужно в метод setIndex() или setIndexUnique() передать в виде второго и последующих 
аргументов поля (объекты класса EntityField), по которым в паре с текущим полем должен быть составной индекс.

Пример:

```php
$cityIdField = (new EntityField('city_id'))->setTypeVarchar(255, true);

$this->migrateEntityTable(NPCostDeliveryDataEntity::class, [
    (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
    (new EntityField('order_id'))->setTypeInt(11)->setIndex(null, $cityIdField),
    $cityIdField,
]);
```

Таким образом будет создан индекс order_id, city_id (`order_id_city_id`).

#### Создание новой таблицы связи <a name="migrateCustomTable"></a>
Чтобы создать таблицу связи, нужно в методе [install() класса Init](./README.md#configuratinFiles)
вызвать метод migrateCustomTable(), который принимает два параметра:
* Название таблицы (без приставки ok_, можно с приставкой __)
* Массив экземпляров класса [Okay\Core\Modules\EntityField](#EntityField)

В массиве полей, нужно описать каждое поле, которое объявлено в Entity модуля.

Пример:
```php
$this->migrateCustomTable('some_table_name', [
    (new EntityField('redelivery'))->setTypeTinyInt(1),
    (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
]);
$this->migrateCustomTable('__second_some_table_name', [
    (new EntityField('redelivery'))->setTypeTinyInt(1),
    (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
]);
```

### Класс Okay\Core\Modules\EntityField <a name="EntityField"></a>

Данный класс нужен для настройки поля (колонки) в базе данных, для их последующей миграции.
Документацию по методам, см. в аннотации к методам.
Метод в конструктор принимает название колонки, далее вся настройка происходит через fluent interface.

Пример:
```php
$notLangField = (new EntityField('not_lang_field'))->setTypeVarchar(255)->setIndex();
$langField = (new EntityField('lang_field'))->setTypeVarchar(255)->setIndex()->setIsLang();
```

