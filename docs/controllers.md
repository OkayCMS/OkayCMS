# Контролери

Контролери в OkayCMS потрібні для обробки маршрутів.
Контролери поділяються на frontend-контролери (клієнтська частина) та backend-контролери (admin-частина).
Також ці контролери поділяються на стандартні (які присутні в системі за замовчуванням) і модульні (які
додаються з модулів).

Загальний порядок роботи контролерів:
одразу після переходу на сторінку роутер створює екземпляр контролера, потім викликає
метод `onInit`, а після нього — метод контролера, вказаний у маршруті.

У методі контролера слід встановити контент, який має повертатися користувачу. Для цього викличте метод
[Response::setContent()](./core/Response.md#setContent).

### Frontend-контролери <a name="frontControllers"></a>

За замовчуванням контролери розташовані у просторі імен `Okay\Controllers\` і МОЖУТЬ наслідуватися від
`Okay\Controllers\AbstractController`.

У більшості випадків бажано наслідуватися від AbstractController, щоб у вас уже був ініціалізований дизайн,
валюти, мови тощо.
Але якщо потрібно зробити “легкий” контролер (наприклад, для обробки нескладних AJAX-запитів), можна
не наслідуватися. У такому разі, якщо потрібен дизайн або ще щось, що ініціалізувалося в AbstractController,
це потрібно отримувати окремо в методі контролера через [інʼєкцію залежностей](./di_container.md#GetService).
Також можна визначити власні методи onInit() і afterController().

У контролері може бути описано кілька методів, і кілька [маршрутів](./routes.md) можуть посилатися на різні
методи одного контролера.

#### Отримання сервісів із контейнера <a name="DIVars"></a>

У методі контролера можна отримувати як залежності екземпляри класів [core](./core/README.md), [helpers](./helpers.md),
[requests](./requests.md), [entity](./entities.md), а також параметри, вказані в [маршруті](./routes.md).
Щоб отримати екземпляр конкретного класу, потрібно прийняти в методі контролера аргумент із зазначенням type hint,
і система автоматично передасть туди екземпляр запитуваного класу.

Наприклад:
```php
namespace Okay\Controllers;

use Okay\Entities\BlogEntity;
use Okay\Helpers\CommentsHelper;

class BlogController extends AbstractController
{
    public function fetchPost(
        BlogEntity $blogEntity,
        CommentsHelper $commentsHelper
    ) {
        //...abstract
    }
}
```

#### Отримання параметрів із маршруту <a name="routeVars"></a>

Щоб отримати параметри з [маршруту](./routes.md) у контролері, потрібно в методі контролера вказати аргумент,
однойменний із параметром маршруту (вказаним у полі slug), при цьому type hint не зазначається.
Якщо маршрут передбачає, що параметр є опціональним, у методі йому також слід задати значення за замовчуванням.

Наприклад, для slug маршруту `/cart/{$variantId}` у методі можна отримати змінну `$variantId`.

Приклад:
```php
namespace Okay\Controllers;

use Okay\Core\Cart;

class CartController extends AbstractController
{
    public function addItem(Cart $cartCore, $variantId)
    {
        //...abstract
    }
}
```

### Backend-контролери <a name="backendControllers"></a>

Контролери admin-частини за замовчуванням розташовані у просторі імен `Okay\Admin\Controllers` (це директорія `backend/Controllers`).
Усі контролери адмінки повинні наслідуватися від `Okay\Admin\Controllers\IndexAdmin`.
У backend-контролерів за замовчуванням викликається метод `fetch()`.
Якщо потрібно викликати інший метод контролера, у URL назву контролера слід вказувати як ControllerName@methodName.
Наприклад: `backend/index.php?controller=OrdersAdmin@myFunc` викличе метод myFunc контролера OrdersAdmin.
Ім’я методу може містити лише символи `a-zA-Z0-9`.
Залежності можна отримувати так само, як і для [frontend-контролерів](#DIVars). Параметрів маршруту для контролера адмінки
не буває — хіба що параметри $_GET.

### Frontend-контролери модулів

Контролери модулів варто розміщувати в директорії `Okay/Modules/Vendor/Module/Controllers`. В іншому вони нічим не
відрізняються від [стандартних frontend-контролерів](#frontControllers). Маршрут до них також прописується у [файлі
`Init/routes.php`](./modules/README.md#configuratinFiles).

### Backend-контролери модулів <a name="backendControllersModules"></a>

Backend-контролери модулів повністю відповідають [стандартним backend-контролерам](#backendControllers), але розташовуються
в директорії `Okay/Modules/Vendor/Module/Backend/Controllers`.
Ще одна відмінність у використанні: стандартний контролер в адмін-панелі доступний за URL
`https://demookay.com/backend/?controller=ProductsAdmin`, контролери модулів мають трохи інший формат.
Назва контролера має складатися з імені постачальника, імені модуля та назви самого контролера, розділених крапкою.

Наприклад: `https://demookay.com/backend/index.php?controller=OkayCMS.FAQ.FAQsAdmin`. Про це варто пам’ятати, коли пишете
URL на контролер у tpl файлі модуля.
