# Dependency injection container

Контейнер реализовывает интерфейс [Psr\Container\ContainerInterface](https://www.php-fig.org/psr/psr-11/).
Описание всех сервисов и их зависимостей описано в файлах:
+ Okay/Core/config/services.php ([основные сервисы ядра](./core/README.md))
+ Okay/Core/config/requests.php ([сервисы Requests](./requests.md))
+ Okay/Core/config/helpers.php ([сервисы хелперов](./helpers.md))

Не допускается использования циклических зависимостей.

### Регистрация сервиса <a name="serviceRegister"></a>
Чтобы зарегистрировать сервис, нужно в одном из файлов описания сервисов добавить его.

Рассмотрим пример регистрации сервиса ядра. Регистрировать его нужно в файле Okay/Core/config/services.php
([версия для модулей](./modules/README.md#Initservices)).

Файл services.php возвращает массив с описанием сервисов, где ключ - это название сервиса, значение - описание сервиса.

`Best practices: в качестве имени сервиса использовать полное имя класса`

Пример:
```php
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

[
    MyClass::class => [ // Имя сервиса
        'class' => MyClass::class, // Имя класса, из которого создавать экземпляр сервиса
        'arguments' => [ // Аргументы конструктора класса MyClass. Принимать в порядке, как здесь передаём
            new SR(OtherClass::class),
            new PR('db.driver'),
        ],
    ],
];
```
Описание классов [ParameterReference](#ParameterReference) и [ServiceReference](#ServiceReference)

### <a name="GetService"></a>Получение сервиса

Чтобы получить экземпляр сервиса нужно получить его через инъекцию в [классе контроллера](./controllers.md),
или воспользоваться [локатором служб](./service_locator.md). Также при регистрации сервиса, можно указать ему 
зависимость.

#### <a name="ParameterReference"></a> ParameterReference

Класс ParameterReference нужен когда сервис зависит от параметров (конфигов).
Параметры для сервисов описываются в файле `Okay/Core/config/parameters.php`.
Параметры это многомерный ассоциативный массив, который может содержать в конечных значениях как статические значения,
так и значения из конфигурационного файла системы. Чтобы указать что нужно подставить значение из конфига,
нужно в значении параметра указать это как переменную.

Пример:
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

Чтобы передать в сервис параметр, нужно в блоке arguments передать экземпляр класса ParameterReference (PR) 
который в конструктор принимает имя параметра-зависимости. В имени стоит через точку разделять вложенность массива
параметров. [Пример](#serviceRegister) передачи в качестве зависимости значения `$parameters['db']['driver']`.

#### <a name="ServiceReference"></a> ServiceReference

Класс ServiceReference нужен когда сервис зависит от других сервисов.
Чтобы передать сервис как зивисимость другому сервису, нужно в описании сервиса в блоке arguments передать
экземпляр класса ServiceReference (SR) который в конструктор принимает имя сервиса-зависимости 
([пример выше](#serviceRegister)).