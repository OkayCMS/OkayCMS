# Extenders

Классы-расширители нужны чтобы расширять функциональность стандартных [хелперов](./../helpers.md),
[реквестов](./../requests.md), [Entities](./../entities.md) или [сервисов ядра](./../core/README.md).
Расширять можно те методы, в которых есть вызов метода `Okay\Core\Modules\Extender\ExtenderFacade::execute()`.
Хелперы и реквесты покрыты максимальным количеством экстендеров. Entities по умолчанию покрыты только стандартные
CRUD операции (у некоторых Entities могут быть дополнительные методы покрыты экстендерами). Классы ядра покрыты 
небольшим количеством экстендеров, только там, где может потребоваться вмешательство из модуля

Экстендеры могу работать как в режиме ChainExtender (цепочный вызов)так и QueueExtender (поочерёдный вызов).

Экстендеры, которые работают в режиме Chain, передают друг другу модифицированный результат.
Они ОБЯЗАТЕЛЬНО должны возвращать результат, который передал вышестоящий хелпер или экстендер.

Например: есть метод CommentsHelper::getList(), он возвращает массив комментариев.
Есть два модуля, которые расширяют функциональность этого метода.
Сразу отработает метод CommentsHelper::getList(), который возвращает результат.
Затем отработает Module1Extender::getList($result), который может изменить данные в $result и ОБЯЗАТЕЛЬНО
должен вернуть $result, чтобы он передался в Module2Extender::getList($result) и соответственно вернулся
в место, где его вызвали (чаще всего в контроллере).

Экстендеры работающие в режиме Queue, ничего не возвращают. Они просто вызываются по очереди.
В них можно описывать какие-то процедуры, которые не модифицируют данные возвращаемые хелпером.

### Аргументы экстендера

В экстендере аргументы нужно принимать по типу 1+N. Т.е. первым аргументом экстендера будет значение, возвращаемое
хелпером, вторым аргументом экстендера будет первый аргумент хелпера (или реквеста, одно и то же).

Если в хелпере аргумент объявлен как не обязательный, в экстендере его тоже нужно объявлять необязательным. 

Пример хелпера:
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
Он принимает как аргумент $feedback, который сформировал [CommonRequest](./../requests.md) и возвращает строку,
с именем ошибки.

Пример экстендера для данного хелпера:
```php
namespace Okay\Modules\Vendor\Module\Extenders;

use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Request;use Okay\Core\Validator;

class FrontExtender implements ExtensionInterface
{
    
    private $request;
    private $validator;
    
    public function __construct(Request $request, Validator $validator)
    {
        $this->request = $request;
        $this->validator = $validator;
    }

    public function getFeedbackValidateError($error, $feedback)
    {
        if ($error == 'empty_email' && $this->validator->isEmail($feedback->email)) { // Перевалидируем поле email
            $error = '';
        }

        if (!$this->validator->isPhone($feedback->phone, true)) {
            $error = 'empty_phone';
        }
        return $error;
    }
}
```
Допустим нам нужно сделать чтобы поле email стало необязательным, а телефон обязательным.

Пример регистрации:
```php
$this->registerQueueExtension(
    ['class' => ValidateHelper::class, 'method' => 'getFeedbackValidateError'],
    ['class' => FrontExtender::class, 'method' => 'getFeedbackValidateError']
);
```

### Регистрация экстендера <a name="registerExtender">
Чтобы зарегистрировать экстендер, нужно описать его в классе.
`Best practices: Описывать экстендеры в классах Okay\Modules\Vendor\Module\Extenders\FrontExtender 
и Okay\Modules\Vendor\Module\Extenders\BackendExtender`
Класс экстендера должен реализовывать интерфейс `Okay\Core\Modules\Extender\ExtensionInterface`.
Если класс экстендера содержит зависимости,
нужно его объявить как [сервис в DI контейнере](./../di_container.md#serviceRegister) или же использовать в методе
экстендера [ServiceLocator](./../service_locator.md).

Пример класса экстендера:
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

Чтобы зарегистрировать экстендер к выполнению после определённого метода хелпера, нужно в методе Init::Init()
вызвать метод registerChainExtension() или registerQueueExtension() в соответствии с нуждами.

Пример инициализации:
```php
$this->registerQueueExtension(
    ['class' => MainHelper::class, 'method' => 'commonAfterControllerProcedure'],
    ['class' => FrontExtender::class, 'method' => 'assignCurrentBanners']
);
```
Теперь метод FrontExtender::assignCurrentBanners() будет выполняться 
после метода MainHelper::commonAfterControllerProcedure().

#### Как определить какой метод какого хелпера нужно расширять?
Чтобы определить какой метод нужно расширять, нужно зайти в контроллер, и посмотреть какой хелпер используется в месте,
которое вы хотите расширить.

Пример задачи:
При добавлении комментария пользователем на сайт, если пользователь залогинен в личном кабинете и у него в профиле
указан номер телефона, нужно отправить ему сообщение в телеграмм "Спасибо за отзыв..."

Решение:
Смотрим на контроллер BlogController и ProductController, видим что для добавления комментария используется
один и тот же хелпер CommentsHelper в котором вызывается метод addCommentProcedure().

```php
$commentsHelper->addCommentProcedure('product', $product->id);
```

Следовательно в модуле нужно расширить метод addCommentProcedure() хелпера CommentsHelper;

Пишем экстендер:
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
Как внутренне будет устроен класс TelegramNotify и метод sendCommentsThanks() зависит уже от разработчика. Но пример его
использования таков.

Объявляем класс FrontExtender в Okay/Modules/Vendor/Module/services.php:
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

Далее инициализируем выполнения этого экстендера:
```php
$this->registerQueueExtension(
    ['class' => CommentsHelper::class, 'method' => 'addCommentProcedure'],
    ['class' => FrontExtender::class, 'method' => 'sendTelegramMessage']
);
```