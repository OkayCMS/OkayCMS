# Класс Okay\Core\Response

<a name="setContent"></a>
```php
setContent( string $content [, string $type = RESPONSE_HTML])
```

Установка данных, которые должны попасть в ответ.

Аргумент | Описание
---|---
$content | Контент, который нужно отдать пользователю.
$type | Одна из [констант типов ответов](#contentTypesConstants)


<a name="contentTypesConstants"></a>
#### Типы ответов

От типа ответа зависит какой адаптер респонса использовать. Все адаптеры находятся в `Okay\Core\Adapters\Response`.
Также каждый адаптер добавляет свои индивидуальные HTTP заголовки (Content-Type etc).

Константа | Тип ответа
---|---
RESPONSE_HTML | Ответ в виде HTML кода. При таком типе ответа в качестве контента можно передавать название tpl файла, результат компиляции которого нужно установить в качестве ответа.
RESPONSE_JSON | Ответ в JSON формате.
RESPONSE_XML  | Ответ в XML формате.
RESPONSE_JAVASCRIPT | Ответ JavaScript. Используется когда отдаются компилированные JS файлы.
RESPONSE_IMAGE | Ответ изображение.
RESPONSE_TEXT | Ответ в виде просто текста.
