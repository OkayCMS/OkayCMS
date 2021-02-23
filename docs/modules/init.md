# Инициализация модуля (класс Init)

Класс `Init\Init` является самым главным конфигурационным классом. Он обязательно должен наследоваться от 
`Okay\Core\Modules\AbstractInit`.
В классе `Init\Init` должны быть реализованы методы install() и init(). Базовый класс `Okay\Core\Modules\AbstractInit` 
предоставляет средства для инициализации модуля в системе. 
Метод install() выполняется один раз, во время установки модуля, метод init() вызывается при каждом запуске системы.

## Обновление модуля

Опционально в классе `Init\Init` можно описывать методы, с названием вида `update_1_2_0()`. Данные методы будут 
выполняться при обновлении модуля в порядке возрастания версии. Когда установленная в системе версия модуля ниже чем 
указанная в [module.json](./module_json.md) в свойстве version, в списке модулей в админ части предлагается его обновить.
Когда пользователь нажмет обновить модуль, выполнятся все методы, для версии модуля выше текущей установленной и ниже
версии, указанной в свойстве version файла [module.json](./module_json.md).

Для получения зависимостей в методах обновления можно использовать [локатор служб](./../service_locator.md).

Для выполнения SQL запросов нужно получить экземпляр одного из классов `Okay\Core\QueryFactory\Insert`, 
`Okay\Core\QueryFactory\Select`, `Okay\Core\QueryFactory\Update` , `Okay\Core\QueryFactory\Delete` 
 или `Okay\Core\QueryFactory\SqlQuery`.

### Методы класса AbstractInit

<a name="registerChainExtension"></a>
```php
registerChainExtension( array $expandable, array $extension)
```
Регистрирует [экстендер](./extenders.md) в режиме Chain.
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$expandable | массив из двух элементов, имени класса [хелпера](./../helpers.md) или [реквеста](./../requests.md) и его метода, который нужно расширить.
$extension | массив из двух элементов, имени класса [экстендера](./extenders.md) и его метода, каким нужно расширить метод хелпера.


<a name="registerQueueExtension"></a>
```php
registerQueueExtension( array $expandable, array $extension)
```

Регистрирует [экстендер](./extenders.md) в режиме Queue.
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$expandable | массив из двух элементов, имени класса [хелпера](./../helpers.md) или [реквеста](./../requests.md) и его метода, который нужно расширить.
$extension | массив из двух элементов, имени класса [экстендера](./extenders.md) и его метода, каким нужно расширить метод хелпера.


<a name="migrateEntityTable"></a>
```php
migrateEntityTable( string $entityClassName, array $fields)
```

Создание таблицы нового [Entity](./../entities.md) модуля. [Пример миграции](./table_migrate.md). 
Вызывать в методе `install()`.

Аргумент | Описание
---|---
$entityClassName | полное имя класса Entity
$fields | массив экземпляров класса [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="migrateEntityField"></a>
```php
migrateEntityField( string $entityClassName, EntityField $field)
```

Добавление дополнительных полей в БД к существующим [сущностям](./../entities.md). Вызывать в методе `install()`.

Аргумент | Описание
---|---
$entityClassName | полное имя существующего класса Entity
$field | экземпляр класса [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="migrateCustomTable"></a>
```php
migrateCustomTable( string $tableName, array $fields)
```

Создание таблицы в БД. В основном используется для создания таблиц связей. Вызывать в методе `install()`.

Аргумент | Описание
---|---
$tableName | название таблицы, которую нужно создать (без префиксов "ok_" или "__")
$fields | массив экземпляров класса [Okay\Core\Modules\EntityField](./table_migrate.md#EntityField)


<a name="registerEntityField"></a>
```php
registerEntityField( string $entityClassName, string $fieldName[, bool $isLang = false])
```

Регистрация дополнительных полей к существующим сущностям.
В базу не добавляются, только учавствуют в селекте и фильтрации. Вызывать в методе `init()`.

Аргумент | Описание
---|---
$entityClassName | Полное имя класса существующего [Entity](./../entities.md)
$fieldName | Название колонки, которую стоит добавить в [Entity](./../entities.md)
$isLang | является ли это поле ленговым


<a name="registerEntityFilter"></a>
```php
registerEntityFilter( string $entityClassName, string $filterName, string $filterClassName, string $filterMethod)
```

Регистрация [пользовательского фильтра для уже существующих](./../entities.md#usersFiltersFromModules) в 
системе [Entities](./../entities.md). Вызывать в методе `init()`.

Аргумент | Описание
---|---
$entityClassName | Полное имя класса существующего [Entity](./../entities.md), для которого регистрируется новый фильтр
$filterName | Имя нового фильтра, которое будет использоваться в массиве совместно с остальными фильтрами
$filterClassName | Класс, в котором описана реализация нового фильтра
$filterMethod | Метод описывающий реализацию нового фильтра


<a name="registerBackendController"></a>
```php
registerBackendController( string $controllerClass)
```

Добавление [бек-контроллера](./../controllers.md#backendControllersModules) в общий список контроллеров. 
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$controllerClass | Имя класса бек-контроллера


<a name="setBackendMainController"></a>
```php
setBackendMainController( string $className)
```

Установка [бек-контроллер](./../controllers.md#backendControllersModules), который будет в админке обрабатываться как 
основной (когда со списка модулей происходит переход внутрь модуля, попадаем на этот контроллер).
Вызывать в методе `install()`.

Аргумент | Описание
---|---
$className | Имя класса бек-контроллера


<a name="addBackendControllerPermission"></a>
```php
addBackendControllerPermission( string $controllerClass, string $permission)
```

Добавление связки разрешения для админа и [бек-контроллера](./../controllers.md#backendControllersModules).
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$controllerClass | Имя класса бек-контроллера
$permission | Название разрешения


<a name="addPermission"></a>
```php
addPermission( string $permission)
```

Добавление разрешения, в общий массив разрешений для менеджеров.
Нужно использовать если нужно разрешение, но [бек-контроллера](./../controllers.md#backendControllersModules) 
для него нет. Вызывать в методе `init()`.

Аргумент | Описание
---|---
$permission | Название разрешения


<a name="setModuleType"></a>
```php
setModuleType( string $type)
```

Установка [типа модуля](./README.md#typesOfModules). Вызывать в методе `install()`.

Аргумент | Описание
---|---
$type | Тип модуля. Константы типов начинаются на MODULE_TYPE_. [Типы модулей](./README.md#typesOfModules).


<a name="extendBackendMenu"></a>
```php
extendBackendMenu( string $firstLevelName, array $menuItemsByControllers[, string $icon = null])
```

Добавить новый пункт
меню в админ-части.
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$firstLevelName | [Название группы меню](./../dev_mode.md#backendMenu), в которую стоит добавить новый пункт. Если указать несуществующую группу, тогда создастся новая.
$menuItemsByControllers | Массив, в котором ключ является названием пункта меню, и должен быть перевод с таким же названием. В виде значения должен быть массив названий [бек-контроллеров](./../controllers.md#backendControllersModules), которые будут в этом пункте меню (обычно это контроллер списка записей и редактирования одной записи).
$icon | Иконка группы меню. Стоит использовать если создаёте новую группу. В виде значения может быть код SVG изображения, или же путь к изображению, относительно директории `Okay/Modules/Vendor/Module/` (напр. 'Backend/design/images/menu_logo.png').

Если указать новый пункт меню, нужно обязательно добавить перевод для админ части, с таким же названием как и пункт 
меню.

Пример Init:
```php
$this->extendBackendMenu('left_faq_title', [
    'left_faq_menu_item' => ['FAQsAdmin', 'FAQAdmin']
],
'Backend/design/images/faq_icon.png');
```
Переводы:
```php
$lang['left_faq_title'] = 'FAQ';
$lang['left_faq_menu_item'] = 'FAQ Item';
```


<a name="addResizeObject"></a>
```php
addResizeObject( string $originalImgDirDirective, string $resizedImgDirDirective)
```

Добавление ресайза сущностей.
Если ваш модуль подразумевает что будут нарезаться изображения, которых в системе ранее не было, то нужно добавить в 
систему информацию об этом. Не забыть в таком случае еще в методе install создать директорию для изображений 
(через функцию mkdir()).
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$originalImgDirDirective | Название директивы из [конфига модуля](./README.md), которая содержит путь к директории оригиналов изображений
$resizedImgDirDirective | Название директивы из [конфига модуля](./README.md), которая содержит путь к директории нарезок изображений

Пример Init:
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
Пример конфига:
```ini
banners_images_dir = files/originals/slides/
resized_banners_images_dir = files/resized/slides/
```


<a name="extendUpdateObject"></a>
```php
extendUpdateObject( string $alias, string $permission, string $entityClassName)
```

Метод расширяет коллекцию объектов 
доступную для использования в файле ajax/update_object.php, который обновляет определенную по алиасу сущность 
повредством AJAX запроса из админ панели сайта.

Аргумент | Описание
---|---
$alias | Уникальный псевдоним, который идентифицирует сущность (указывается в атрибуте data-controller="алиас" тега в админ панели)
$permission | Название разрешения доступа к псевдониму для менеджера, добавленые через [addBackendControllerPermission](#addBackendControllerPermission) или [addPermission](#addPermission)
$entityClassName | Полное имя [сущности](./../entities.md), которая будет обновляться.

Пример Init:
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

Пример banners.tpl (добавляем data-controller):
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

Добавление [шорт-блока](./../dev_mode.md#shortBLock)
в админ-панель сайта.

Аргумент | Описание
---|---
$blockName | Имя [шорт-блока](./../dev_mode.md#shortBLock) админ-панели.
$blockTplFile | Путь к tpl файлу (относительно директории `Okay/Modules/Vendor/Module/Backend/design/html/`), в котором размещается верстка блока. В блоке работаем, как будто его добавят в основной файл через include (все переменные поддерживаются).
$callback | Ф-ция которую нужно вызвать перед отрисовкой шортблока. Может использоваться для передачи в дизайн данных, нужных для отрисовки шортблока. Можно указывать как аргументы с указанием type hint Services, Entities etc.


<a name="addFrontBlock"></a>
```php
addFrontBlock( string $blockName, string $blockTplFile, callable $callback = null)
```

Добавление [шорт-блока](./../dev_mode.md#shortBLock)
на клиентскую часть сайта.

Аргумент | Описание
---|---
$blockName | Имя [шорт-блока](./../dev_mode.md#shortBLock) клиентской части сайта.
$blockTplFile | Путь к tpl файлу (относительно директории `Okay/Modules/Vendor/Module/design/html/`), в котором размещается верстка блока. В блоке работаем, как будто его добавят в основной файл через include (все переменные поддерживаются).
$callback | Ф-ция которую нужно вызвать перед отрисовкой шортблока. Может использоваться для передачи в дизайн данных, нужных для отрисовки шортблока. Можно указывать как аргументы с указанием type hint Services, Entities etc.


<a name="registerPurchaseDiscountSign"></a>
```php
registerPurchaseDiscountSign( string $sign, string $name, string $description)
```

Регистрация знака скидки для позиции.\
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$sign|Знак скидки. Должен быть уникальным в рамках обеих сущностей(корзина и позиция корзины).
$name|Название скидки. Необходимо для подсказки администратору. Представляет собой языковую переменную.
$description|Описание скидки. Необходимо для подсказки администратору. Представляет собой backend языковую переменную.


<a name="registerCartDiscountSign"></a>
```php
registerCartDiscountSign( string $sign, string $name, string $description)
```

Регистрация знака скидки корзины.\
Вызывать в методе `init()`.

Аргумент | Описание
---|---
$sign|Знак скидки. Должен быть уникальным в рамках обеих сущностей(корзина и позиция корзины).
$name|Название скидки. Необходимо для подсказки администратору. Представляет собой языковую переменную.
$description|Описание скидки. Необходимо для подсказки администратору. Представляет собой backend языковую переменную.