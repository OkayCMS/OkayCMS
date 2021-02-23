# Модификация tpl файлов

Помимо встраивания в [шорт-блоки](./dev_mode.md#shortBLock), в OkayCMS есть функционал модификации tpl файлов без
их изменения. Данный функционал работает так, что оригинальные файлы остаются неизмененными, но в compiled попадает файл
в измененном состоянии, будто был изменен оригинальный tpl файл.

`compiled` - это специальные php файлы, которые генерирует шаблонизатор Smarty на основе содержимого tpl файла.
Нужны они для того, чтобы интерпретатор мог обработать файл шаблона (поскольку интерпретатор php не умеет обрабатывать
напрямую tpl файлы). Располагаются они по умолчанию в директориях `compiled/<themeName>` для клиентской части и 
`bachend/design/compiled` для админ-части.

<a name="DOMNodes"></a>
#### DOM nodes

Файл разбирается на некое DOM дерево, но в качестве ноды может быть также Smarty элемент.
На данный момент из Smarty элементов в качестве ноды имеющей дочерние элементы поддерживаются только foreach, function 
и if (foreachelse и else будут просто текстовыми дочерними элементами). Все остальные Smarty элементы будут как 
текстовые ноды.

`Нода` - это элемент DOM (Document Object Model) дерева. Нода может содержать дочерние ноды, только если это не 
самозакрывающаяся (`<img>`, `<br>`, `<input>` etc) и не текстовая нода.

В качестве имени ноды используется открывающий элемент блочного тега или весь элемент, если это самозакрывающийся тег
или текстовая нода.

Например для кода:
```smarty
<div class="some-class">
    {foreach $arr as $item}
        {$item|escape}
    {/foreach}
</div>
```

Будет создано три ноды: 
Html блочная нода `<div class="some-class">` её дочерняя smarty foreach нода `{foreach $arr as $item}` 
и её дочерняя текстовая нода `{$item|escape}`.

<a name="registerModifications"></a>
#### Регистрация изменений шаблона

Все регистрации изменений шаблона производятся в блоке modifications файла [module.json](./modules/module_json.md) вашего модуля 
(файл должен располагаться в директории Init модуля).

Общая структура блока modifications:

```json
{
  "modifications": {
    "backend": [
      {
        "file": "product.tpl",
        "changes": [
          {
            "find": "{foreach $product_images as $image}",
            "closestFind": "<div class=\"row",
            "appendBefore": "{if $product->id}",
            "appendAfter": "{/if}"
          }
        ]
      }
    ],
    "front": [
      {
        "file": "product.tpl",
        "changes": [
          {
            "like": "<select .*? class=\"fn_variant variant_select .*",
            "appendBefore": "<span>appendBefore</span>",
            "html": "test.tpl"
          }
        ]
      }
    ]
  }
}
```

Блок modifications содержит два свойства backend и front. В backend описываются модификации файлов админ-части, во 
front соответственно модификации клиентской части.
Оба этих блока содержат внутри массив модификаций, каждый из которых содержит свойство file содержащее имя файла
который хотим модифицировать и свойство changes содержащее уже сами изменения.

Если файл лежит в поддиректории от стандартной директории html (например шаблоны писем) имя файла указываем как 
`email/admin_email.tpl`

Само изменение должно содержать одно из свойств find или like в котором указывается какую ноду нужно найти.

`find` ищет по вхождению подстроки в строку названия открывающей ноды.

`like` ищет по регулярному выражению строки названия открывающей ноды 
(для отладки регулярок рекомендуем сервис [regex101.com](https://regex101.com)).

После поиска элемента можно дополнительно указать свойство `parent` (без значения) для внесения изменений в 
непосредственного родителя элемента или можно указать `closestFind`/`closestLike` для поиска первого родителя, 
удовлетворяющего критериям поиска. `closestFind` и `closestLike` работают по принципу `find` и `like` но идут вверх 
по дереву относительно найденного элемента.

Также можно искать дочерние ноды относительно текущей. Для этого используйте свойство `childrenFind` или `childrenLike`,
которые также работают по принципу `find` и `like` но производят поиск первой дочерней ноды удовлетворяющей условиям поиска.

`Совет`: свойства `find`/`like`, `parent`/`closestFind`/`closestLike` и `childrenFind`/`childrenLike` можно комбинировать,
но отработают они в последовательности как указано выше.

Например: найти ноду, затем её родителя и уже внутри родителя найти другую ноду, в которую нужно внести изменения.

```json
{
  "modifications": {
    "front": [
      {
        "file": "main.tpl",
        "changes": [
          {
            "find": "{$lang->main_new_products}",
            "closestFind": "{if $new_products}",
            "childrenLike": "{foreach \\$new.+?uct",
            "prepend": "some elements"
          }
        ]
      }
    ]
  }
}
```

Для внесения самих изменений есть несколько свойств.
В качестве значения можно указать как сам код, так и имя файла в вашем модуле, где лежат изменения.
Для изменений фронтенда файл изменения должен лежать в директории `Okay/Modules/Vendor/Module/design/html/`,
для изменений бекенда файл должен находиться в директории `Okay/Modules/Vendor/Module/Backend/design/html/`.

Возможные свойства:

Свойство | Значение | Описание
---|---|---
append | текст или имя файла с содержимым | добавляет содержимое в конец указанной ноды
prepend | текст или имя файла с содержимым | добавляет содержимое в начало указанной ноды
appendBefore | текст или имя файла с содержимым | добавляет содержимое в родирельскую ноду но перед текущей
appendAfter | текст или имя файла с содержимым | добавляет содержимое в родирельскую ноду но после текущей
html | текст или имя файла с содержимым | заменяет содержимое выбраной ноды на указанное
text | текст или имя файла с содержимым | синоним html
replace | текст или имя файла с содержимым | позволяет изменить текст открывающей ноды (может понадобиться для добавления/изменения атрибутов etc)
remove | значение не принимается | удаляет текущую ноду со всеми её потомками

В примере выше показано как можно весь row в котором выводятся изображения обернуть в `{if $product->id}`.

`Важно` - после внесения изменений в блок modifications нужно очистить директорию compiled чтобы увидеть изменения.

`Совет`: для отладки модификаторов, можно в файле `config/config.php` (`config/config.local.php`) включить параметр `smarty_force_compile` чтобы файлы постоянно
перекомпилировались. Важно не забыть выключить этот параметр для production.

`Совет 2`: для более лёгкого внедрения модификаций их можно внести прямо в оригинальный файл, полностью отладить работу
модуля с этим кодом и уже после этого перенести данное изменение в файл module.json.

<a name="examples"></a>
#### Примеры использования

###### Пример №1

в файл product_list.tpl добавить к названию товара что-то

Содержимое tpl файла:

```smarty
...
<img src="{$product->image->filename|resize:300:180}" alt="{$product->name|escape}" title="{$product->name|escape}"/>
...
<div class="product_preview__name">
    {* Product name *}
    <a class="product_preview__name_link" data-product="{$product->id}" href="{url_generator route="product" url=$product->url}">
        {$product->name|escape}
    </a>
</div>
...
``` 

Как видим, искать ноду через find по содержимому "{$product->name|escape}" нельзя, т.к. под поиск подпадёт и изображение
товара, здесь два варианта решения: искать через родителя или по регулярному выражению.

```json
{
  "modifications": {
    "front": [
      {
        "file": "product_list.tpl",
        "changes": [
          {
            "find": "product_preview__name_link",
            "childrenFind": "{$product->name|escape}",
            "appendAfter": "Добавили через родителя"
          },
          {
            "like": "^\\s+?{\\$product->name\\|escape}",
            "appendAfter": "Нашли по регулярному выражению"
          }
        ]
      }
    ]
  }
}
```

###### Пример №2

Добавить в файл products_sort.tpl ещё одну кнопку сортировки. Располагаться она должна перед сортировкой по цене.

Содержимое tpl файла `products_sort.tpl`:

```smarty
...
<div class="fn_ajax_buttons d-flex flex-wrap align-items-center products_sort">
    <span class="product_sort__title hidden-sm-down" data-language="products_sort_by">{$lang->products_sort_by}:</span>

    <form class="product_sort__form" method="post">
        <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link{if $sort=='position'} active_up{/if} no_after" value="{furl sort=position page=null absolute=1}">
            <span data-language="products_by_default">{$lang->products_by_default}</span>
        </button>
    </form>

    <form class="product_sort__form" method="post">
        <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link{if $sort=='price'} active_up{elseif $sort=='price_desc'} active_down{/if}" value="{if $sort=='price'}{furl sort=price_desc page=null absolute=1}{else}{furl sort=price page=null absolute=1}{/if}">
            <span data-language="products_by_price">{$lang->products_by_price}</span>
            {include file="svg.tpl" svgId="sort_icon"}
        </button>
    </form>
...
``` 

Здесь будем искать кнопку сортировки по цене, затем её родителя с классом "product_sort__form" и перед ним вставлять
нашу кнопку. Сама кнопка будет храниться в файле `Okay/Modules/<Vendor>/<Module>/design/html/sort_button.tpl` в виде
tpl кода:

```smarty
<form class="product_sort__form" method="post">
    <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link {if $sort=='my_sort'} active_up{elseif $sort=='my_sort_desc'} active_down{/if}" value="{if $sort=='my_sort'}{furl sort=my_sort_desc page=null absolute=1}{else}{furl sort=my_sort page=null absolute=1}{/if}">
        <span>Моя сортировка</span>
        {include file="svg.tpl" svgId="sort_icon"}
    </button>
</form>
```

Регистрация изменения будет выглядеть так:

```json
{
  "modifications": {
    "front": [
      {
        "file": "products_sort.tpl",
        "changes": [
          {
            "find": "data-language=\"products_by_price\"",
            "closestFind": "class=\"product_sort__form\"",
            "appendBefore": "sort_button.tpl"
          }
        ]
      }
    ]
  }
}
```