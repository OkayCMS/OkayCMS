# Створення модуля

Модулі дозволяють розширювати функціональність OkayCMS і втручатися у стандартний хід виконання різних операцій.
Кожен модуль включає класи контролерів, моделей, шаблони відображення, зображення, CSS-стилі,
JS-файли, мовні файли.
Така інкапсуляція дозволяє легко переносити модуль між застосунками на платформі OkayCMS.
Модуль в OkayCMS — це папка з певною структурою даних усередині.
Модуль обовʼязково має розташовуватися в каталозі модулів `Okay/Modules/VendorModule/NameModule/`
та мати таку структуру.

##### Структура файлів модуля

    .
    ├── Init
    |   ├── Init.php
    |   ├── module.json
    |   ├── routes.php
    |   ├── SmartyPlugins.php
    |   └── services.php
    ├── Backend
    |   ├── Controllers
    |   |   └── Файли backend-контролерів модуля
    |   ├── design
    |   |   ├── html
    |   |   |   └── Файли дизайну backend
    |   |   ├── css
    |   |   |   └── Файли стилів backend
    |   |   ├── js
    |   |   |   └── Файли скриптів backend
    |   |   ├── images
    |   |   |   └── Файли зображень backend
    |   |   ├── css.php
    |   |   └── js.php
    |   └── lang
    |       └── Файли перекладів backend
    ├── config
    |   └── config.php
    ├── Controllers
    |   └── Файли контролерів модуля
    ├── Entities
    |   └── Файли сутностей модуля
    ├── Extenders
    |   └── Файли extender модуля
    ├── design
    |   ├── html
    |   |   └── Файли дизайну
    |   ├── css
    |   |   └── Файли стилів модуля
    |   ├── js
    |   |   └── Файли скриптів модуля
    |   ├── images
    |   |   └── Файли зображень модуля
    |   ├── lang
    |   |   └── Файли перекладів клієнтської частини модуля
    |   ├── css.php
    |   └── js.php
    ├── settings.xml
    └── preview.(jpeg|jpg|png|gif|svg)

##### Конфігураційні файли модуля <a name="configuratinFiles"></a>

Файл `Init/Init.php` є головним конфігураційним файлом. Він обовʼязково має наслідувати `Okay\Core\Modules\AbstractInit`.
У класі Init мають бути реалізовані методи `install()` і `init()`. Базовий клас AbstractInit надає засоби для ініціалізації модуля в системі.
Метод `install()` виконується один раз під час встановлення модуля, метод `init()` викликається під час кожного запуску системи.
У методі `install()` варто викликати методи на кшталт `setBackendMainController()`,
[migrateEntityTable()](./table_migrate.md), [setModuleType()](#typesOfModules).
Див. [приклад ініціалізації модуля](./quick_start.md#InitInitphp) і [повний опис ініціалізації](./init.md).

Файл `Init/module.json` містить мета-інформацію про модуль. [Докладніше](./module_json.md)

Файл `Init/routes.php` містить маршрути поточного модуля. Структура файла повністю повторює структуру
[системних маршрутів](./../routes.md)

Файл `Init/services.php` <a name="Initservices"></a> містить сервіси поточного модуля.
Реєстрація сервісів у модулі здійснюється так само, як і [системних сервісів](./../di_container.md#serviceRegister),
але у файлі `Init/services.php`.
Усі вони мають бути частиною [DI контейнера](./../di_container.md "Dependency injection container").

Файл `Init/SmartyPlugins.php` містить плагіни Smarty поточного модуля.
Реєстрація плагінів у модулі здійснюється так само, як і [системних плагінів Smarty](./../smarty_plugins.md),
але у файлі `Init/SmartyPlugins.php`.

Файл `preview.(jpeg|jpg|png|gif|svg)` може бути присутнім у кореневій директорії модуля — він автоматично
відображатиметься в адмін-панелі у списку модулів.

Файл `settings.xml` потрібен для модулів доставок та оплат. Файл містить структуру налаштувань, які потрібно вивести
в адмін-панелі у способі доставки або способі оплати. (Використовується під час [встановлення типу модуля](#typesOfModules)
	MODULE_TYPE_PAYMENT або MODULE_TYPE_DELIVERY).

Файли `design/js.php`, `design/css.php`, `Backend/design/js.php` і `Backend/design/css.php` потрібні для [реєстрації JS
і CSS файлів](./../js_css_files.md).

Структура файла:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<module>
    <settings><!--Якщо вказати більше одного <options>, буде виведено як HTML select (випадний список)-->
        <variable>service_type</variable><!--Назва змінної-->
        <name>{$lang->settings_np_service_type}</name><!--Назва параметра (підтримується з перекладів)-->
        <options>
            <name>{$lang->settings_np_service_dd}</name>
            <value>DoorsDoors</value>
        </options>
        <options>
            <name>{$lang->settings_np_service_wd}</name>
            <value>WarehouseDoors</value>
        </options>
    </settings>
    <settings><!--Так буде виведено текстове поле-->
        <variable>wayforpay_merchant</variable>
        <name>{$lang->way_for_pay_merchant}</name>
    </settings>
    <settings type="hidden|text|date|checkbox"><!--Так буде виведено як input вказаного типу-->
        <variable>wayforpay_merchant</variable>
        <name>{$lang->way_for_pay_merchant}</name>
    </settings>
</module>
```

Файл `config/config.php` може містити директиви такі ж, як і в системному конфігу. Можна лише додавати директиви —
перевизначати системні директиви не можна.

##### Типи модулів <a name="typesOfModules"></a>

Тип модуля може впливати на певну його поведінку в системі. Наразі існують такі типи модулів:
* MODULE_TYPE_PAYMENT — модуль оплати
* MODULE_TYPE_DELIVERY — модуль доставки
* MODULE_TYPE_XML — модуль, що створює вивантаження в XML-файл.

Тип модуля можна встановити в методі `install()` класу Init за допомогою методу `setModuleType` (завжди використовуйте константи, що починаються
з `MODULE_TYPE_`)
```php
$this->setModuleType(MODULE_TYPE_DELIVERY);
```
Наприклад, модуль із типом MODULE_TYPE_DELIVERY виводитиме налаштування, визначені у файлі `settings.xml`,
в адмін-панелі у способі доставки. Також цей модуль буде показуватися у списку доступних модулів доставки
[у налаштуваннях способу доставки](https://demookay.com/backend/index.php?controller=DeliveryAdmin).

[Модуль: швидкий старт](./quick_start.md)
