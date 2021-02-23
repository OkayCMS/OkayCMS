# Документация OkayCMS

## Основные положения

В документации часто может встречаться запись вида "Okay\Core\Response::setContent()", она значит, что имеется в виду 
метод "setContent()" класса "Okay\Core\Response". Это не означает что этот метод статический. Если метод статический,
об этом говорится отдельно.

Если встречаются пути, которые разделенные обратным слешем "\\" это имеется в виду namespace, если пути разделенные
прямым слешем "/" это имеется в виду путь в файловой системе.
Пример неймспейса `Okay\Admin\Controllers`, пример пути `backend/Controllers`.

## Основные типы классов

* [Ядро системы (Core)](./core/README.md)
* [Контроллеры](./controllers.md)
* [Классы сущностей (Entities)](./entities.md)
* [Helpers](./helpers.md)
* [Requests](./requests.md)
* [Маршруты](./routes.md)
* [Подключение внешних файлов дизайна](./js_css_files.md)
* [Smarty плагины](./smarty_plugins.md)
* [Модульность](./modules/README.md)
* [Модуль, быстрый старт](./modules/quick_start.md)
* [Режим разработчика](./dev_mode.md)
* [Пример конфигурации сервера Nginx](./nginx/nginx.conf)
* [Импорт](./import.md)
* [Экспорт](./export.md)
* [Модификация tpl файлов](./tpl_modifiers.md)
* [Работа со скидками](./discounts_management.md)
