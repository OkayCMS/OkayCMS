# Entities

Классы сущностей нужны для управления наборами данных хранящихся в постоянной памяти.
Большинство классов Entities в OkayCMS работают с базой данных, но есть некоторые, которые хранят записи в файловой 
системе.

По умолчанию классы сущностей лежат в Okay/Entities/. Сущности из модулей нужно хранить в 
`Okay/Modules/Vendor/Module/Entities`.

Все классы реализовывают интерфейс `Okay\Core\Entity\EntityInterface`
Каждый класс сущности должен наследоваться от класса `Okay\Core\Entity\Entity`.

В классе `Okay\Core\Entity\Entity` уже есть базовая реализация класса Entity для работы с БД. Для корректной работы
нужно произвести первоначальную настройку.

### Настройка Entity для работы с БД

Для настройки нужно указать некоторые защищенные статические (protected static) свойства.

Обязательные свойства:
* `$table` - string название таблицы, в которой нужно сохранять данные (можно с префиксом `__`, можно без него)
* `$tableAlias` - string алиас для основной таблицы, который стоит использовать в SQL запросах
* `$fields` - array список полей, которые нужно доставать из БД

Необязательные свойства:
* `$langTable` - string название таблицы, в которой хранятся переводы (без `__lang_`)
* `$langFields` - array список мультиязычных полей, которые нужно доставать из БД
* `$langObject` - string используется для связи с мультиязычными данными (в языковых таблицах blog_id, product_id)
* `$searchFields` - array список полей по которым происходит текстовый поиск (можно указывать и ленговые и нет). Будет
использоваться если передать ['keyword' => 'name of entity item'].
* `$additionalFields` - array список дополнительных полей сущности, с других таблиц или которые как подзапросы идут 
(к ним префикс таблицы не добавляется).
* `$defaultOrderFields` - array список полей по которым происходит сортировка по умолчанию (с указанием направления).
* `$alternativeIdField` - string поле по которому может происходить get() если id передали строкой (url, code etc...)
Предпочтительнее использовать метод findOne(['field' => $value]).

Пример настройки:

```php
namespace Okay\Entities;
use Okay\Core\Entity\Entity;
class SomeEntity extends Entity
{
    protected static $fields = [
        'id',
        'url',
        'visible',
    ];
    
    protected static $langFields = [
        'name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'annotation',
        'description',
    ];
    
    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];

    protected static $table = 'some_entities';
    protected static $langObject = 'some_entity';
    protected static $langTable = 'some_entities';
    protected static $tableAlias = 's';
}
```

### Фильтрация выборки Entity из БД

Каждый экземпляр класса Entity содержит приватное свойство $select, в котором лежит экземпляр класса 
[Aura\SqlQuery\Common\SelectInterface](https://github.com/auraphp/Aura.SqlQuery/blob/3.x/docs/select.md).

Сброс состояния производится вызовом метода Entity::flush(). По умолчанию состояние сбрасывается автоматически после
вызова методов find(), count(), get() ect. Сбрасывать его вручную может потребоваться в каких-то особых случаях.

#### "Магические" фильтры

Методы find(), count() etc принимают ассоциативный массив данных, по которым нужно фильтровать, где ключ массива - это 
название фильтра. "Магические" фильтры работают в случае если передали фильтр с названием как название колонки,
и при этом данный фильтр не переопределён. Эти фильтры также строят разные запросы в случае если передали строку
или другое единичное значение, и если передали массив значений.

Например:
```php
namespace Okay\Entities;
use Okay\Core\Entity\Entity;
class SomeEntity extends Entity
{
    protected static $fields = [
        'id',
        'url',
    ];
    
    protected static $langFields = [
        'name',
    ];

    // ...abstract 
}
```

Вызов с единичными значениями:
```php
$someEntity->find([
    'url' => 'some/url',
]);

$someEntity->find([
    'name' => 'name of entity item',
]);
```

построит запросы `SELECT ... WHERE entity_table.url = 'some/url'` и `SELECT ... WHERE lang_entity_table.name = 'name of entity item'`.

Вызов с множеством значений:
```php
$someEntity->find([
    'id' => [1, 2, 3, 4, 5],
]);
```

построит запрос `SELECT ... WHERE entity_table.id IN (1,2,3,4,5)`.

#### Пользовательские фильтры <a name="usersFilters"></a>

Если поведение "магических" фильтров не устраивает, или его нужно по какой-то причине отменить вообще, или вы фильтруете
не по полю, а скажем по таблице связей, нужно объявить свой пользовательский фильтр в вашем классе Entity.

Это должен быть защищенный (protected) метод, название которого состоит из ключевого слова `filter__` (обратите
внимание на два символа подчёркивания) и самого названия фильтра (он же будет ключем массива фильтра при вызове find(), 
count() ...). Внутри этого метода мы работаем с объёктом 
[QueryBuilder](https://github.com/auraphp/Aura.SqlQuery/blob/3.x/docs/select.md), который лежит в свойстве $select.
Метод может принимать два аргумента, первым будет значение которое передали в этот фильтр при вызове find() или count(), 
вторым будет полностью весь массив $filter (который не обязательно принимать).

Пример вызова:
```php
$someEntity->find([
    'url' => 'some/url',
    'field' => 'value',
]);
```

Пример пользовательского фильтра:
```php
namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Aura\SqlQuery\Common\Select;

class SomeEntity extends Entity
{
    /** @var Select */
    protected $select;
    protected static $tableAlias = 'e';

    // ...abstract 

    protected function filter__field($val, $filter)
    {
        // $val = 'value';
        // $filter = [
        //               'url' => 'some/url',
        //               'field' => 'value',
        //           ];
        
        $this->select->join('inner', '__second_table AS st', 'e.id = st.entity_id AND st.field=:value')
            ->bindValue('value', $val);
        
        $this->select->groupBy(['e.id']);
    }
}
```

#### Пользовательские фильтры созданные из модулей для существующего Entity <a name="usersFiltersFromModules"></a>

Если нужно добавить пользовательский фильтр в существующий Entity, его можно добавить через модуль.
Для этого нужно описать метод, который будет выполнять роль пользовательского фильтра в классе
наследнике `Okay\Core\Modules\AbstractModuleEntityFilter`. Метод такого фильтра должен быть публичным (public).

Пример:
```php
namespace Okay\Modules\OkayCMS\GoogleMerchant\ExtendsEntities;


use Okay\Core\Modules\AbstractModuleEntityFilter;
use Okay\Modules\OkayCMS\GoogleMerchant\Init\Init;

class ProductsEntity extends AbstractModuleEntityFilter
{
    public function okaycms__google_merchant__only($categoriesIds, $filter)
    {
        $categoryFilter = '';
        if (!empty($categoriesIds)) {
            $categoryFilter = "OR p.id IN (SELECT product_id FROM __products_categories WHERE category_id IN (:category_id))";
            $this->select->bindValue('category_id', (array)$categoriesIds);
        }

        $this->select->where('not_to__okaycms__google_merchant != 1');
        $this->select->where("(
            p.".Init::TO_FEED_FIELD."=1 
            OR p.brand_id IN (SELECT id FROM __brands WHERE ".Init::TO_FEED_FIELD." = 1)
            {$categoryFilter}
        )");
    }
}
```
Внутри метода фильтра работа выполняется так же как и в [обычном пользовательском фильтре](#usersFilters).
Но данный метод хоть чуть более сложный, но он позволяет из модуля добавить пользовательский фильтр к существующему 
Entity не правя файл, где описан их класс.

Пример регистрации данного фильтра:
```php
namespace Okay\Modules\OkayCMS\GoogleMerchant\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Entities\ProductsEntity;

class Init extends AbstractInit
{
    public function install()
    {
        // ...abstract
    }

    public function init()
    {
        // ...abstract
        $this->registerEntityFilter(
            ProductsEntity::class,
            'okaycms__google_merchant__only',
            \Okay\Modules\OkayCMS\GoogleMerchant\ExtendsEntities\ProductsEntity::class,
            'okaycms__google_merchant__only'
        );
    }
}
```

### Сортировка выборки Entity из БД

Для указания сортировки выборки отличной от указанной в свойстве `$defaultOrderFields` нужно при вызове метода find()
вызвать дополнительный метод order(), который принимает два параметра. Первый - название сортировки, второй это массив
который может использоваться для пользовательских целей, по умолчанию не влияет на функциональность.

#### "Магические" сортировки

Если указали сортировку с именем, совпадающем с именем поля в Entity, применится "магическая" сортировка по данному 
полю, если не задана пользовательская сортировка с таким именем. Также можно передавать название сортировки и 
её направление. Можно передавать `name`, `name_asc` (одно и то же) или `name_desc` и если у Entity есть колонки 'name' 
(не важно она ленговая или нет) автоматически добавится `SELECT ... ORDER BY name ASC`.
Их недостаток, работают они только с одним полем.

#### Пользовательские сортировки

Если же нужно использовать более сложную сортировку, нужно её отдельно описать.
Чтобы её описать, нужно в классе вашего Entity перегрузить метод customOrder().
Он может содержать три параметра:
* `$order` - string название сортировки, которое передали
* `$orderFields` - array массив полей, которые определили сортировки "выше" (чаще всего это "магическия" сортировка)
* `$additionalData` - array массив пользовательских данных, которые передали в метод order().

Пример:
```php
namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ProductsEntity extends Entity
{
    // ...abstract 

    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {
        switch ($order) {
            case 'rand':
                $orderFields = ['RAND()'];
                break;
            case 'position':
                $orderFields = ['p.position DESC'];
                break;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }
}
```

### Маппинг результатов выборки Entity из БД

Если нужно чтобы массив результатов выборки из БД в качестве ключей массива содержал значение из определенной колонки,
нужно вызвать метод mappedBy($columnName) с указанием колонки, данные которой будут являться ключами.

Пример:
```php
$products = [];
foreach ($productsEntity->find(['category_id' => 1]) as $product) {
    $products[$product->id] = $product;
}

// то же самое что и
$products = $productsEntity->mappedBy('id')->find(['category_id' => 1]);
```

### Ограничение выбираемых данных из БД

Иногда при поиске набора сущностей, нужно ограничить объем доставаемых данных (получать не все колонки).
Для этого нужно вызвать метод cols(), он принимает массив колонок, которые нужно достать.

Например в списке товаров не нужно доставать описание и метаданные всех товаров:
```php
$products = $productsEntity->cols([
        'id',
        'name',
        'url',
        'name',
        'special',
        'annotation',
    ])->find($filter);
```

### Получить объект Select класса Entity

Если нужно получить объект запроса, который строит Entity, его можно получить с помощью метода YourEntity::getSelect()
(имеется ввиду метод getSelect() нужного Entity). Он вернет объект запроса, который бы метод find или findOne отправил
в базу. Имейте в виду, если вы перегружаете метод find базового класса Entity, для корректной работы getSelect вам может
понадобиться также перегрузить метод getSelect() в этом Entity. 

Например: если у вас метод перед отработкой родительского метода добавляет к запросу еще какие-то данные, это может быть
нужно повторить для метода getSelect()
```php
class ProductsEntity extends Entity

    public function find(array $filter = [])
    {
        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=p.url AND r.type="product"');
        
        return parent::find($filter);
    }
    
    public function getSelect(array $filter = [])
    {
        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=p.url AND r.type="product"');
        
        return parent::getSelect($filter);
    }
}
```

Метод getSelect() принимает массив фильтра так же как методы find() или findOne() и возвращает полноценный объект Select
который можно в дальнейшем модифицировать и выполнить.

Пример:
```php
$query = $commentsEntity->getSelect(['type' => 'post', 'object_id' => $postsIds]);
$query->groupBy(['object_id'])->resetCols()->cols(["COUNT( DISTINCT id) as count", "object_id"]);

foreach ($query->results() as $result) {
    if (isset($posts[$result->object_id])) {
        $posts[$result->object_id]->comments_count = $result->count;
    }
}
```

### Дебаг запросов в классе Entity

Если необходимо увидеть текст запроса класса Entity, который летит в БД, можно перед вызовом метода find() или findOne() 
вызвать метод debug() и текст запроса будет передан на вывод (осторожно на проде.).

Пример:

```php
$productsEntity->debug()->find($filter);
```

Такой запрос будет выполнен и передан в вывод.
Также у класса Select можно вызвать метод debugPrint() который сделает то же самое.

Пример:
```php
$select = $productsEntity->getSelect($filter);
$select->debugPrint();

// тоже самое
$productsEntity->getSelect($filter)->debugPrint();
```

### Лимит выборки результатов

По умолчанию все SELECT запросы через классы Entity выполняются с SQL лимитом, даже если его явно не передали, он по умолчанию
для безопасности устанавливается в 100. Но если вам нужно достать вообще все данные и не важно сколько это строк,
на свой страх и риск можно вызвать метод noLimit() класса Entity перед вызовом find().

Например:

```php
$productsEntity->noLimit()->find($filter);
```

Но будьте осторожны, при большом количестве данных запрос может существенно "тормозить".