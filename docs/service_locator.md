# Локатор сервісів (Service Locator)

Локатор сервісів потрібен для отримання залежностей, які зареєстровані в [DI контейнері](./di_container.md).
Якщо з якихось причин не виходить передати залежність через інʼєкцію залежностей або це недоцільно
(наприклад, якщо залежність потрібна лише одному методу), можна використати service locator.

Приклад використання:

```php

use Okay\Core\ServiceLocator;
use Okay\Core\EntityFactory;

class SomeClass {
    public function someMethod()
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        //...abstract
    }
}
```
