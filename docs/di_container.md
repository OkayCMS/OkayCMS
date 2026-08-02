# Контейнер інʼєкції залежностей (Dependency injection container)

Контейнер реалізує інтерфейс [Psr\Container\ContainerInterface](https://www.php-fig.org/psr/psr-11/).
Опис усіх сервісів та їхніх залежностей міститься у файлах:
+ `Okay/Core/config/services.php` ([основні сервіси core](./core/README.md))
+ `Okay/Core/config/requests.php` ([сервіси Requests](./requests.md))
+ `Okay/Core/config/helpers.php` ([сервіси helpers](./helpers.md))

Не допускається використання циклічних залежностей.

### Реєстрація сервісу <a name="serviceRegister"></a>
Щоб зареєструвати сервіс, потрібно додати його в одному з файлів опису сервісів.

Розглянемо приклад реєстрації сервісу core. Реєструвати його потрібно у файлі `Okay/Core/config/services.php`
([версія для модулів](./modules/README.md#Initservices)).

Файл `services.php` повертає масив з описом сервісів, де ключ — це назва сервісу, значення — опис сервісу.

Як імʼя сервісу рекомендовано використовувати повне імʼя класу.

Приклад:
```php
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

[
    MyClass::class => [ // Назва сервісу
        'class' => MyClass::class, // Імʼя класу, з якого створювати екземпляр сервісу
        'arguments' => [ // Аргументи конструктора класу MyClass. Приймати в порядку, як тут передаємо
            new SR(OtherClass::class),
            new PR('db.driver'),
        ],
    ],
];
```
Опис класів [ParameterReference](#ParameterReference) і [ServiceReference](#ServiceReference)

### <a name="GetService"></a>Отримання сервісу

Щоб отримати екземпляр сервісу, потрібно отримати його через інʼєкцію в [класі контролера](./controllers.md),
або скористатися [service locator](./service_locator.md). Також під час реєстрації сервісу можна вказати для нього
залежність.

#### <a name="ParameterReference"></a> ParameterReference

Клас ParameterReference потрібен, коли сервіс залежить від параметрів (конфігів).
Параметри для сервісів описуються у файлі `Okay/Core/config/parameters.php`.
Параметри — це багатовимірний асоціативний масив, який може містити у кінцевих значеннях як статичні значення,
так і значення з конфігураційного файла системи. Щоб вказати, що потрібно підставити значення з системного конфіга,
у значенні параметра потрібно вказати це як змінну.

Приклад:
```php
$parameters = [
    'root_dir' => '{$root_dir}',
    'logger' => [
        'file' => __DIR__ . '/../../log/app.log',
    ],
    'db' => [
        'driver'   => '{$db_driver}',
        'dsn'      => '{$db_driver}:host={$db_server};dbname={$db_name};charset={$db_charset}',
        'user'     => '{$db_user}',
        'password' => '{$db_password}',
        'prefix'   => '{$db_prefix}',
        'db_sql_mode' => '{$db_sql_mode}',
        'db_timezone' => '{$db_timezone}',
        'db_names' => '{$db_names}',
    ],
];
```

Щоб передати в сервіс параметр, у блоці arguments потрібно передати екземпляр класу ParameterReference (PR),
який у конструктор приймає імʼя параметра-залежності. В імені варто через крапку розділяти вкладеність масиву
параметрів. [Приклад](#serviceRegister) передачі як залежності значення `$parameters['db']['driver']`.

#### <a name="ServiceReference"></a> ServiceReference

Клас ServiceReference потрібен, коли сервіс залежить від інших сервісів.
Щоб передати сервіс як залежність іншому сервісу, в описі сервісу в блоці arguments потрібно передати
екземпляр класу ServiceReference (SR), який у конструктор приймає імʼя сервісу-залежності
([приклад вище](#serviceRegister)).
