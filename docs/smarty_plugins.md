# Плагіни Smarty

Плагіни Smarty в OkayCMS потрібні для розширення функціональності дизайну.
Плагіни можуть працювати в режимі модифікатора або функції.

<a name="pluginRegister"></a>
#### Реєстрація плагінів

У системі плагіни реєструються у файлі `Okay/Core/SmartyPlugins/SmartyPlugins.php` і по суті є сервісами
[DI контейнера](./di_container.md). Реалізації плагінів розташовані в `Okay\Core\SmartyPlugins\Plugins` і мають
наслідувати `Okay\Core\SmartyPlugins\Func` (для роботи в режимі функції) або `Okay\Core\SmartyPlugins\Modifier`
(для роботи в режимі модифікатора).

Клас плагіна має реалізувати метод `run()`, який і буде реалізацією функціональності плагіна.
Також клас має оголосити одну захищену (protected) властивість `$tag`, значення якої і буде назвою функції
в tpl файлі.

<a name="funcArguments"></a>
##### Аргументи методу в режимі функції

У режимі функції всі аргументи виклику будуть передаватися в метод `run()` у вигляді асоціативного масиву.
Також другим аргументом можна отримати екземпляр `\Smarty\Template`.

Приклад виклику:
```smarty
{some_plugin var1=foo var2=bar}
```

у методі плагіна ми отримаємо:
```php
public function run(array $params, \Smarty\Template $smarty): void
{
    // $params = [
    //    'var1' => 'foo',
    //    'var2' => 'bar',
    //];
}
```

Рекомендований підхід: передавати в плагін змінну `var`; її значення буде назвою змінної, у яку плагін запише результат.

Приклад:
```smarty
{get_new_products var=new_products limit=5}
{if $new_products}
    {foreach $new_products as $product}
        // ...abstract
    {/foreach}
{/if}
```

у методі плагіна ми отримаємо:
```php
public function run(array $params, \Smarty\Template $smarty): void
{
    if (!empty($params['var'])) {
        $smarty->assign($params['var'], $products);
    }
}
```

<a name="modifierArguments"></a>
##### Аргументи методу в режимі модифікатора

У режимі модифікатора аргументи виклику будуть передаватися в метод `run()` у такому порядку:

Перший аргумент — це, власне, те, до чого застосували модифікатор; другим і наступними аргументами будуть
параметри, передані модифікатору. Передача параметрів відбувається послідовно з розділенням параметрів
двокрапкою ":".

Приклад виклику:
```smarty
{$product->name|some_modifier:foo:bar}
```

у методі модифікатора ми отримаємо:
```php
public function run($productName, $param1, $param2 = null)
{
    // $param1 = 'foo';
    // $param2 = 'bar';
    // ...abstract
}
```

#### Приклад плагіна

```php
namespace Okay\Core\SmartyPlugins\Plugins;

use Okay\Core\EntityFactory;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Core\SmartyPlugins\Func;

class GetNewProducts extends Func
{

    protected $tag = 'get_new_products';
    
    /** @var ProductsEntity */
    private $productsEntity;
    
    /** @var ProductsHelper */
    private $productsHelper;

    
    public function __construct(EntityFactory $entityFactory, ProductsHelper $productsHelper)
    {
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
        $this->productsHelper = $productsHelper;
    }

    public function run($params, \Smarty\Template $smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }

        if (!empty($params['var'])) {
            $sort = $params['sort'] ?? 'created_desc';
            $products = $this->productsHelper->getList($params, $sort);
            $smarty->assign($params['var'], $products);
        }
    }
}
```

#### Приклад модифікатора

```php
namespace Okay\Core\SmartyPlugins\Plugins;

use Okay\Core\Money;
use Okay\Core\SmartyPlugins\Modifier;

class Convert extends Modifier
{

    /** @var Money */
    private $money;

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public function run($price, $currency_id = null, $format = true, $revers = false, $precision = null)
    {
        return $this->money->convert($price, $currency_id, $format, $revers, $precision);
    }
}
```

#### Плагіни Smarty в модулях

Плагіни в модулях реєструються так само, як і системні плагіни, але їх реєстрація відбувається у файлі
`Okay/Modules/Vendor/Module/Init/SmartyPlugins.php`.

Реалізації плагінів модуля зберігайте в директорії `Okay/Modules/Vendor/Module/Plugins`.
