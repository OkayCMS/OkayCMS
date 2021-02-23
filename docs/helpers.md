# Helpers

Хелперы предназначены для того, чтобы вынести часть логики (бизнес логики, или логики приложения) из контроллера.
Методы хелперов могут переиспользоваться в различных частях системы. Например Okay\Helpers\ProductsHelper::getProductList.
Также методы любого хелпера могут [расширяться из модуля](./modules/extenders.md).

Название всех сервисов хелперов заканчиваются на ключевое слово Helper.
По умолчанию все хелперы хранятся в директории Okay/Helpers/ и backend/Helpers/.

Хелперы могут возвращать результат выполнения. Но результат выполенния дложен возвращаться не напрямую,
а через ExtenderFacade::execute();.
Метод execute() принимает три параметра, имя метода (строка или массив), в котором он запускается, данные которые нужно вернуть
и массив аргументов данного метода.

Пример возвращения результата в хелпером:
```php
return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
```

Пример хелпера:
```php
class BrandsHelper
{

    //...abstract 

    public function getBrandsList($filter = [])
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $brands = $brandsEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }
}
```
Данный хелпер достает из базы список брендов. По большому счёту, это можно считать декоратором к методу 
BrandsEntity::find().

Более интересный пример:
```php
class ProductsHelper
{

    //...abstract 
    
    public function getProductList($filter = [])
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }
    
        $products = $productsEntity->mappedBy('id')->find($filter);
    
        if (empty($products)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }
    
        $products = $this->attachVariants($products);
    
        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }
}
```
данный хелпер не только достает список товаров, а и добавляет к ним варианты, тем самым декорируя результат 
ProductsEntity::find().

### ValidateHelper

Хелпер валидации требует отдельного внимания.
Если все хелперы подроблены каждый под свою сущность, то хелпер валидации собрал 
в себе валидации всех [реквестов](./requests.md).
Методы там называются от обратного getFeedbackValidateError() и подобные.

Пример: 
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

Пример использования:
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
                // Обработка ошибки
            } else {
                //...abstract
            }
        }
        //...abstract
    }
}
```

#### Хелперы модулей <a name="modulesHelpers"></a>
Модуль также может содержать свои хелперы. Рекомендуется по возможности, все логические части кода выносить в хелперы.
Это обеспечит более гибкое взаимодействие между модулями. Хелперы модуля регистрируются также как и 
[сервисы модуля](./modules/README.md#Initservices)
