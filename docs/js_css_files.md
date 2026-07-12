# Підключення JS і CSS файлів

В OkayCMS JS і CSS файли не підключаються напряму через теги `<script></script>` або `<link />` — їх потрібно реєструвати.
Усі зареєстровані файли збираються в кілька (залежить від параметрів) спільних бандлів, які мініфікуються та
підключаються в шаблон.
Реєстрація JavaScript для клієнтської частини відбувається у файлі `design/<theme name>/js.php`, CSS — у
`design/<theme name>/css.php`.
Для admin-частини реєстрація відбувається в `backend/design/js.php` і `backend/design/css.php`.

Щоб підключити JS файли, потрібно створити файл `design/<theme name>/js.php`, який повертає масив обʼєктів
[Okay\Core\TemplateConfig\Js](#TemplateConfigJs). Або файл `design/<theme name>/css.php` з масивом
[Okay\Core\TemplateConfig\Css](#TemplateConfigCss) відповідно.

Із модуля ці файли також можна підключати, розмістивши реєстраційні файли в директорії
`Okay/Modules/Vendor/Module/design/` — для підключення файлів у клієнтський шаблон, і в директорії
`Okay/Modules/Vendor/Module/Backend/design/` — для підключення файлів у admin-частину.


<a name="commonScript"></a>
#### Загальний опис класів Okay\Core\TemplateConfig\Js і Okay\Core\TemplateConfig\Css

Клас у конструкторі приймає назву файла, який потрібно зареєструвати (без шляху).
Якщо шлях не вказати, мається на увазі, що файл лежить у `design/<theme name>/js/` або `design/<theme name>/css/`.
Якщо підключається файл із модуля, мається на увазі директорія
`Okay/Modules/Vendor/Module/design/js/` або `Okay/Modules/Vendor/Module/design/css/`.
За замовчуванням усі зареєстровані скрипти виводяться в одному спільному файлі в head шаблону.
Обидва класи (`Okay\Core\TemplateConfig\Js` і `Okay\Core\TemplateConfig\Css`) мають спільну реалізацію
(у `Okay\Core\TemplateConfig\Common`) таких методів:


<a name="setDir"></a>
```php
setDir( string $dir)
```

Встановлення директорії скрипта.
Якщо скрипт знаходиться в темі (директорія js або css відповідно), директорію можна не вказувати.

Аргумент | Опис
---|---
$dir | Шлях до директорії скрипта відносно кореня сайту.


<a name="setPosition"></a>
```php
setPosition( string $position)
```

Встановлення позиції, де потрібно виводити скрипт (head/footer)

Аргумент | Опис
---|---
$position | Позиція скрипта (`head` або `footer`).


<a name="setIndividual"></a>
```php
setIndividual( bool $individual)
```

Встановлення прапора, що файл має підключатися індивідуально, а не в спільному скомпільованому файлі

Аргумент | Опис
---|---
$individual | true — підключаємо індивідуально, false — файл буде підключений у спільному скомпільованому файлі.


<a name="preload"></a>
```php
preload()
```

Встановлення прапора, що потрібно додати для цього файла предзавантаження `link rel=\"preload\"`. Працює лише для файлів,
позначених через `setIndividual`; предзавантаження спільними файлами керується у файлі `config/config.php` директивами
`preload_head_css`, `preload_head_js`, `preload_footer_css` і `preload_footer_js`

<a name="TemplateConfigCss"></a>
#### Клас Okay\Core\TemplateConfig\Css

Клас `Okay\Core\TemplateConfig\Css` не має індивідуальної реалізації та містить лише
[спільні методи](#commonScript).

Приклад реєстрації:
```php
use Okay\Core\TemplateConfig\Css;

return [
    (new Css('font.css')),
    (new Css('font-awesome.min.css'))->setPosition('footer'),
    (new Css('grid.css'))->setDir('/custom_js/')->setIndividual(true),
];
```


<a name="TemplateConfigJs"></a>
#### Клас Okay\Core\TemplateConfig\Js

Клас `Okay\Core\TemplateConfig\Js` має індивідуальну реалізацію такого методу, в іншому він відповідає
[спільній реалізації](#commonScript).

<a name="setDefer"></a>
```php
setDefer( bool $defer)
```

Встановлення для JavaScript файла прапора defer. Прапор defer буде додано, якщо [individual](#setIndividual) = true

Аргумент | Опис
---|---
$defer | Прапор defer (булеве значення).

Приклад реєстрації:
```php
use Okay\Core\TemplateConfig\Js;

return [
    (new Js('jquery-3.7.0.min.js')),
    (new Js('owl.carousel.min.js'))->setIndividual(true)->setDefer(true),
    (new Js('select2.min.js'))->setPosition('footer'),
];
```

<a name="TemplateConfigSmarty"></a>
#### Підключення файлів через Smarty

Підключення файлів через Smarty може знадобитися, якщо потрібно підключити файл за умовою.
Для підключення файла потрібно викликати один із Smarty-плагінів `{css}` або `{js}`.
Можливі аргументи плагіна:

Аргумент | Опис
---|---
filename | Імʼя підключуваного файла. Те саме, що передається в конструктор `Okay\Core\TemplateConfig\Js` або `Okay\Core\TemplateConfig\Css`
file | Синонім filename
dir | Аналог методу `Okay\Core\TemplateConfig\Js::setDir()` або `Okay\Core\TemplateConfig\Css::setDir()`
backend | Булевий тип. Вказує, що підключаємо файл для admin-частини. За замовчуванням вважається, що підключається файл для клієнтської частини
admin | Синонім backend
defer | Булевий тип. Вказує, чи потрібно додавати атрибут defer. Доступно лише для плагіна `{js}`
