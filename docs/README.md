---
title: Документація розробника OkayCMS
type: developer-docs-index
status: active
date: 2026-05-15
verification_status: current
---

# Документація розробника OkayCMS

Цей розділ містить довідкову документацію для розробників OkayCMS: архітектурні
точки розширення, модулі, маршрути, сутності, хелпери, шаблони, імпорт,
експорт і приклади конфігурації.

Документація зберігає початкове призначення: швидко пояснити, як користуватися
наявними API і структурами проєкту. Для деталей, які очевидно видно з типів,
сигнатур, PHPDoc або поточної реалізації, першоджерелом лишається код.

## Розділи

| Розділ | Точка входу |
|---|---|
| Класи ядра | [core/README.md](core/README.md) |
| Контролери | [controllers.md](controllers.md) |
| Сутності | [entities.md](entities.md) |
| Хелпери | [helpers.md](helpers.md) |
| Requests | [requests.md](requests.md) |
| Маршрути | [routes.md](routes.md) |
| Service locator і DI | [service_locator.md](service_locator.md), [di_container.md](di_container.md) |
| Smarty і шаблони | [smarty_plugins.md](smarty_plugins.md), [tpl_modifiers.md](tpl_modifiers.md) |
| Модулі | [modules/README.md](modules/README.md), [modules/quick_start.md](modules/quick_start.md) |
| Імпорт і експорт | [import.md](import.md), [export.md](export.md) |
| Файли і планувальник | [files.md](files.md), [scheduler.md](scheduler.md) |
| JS, CSS і візуальні матеріали | [js_css_files.md](js_css_files.md), [images/](images/) |
| Приклад Nginx | [nginx/nginx.conf](nginx/nginx.conf) |
| Режим розробника | [dev_mode.md](dev_mode.md) |

## Актуальні джерела

- код у `Okay/`, `backend/`, `design/` і `Okay/Modules/`;
- конфігурація сервісів у `Okay/Core/config/`;
- шаблони та ресурси поточної теми;
- `composer.json` для поточних команд перевірки якості.
