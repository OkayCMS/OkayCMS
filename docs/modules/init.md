# Ініціалізація модуля (клас Init)

Клас `Init\Init` є головним конфігураційним класом. Він обовʼязково має наслідуватися від
`Okay\Core\Modules\AbstractInit`.
У класі `Init\Init` мають бути реалізовані методи `install()` і `init()`. Базовий клас `Okay\Core\Modules\AbstractInit`
надає засоби для ініціалізації модуля в системі.
Метод `install()` виконується один раз під час встановлення модуля, метод `init()` викликається під час кожного запуску системи.

## Оновлення модуля

Опціонально в класі `Init\Init` можна описувати методи з назвою виду `update_1_2_0()`. Ці методи будуть
виконуватися під час оновлення модуля в порядку зростання версії. Коли встановлена в системі версія модуля нижча за
вказану в [module.json](./module_json.md) у властивості version, у списку модулів в admin-частині пропонується його оновити.
Коли користувач натисне “Оновити модуль”, виконаються всі методи для версій модуля, вищих за поточну встановлену, і нижчих або рівних
версії, вказаної у властивості version файла [module.json](./module_json.md).

Для отримання залежностей у методах оновлення можна використовувати [service locator](./../service_locator.md).

Для виконання SQL-запитів потрібно отримати екземпляр одного з класів `Okay\Core\QueryFactory\Insert`,
`Okay\Core\QueryFactory\Select`, `Okay\Core\QueryFactory\Update` , `Okay\Core\QueryFactory\Delete`
або `Okay\Core\QueryFactory\SqlQuery`.

### Методи класу AbstractInit

<a name="registerChainExtension"></a>
```php
registerChainExtension( array $expandable, array $extension)
```
Реєструє [extender](./extenders.md) у режимі Chain.
Викликати в методі `init()`.

Аргумент | Опис
---|---
$expandable | масив із двох елементів: імʼя класу [helper](./../helpers.md) або [request](./../requests.md) і його метод, який потрібно розширити.
$extension | масив із двох елементів: імʼя класу [extender](./extenders.md) і його метод, яким потрібно розширити метод helper.


<a name="registerQueueExtension"></a>
```php
registerQueueExtension( array $expandable, array $extension)
```

Реєструє [extender](./extenders.md) у режимі Queue.
Викликати в методі `init()`.

Аргумент | Опис
---|---
$expandable | масив із двох елементів: імʼя класу [helper](./../helpers.md) або [request](./../requests.md) і його метод, який потрібно розширити.
$extension | масив із двох елементів: імʼя класу [extender](./extenders.md) і його метод, яким потрібно розширити метод helper.


<a name="migrateEntityTable"></a>
```php
migrateEntityTable( string $entityClassName, array $fields)
```

Створення таблиці нового [Entity](./../entities.md) модуля. [Приклад оновлення таблиці](./table_migrate.md).
Викликати в методі `install()`.

Аргумент | Опис
---|---
$entityClassName | повне імʼя класу Entity
$fields | масив екземплярів класу [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="migrateEntityField"></a>
```php
migrateEntityField( string $entityClassName, EntityField $field)
```

Додавання додаткових полів у БД до наявних [сутностей](./../entities.md). Викликати в методі `install()`.

Аргумент | Опис
---|---
$entityClassName | повне імʼя наявного класу Entity
$field | екземпляр класу [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="migrateCustomTable"></a>
```php
migrateCustomTable( string $tableName, array $fields)
```

Створення таблиці в БД. Здебільшого використовується для створення таблиць звʼязків. Викликати в методі `install()`.

Аргумент | Опис
---|---
$tableName | назва таблиці, яку потрібно створити (без префіксів "ok_" або "__")
$fields | масив екземплярів класу [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="registerEntityField"></a>
```php
registerEntityField( string $entityClassName, string $fieldName[, bool $isLang = false])
```

Реєстрація додаткових полів для наявних сутностей.
У базу не додаються — лише беруть участь у select і фільтрації. Викликати в методі `init()`.

Аргумент | Опис
---|---
$entityClassName | Повне імʼя класу наявного [Entity](./../entities.md)
$fieldName | Назва колонки, яку варто додати в [Entity](./../entities.md)
$isLang | чи є це поле мультимовним


<a name="registerEntityFilter"></a>
```php
registerEntityFilter( string $entityClassName, string $filterName, string $filterClassName, string $filterMethod)
```

Реєстрація [користувацького фільтра для наявних](./../entities.md#usersFiltersFromModules) у
системі [Entities](./../entities.md). Викликати в методі `init()`.

Аргумент | Опис
---|---
$entityClassName | Повне імʼя класу наявного [Entity](./../entities.md), для якого реєструється новий фільтр
$filterName | Імʼя нового фільтра, яке використовуватиметься в масиві разом з іншими фільтрами
$filterClassName | Клас, у якому описана реалізація нового фільтра
$filterMethod | Метод, що описує реалізацію нового фільтра


<a name="registerBackendController"></a>
```php
registerBackendController( string $controllerClass)
```

Додавання [backend-контролера](./../controllers.md#backendControllersModules) до загального списку контролерів.
Викликати в методі `init()`.

Аргумент | Опис
---|---
$controllerClass | Імʼя класу backend-контролера


<a name="setBackendMainController"></a>
```php
setBackendMainController( string $className)
```

Встановлення [backend-контролера](./../controllers.md#backendControllersModules), який в admin-частині оброблятиметься як
основний (коли зі списку модулів відбувається перехід усередину модуля — потрапляємо на цей контролер).
Викликати в методі `install()`.

Аргумент | Опис
---|---
$className | Імʼя класу backend-контролера


<a name="addBackendControllerPermission"></a>
```php
addBackendControllerPermission( string $controllerClass, string $permission)
```

Додавання звʼязки permission для admin і [backend-контролера](./../controllers.md#backendControllersModules).
Викликати в методі `init()`.

Аргумент | Опис
---|---
$controllerClass | Імʼя класу backend-контролера
$permission | Назва permission


<a name="addPermission"></a>
```php
addPermission( string $permission)
```

Додавання permission у загальний масив permissions для менеджерів.
Використовуйте, якщо потрібен permission, але [backend-контролера](./../controllers.md#backendControllersModules)
для нього немає. Викликати в методі `init()`.

Аргумент | Опис
---|---
$permission | Назва permission


<a name="setModuleType"></a>
```php
setModuleType( string $type)
```

Встановлення [типу модуля](./README.md#typesOfModules). Викликати в методі `install()`.

Аргумент | Опис
---|---
$type | Тип модуля. Константи типів починаються на MODULE_TYPE_. [Типи модулів](./README.md#typesOfModules).


<a name="extendBackendMenu"></a>
```php
extendBackendMenu( string $firstLevelName, array $menuItemsByControllers[, string $icon = null])
```

Додати новий пункт меню в admin-частині.
Викликати в методі `init()`.

Аргумент | Опис
---|---
$firstLevelName | [Назва групи меню](./../dev_mode.md#backendMenu), до якої слід додати новий пункт. Якщо вказати неіснуючу групу — буде створено нову.
$menuItemsByControllers | Масив: ключ — назва пункту меню, і має існувати переклад із такою ж назвою. Значення — масив назв [backend-контролерів](./../controllers.md#backendControllersModules), які будуть у цьому пункті меню (зазвичай це контролер списку записів і редагування одного запису).
$icon | Іконка групи меню. Варто використовувати, якщо створюєте нову групу. Як значення може бути SVG-код або шлях до зображення відносно директорії `Okay/Modules/Vendor/Module/` (напр. 'Backend/design/images/menu_logo.png').

Якщо додали новий пункт меню — потрібно обовʼязково додати переклад для admin-частини з такою ж назвою, як і пункт меню.

Приклад Init:
```php
$this->extendBackendMenu('left_faq_title', [
    'left_faq_menu_item' => ['FAQsAdmin', 'FAQAdmin']
],
'Backend/design/images/faq_icon.png');
```
Переклади:
```php
$lang['left_faq_title'] = 'FAQ';
$lang['left_faq_menu_item'] = 'FAQ Item';
```


<a name="addResizeObject"></a>
```php
addResizeObject( string $originalImgDirDirective, string $resizedImgDirDirective)
```

Додавання resize для сутностей.
Якщо ваш модуль передбачає нарізання зображень, яких у системі раніше не було, потрібно додати в систему інформацію про це.
У такому разі не забудьте також у методі install створити директорію для зображень (через `mkdir()`).
Викликати в методі `init()`.

Аргумент | Опис
---|---
$originalImgDirDirective | Назва директиви з [конфіга модуля](./README.md), яка містить шлях до директорії оригіналів зображень
$resizedImgDirDirective | Назва директиви з [конфіга модуля](./README.md), яка містить шлях до директорії нарізок зображень

Приклад Init:
```php
class Init extends AbstractInit
{   
    public function install()
    {
        if (!is_dir('files/originals/slides')) {
            mkdir('files/originals/slides');
        }
        
        if (!is_dir('files/resized/slides')) {
            mkdir('files/resized/slides');
        }
        // ...abstract
    }
    
    public function init()
    {
        // ...abstract
        $this->addResizeObject('banners_images_dir', 'resized_banners_images_dir');
    }
}
```
Приклад конфіга:
```ini
banners_images_dir = files/originals/slides/
resized_banners_images_dir = files/resized/slides/
```


<a name="extendUpdateObject"></a>
```php
extendUpdateObject( string $alias, string $permission, string $entityClassName)
```

Метод розширює колекцію обʼєктів, доступну для використання у файлі `ajax/update_object.php`, який оновлює сутність,
визначену за alias, через AJAX-запит з admin-частини сайту.

Аргумент | Опис
---|---
$alias | Унікальний псевдонім, що ідентифікує сутність (вказується в атрибуті `data-controller=\"alias\"` тега в admin-частині)
$permission | Permission доступу до псевдоніма для менеджера, доданий через [addBackendControllerPermission](#addBackendControllerPermission) або [addPermission](#addPermission)
$entityClassName | Повне імʼя [сутності](./../entities.md), яка буде оновлюватися.

Приклад Init:
```php
class Init extends AbstractInit
{
    const PERMISSION = 'okaycms_banners';
    // ...abstract
    public function init()
    {
        // ...abstract
        $this->addBackendControllerPermission('BannersAdmin', self::PERMISSION);
        $this->extendUpdateObject('okay_cms__banners', self::PERMISSION, BannersEntity::class);
    }
}
```

Приклад banners.tpl (додаємо data-controller):
```smarty
// ...abstract
{foreach $banners as $banner}
    <div class="fn_row okay_list_body_item fn_sort_item">
        <div class="okay_list_row">
            <div class="okay_list_boding okay_list_features_name">
                <a class="link" href="{url controller=[OkayCMS,Banners,BannerAdmin] id=$banner->id return=$smarty.server.REQUEST_URI}">
                    {$banner->name|escape}
                </a>
            </div>
            // ...abstract
            <div class="okay_list_boding okay_list_status">
                {*visible*}
                <div class="col-lg-4 col-md-3">
                    <label class="switch switch-default">
                        <input class="switch-input fn_ajax_action {if $banner->visible}fn_active_class{/if}" data-controller="okay_cms__banners" data-action="visible" data-id="{$banner->id}" name="visible" value="1" type="checkbox"  {if $banner->visible}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
{/foreach}
// ...abstract
```

<a name="addBackendBlock"></a>
```php
addBackendBlock( string $blockName, string $blockTplFile, callable $callback = null)
```

Додавання [шорт-блока](./../dev_mode.md#shortBLock)
в admin-частину сайту.

Аргумент | Опис
---|---
$blockName | Імʼя [шорт-блока](./../dev_mode.md#shortBLock) admin-частини.
$blockTplFile | Шлях до tpl файла (відносно директорії `Okay/Modules/Vendor/Module/Backend/design/html/`), у якому розміщується верстка блока. У блоці працюємо так, наче його додадуть у основний файл через include (усі змінні підтримуються).
$callback | Функція, яку потрібно викликати перед відмальовуванням шорт-блока. Може використовуватися для передачі в дизайн даних, потрібних для відмальовування. Можна вказувати аргументи з type hint (Services, Entities тощо).


<a name="addFrontBlock"></a>
```php
addFrontBlock( string $blockName, string $blockTplFile, callable $callback = null)
```

Додавання [шорт-блока](./../dev_mode.md#shortBLock)
на клієнтську частину сайту.

Аргумент | Опис
---|---
$blockName | Імʼя [шорт-блока](./../dev_mode.md#shortBLock) клієнтської частини сайту.
$blockTplFile | Шлях до tpl файла (відносно директорії `Okay/Modules/Vendor/Module/design/html/`), у якому розміщується верстка блока. У блоці працюємо так, наче його додадуть у основний файл через include (усі змінні підтримуються).
$callback | Функція, яку потрібно викликати перед відмальовуванням шорт-блока. Може використовуватися для передачі в дизайн даних, потрібних для відмальовування. Можна вказувати аргументи з type hint (Services, Entities тощо).


<a name="registerPurchaseDiscountSign"></a>
```php
registerPurchaseDiscountSign( string $sign, string $name, string $description)
```

Реєстрація знака знижки для позиції.\
Викликати в методі `init()`.

Аргумент | Опис
---|---
$sign|Знак знижки. Має бути унікальним у межах обох сутностей (кошик і позиція кошика).
$name|Назва знижки. Потрібна як підказка адміністратору. Є мовною змінною.
$description|Опис знижки. Потрібний як підказка адміністратору. Є backend мовною змінною.


<a name="registerCartDiscountSign"></a>
```php
registerCartDiscountSign( string $sign, string $name, string $description)
```

Реєстрація знака знижки кошика.\
Викликати в методі `init()`.

Аргумент | Опис
---|---
$sign|Знак знижки. Має бути унікальним у межах обох сутностей (кошик і позиція кошика).
$name|Назва знижки. Потрібна як підказка адміністратору. Є мовною змінною.
$description|Опис знижки. Потрібний як підказка адміністратору. Є backend мовною змінною.
