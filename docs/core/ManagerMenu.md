# Класс Okay\Core\ManagerMenu

Данный класс предназначен для работы с меню менеджера в админ-панеле и в клиентской части меню быстрого редактирования.

<a name="addCounter"></a>
```php
addCounter( string $menuItemTitle, int $counter)
```

Добавления счетчика новых событий в 
[админ-меню](./../dev_mode.md#backendMenu)

Аргумент | Описание
---|---
$menuItemTitle | [Название пункта меню](./../dev_mode.md#backendMenu), в который стоит добавить счётчик событий. К группе меню счётчик добавляется автоматически.
$counter | Количество новых событий, которое нужно вывести в меню.

Для добавления счетчика, следует создать [экстендер](./../modules/extenders.md), который расширит [хелпер](./../helpers.md) 
`Okay\Admin\Helpers\BackendMainHelper::evensCounters()`.

Пример экстендера:
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

Пример инициализации:
```php
class Init extends AbstractInit
{
    public function init()
    {
        // ...abstract
        $this->registerChainExtension(
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

Добавление элементов в меню быстрого редактирования (admintooltip).

Аргумент | Описание
---|---
$dataProperty | data-атрибут по которому нужно открыть именно это меню
... | Двумерный массив с описанием ссылок, которые стоит добавить в меню.

Описание ссылки должно быть в виде ассоциативного массива.
Параметры:

Параметр | Описание
---|---
controller | Название контроллера на который нужно перевести пользователя в админ-панеле. Обратите внимание, контроллеры модулей в админ-панеле именуются как Vendor.Module.Controller
translation | Название перевода из админ-панели
params | Ассоциативный массив, где ключ имя GET параметра, который нужно добавить, значение - название js переменной, значение которой нужно подставить. На данный момент поддерживается только id (значение указанное в атрибуте data-...)
action | Вариант стилизации ссылки. Возможные значения: edit, add. Если передали params['id'], система по умолчанию установит action=edit

Пример добавления элемента меню быстрого редактирования:

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