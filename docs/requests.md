# Requests

Классы реквестов предназначены для сбора сгруппированных данных из запроса (в частности POST).
Реквесты регистрируются, также как и стандартные [сервисы ядра](./di_container.md#serviceRegister)
и могут [расширяться из модуля](./modules/extenders.md).

Название всех сервисов реквестов заканчиваются на ключевое слово Request.
По умолчанию все реквесты хранятся в директории Okay/Requests/ и backend/Requests/.

Реквесты обязательно должны возвращать результат (даже пустой). Но результат выполнения должен возвращаться не напрямую,
а через ExtenderFacade::execute();.
Метод execute() принимает три параметра:
* имя метода (строка или массив) в котором он запускается,
* данные которые нужно вернуть, 
* массив аргументов данного метода.

Пример:
```php
use Okay\Core\Request;
//...abstract
class CommonRequest
{
    //...abstract
    /** @var Request  */
    private $request;
    //...abstract

    public function postComment()
    {
        $comment = null;
        if ($this->request->post('comment')) {
            $comment = new \stdClass;
            $comment->name = $this->request->post('name');
            $comment->email = $this->request->post('email');
            $comment->text = $this->request->post('text');
        }
    
        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }
}
```

Таким образом, данный метод реквеста возвращает данные полученные из $_POST. Также этот метод можно
[расширить из модуля](./modules/extenders.md).

Пример использования:
```php
use Okay\Requests\CommonRequest;
//...abstract

class FeedbackController extends AbstractController
{
    
    //...abstract

    public function render(
        //...abstract
        CommonRequest $commonRequest
    ) {
        if (($feedback = $commonRequest->postFeedback()) !== null) {
            //...abstract
        }
        //...abstract
    }
}
```

