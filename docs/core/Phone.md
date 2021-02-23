# Класс Okay\Core\Phone

Класс предназначен для работы с номерами телефонов.

<a name="format"></a>
```php
format( string $phoneNumber [, int $numberFormat = null])
```

Метод форматирует телефон в соответствии с указанным форматом. Если формат не указан, он берется из настроек сайта.
Также этот метод можно вызвать в дизайне через Smarty модификатор `|phone`. 

Пример:
```smarty
{$user->phone|phone}
```

Аргумент | Описание
---|---
$phoneNumber | Номер телефона, который нужно отформатировать
$numberFormat | Одна из констант класса \libphonenumber\PhoneNumberFormat

Варианты констант:

Константа | Пример номера телефона
---|---
E164 | +380442903833
INTERNATIONAL | +380 44 290 3833
NATIONAL | 044 290 3833
RFC3966 | tel:+380-44-290-3833

<a name="toSave"></a>
```php
toSave( string $phoneNumber)
```

Метод подготавливает номер телефона для сохранения в базу, в базе они хранятся в стандарте E164.

Аргумент | Описание
---|---
$phoneNumber | Номер телефона, который будет сохранятся в базу

Пример сохранения телефона в заказе:
```php
use Okay\Core\Phone;

//...abstract

$order = new \stdClass;
$order->name  = $this->request->post('name');
$order->email = $this->request->post('email');
$order->phone = Phone::toSave($this->request->post('phone'));
```

<a name="clear"></a>
```php
clear( string $phoneNumber)
```

Метод очищает телефон от всех лишних символов, которые не могут быть номером телефона

Аргумент | Описание
---|---
$phoneNumber | Номер телефона, который нужно очистить

<a name="isValid"></a>
```php
isValid( string $phoneNumber)
```

Метод валидирует телефон с учетом страны по умолчанию указанной в настройках сайта 

Аргумент | Описание
---|---
$phoneNumber | Номер телефона, который нужно провалидировать
