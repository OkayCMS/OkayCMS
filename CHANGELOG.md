# Changelog

Усі помітні зміни в OkayCMS документуються в цьому файлі.

Формат базується на [Keep a Changelog](https://keepachangelog.com/uk/1.1.0/),
а проєкт дотримується [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Невипущено]

- Заплановано першочерговий план виправлення безпекових меж (Remediation Map): ліквідація рефлектованого XSS у формі коментарів та впровадження обов'язкової CSRF-валідації для публічних мутацій (коментарі, замовлення, швидке замовлення).
- Заплановано забезпечення цілісності платіжних інтеграцій: посилення перевірки автентичності та обов'язкової перевірки підписів для інбоунд-колбеків WayForPay та RozetkaPay перед фіксацією оплати, а також відмова від небезпечного unserialize на користь JSON у налаштуваннях модулів.
- Заплановано додання комплексу заходів із посилення безпеки оточення: впровадження CSP (Content Security Policy) у Report-Only режимі з подальшим переведенням в Enforce, ізоляція сесійних кук (`okay_sid` / `okay_admin_sid`) та аутентифікаційних токенів, а також ліквідація авторизованого traversal-шляху в інтеграції з 1С.
- Заплановано оптимізації SQL-запитів та продуктивності для MySQL 8.4:
  - Прискорення фільтрації й сортування за ціною/залишками.
  - Усунення N+1 проблеми завантаження категорій у `ProductsHelper::attachDescriptionByTemplate()`
  - Консервативне скасування надлишкового `DISTINCT` для важких `mediumtext` колонок у базовому `CRUD.php` за наявності активного `GROUP BY`.
  - Оптимізація випадкового вибору товарів (`ORDER BY RAND()`)

### Додано

- Додано потоковий нормалізатор CSV-імпорту: завантажений файл визначається за BOM/UTF-8-перевіркою та набором поширених кириличних кодувань Windows/macOS/DOS/Unix, після чого внутрішній `backend/files/import/import.csv` завжди зберігається як UTF-8 без BOM.
- Додано визначення розділювача CSV-полів для імпорту (`;`, `,`, `|`, tab) з підтримкою Excel-підказки `sep=...`; внутрішній файл імпорту нормалізується до поточного розділювача `;`.
- Додано нормалізацію числових значень імпорту після зіставлення колонок: ціна, стара ціна та вага коректно приймають decimal comma/decimal dot і прості розділювачі тисяч, не змінюючи текстові поля на кшталт SKU або телефону.
- Додано вибір формату експорту товарів у backend UI: `CSV UTF-8` за замовчуванням і `CSV UTF-8 BOM (Excel)` для сумісності з Excel на Windows.
- Додано спільний CSV writer для backend-експортів із підтримкою UTF-8 без BOM, UTF-8 BOM, потокового append-запису та захисту Excel/BOM-експорту від формульно-подібних текстових значень.
- Додано root-вимогу Composer `ext-iconv`, потрібну для потокової конвертації кодувань CSV-імпорту.
- Додано `scheduler:list --json` для машинного читання зареєстрованих scheduled tasks у CLI/agent/automation сценаріях.
- Додано документацію для agent workflow: issue tracker, triage labels і domain-boundary для Core та symlinked/vendor-модулів.
- Додано плани подальшого security remediation для storefront і backend, а також окремий review-план оптимізації бази даних.
- Додано план другої ітерації імпорту/експорту XLSX через OpenSpout v5; legacy `.xls` залишено поза scope.
- Додано release-команди `release:changelog:draft` і `release:changelog:check`, які формують reviewable draft за Conventional Commits і перевіряють, чи `[Невипущено]` покриває зміни з вибраного git-діапазону.

### Змінено

- CSV-експорти більше не перекодовують фінальний файл у Windows-1251: товари, замовлення, користувачі, підписники і статистика записуються як валідний UTF-8, зберігаючи існуючий chunked AJAX progress і append-запис для великих файлів.
- При CSV-імпорті UTF-8 BOM більше не потрапляє у перший заголовок колонки, тому зіставлення колонок не ламається через невидимий BOM-символ.
- При CSV-імпорті всі текстові значення очищуються від незначущих пробілів зліва і справа, але внутрішні пробіли в значеннях зберігаються.
- Файл `backend/files/import/example.csv` переведено у UTF-8 без BOM, щоб приклад імпорту відповідав новому внутрішньому контракту.
- Оновлено імпортний file input у backend: на першій CSV-ітерації дозволено вибір `.csv`; `.xlsx` буде додано в UI тільки разом із реальною OpenSpout-реалізацією.
- Для Нової Пошти розділено refs міста для доставки у відділення і settlement ref для адресної доставки, щоб browser autofill або попереднє замовлення не могли підставити ref з одного режиму доставки в інший.
- Для Нової Пошти checkout-збереження, валідація та відновлення даних останнього замовлення тепер використовують mode-specific поля: `novaposhta_warehouse_city_ref` для відділення і `novaposhta_door_settlement_ref` для адресної доставки.
- Для Нової Пошти API throttling зроблено opt-in: пауза між запитами залишилася для bulk cache refresh/cron/admin-оновлень, але storefront autocomplete і checkout-запити більше не отримують зайву затримку.
- Адмінське оновлення кешу Нової Пошти збільшено до більших batch-ів і явно викликає throttled API-запити, щоб прискорити оновлення довідників без перевантаження API.
- На storefront перший банер головної сторінки отримує `loading="eager"`, `fetchpriority="high"` і `decoding="async"`, а Font Awesome повернено в локальний CSS bundle замість окремого CDN preload, щоб критичні ресурси оброблялися передбачуваніше.
- На storefront jQuery 3.7.0 і Fancybox CSS тепер завантажуються з локальних theme assets замість CDN, щоб frontend не залежав від зовнішніх ресурсів під час рендерингу магазину.
- Уточнено тестовий контракт реєстру багатомовних сутностей: core product language fields перевіряються як стабільний префікс, дозволяючи модулям додавати власні поля у тому самому PHPUnit-процесі.
- Оновлено PHPStan до гілки 2.2 і синхронізовано release/tooling-залежності Composer, щоб статичний аналіз працював на актуальному стеку PHP 8.5.
- Файл `backend/files/import/import.csv` оновлено як нормалізований UTF-8 fixture із розділювачем `;`, щоб smoke-перевірки імпорту відповідали новому CSV-контракту.
- У `.gitignore` додано nested runtime logs, щоб локальні/generated лог-файли не потрапляли у робоче дерево.

### Виправлено

- Виправлено autocomplete Нової Пошти в checkout: вибір міста/вулиці тепер прив'язаний до конкретного блоку доставки та input, а не до глобально вибраного radio delivery, тому кілька способів доставки на сторінці не перезаписують дані один одного.
- Виправлено пошук вулиць Нової Пошти: autocomplete ініціалізується для вже вибраного міста адресної доставки, працює з першого символу, не кешує некоректні порожні відповіді та не блокує повторні пошуки після API-помилки.
- Виправлено API-відповіді autocomplete Нової Пошти: при порожній або помилковій відповіді API endpoint повертає стабільний JSON із `suggestions: []`, а не payload з `error`, який ламав frontend autocomplete contract.
- Виправлено checkout Нової Пошти для доставки у відділення: серверна валідація тепер вимагає саме warehouse city ref і не приймає ref адресної доставки як місто відділення.
- Виправлено видалення товарів із кошика при натисканні Enter у checkout-полях: кнопки видалення більше не є submit-кнопками форми та не можуть стати implicit default submitter замість оформлення замовлення.
- Виправлено CLI entrypoint `ok`: режим відладки тепер береться з основного `debug_mode` і однаково керує показом startup errors та debug-деталей у console-командах.

### Безпека

- Підготовлено окремі remediation-плани для frontend і backend security audit, щоб після 4.6.0 виправлення безпекових меж можна було виконувати контрольованими, перевірюваними ітераціями.
- У Responsive Filemanager дозволено завантаження SVG тільки після sanitization boundary: активний вміст і небезпечні атрибути відсікаються, а hardened upload whitelist зберігається.

## [4.6.0] - 2026-05-21

### Несумісні зміни

- Мінімальна підтримувана версія PHP підвищена до PHP 8.5; встановлення та оновлення тепер потребують сумісного runtime і залежностей Composer для PHP 8.5.
- Оновлено основні runtime-залежності до нових major-версій, зокрема Smarty 5, Symfony 8, PSR-3/PSR-11, Aura SQL 6, PHPMailer 7, Mobile Detect 4, libphonenumber 9, chillerlan QR Code 6, DebugBar 3 та PHPUnit 13 для тестового середовища.

### Додано

- Додано єдиний механізм очищення runtime-артефактів (`composer cache:clear`) з підтримкою `.keep_folder`, збереженням `cache/codes/` і викликом Smarty `clearCache()`.
- Додано `.prodignore` як єдине джерело виключень для production install/upgrade package builders.
- Додано локальні кнопки поширення для сторінок товарів і публікацій без залежності від зовнішньої бібліотеки `jssocials`, включно з SVG-іконками для популярних каналів і копіюванням посилання.
- Додано керування порогом показу кнопки Back to TOP через атрибут `data-show-offset` у шаблоні кнопки: `data-show-offset="auto"` або порожнє/некоректне значення зберігає стару поведінку з показом після прокрутки на висоту екрана; `data-show-offset="500"` показує кнопку після 500px прокрутки; `data-show-offset="0"` показує кнопку одразу після початку прокрутки.
- Додано генератор QR-коду НБУ з прикладом використання та тестами для перевірки формату результату.
- Додано власний AI-клієнт для OpenAI-сумісного текстового API, каталог моделей і потокову відповідь без залежності від `orhanerday/open-ai`.
- Додано підтримку resize-адаптера на базі Intervention Image та окремий WebP-конвертер для сучасного стеку обробки зображень.
- Додано PDO-collector і форматування SQL-запитів для DebugBar 3, щоб діагностика запитів залишалася доступною після оновлення debug-панелі.
- Додано явний реєстр багатомовних сутностей ядра замість runtime-пошуку класів через `haydenpierce/class-finder`.
- Додано набір міграційних перевірок, smoke-тестів і Composer-команд для аналізу, тестування, аудиту безпеки та контролю PHP 8.5-сумісності.
- Додано безпековий workflow `Security Scan` у GitHub Actions: Gitleaks виконує blocking-перевірку секретів, а Semgrep CE запускається як non-blocking SAST-перевірка для поступового triage.
- Додано локальні wrapper-скрипти `dev/scripts/security-gitleaks.sh` і `dev/scripts/security-semgrep.sh`, щоб ті самі перевірки можна було запускати перед PR або релізом без додаткового платного сервісу.
- Додано browser-smoke інструкцію для Responsive Filemanager, щоб перевіряти upload, preview, insert, rename, download і доступи після змін у security boundary.
- Додано відстежувану DDEV-конфігурацію (`.ddev/config.yaml`, post-start hooks) і шаблони в `dev/ddev/` для передбачуваного cold-start без ручного bootstrap.
- Додано `database:deploy --yes` / `-y` для неінтерактивного розгортання БД (агенти, CI, `composer create-project`).
- Додано `Makefile`, `dev/scripts/composer.sh` (DDEV-first) та `agent-compatibility.config.json` для agent-compatibility scan.
- Додано проєктну конфігурацію Phpactor зі схемою для автодоповнення, PHPStan diagnostics і PHPCS diagnostics у редакторі.
- Додано `phpcbf-on-save`, спільний helper для IDE wrapper-скриптів і перезапис шляхів PHPCS у JSON-виводі, щоб локальні редактори коректно працювали з DDEV-шляхами.

### Змінено

- Ядро, frontend, backend, модулі та шаблони адаптовано до PHP 8.5: уточнено типи, PHPDoc-контракти, nullability, обробку ресурсів, enum-значень, iterable-структур і edge-case вхідних даних без цільової зміни бізнес-логіки.
- Smarty оновлено до 5.x; шаблони, плагіни та конфігурація frontend/backend приведені до сумісного синтаксису та поведінки.
- Стек роботи із зображеннями переведено з `gregwar/image` і `rosell-dk/webp-convert` на Intervention Image та локальний WebP boundary.
- Мінімізацію JavaScript переведено з `matthiasmullie/minify` на `wikimedia/minify`, а конфігурацію template assets оновлено під новий runtime.
- Snowplow Referer Parser переведено у vendored runtime-код, щоб прибрати залежність від застарілого пакета й зберегти визначення джерел переходів.
- Mobile Detect оновлено до 4.x із новим runtime-класом і перевіркою реального bootstrap-шляху дизайну.
- PHPMailer оновлено до 7.x; mail-boundary і перевірки Reply-To адаптовано до нового формату бібліотеки.
- Symfony Console, Process і Lock оновлено до 8.x; CLI-команди, scheduler і lock-release поведінку покрито characterization/regression-тестами.
- PSR Container і PSR Log оновлено до актуальних контрактів, а контейнер і logging integration приведено до нових інтерфейсів.
- Aura SQL Query оновлено до сучасних версій, а проблемні запити з `IN`, `joinSubSelect` і bind values приведено до сумісних патернів.
- PHPStan піднято до рівня 8 для core gate; код і baseline очищені від застарілих suppressions, а active vendor modules включені в поточний статичний контроль.
- PHPCS переведено на версію 4 із blocking-перевіркою entrypoints і окремим advisory full-tree style check.
- Composer-скрипти оновлено для єдиного запуску аналізу, тестів, аудиту безпеки та міграційних gate-команд; фінальний release gate використовує `composer check` із Composer validation, locked security audit, full-tree PHPCS, PHPStan і PHPUnit.
- GitHub Actions оновлено до єдиного workflow `PHP Quality`, а Dependabot налаштовано для регулярних minor/patch оновлень Composer і GitHub Actions без автоматичного major-стрибка.
- Semgrep налаштовано ігнорувати згенеровані/runtime директорії, bundled frontend-бібліотеки та вже розібраний legacy-шум Responsive Filemanager, щоб нові security finding-и поза цією legacy-зоною були помітні без ручного відсіювання відомих спрацювань.
- Уточнено PHPDoc для route-параметрів storefront-контролерів, щоб IDE diagnostics не спонукали додавати native scalar types там, де роутер трактує типізовані параметри як service injection.
- Storefront performance tracker `ut_tracker` тепер підключається лише в `debug_mode`, а production-конфігурація за замовчуванням явно вимикає debug output.
- Нормалізовано `design/okay_shop/css/theme-settings.css`, щоб файл мав службовий коментар і стабільний формат CSS variables для налаштувань теми.

### Видалено

- Видалено залежності `gregwar/image`, `rosell-dk/webp-convert`, `orhanerday/open-ai`, `haydenpierce/class-finder`, `snowplow/referer-parser`, `matthiasmullie/minify` і `jssocials`.
- Видалено legacy frontend-шаблони, npm/semgrep scaffolding, експериментальні build-файли, які не входять у production runtime.
- Видалено застарілі export CSV-зразки з backend files, щоб не тримати generated/example data у релізному дереві.
- Видалено застарілий `backend/design/js/codemirror/package.json`, який не використовується production runtime і створював зайвий шум для dependency/security tooling.

### Виправлено

- Виправлено bootstrap `Image` через `AdapterManager::configure()` після типізації PHP 8.5: offset watermark з `Settings` тепер безпечно приводяться до `int`.
- Виправлено несумісності PHP 8.3-8.5 у фільтрах, SQL-запитах, приведенні типів, null handling, CSV/import/export потоках, resize-адаптерах і Smarty-шаблонах.
- Виправлено OpenAI SSE stream: відповіді більше не ламаються через некоректне розбиття рядків, а EventSource URL формується без небезпечного ручного складання query string.
- Виправлено дублювання та некоректну нормалізацію category filter IDs, зокрема для порожніх і брудних значень.
- Виправлено сумісність PhoneNumberFormat у налаштуваннях і шаблонах після переходу на libphonenumber 9.
- Виправлено backend і storefront password recovery: recovery-посилання більше не залежать від початкової PHP-сесії, не логінять користувача до явної зміни пароля, не приймають порожній пароль як валідний reset і коректно обробляють повторне використання токенів.
- Виправлено валідацію збережених паролів менеджерів: порожні або некоректні хеші тепер безпечно відхиляються без PHP warnings, а валідні legacy APR1-хеші перевіряються через явну сумісну гілку.
- Виправлено runtime-помилки з WebpConverter DI, Vitalisoft Analytics route handling, Novaposhta NPCalcVO та `axy/sourcemap` на нових PHP-версіях.
- Виправлено відображення DebugBar 3 toolbar CSS і сумісність debug widgets після оновлення бібліотеки.
- Виправлено release scheduler lock release path, щоб lock звільнявся навіть після помилок у виконанні задач.
- Виправлено генерацію шляхів до шаблонів модулів, щоб змонтовані або symlinked модулі не втрачали `design/html` через `realpath()` на проміжному шляху.
- Виправлено PHP 8.5 warning/deprecation у Responsive Filemanager upload handler для non-chunked upload, порожнього `HTTP_REFERER` і `basename()` без явного suffix.
- Виправлено download у Responsive Filemanager після hardening: форма більше не залежить від jQuery submit у inline handler, а шлях і назва файлу проходять нормалізацію перед читанням.
- Виправлено update path для налаштувань social share після заміни `jssocials`: clean install і оновлення 4.5.2 -> 4.6.0 тепер нормалізують legacy theme values, legacy share IDs, порожні та некоректні serialized selection-и до поточного набору кнопок.
- Виправлено `database:upgrade --dry-run`, щоб для діапазону 4.5.2 -> 4.6.0 він показував post-SQL normalizer `social-share-settings` без підключення до бази.
- Виправлено тестову ізоляцію `UpgradeApplierTest`: writable fixtures копіюються в тимчасовий проєкт, тому перевірка більше не створює `Okay/NewFile.php`, `config/config.local.php` чи інші артефакти в реальному checkout.
- Виправлено storefront AJAX для пошуку товарів, щоб виклики debug tracker не виконувалися, коли `ut_tracker` не оголошено.

### Безпека

- Додано Composer security audit до релізних gate-команд і зафіксовано clean-audit workflow для PHP 8.5-гілки.
- Посилено безпеку backend: pre-auth шаблони екрануються у правильному контексті, CSRF перевіряється до mutation-коду, admin session ID регенерується після входу або recovery-login, а cookie-атрибути задаються явно.
- Обмежено доступ до filemanager і protected backend downloads: direct entrypoints перевіряють авторизованого менеджера з потрібними дозволами, активний web-контент блокується для upload/URL upload/rename/duplicate/text-file creation, а generated downloads прив'язані до конкретних permissions.
- Посилено storefront auth boundary: recovery tokens зберігаються як digest, reset URL працює як bearer authority тільки до обміну на короткоживучий state, CSRF cookie/session state ізольовано від privilege transitions.
- Нові й оновлені паролі менеджерів зберігаються через `password_hash()` з Argon2id або bcrypt fallback; legacy APR1-хеші автоматично rehash-яться після успішної перевірки.
- Оператори фільтрів feed presets для ціни й залишків нормалізуються через allowlist `<`, `>` і `=`, щоб збережені або POST-значення не могли змінювати SQL-фрагмент.
- Посилено Responsive Filemanager: локальні filesystem paths проходять через resolver, який відхиляє traversal, absolute paths, scheme paths і вихід за межі upload-root; `copy`, `cut`, `chmod`, preview, download, upload і archive extraction більше не складають локальний шлях напряму з request-параметрів.
- Вимкнено remote URL upload у Responsive Filemanager, щоб адміністраторський filemanager не міг завантажувати довільний server-side URL.
- Екрановано preview/download HTML і iframe URL у Responsive Filemanager, щоб назви файлів, title і сформовані URL не потрапляли в HTML/JavaScript без контекстного escaping.
- Посилено приклади nginx-конфігурації: прибрано redirect через host-derived `$server_name` і широкі static asset headers, а `/admin` redirect у документації зроблено явним для цільового домену.
- Задокументовано legacy admin password migration boundary, включно з вимогою безпечно відхиляти некоректні legacy-хеші та не приймати порожній пароль як валідний reset.
- Посилено storefront cart boundary: додавання, видалення, зміна кількості, coupon apply і checkout тепер виконуються через POST із наявним `customer_csrf_token`; GET-мутації повертають 405, а некоректний токен повертає 403.
- Посилено P1 storefront-мутації для wishlist, comparison, feedback, subscribe, blog і product форм: вони використовують існуючий customer CSRF boundary, а theme templates і AJAX передають токен явно.
- Додано базові HTML security headers для storefront response (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`) і прибрано точний `X-Powered-CMS` version header.
- Уточнено обробку plain-text POST-полів: `Request::post()` зберігає raw value за замовчуванням, а storefront text-boundary-и явно вмикають tag stripping і перевірку `Validator::isSafe()`.
- Посилено support endpoint: дозволено тільки JSON POST, додано content-type/status validation, rate limiting, constant-time key comparison, безпечне логування відмов і очищення auth-attempts після успішної авторизації.
- Розділено frontend і backend session names (`okay_sid` / `okay_admin_sid`) та додано helper для синхронізації/очищення admin session без session name на базі user-agent hash.
- Cookie-и кошика, browsing/preference state і admin logout тепер отримують явні `secure`, `httponly` і `samesite=Lax` атрибути, а storefront JavaScript більше не читає `shopping_cart` напряму.
- reCAPTCHA з `invalid-input-secret` тепер fail-closed і пише діагностичний log message замість прийняття перевірки як успішної.
- Посилено resize/image boundary: traversal, absolute paths, NUL bytes, небезпечні remote image paths і не-HTTPS remote source-и відхиляються до роботи з filesystem.
