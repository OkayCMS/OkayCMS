# Клас Okay\Core\ManagerMenu

Цей клас призначений для роботи з меню менеджера в адмін-панелі та в клієнтській частині — меню швидкого редагування.

<a name="addCounter"></a>
```php
addCounter( string $menuItemTitle, int $counter)
```

Додавання лічильника нових подій у
[адмін-меню](./../dev_mode.md#backendMenu)

Аргумент | Опис
---|---
$menuItemTitle | [Назва пункту меню](./../dev_mode.md#backendMenu), до якого слід додати лічильник подій. До групи меню лічильник додається автоматично.
$counter | Кількість нових подій, яку потрібно вивести в меню.

Щоб додати лічильник, слід створити [extender](./../modules/extenders.md), який розширить [helper](./../helpers.md)
`Okay\Admin\Helpers\BackendMainHelper::evensCounters()`.

Приклад extender:
```php
class BackendExtender implements ExtensionInterface
{
    private $managerMenu;
    private $entityFactory;
    
    public function __construct(ManagerMenu $managerMenu, EntityFactory $entityFactory)
    {
        $this->managerMenu = $managerMenu;
        $this->entityFactory = $entityFactory;
    }

    public function setNewEventsProcedure()
    {
        /** @var SomeEntity $someEntity */
        $someEntity = $this->entityFactory->get(SomeEntity::class);
        $this->managerMenu->addCounter('left_custom_form_data_title', $someEntity->count(['processed' => 0]));
    }
}
```

Приклад ініціалізації:
```php
class Init extends AbstractInit
{
    public function init()
    {
        // ...abstract
        $this->registerQueueExtension(
            ['class' => BackendMainHelper::class, 'method' => 'evensCounters'],
            ['class' => BackendExtender::class, 'method' => 'setNewEventsProcedure']
        );
    }
}
```

<a name="addFastMenuItem"></a>
```php
addFastMenuItem( string $dataProperty,  array $...)
```

Додавання елементів у меню швидкого редагування (admintooltip).

Аргумент | Опис
---|---
$dataProperty | data-атрибут, за яким потрібно відкрити саме це меню
... | Двовимірний масив з описом посилань, які слід додати в меню.

Опис посилання має бути у вигляді асоціативного масиву.
Параметри:

Параметр | Опис
---|---
controller | Назва контролера, на який потрібно перенаправити користувача в адмін-панелі. Зверніть увагу: контролери модулів в адмін-панелі мають формат Vendor.Module.Controller
translation | Назва перекладу з адмін-панелі
params | Асоціативний масив: ключ — ім’я GET-параметра, який потрібно додати; значення — назва JS-змінної, значення якої потрібно підставити. Наразі підтримується лише `id` (значення, вказане в атрибуті data-...)
action | Варіант стилізації посилання. Можливі значення: edit, add. Якщо передали params['id'], система за замовчуванням встановить action=edit

Приклад додавання елемента меню швидкого редагування:

```php
class Init extends AbstractInit
{
    public function init()
    {
        // ...abstract
        $this->addFastMenuItem('property', [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_add',
        ], [
            'controller' => 'Vendor.Module.Controller',
            'translation' => 'translation_var_edit',
            'params' => [
                'id' => 'id',
            ],
            'action' => 'edit',
        ]);
    }
}
```
