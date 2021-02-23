# Service Locator

Локатор служб нужен для получения зависимостей, которые зарегистрированы в [DI контейнере](./di_container.md).
Если по каким-то причинам, не получается прокинуть зависимость через инъекцию зависимости, или же это не рационально 
(если зависимость нужна одному методу), можно использовать локатор служб.

Пример использования:

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
