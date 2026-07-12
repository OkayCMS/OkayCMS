# Модифікація tpl файлів

Окрім вбудовування в [шорт-блоки](./dev_mode.md#shortBLock), в OkayCMS є функціонал модифікації tpl файлів без
їх зміни. Цей функціонал працює так, що оригінальні файли залишаються незмінними, але в compiled потрапляє файл
у зміненому стані, наче був змінений оригінальний tpl файл.

`compiled` — це спеціальні PHP-файли, які генерує шаблонізатор Smarty на основі вмісту tpl файла.
Вони потрібні для того, щоб інтерпретатор міг обробити файл шаблону (оскільки інтерпретатор PHP не вміє обробляти
tpl файли напряму). За замовчуванням вони розташовані в директоріях `compiled/<themeName>` для клієнтської частини та
`backend/design/compiled` для admin-частини.

<a name="DOMNodes"></a>
#### DOM nodes

Файл розбирається на певне DOM-дерево, але як нода також може бути Smarty-елемент.
Наразі зі Smarty-елементів як ноди, що мають дочірні елементи, підтримуються лише foreach, function
та if (foreachelse і else будуть просто текстовими дочірніми елементами). Усі інші Smarty-елементи будуть
текстовими нодами.

`Нода` — це елемент DOM (Document Object Model) дерева. Нода може містити дочірні ноди, лише якщо це не
самозакривальний (`<img>`, `<br>`, `<input>` тощо) та не текстова нода.

Як імʼя ноди використовується відкривальний елемент блокового тега або весь елемент, якщо це самозакривальний тег
або текстова нода.

Наприклад, для коду:
```smarty
<div class="some-class">
    {foreach $arr as $item}
        {$item|escape}
    {/foreach}
</div>
```

Буде створено три ноди:
HTML-блокова нода `<div class="some-class">`, її дочірня Smarty foreach-нода `{foreach $arr as $item}`
та її дочірня текстова нода `{$item|escape}`.

<a name="registerModifications"></a>
#### Реєстрація змін шаблону

Усі реєстрації змін шаблону виконуються в блоці modifications файла [module.json](./modules/module_json.md) вашого модуля
(файл має розташовуватися в директорії Init модуля).

Загальна структура блока modifications:

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

Блок modifications містить дві властивості: backend і front. У backend описуються модифікації файлів admin-частини, у
front — відповідно модифікації клієнтської частини.
Обидва ці блоки містять усередині масив модифікацій; кожна модифікація містить властивість file з іменем файла,
який хочемо модифікувати, і властивість changes зі змінами.

Якщо файл лежить у піддиректорії від стандартної директорії html (наприклад, шаблони листів), імʼя файла вказуємо як
`email/admin_email.tpl`

Сама зміна має містити одну з властивостей find або like, у якій вказується, яку ноду потрібно знайти.

`find` шукає за входженням підрядка в рядок назви відкривальної ноди.

`like` шукає за регулярним виразом рядка назви відкривальної ноди
(для відлагодження регулярних виразів рекомендуємо сервіс [regex101.com](https://regex101.com)).

Після пошуку елемента можна додатково вказати властивість `parent` (без значення), щоб внести зміни в
безпосереднього батька елемента, або можна вказати `closestFind`/`closestLike` для пошуку першого батька,
який відповідає критеріям пошуку. `closestFind` і `closestLike` працюють за принципом `find` і `like`, але йдуть вгору
по дереву відносно знайденого елемента.

Також можна шукати дочірні ноди відносно поточної. Для цього використовуйте властивість `childrenFind` або `childrenLike`,
які також працюють за принципом `find` і `like`, але виконують пошук першої дочірньої ноди, що відповідає умовам пошуку.

`Порада`: властивості `find`/`like`, `parent`/`closestFind`/`closestLike` і `childrenFind`/`childrenLike` можна комбінувати,
але вони спрацюють у послідовності, вказаній вище.

Наприклад: знайти ноду, потім її батька і вже всередині батька знайти іншу ноду, у яку потрібно внести зміни.

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

Для внесення самих змін є кілька властивостей.
Як значення можна вказати як сам код, так і імʼя файла у вашому модулі, де лежать зміни.
Для змін frontend файл зі змінами має лежати в директорії `Okay/Modules/Vendor/Module/design/html/`,
для змін backend файл має лежати в директорії `Okay/Modules/Vendor/Module/Backend/design/html/`.

Можливі властивості:

Властивість | Значення | Опис
---|---|---
append | текст або імʼя файла з вмістом | додає вміст у кінець вказаної ноди
prepend | текст або імʼя файла з вмістом | додає вміст на початок вказаної ноди
appendBefore | текст або імʼя файла з вмістом | додає вміст у батьківську ноду, але перед поточною
appendAfter | текст або імʼя файла з вмістом | додає вміст у батьківську ноду, але після поточної
html | текст або імʼя файла з вмістом | замінює вміст вибраної ноди на вказаний
text | текст або імʼя файла з вмістом | синонім html
replace | текст або імʼя файла з вмістом | дозволяє змінити текст відкривальної ноди (може знадобитися для додавання/зміни атрибутів тощо)
remove | значення не приймається | видаляє поточну ноду з усіма її нащадками

У прикладі вище показано, як можна весь row, у якому виводяться зображення, обгорнути в `{if $product->id}`.

`Важливо`: після внесення змін у блок modifications потрібно очистити директорію compiled, щоб побачити зміни.

`Порада`: для відлагодження модифікаторів можна у файлі `config/config.php` (або `config/config.local.php`) увімкнути параметр `smarty_force_compile`, щоб файли постійно
перекомпілювалися. Важливо не забути вимкнути цей параметр для production.

`Порада 2`: щоб легше впроваджувати модифікації, їх можна внести прямо в оригінальний файл, повністю відлагодити роботу
модуля з цим кодом, і вже після цього перенести цю зміну у файл module.json.

<a name="examples"></a>
#### Приклади використання

###### Приклад №1

у файл product_list.tpl додати до назви товару щось

Вміст tpl файла:

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

Як бачимо, шукати ноду через find за вмістом "{$product->name|escape}" не можна, оскільки під пошук потрапить і зображення
товару. Тут є два варіанти: шукати через батька або за регулярним виразом.

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
            "appendAfter": "Додали через батька"
          },
          {
            "like": "^\\s+?{\\$product->name\\|escape}",
            "appendAfter": "Знайшли за регулярним виразом"
          }
        ]
      }
    ]
  }
}
```

###### Приклад №2

Додати у файл products_sort.tpl ще одну кнопку сортування. Вона має розташовуватися перед сортуванням за ціною.

Вміст tpl файла `products_sort.tpl`:

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

Тут будемо шукати кнопку сортування за ціною, потім її батька з класом "product_sort__form" і перед ним вставляти
нашу кнопку. Сама кнопка зберігатиметься у файлі `Okay/Modules/<Vendor>/<Module>/design/html/sort_button.tpl` у вигляді
tpl коду:

```smarty
<form class="product_sort__form" method="post">
    <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link {if $sort=='my_sort'} active_up{elseif $sort=='my_sort_desc'} active_down{/if}" value="{if $sort=='my_sort'}{furl sort=my_sort_desc page=null absolute=1}{else}{furl sort=my_sort page=null absolute=1}{/if}">
        <span>Моє сортування</span>
        {include file="svg.tpl" svgId="sort_icon"}
    </button>
</form>
```

Реєстрація зміни виглядатиме так:

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
