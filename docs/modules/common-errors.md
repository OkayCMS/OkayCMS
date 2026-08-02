# Типові помилки під час розробки модулів

## Коротка таблиця

| Помилка | Симптом | Швидке виправлення | Довідка |
|-------|---------|-----------|-----------|
| Ручна серіалізація Settings | `array given, string expected` | Приберіть ручні `serialize()` / `unserialize()` | [Сумісність модулів](compatibility.md) |
| Немає `return` у chain extension | AJAX-запит завершується без зрозумілої помилки | Поверніть отриманий результат | [Сумісність модулів](compatibility.md) |
| Не зареєстровані залежності extender | `Too few arguments to function __construct()` | Створіть `Init/services.php` | [Сумісність модулів](compatibility.md) |
| Валідація запускається не в той момент | Кнопка додавання в кошик не працює | Перевірте POST-параметр `checkout` | [Сумісність модулів](compatibility.md) |
| JS-файли лежать не в тій директорії | `File not found` | Для `addFrontBlock()` використовуйте `design/html/` | [Швидкий старт](quick_start.md) |
| Некоректний namespace контролера | `Class not exists` | Використовуйте `::class`, а не запис через крапки | [Контролери](../controllers.md) |
| Нестандартний backend UI | Сторінка адмін-панелі не узгоджується з системною | Дотримуйтеся стандартної структури | [Структура проєкту](../../../docs/project-structure.md) |
| Неправильна мова адмін-панелі | Переклади не оновлюються | Використовуйте `BackendTranslations::getLangLabel()` | [Сумісність модулів](compatibility.md) |
| Неправильна мова AJAX-запиту | Завжди використовується одна мова | Визначайте мову з referrer | [Сумісність модулів](compatibility.md) |
| Валюта не відповідає мові | Знак валюти завжди один | Встановіть мову перед отриманням валюти | [Сумісність модулів](compatibility.md) |
| Тексти налаштувань збережені у мовних файлах | Типові повідомлення не змінюються | Створіть Entity для повідомлень | [Сумісність модулів](compatibility.md) |
| Позиційні placeholders в Aura SQL | `Undefined array key` | Використовуйте named placeholders | [Патерни Aura SQL](../../../docs/patterns/aura-sql.md) |
| `bindValues` після `joinSubSelect` | `Undefined array key` | Викликайте `bindValues` перед `joinSubSelect` | [Патерни Aura SQL](../../../docs/patterns/aura-sql.md) |
| Поле Entity не зареєстроване | Поле відсутнє в SELECT-запитах | Викликайте `registerEntityField()` в `init()` | [Сумісність модулів](compatibility.md) |

## Пояснення

### Ручна серіалізація Settings

**Помилка:** `TypeError: array given, string expected`

**Причина:** `Settings::set()` сам серіалізує масиви. Ручний `serialize()` створює подвійну серіалізацію.

**Виправлення:**
```php
// Wrong
$this->settings->set('key', serialize($array));
$value = unserialize($this->settings->get('key'));

// Correct
$this->settings->set('key', $array);
$value = $this->settings->get('key');
```

### Немає `return` у chain extension

**Помилка:** AJAX-запити завершуються без зрозумілої помилки, виконання chain зупиняється.

**Причина:** Chain extension має повернути результат, щоб наступні extension працювали з очікуваним значенням.

**Виправлення:**
```php
// Wrong
class WrongExtender
{
    public function extendAddItem($cart)
    {
        $this->checkSomething($cart);
    }
}

// Correct
class CorrectExtender
{
    public function extendAddItem($cart)
    {
        $this->checkSomething($cart);

        return $cart;
    }
}
```

### Не зареєстровані залежності extender

**Помилка:** `Too few arguments to function __construct(), 0 passed`

**Причина:** Extender із залежностями в конструкторі має бути зареєстрований у `Init/services.php`.

**Виправлення:** створіть `Init/services.php`:
```php
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    YourExtender::class => [
        'class' => YourExtender::class,
        'arguments' => [
            new SR(Settings::class),
            // ... all dependencies
        ],
    ],
];
```

### Валідація запускається не в той момент

**Помилка:** кнопка додавання в кошик не працює, бо валідація запускається занадто рано.

**Причина:** checkout-валідація має запускатися під час оформлення замовлення, а не під час додавання товару в кошик.

**Виправлення:**
```php
public function extendGetCartValidateError($error): ?string
{
    if (!empty($error)) {
        return $error;
    }
    
    // Validate only during checkout.
    if (!$this->request->post('checkout')) {
        return $error;
    }
    
    // ... validation logic
}
```

### JS-файли лежать не в тій директорії

**Помилка:** `File not found` під час використання `addFrontBlock()`.

**Причина:** файл, який передається в `addFrontBlock()`, має лежати в `design/html/`, а не в `design/js/`.

**Виправлення:** перенесіть файл із `design/js/script.js` до `design/html/script.js`.

### Позиційні placeholders в Aura SQL

**Помилка:** `Undefined array key` в `AbstractQuery.php:437`.

**Причина:** позиційні placeholders `?` з числовими індексами масиву працюють некоректно в Aura SQL 6.x.

**Виправлення:** використовуйте named placeholders:
```php
// Wrong
$this->select->where("field = ? AND id IN (?)", ...$binds);

// Correct
$placeholder = "filter_id_" . (int)$id;
$this->select->where("field = :{$placeholder}")
    ->bindValue($placeholder, (int)$id);
```

**Детальніше:** [Патерни Aura SQL](../../../docs/patterns/aura-sql.md).

### Поле Entity не зареєстроване

**Помилка:** поле відсутнє в SELECT-запитах, `get()` повертає `null`.

**Причина:** поля, додані через `migrateEntityField()`, потрібно зареєструвати в `init()`.

**Виправлення:**
```php
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\VariantsEntity;

class Init extends AbstractInit
{
    public function install()
    {
        $field = (new EntityField('field_name'))->setTypeVarchar(255);

        $this->migrateEntityField(VariantsEntity::class, $field);
    }

    public function init()
    {
        $this->registerEntityField(VariantsEntity::class, 'field_name');
    }
}
```
