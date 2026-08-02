# Оновлення таблиць БД модулів

Для модулів доступні три способи змінити структуру БД під час встановлення:
* [Додавання поля до наявного класу Entity](#migrateEntityField)
* [Створення нової таблиці для Entity модуля](#migrateEntityTable)
* [Створення нової таблиці звʼязку](#migrateCustomTable)

#### Додавання поля до наявного класу Entity <a name="migrateEntityField"></a>
Щоб додати нове поле до наявного класу [Entity](./../entities.md),
потрібно в методі [install() класу Init](./README.md#configuratinFiles) викликати метод `migrateEntityField()`,
який приймає два параметри:
* Імʼя класу Entity, до якого потрібно додати поле
* Екземпляр класу [Okay\Core\Modules\EntityField](#EntityField)

Приклад:
```php
$this->migrateEntityField(VariantsEntity::class, (new EntityField('field_name'))->setTypeVarchar(255)->setIndex());
```

Також під час додавання поля до вже наявних сутностей потрібно зареєструвати його в системі, щоб воно брало участь
у SELECT і фільтрації ([докладніше про Entities](./../entities.md)).

Приклад:
```php
$this->registerEntityField(VariantsEntity::class, 'field_name');
```
Це те саме, якби це поле було прописано в одну з властивостей класу VariantsEntity
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

#### Створення нової таблиці для Entity <a name="migrateEntityTable"></a>
Щоб створити таблицю для нового Entity (який додає модуль),
потрібно в методі [install() класу Init](./README.md#configuratinFiles) викликати метод `migrateEntityTable()`,
який приймає два параметри:
* Імʼя класу Entity, для якого потрібно створити таблицю
* Масив екземплярів класу [Okay\Core\Modules\EntityField](#EntityField)

У масиві полів потрібно описати кожне поле, оголошене в Entity модуля.

Приклад:
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

Щоб створити складений індекс, у метод `setIndex()` або `setIndexUnique()` потрібно передати другим і наступними
аргументами поля (обʼєкти класу EntityField), за якими в парі з поточним полем має бути складений індекс.

Приклад:

```php
$cityIdField = (new EntityField('city_id'))->setTypeVarchar(255, true);

$this->migrateEntityTable(NPCostDeliveryDataEntity::class, [
    (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
    (new EntityField('order_id'))->setTypeInt(11)->setIndex(null, $cityIdField),
    $cityIdField,
]);
```

Таким чином буде створено індекс order_id, city_id (`order_id_city_id`).

#### Створення нової таблиці звʼязку <a name="migrateCustomTable"></a>
Щоб створити таблицю звʼязку, у методі [install() класу Init](./README.md#configuratinFiles)
потрібно викликати метод `migrateCustomTable()`, який приймає два параметри:
* Назва таблиці (без префікса ok_, можна з префіксом __)
* Масив екземплярів класу [Okay\Core\Modules\EntityField](#EntityField)

У масиві полів потрібно описати кожне поле, яке оголошено в Entity модуля.

Приклад:
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

### Клас Okay\Core\Modules\EntityField <a name="EntityField"></a>

Цей клас потрібен для налаштування поля (колонки) в базі даних.
Документацію щодо методів див. в анотаціях до методів.
Конструктор приймає назву колонки, далі всі налаштування відбуваються через fluent interface.

Приклад:
```php
$notLangField = (new EntityField('not_lang_field'))->setTypeVarchar(255)->setIndex();
$langField = (new EntityField('lang_field'))->setTypeVarchar(255)->setIndex()->setIsLang();
```
