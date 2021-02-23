# Создание модуля

Модули позволяют расширять функционал OkayCMS и вмешиваться в стандартный ход выполнения различных операций.
Каждый модуль включает в себя классы контроллеров, моделей, шаблоны отображения, изображения, CSS стили,
JS файлы, языковые файлы.
Такая инкапсуляция позволяет легко переносить модуль между приложениями на платформе OkayCMS.
Модуль в OkayCMS - это папка с определенной структурой данных внутри. 
Модуль обязательно должен располагаться в каталоге для модулей Okay/Modules/VendorModule/NameModule/
и иметь следующую структуру.

##### Структура файлов модуля

    .
    ├── Init
    |   ├── Init.php
    |   ├── module.json
    |   ├── routes.php
    |   ├── SmartyPlugins.php
    |   └── services.php
    ├── Backend
    |   ├── Controllers
    |   |   └── Файлы контроллеров бекенда модуля
    |   ├── design
    |   |   ├── html
    |   |   |   └── Файлы дизайна бекенда
    |   |   ├── css
    |   |   |   └── Файлы стилей бекенда
    |   |   ├── js
    |   |   |   └── Файлы стилей бекенда
    |   |   ├── images
    |   |   |   └── Файлы изображений бекенда
    |   |   ├── css.php
    |   |   └── js.php
    |   └── lang
    |       └── Файлы переводов бекенда
    ├── config
    |   └── config.php
    ├── Controllers
    |   └── Файлы контроллеров модуля
    ├── Entities
    |   └── Файлы сущностей модуля
    ├── Extenders
    |   └── Файлы экстендеров модуля
    ├── design
    |   ├── html
    |   |   └── Файлы дизайна
    |   ├── css
    |   |   └── Файлы стилей модуля
    |   ├── js
    |   |   └── Файлы скриптов модуля
    |   ├── images
    |   |   └── Файлы изображений модуля
    |   ├── lang
    |   |   └── Файлы переводов клиентской части модуля
    |   ├── css.php
    |   └── js.php
    ├── settings.xml
    └── preview.(jpeg|jpg|png|gif|svg)

##### Конфигурационные файлы модуля <a name="configuratinFiles"></a>

Файл `Init/Init.php` является самым главным конфигурационным файлом. Он обязательно должен унаследоваться от 
Okay\Core\Modules\AbstractInit.
В классе Init должны быть реализованы методы install() и init(). Базовый класс AbstractInit предоставляет средства
для инициализации модуля в системе. Метод install() выполняется один раз, во время установки модуля, метод init() 
вызывается при каждом запуске системы.
В методе install() стоит вызывать такие методы как setBackendMainController(), 
[migrateEntityTable()](./table_migrate.md), [setModuleType()](#typesOfModules).
[Пример инициализации модуля](./quick_start.md#InitInitphp) и [полное описание инициализации](./init.md).

Файл `Init/module.json` Файл содержащий мета информацию об модуле. [Подробнее](./module_json.md)

Файл `Init/routes.php` содержит роуты для текущего модуля. Структура файла полностью повторяет структуру 
[системных роутов](./../routes.md)

Файл `Init/services.php` <a name="Initservices"></a> содержит сервисы для текущего модуля.
Регистрация сервисов в модуле осуществляется также как и [системные сервисы](./../di_container.md#serviceRegister),
но в файле Init/services.php.
Все они должны быть частью [DI контейнера](./../di_container.md "Dependency injection container").

Файл `Init/SmartyPlugins.php` содержит Smarty плагины для текущего модуля.
Регистрация плагинов в модуле осуществляется также как и [системные Smarty плагины](./../smarty_plugins.md),
но в файле Init/SmartyPlugins.php.

Файл `preview.(jpeg|jpg|png|gif|svg)` может присутствовать в корневой директории модуля, он будет автоматически
отображаться в админ-панеле, в списке модулей.

Файл `settings.xml` нужен для модулей доставок и оплат. Файл содержит структуру настроек, которые нужно вывести
в админ-панели в способе доставки или способе оплаты. (Используется при [установке типа модуля](#typesOfModules)
MODULE_TYPE_PAYMENT или MODULE_TYPE_DELIVERY).

Файлы `design/js.php`, `design/css.php`, `Backend/design/js.php` и `Backend/design/css.php` нужны для [регистрации js
и css файлов](./../js_css_files.md).

Структура файла:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<module>
    <settings><!--Если указать больше одного <options> будет выведено как HTML select (выпадающий список)-->
        <variable>service_type</variable><!--Название переменной-->
        <name>{$lang->settings_np_service_type}</name><!--Название параметра (поддерживается из переводов)-->
        <options>
            <name>{$lang->settings_np_service_dd}</name>
            <value>DoorsDoors</value>
        </options>
        <options>
            <name>{$lang->settings_np_service_wd}</name>
            <value>WarehouseDoors</value>
        </options>
    </settings>
    <settings><!--Так будет выведено текстовое поле-->
        <variable>wayforpay_merchant</variable>
        <name>{$lang->way_for_pay_merchant}</name>
    </settings>
    <settings type="hidden|text|date|checkbox"><!--Так будет выведено как инпут указанного типа-->
        <variable>wayforpay_merchant</variable>
        <name>{$lang->way_for_pay_merchant}</name>
    </settings>
</module>
```

Файл `config/config.php` может содержать директивы, такие же как и в системном конфиге. Можно только добавлять
директивы, переопределять системные директивы нельзя.

##### Типы модулей <a name="typesOfModules"></a>

Тип модуля может влиять на некоторое его поведение в системе. На данный момент существуют такие типы модулей:
* MODULE_TYPE_PAYMENT - модуль оплаты
* MODULE_TYPE_DELIVERY - модуль доставки
* MODULE_TYPE_XML - модуль создающий выгрузку в xml файл.

Тип модуля можно установить в методе install() класса Init с помощью метода setModuleType (всегда используйте константы начинающиеся
на `MODULE_TYPE_`)
```php
$this->setModuleType(MODULE_TYPE_DELIVERY);
```
Например модуль с типом MODULE_TYPE_DELIVERY будет выводить настройки, определённые в файле `settings.xml` 
в админ-панеле в способе доставки. Также этот модуль будет выводиться в списке доступных модулей доставки 
[в настройках способа доставки](https://demookay.com/backend/index.php?controller=DeliveryAdmin).

[Модуль, быстрый старт](./quick_start.md)
