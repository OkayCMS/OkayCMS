# Сутності (Entities)

Класи сутностей потрібні для керування наборами даних, що зберігаються в постійній памʼяті.
Більшість класів Entities в OkayCMS працюють із базою даних, але є й такі, що зберігають записи у файловій системі.

За замовчуванням класи сутностей розташовані в `Okay/Entities/`. Сутності з модулів потрібно зберігати в
`Okay/Modules/Vendor/Module/Entities`.

Усі класи реалізують інтерфейс `Okay\Core\Entity\EntityInterface`.
Кожен клас сутності має наслідуватися від класу `Okay\Core\Entity\Entity`.

У класі `Okay\Core\Entity\Entity` уже є базова реалізація Entity для роботи з БД. Для коректної роботи
потрібно виконати початкове налаштування.

### Налаштування Entity для роботи з БД

Для налаштування потрібно вказати деякі protected static властивості.

Обовʼязкові властивості:
* `$table` — string назва таблиці, у якій потрібно зберігати дані (можна з префіксом `__`, можна без нього)
* `$tableAlias` — string alias для основної таблиці, який варто використовувати в SQL-запитах
* `$fields` — array список полів, які потрібно діставати з БД

Необовʼязкові властивості:
* `$langTable` — string назва таблиці, у якій зберігаються переклади (без `__lang_`)
* `$langFields` — array список мультимовних полів, які потрібно діставати з БД
* `$langObject` — string використовується для звʼязку з мультимовними даними (у мовних таблицях blog_id, product_id)
* `$searchFields` — array список полів, за якими відбувається текстовий пошук (можна вказувати як мовні, так і немовні). Буде
використано, якщо передати `['keyword' => 'name of entity item']`.
* `$additionalFields` — array список додаткових полів сутності з інших таблиць або таких, що йдуть як subquery
(до них префікс таблиці не додається).
* `$defaultOrderFields` — array список полів, за якими відбувається сортування за замовчуванням (із зазначенням напряму).
* `$alternativeIdField` — string поле, за яким може відбуватися `get()`, якщо id передали рядком (url, code тощо).
Замість цього бажано використовувати `findOne(['field' => $value])`.

Приклад налаштування:

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

### Фільтрація вибірки Entity з БД

Кожен екземпляр класу Entity містить protected-властивість `$select`, у якій знаходиться екземпляр класу
[Aura\SqlQuery\Common\SelectInterface](https://github.com/auraphp/Aura.SqlQuery/blob/3.x/docs/select.md).

Скидання стану виконується викликом методу `Entity::flush()`. За замовчуванням стан скидається автоматично після
виклику методів `find()`, `count()`, `get()` тощо. Скидати його вручну може знадобитися в окремих випадках.

#### “Магічні” фільтри

Методи `find()`, `count()` тощо приймають асоціативний масив даних, за якими потрібно фільтрувати, де ключ масиву —
назва фільтра. “Магічні” фільтри працюють, якщо передали фільтр з назвою, що збігається з назвою колонки,
і при цьому цей фільтр не перевизначено. Також ці фільтри будують різні запити залежно від того, передали рядок/одиничне
значення або масив значень.

Наприклад:
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

Виклик з одиничними значеннями:
```php
$someEntity->find([
    'url' => 'some/url',
]);

$someEntity->find([
    'name' => 'name of entity item',
]);
```

побудує запити `SELECT ... WHERE entity_table.url = 'some/url'` і `SELECT ... WHERE lang_entity_table.name = 'name of entity item'`.

Виклик з множиною значень:
```php
$someEntity->find([
    'id' => [1, 2, 3, 4, 5],
]);
```

побудує запит `SELECT ... WHERE entity_table.id IN (1,2,3,4,5)`.

#### Користувацькі фільтри <a name="usersFilters"></a>

Якщо поведінка “магічних” фільтрів не підходить, або її з якоїсь причини потрібно скасувати, або ви фільтруєте
не за полем, а, наприклад, за таблицею звʼязків — потрібно оголосити власний користувацький фільтр у вашому Entity-класі.

Це має бути protected метод, назва якого складається з ключового слова `filter__` (зверніть увагу на два символи підкреслення)
і власне назви фільтра (вона ж буде ключем масиву фільтрів під час виклику `find()`, `count()` тощо). Усередині цього методу ми працюємо з обʼєктом
[QueryBuilder](https://github.com/auraphp/Aura.SqlQuery/blob/3.x/docs/select.md), який знаходиться у властивості `$select`.
Метод може приймати два аргументи: перший — значення, яке передали в цей фільтр під час виклику `find()` або `count()`,
другий — увесь масив `$filter` (його приймати не обовʼязково).

Приклад виклику:
```php
$someEntity->find([
    'url' => 'some/url',
    'field' => 'value',
]);
```

Приклад користувацького фільтра:
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

#### Користувацькі фільтри з модулів для наявного Entity <a name="usersFiltersFromModules"></a>

Якщо потрібно додати користувацький фільтр у наявний Entity, це можна зробити через модуль.
Для цього потрібно описати метод, який виконуватиме роль користувацького фільтра в класі-нащадку
`Okay\Core\Modules\AbstractModuleEntityFilter`. Метод такого фільтра має бути public.

Приклад:
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
Усередині методу фільтра робота виконується так само, як і в [звичайному користувацькому фільтрі](#usersFilters).
Хоч цей метод і трохи складніший, він дозволяє з модуля додати користувацький фільтр до наявного Entity
без правок файла, де описано його клас.

Приклад реєстрації цього фільтра:
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

### Сортування вибірки Entity з БД

Щоб вказати сортування вибірки, відмінне від заданого у властивості `$defaultOrderFields`, під час виклику методу `find()`
потрібно викликати додатковий метод `order()`, який приймає два параметри. Перший — назва сортування, другий — масив,
який може використовуватися для користувацьких цілей і за замовчуванням не впливає на функціональність.

#### “Магічні” сортування

Якщо вказали сортування з імʼям, що збігається з імʼям поля в Entity, застосовується “магічне” сортування за цим полем,
якщо не задано користувацьке сортування з таким імʼям. Також можна передавати назву сортування та його напрям.
Можна передавати `name`, `name_asc` (те саме) або `name_desc`, і якщо в Entity є колонка `name`
(неважливо, мовна вона чи ні) — автоматично додасться `SELECT ... ORDER BY name ASC`.
Недолік: працює лише з одним полем.

#### Користувацькі сортування

Якщо потрібно використати складніше сортування — його потрібно описати окремо.
Для цього в класі вашого Entity потрібно перевизначити метод `customOrder()`.
Він може мати три параметри:
* `$order` — string назва сортування, яку передали
* `$orderFields` — array масив полів, які визначили сортування “вище” (найчастіше це “магічне” сортування)
* `$additionalData` — array масив користувацьких даних, які передали в метод `order()`.

Приклад:
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

### Мапінг результатів вибірки Entity з БД

Якщо потрібно, щоб масив результатів вибірки з БД як ключі містив значення з певної колонки,
потрібно викликати метод `mappedBy($columnName)` із зазначенням колонки, дані якої будуть ключами.

Приклад:
```php
$products = [];
foreach ($productsEntity->find(['category_id' => 1]) as $product) {
    $products[$product->id] = $product;
}

// те саме, що й
$products = $productsEntity->mappedBy('id')->find(['category_id' => 1]);
```

### Обмеження даних, що вибираються з БД

Іноді під час пошуку набору сутностей потрібно обмежити обсяг даних, що дістаються (не отримувати всі колонки).
Для цього потрібно викликати метод `cols()` — він приймає масив колонок, які потрібно отримати.

Наприклад, у списку товарів не потрібно діставати опис і метадані всіх товарів:
```php
$products = $productsEntity->cols([
        'id',
        'name',
        'url',
        'special',
        'annotation',
    ])->find($filter);
```

### Отримати обʼєкт Select з Entity

Якщо потрібно отримати обʼєкт запиту, який будує Entity, його можна отримати за допомогою `YourEntity::getSelect()`
(мається на увазі `getSelect()` потрібного Entity). Він поверне обʼєкт запиту, який метод `find()` або `findOne()` відправив би
в базу. Майте на увазі: якщо ви перевизначаєте метод `find()` базового класу Entity, для коректної роботи `getSelect()` вам може
знадобитися також перевизначити `getSelect()` у цьому Entity.

Наприклад: якщо у вас метод перед виконанням батьківського методу додає до запиту ще якісь дані, це може знадобитися
повторити й для методу `getSelect()`.
```php
class ProductsEntity extends Entity
{

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

Метод `getSelect()` приймає масив фільтра так само, як методи `find()` або `findOne()`, і повертає повноцінний обʼєкт Select,
який можна надалі модифікувати та виконати.

Приклад:
```php
$query = $commentsEntity->getSelect(['type' => 'post', 'object_id' => $postsIds]);
$query->groupBy(['object_id'])->resetCols()->cols(["COUNT( DISTINCT id) as count", "object_id"]);

foreach ($query->results() as $result) {
    if (isset($posts[$result->object_id])) {
        $posts[$result->object_id]->comments_count = $result->count;
    }
}
```

### Debug запитів у класі Entity

Якщо потрібно побачити текст запиту Entity-класу, який іде в БД, можна перед викликом методу `find()` або `findOne()`
викликати метод `debug()` — і текст запиту буде виведено (обережно на production).

Приклад:

```php
$productsEntity->debug()->find($filter);
```

Такий запит буде виконано і виведено.
Також у класу Select можна викликати метод `debugPrint()`, який зробить те саме.

Приклад:
```php
$select = $productsEntity->getSelect($filter);
$select->debugPrint();

// те саме
$productsEntity->getSelect($filter)->debugPrint();
```

### Ліміт вибірки результатів

За замовчуванням усі SELECT-запити через класи Entity виконуються з SQL-лімітом, навіть якщо його явно не передали: для безпеки
він за замовчуванням встановлюється в 100. Але якщо вам потрібно отримати взагалі всі дані (і неважливо, скільки це рядків),
на свій ризик можна викликати метод `noLimit()` класу Entity перед викликом `find()`.

Наприклад:

```php
$productsEntity->noLimit()->find($filter);
```

Але будьте обережні: за великої кількості даних запит може суттєво “гальмувати”.
