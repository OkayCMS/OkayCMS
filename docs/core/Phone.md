# Клас Okay\Core\Phone

Клас призначений для роботи з номерами телефонів.

<a name="format"></a>
```php
format( string $phoneNumber [, int|string|\libphonenumber\PhoneNumberFormat|null $numberFormat = null]): string
```

Метод форматує телефон відповідно до вказаного формату. Якщо формат не вказано, він береться з налаштувань сайту.
Також цей метод можна викликати в дизайні через Smarty-модифікатор `|phone`.

Приклад:
```smarty
{$user->phone|phone}
```

Аргумент | Опис
---|---
$phoneNumber | Номер телефону, який потрібно відформатувати
$numberFormat | Формат номера: enum `\libphonenumber\PhoneNumberFormat`, int/string-значення формату або `null`

Варіанти констант:

Enum case | Приклад номера телефону
---|---
PhoneNumberFormat::E164 | +380442903833
PhoneNumberFormat::INTERNATIONAL | +380 44 290 3833
PhoneNumberFormat::NATIONAL | 044 290 3833
PhoneNumberFormat::RFC3966 | tel:+380-44-290-3833

<a name="toSave"></a>
```php
toSave( string $phoneNumber): ?string
```

Метод готує номер телефону для збереження в базу даних; у базі вони зберігаються у стандарті E164.

Аргумент | Опис
---|---
$phoneNumber | Номер телефону, який буде збережено в базу даних

Приклад збереження телефону в замовленні:
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
clear( string $phoneNumber): string
```

Метод очищує телефон від усіх зайвих символів, які не можуть бути номером телефону

Аргумент | Опис
---|---
$phoneNumber | Номер телефону, який потрібно очистити

<a name="isValid"></a>
```php
isValid( string $phoneNumber): bool
```

Метод валідує телефон з урахуванням країни за замовчуванням, вказаної в налаштуваннях сайту

Аргумент | Опис
---|---
$phoneNumber | Номер телефону, який потрібно провалідувати
