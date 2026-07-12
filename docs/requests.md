# Requests

Класи requests призначені для збору згрупованих даних із запиту (зокрема POST).
Requests реєструються так само, як і стандартні [сервіси core](./di_container.md#serviceRegister),
і можуть [розширюватися з модуля](./modules/extenders.md).

Назви всіх request-сервісів закінчуються на ключове слово Request.
За замовчуванням усі requests розташовані в директоріях `Okay/Requests/` і `backend/Requests/`.

Requests обовʼязково мають повертати результат (навіть порожній). Але результат виконання має повертатися не напряму,
а через `ExtenderFacade::execute()`.
Метод `execute()` приймає три параметри:
* імʼя методу (рядок або масив), у якому він запускається,
* дані, які потрібно повернути,
* масив аргументів цього методу.

Приклад:
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

Таким чином, цей метод request повертає дані, отримані з `$_POST`. Також цей метод можна
[розширити з модуля](./modules/extenders.md).

Приклад використання:
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
