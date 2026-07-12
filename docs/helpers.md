# Helpers

Хелпери призначені для того, щоб винести частину логіки (бізнес-логіки або логіки застосунку) з контролера.
Методи хелперів можуть перевикористовуватися в різних частинах системи. Наприклад `Okay\Helpers\ProductsHelper::getList`.
Також методи будь-якого хелпера можуть [розширюватися з модуля](./modules/extenders.md).

Назви всіх helper-сервісів закінчуються на ключове слово Helper.
За замовчуванням усі хелпери розташовані в директоріях `Okay/Helpers/` і `backend/Helpers/`.

Хелпери можуть повертати результат виконання. Але результат виконання має повертатися не напряму,
а через `ExtenderFacade::execute()`.
Метод `execute()` приймає три параметри: імʼя методу (рядок або масив), у якому він запускається, дані, які потрібно повернути,
та масив аргументів цього методу.

Приклад повернення результату в helper:
```php
return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
```

Приклад helper:
```php
class BrandsHelper
{

    //...abstract 

    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $brandsEntity->order($sortName);
        $brands = $brandsEntity->find($filter);

        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }
}
```
Цей helper дістає з бази список брендів. По суті, це можна вважати декоратором до методу
BrandsEntity::find().

Більш цікавий приклад:
```php
class ProductsHelper
{

    //...abstract 
    
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }
    
        $products = $productsEntity->mappedBy('id')->order($sortName)->find($filter);
    
        if (empty($products)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }
    
        $products = $this->attachVariants($products);
    
        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }
}
```
цей helper не лише дістає список товарів, а й додає до них варіанти, тим самим декоруючи результат
ProductsEntity::find().

### ValidateHelper

Helper валідації потребує окремої уваги.
Якщо всі хелпери поділені — кожен під свою сутність, то helper валідації зібрав
у собі валідації всіх [requests](./requests.md).
Методи там називаються “від зворотного”: `getFeedbackValidateError()` та подібні.

Приклад:
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
        $captchaCode =  $this->request->post('captcha_code', 'string');
        
        $error = null;
        if (!$this->validator->isName($feedback->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($feedback->email, true)) {
            $error = 'empty_email';
        } elseif (!$this->validator->isComment($feedback->message, true)) {
            $error = 'empty_text';
        } elseif ($this->settings->get('captcha_feedback') && !$this->validator->verifyCaptcha('captcha_feedback', $captchaCode)) {
            $error = 'captcha';
        }
    
        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
}
```

Приклад використання:
```php
use Okay\Helpers\ValidateHelper;
//...abstract
class FeedbackController extends AbstractController
{
    //...abstract
    public function render(
        //...abstract
        CommonRequest $commonRequest,
        ValidateHelper $validateHelper
    ) {
        if (($feedback = $commonRequest->postFeedback()) !== null) {
            if ($error = $validateHelper->getFeedbackValidateError($feedback)) {
                // Обробка помилки
            } else {
                //...abstract
            }
        }
        //...abstract
    }
}
```

#### Хелпери модулів <a name="modulesHelpers"></a>
Модуль також може містити свої хелпери. Рекомендується за можливості всі логічні частини коду виносити в хелпери.
Це забезпечить гнучкішу взаємодію між модулями. Хелпери модуля реєструються так само, як і
[сервіси модуля](./modules/README.md#Initservices)
