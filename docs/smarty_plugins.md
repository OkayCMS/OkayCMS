# Smarty плагины

Плагины для смарти в OkayCMS нужны для расширения функциональности дизайна.
Планины могут работать в режиме модификатора или функции.

<a name="pluginRegister"></a>
#### Регистрация плагинов

В системе плагины регистрируются в файле `Okay/Core/SmartyPlugins/SmartyPlugins.php`, и являются по сути сервисами
[DI контейнера](./di_container.md). Сами реализации плагинов располагаются в `Okay\Core\SmartyPlugins\Plugins` и должны
быть наследником `Okay\Core\SmartyPlugins\Func` (для работы в режиме функции) или `Okay\Core\SmartyPlugins\Modifier` 
(для работы в режиме модификатора).

Класс плагина должен реализовать метод `run()`, который и будет реализацией функциональности плагина.
Также класс должен объявить одно защищеное (protected) свойство `$tag`, значение которого и будет названием функции
в tpl файле.

<a name="funcArguments"></a>
##### В аргументы метода в режиме функции

В режиме функции все аргументы вызова будут передаваться в метод `run()` в виде ассоциативного массива.
Также вторым аргументом можно ловить экземпляр `Smarty`.

Пример вызова:
```smarty
{some_plugin var1=foo var2=bar}
```

в методе плагина мы получим так:
```php
public function run($params)
{
    // $params = [
    //    'var1' => 'foo',
    //    'var2' => bar,
    //];
    
    // ...abstract
}
```

`Best practices: в плагин передавать переменную "var", значение которой будет названием переменной - результатом работы`

Пример:
```smarty
{get_new_products var=new_products limit=5}
{if $new_products}
    {foreach $new_products as $product}
        // ...abstract
    {/foreach}
{/if}
```

в методе плагина мы получим так:
```php
public function run($params)
{
    // ...abstract
    if (!empty($params['var'])) {
        $smarty->assign($params['var'], $products);
    }
}
```

<a name="modifierArguments"></a>
##### В аргументы метода в режиме модификатора

В режиме модификатора аргументы вызова будут передаваться в метод `run()` в следующем порядке:

Первый аргумент, это будет собственно то, к чему применили модификатор, вторым и последующими аргументами будут 
параметры, переданные модификатору. Передача параметров происходит последовательно с разделением параметров 
двоеточием ":". 

Пример вызова:
```smarty
{$product->name|some_modifier:foo:bar}
```

в методе модификатора мы получим так:
```php
public function run($productName, $param1, $param2 = null)
{
    // $param1 = 'foo';
    // $param2 = 'bar';
    // ...abstract
}
```

#### Пример плагина

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

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        if (!empty($params['var'])) {
            $products = $this->productsHelper->getProductList($params);
            $smarty->assign($params['var'], $products);
        }
    }
}
```

#### Пример модификатора

```php
namespace Okay\Core\SmartyPlugins\Plugins;

use Okay\Core\Money;
use Okay\Core\SmartyPlugins\Modifier;

class Convert extends Modifier
{

    /** @var Money */
    private $money;
    protected $tag = 'convert';

    public function __construct(Money $money)
    {
        $this->money = $money;
    }

    public function run($price, $currency_id = null, $format = true)
    {
        return $this->money->convert($price, $currency_id, $format);
    }
}
```

#### Smarty плагины в модулях

Плагины в модулях регистрируются, также как и системные плагины, но их регистрация происходит в файле
`Okay/Core/Modules/Vendor/Module/Init/SmartyPlugins.php`.

`Best practices: реализации плагинов хранить в директории 'Okay/Core/Modules/Vendor/Module/Plugins'`
