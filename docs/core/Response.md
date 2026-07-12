# Клас Okay\Core\Response

<a name="setContent"></a>
```php
setContent( mixed $content [, ?string $type = null]): self
```

Встановлення даних, які мають потрапити у відповідь.

Аргумент | Опис
---|---
$content | Контент, який потрібно віддати користувачу.
$type | Одна з [констант типів відповідей](#contentTypesConstants)


<a name="contentTypesConstants"></a>
#### Типи відповідей

Від типу відповіді залежить, який адаптер Response використовувати. Усі адаптери розташовані в `Okay\Core\Adapters\Response`.
Також кожен адаптер додає свої індивідуальні HTTP-заголовки (Content-Type тощо).

Константа | Тип відповіді
---|---
RESPONSE_HTML | Відповідь у вигляді HTML-коду. За такого типу відповіді як контент можна передавати назву tpl файлу, результат компіляції якого потрібно встановити як відповідь.
RESPONSE_JSON | Відповідь у форматі JSON.
RESPONSE_XML  | Відповідь у форматі XML.
RESPONSE_JAVASCRIPT | Відповідь JavaScript. Використовується, коли віддаються скомпільовані JS файли.
RESPONSE_IMAGE | Відповідь-зображення.
RESPONSE_TEXT | Відповідь у вигляді простого тексту.
