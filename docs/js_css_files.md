# Подключение Js и Css файлов

В OkayCMS Js и Css файлы не подключаются напрямую через тег `<script></script>` или `<link />`, их нужно регистрировать.
Все зарегистрированные файлы собираются в несколько (зависит от параметров) общих, которые минифицируются, и 
подключаются в шаблон.
Регистрация JavaScript для клиентской части происходит в файле `design/<theme name>/js.php`, Css соответственно в 
`design/<theme name>/css.php`. 
Для админ части регистрация происходит в `backend/design/js.php` и `backend/design/css.php`.

Для подключения Js файлов, нужно создать файл `design/<theme name>/js.php`, который возвращает массив объектов
[Okay\Core\TemplateConfig\Js](#TemplateConfigJs). Или файл `design/<theme name>/css.php` с массивом 
[Okay\Core\TemplateConfig\Css](#TemplateConfigCss) соответственно.

Из модуля также эти файлы можно подключать, расположив регистрационные файлы в директории 
`Okay/Modules/Vendor/Module/design/` для подключения файлов в клиентский шаблон и в директорию
`Okay/Modules/Vendor/Module/Backend/design/` для подключения файлов в админ часть.


<a name="commonScript"></a>
#### Общее описание классов Okay\Core\TemplateConfig\Js и Okay\Core\TemplateConfig\Css

Класс в конструктор принимает название файла, который нужно зарегистрировать (без пути).
Если путь не указать, это имеется в виду, что файл лежит в `design/<theme name>/js/` или `design/<theme name>/css/`.
В случае если подключается файл из модуля, имеется в виду директория 
`Okay/Modules/Vendor/Module/design/js/` или `Okay/Modules/Vendor/Module/design/css/`.
По умолчанию все зарегистрированные скрипты выводятся в одном общем файле в head шаблона.
Оба класса (`Okay\Core\TemplateConfig\Js` и `Okay\Core\TemplateConfig\Css`) имеют общую реализацию
(в `Okay\Core\TemplateConfig\Common`) следующих методов:


<a name="setDir"></a>
```php
setDir( string $dir)
```

Установка директории скрипта.
Если скрипт находится в теме (директория js или css соответственно), директорию можно не указывать.

Аргумент | Описание
---|---
$dir | Путь к директории скрипта, относительно корня сайта.


<a name="setPosition"></a>
```php
setPosition( string $position)
```

Установка позиции, где нужно выводить скрипт (head/footer)

Аргумент | Описание
---|---
$position | Позиция скрипта (head/footer).


<a name="setIndividual"></a>
```php
setIndividual( bool $individual)
```

Установка флага что файл должен подключиться индивидуально, не в общем скомпилированном файле

Аргумент | Описание
---|---
$individual | true - подключаем индивидуально, false - файл будет подключен в общем скомпилированном файле.


<a name="preload"></a>
```php
preload()
```

Установка флага, что нужно добавить для этого файла предзагрузчик link rel="preload". Работает только для файлов 
отмеченных через setIndividual, предзагрузка общими файлами управляется в файле config/config.php директивами
`preload_head_css`, `preload_head_js`, `preload_footer_css` и `preload_footer_js`

<a name="TemplateConfigCss"></a>
#### Класс Okay\Core\TemplateConfig\Css

Класс `Okay\Core\TemplateConfig\Css` не имеет индивидуальной реализации, содержит только 
[общие методы](#commonScript).

Пример регистрации:
```php
use Okay\Core\TemplateConfig\Css;

return [
    (new Css('font.css')),
    (new Css('font-awesome.min.css'))->setPosition('footer'),
    (new Css('grid.css'))->setDir('/custom_js/')->setIndividual(true),
];
```


<a name="TemplateConfigJs"></a>
#### Класс Okay\Core\TemplateConfig\Js

Класс `Okay\Core\TemplateConfig\Js` имеет индивидуальную реализацию следующего метода, в остальном он соответствует 
[общей реализации](#commonScript).

<a name="setDefer"></a>
```php
setDefer( string $defer)
```

Установка JavaScript файлу флага defer. Флаг defer будет добавлен в случае [individual](#setIndividual) = true

Аргумент | Описание
---|---
$defer | Путь к директории скрипта, относительно корня сайта.

Пример регистрации:
```php
use Okay\Core\TemplateConfig\Js;

return [
    (new Js('jquery-3.4.1.min.js')),
    (new Js('owl.carousel.min.js'))->setIndividual(true)->setDefer(true),
    (new Js('select2.min.js'))->setPosition('footer'),
];
```

<a name="TemplateConfigSmarty"></a>
#### Подключение файлов через Smarty

Подключение файлов через Smarty может понадобиться если нужно подключить файл по условию.
Для подключения файла нужно вызвать один из плагинов Smarty {css} или {js}. 
Возможные аргументы плагина:

Аргумент | Описание
---|---
filename | Имя подключаемого файла. То же что передается в конструктор Okay\Core\TemplateConfig\Js или Okay\Core\TemplateConfig\Css
file | Синоним filename
dir | Аналог метода Okay\Core\TemplateConfig\Js::setDir() или Okay\Core\TemplateConfig\Css::setDir()
backend | Булев тип. Указание что подключаем файл для админ части. По умолчанию считается что подключается файл для клиентской части
admin | Синоним backend
defer | Булев тип. Указывает нужно ли добавлять атрибут defer. Доступно только для плагина {js}
