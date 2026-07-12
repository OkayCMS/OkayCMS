# Extenders

Класи-розширювачі потрібні, щоб розширювати функціональність стандартних [helpers](./../helpers.md),
[requests](./../requests.md), [Entities](./../entities.md) або [сервісів core](./../core/README.md).
Розширювати можна ті методи, у яких є виклик `Okay\Core\Modules\Extender\ExtenderFacade::execute()`.
Helpers і requests покриті найбільшою кількістю extender. Entities за замовчуванням покриті лише стандартними
CRUD-операціями (у деяких Entities можуть бути додаткові методи, покриті extender). Класи core покриті
невеликою кількістю extender — лише там, де може знадобитися втручання з модуля.

Extender можуть працювати як у режимі ChainExtender (ланцюжковий виклик), так і QueueExtender (послідовний виклик).

Extender, які працюють у режимі Chain, передають один одному модифікований результат.
Вони ОБОВʼЯЗКОВО мають повертати результат, який передав “вище” helper або extender.

Наприклад: є метод `CommentsHelper::getList()`, він повертає масив коментарів.
Є два модулі, які розширюють функціональність цього методу.
Спочатку відпрацює `CommentsHelper::getList()`, який повертає результат.
Потім відпрацює `Module1Extender::getList($result)`, який може змінити дані в `$result` і ОБОВʼЯЗКОВО
має повернути `$result`, щоб він передався в `Module2Extender::getList($result)` і, відповідно, повернувся
в місце виклику (найчастіше — у контролер).

Extender, що працюють у режимі Queue, нічого не повертають. Вони просто викликаються по черзі.
У них можна описувати процедури, які не модифікують дані, що повертаються helper.

### Аргументи extender

В extender аргументи потрібно приймати за схемою 1+N. Тобто першим аргументом extender буде значення, повернуте
helper, другим аргументом extender буде перший аргумент helper (або request — це одне й те саме).

Якщо в helper аргумент оголошено як необовʼязковий — у extender його також потрібно оголошувати необовʼязковим.

Приклад helper:
```php
use Okay\Core\Validator;
//...abstract
class ValidateHelper
{

    //...abstract 
    /** @var Validator  */
    private $validator;
    //...abstract 

    public function getFeedbackValidateError($feedback)
    {
        $error = null;
        if (!$this->validator->isName($feedback->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($feedback->email, true)) {
            $error = 'empty_email';
        } else {
            //...abstract 
        }
    
        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
}
```
Він приймає як аргумент `$feedback`, який сформував [CommonRequest](./../requests.md), і повертає рядок
з іменем помилки.

Приклад extender для цього helper:
```php
namespace Okay\Modules\Vendor\Module\Extenders;

use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;
use Okay\Core\Validator;

class FrontExtender implements ExtensionInterface
{
    
    private $request;
    private $validator;
    
    public function __construct(Request $request, Validator $validator)
    {
        $this->request = $request;
        $this->validator = $validator;
    }

    public function getFeedbackValidateError(?string $error, $feedback): ?string
    {
        if ($error === 'empty_email' && $this->validator->isEmail($feedback->email)) {
            $error = null;
        }

        if (!$this->validator->isPhone($feedback->phone, true)) {
            $error = 'empty_phone';
        }

        return $error;
    }
}
```
Припустімо, нам потрібно зробити поле email необовʼязковим, а телефон — обовʼязковим.

Приклад реєстрації:
```php
$this->registerChainExtension(
    ['class' => ValidateHelper::class, 'method' => 'getFeedbackValidateError'],
    ['class' => FrontExtender::class, 'method' => 'getFeedbackValidateError']
);
```

### Реєстрація extender <a name="registerExtender">
Щоб зареєструвати extender, потрібно описати його як клас.
Рекомендовано описувати extender у класах `Okay\Modules\Vendor\Module\Extenders\FrontExtender` і
`Okay\Modules\Vendor\Module\Extenders\BackendExtender`.
Клас extender має реалізовувати інтерфейс `Okay\Core\Modules\Extender\ExtensionInterface`.
Якщо клас extender має залежності, його потрібно оголосити як [сервіс у DI контейнері](./../di_container.md#serviceRegister).
[ServiceLocator](./../service_locator.md) використовуйте лише тоді, коли інʼєкція через конструктор не підходить.

Приклад класу extender:
```php
namespace Okay\Modules\Vendor\Module\Extenders;

use Okay\Core\Design;
use Okay\Core\Modules\Extender\ExtensionInterface;

class FrontExtender implements ExtensionInterface
{
    private $design;
    
    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function extenderMethod()
    {
        //...abstract
        $this->design->assign('param', 'value');
    }
}
```

Щоб зареєструвати extender для виконання після певного методу helper, потрібно в `Init\Init::init()`
викликати `registerChainExtension()` або `registerQueueExtension()` відповідно до потреб.

Приклад ініціалізації:
```php
$this->registerQueueExtension(
    ['class' => MainHelper::class, 'method' => 'commonAfterControllerProcedure'],
    ['class' => FrontExtender::class, 'method' => 'assignCurrentBanners']
);
```
Тепер метод `FrontExtender::assignCurrentBanners()` виконуватиметься
після методу `MainHelper::commonAfterControllerProcedure()`.

#### Як визначити, який метод якого helper потрібно розширювати?
Щоб визначити, який метод потрібно розширювати, зайдіть у контролер і подивіться, який helper використовується в місці,
яке ви хочете розширити.

Приклад задачі:
Під час додавання коментаря користувачем на сайт, якщо користувач залогінений в особистому кабінеті й у нього в профілі
вказано номер телефону, потрібно надіслати йому повідомлення в Telegram “Дякуємо за відгук…”.

Рішення:
Дивимося на контролери BlogController і ProductController та бачимо, що для додавання коментаря використовується
один і той самий helper CommentsHelper, у якому викликається метод `addCommentProcedure()`.

```php
$commentsHelper->addCommentProcedure('product', $product->id);
```

Отже, у модулі потрібно розширити метод `addCommentProcedure()` helper CommentsHelper.

Пишемо extender:
```php
namespace Okay\Modules\Vendor\Module\Extenders;

use Okay\Core\Design;
use Okay\Core\Modules\Extender\ExtensionInterface;

class FrontExtender implements ExtensionInterface
{
    private $design;
    private $telegramNotify;
    
    public function __construct(Design $design, TelegramNotify $telegramNotify)
    {
        $this->design = $design;
        $this->telegramNotify = $telegramNotify;
    }

    public function sendTelegramMessage()
    {
        if (($user = $this->design->getVar('user')) && !empty($user->phone)) {
            $this->telegramNotify->sendCommentsThanks($user->phone);
        }
    }
}
```
Як внутрішньо буде влаштований клас TelegramNotify і метод `sendCommentsThanks()` — залежить від розробника. Але приклад використання такий.

Оголошуємо клас FrontExtender у `Okay/Modules/Vendor/Module/Init/services.php`:
```php
namespace Okay\Modules\Vendor\Module;

return [
    Extenders\FrontExtender::class => [
        'class' => Extenders\FrontExtender::class,
        'arguments' => [
            new SR(Design::class),
            new SR(TelegramNotify::class),
        ],
    ],
    TelegramNotify::class => [
        'class' => TelegramNotify::class,
        'arguments' => [
            //...abstract
        ],
    ],
];
```

Далі ініціалізуємо виконання цього extender:
```php
$this->registerQueueExtension(
    ['class' => CommentsHelper::class, 'method' => 'addCommentProcedure'],
    ['class' => FrontExtender::class, 'method' => 'sendTelegramMessage']
);
```
