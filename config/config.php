;<? exit(); ?>

[database]

;Сервер базы данных
db_server = localhost

;Пользователь базы данных
db_user = root

;Пароль к базе
db_password = ""

;Имя базы
db_name = okaycms-git

;Драйвер базы данных
db_driver = mysql

;Префикс для таблиц
db_prefix = ok_

;Кодировка базы данных
db_charset = UTF8MB4
db_names = utf8mb4

;Режим SQL
db_sql_mode = "ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION"

;Смещение часового пояса
;db_timezone = +04:00

[modules]
marketplace_url = https://modules.okay-cms.com/

[php]
error_reporting = E_ALL
php_charset = UTF8
php_locale_collate = ru_RU
php_locale_ctype = ru_RU
php_locale_monetary = ru_RU
php_locale_numeric = ru_RU
php_locale_time = ru_RU
;php_timezone = Europe/Moscow
debug_mode = false
tmp_dir = tmp/

[smarty]
smarty_compile_check = true
smarty_caching = false
smarty_cache_lifetime = 0
smarty_debugging = false
smarty_html_minify = false
smarty_security = true
; Данный параметр может быть полезным при отладке модификаторов tpl файлов
smarty_force_compile = false

[design]
debug_translation = false
scripts_defer = true
;Режим разработчика, пока только в админке подписывает блоки
dev_mode = false
preload_head_css = false
preload_head_js = false
preload_footer_css = false
preload_footer_js = false

;Пока TplMod находится в состоянии теста, оставим здесь возможность экстренного отключения
;disable_tpl_mod = true

[images]
;Указываем какую библиотеку использовать для нарезки изображений. Варианты: Gregwar, Imagick или GD. Это имя класса адаптера
resize_adapter = Gregwar

;Директория общих изображений дизайна (лого, фавикон...)
design_images = files/images/

;Файл изображения с водяным знаком
watermark_file = backend/files/watermark/watermark.png

;Промо изображения
special_images_dir = files/special/

;Директория оригиналов и нарезок фоток товаров
original_images_dir = files/originals/products/
resized_images_dir = files/resized/products/

;Изображения оригиналов и нарезок фоток блога
original_blog_dir = files/originals/blog/
resized_blog_dir = files/resized/blog/

;Изображения оригиналов и нарезок фоток брендов
original_brands_dir = files/originals/brands/
resized_brands_dir = files/resized/brands/

;Изображения оригиналов и нарезок фоток категории
original_categories_dir = files/originals/categories/
resized_categories_dir = files/resized/categories/

;Изображения оригиналов и нарезок фоток категории блога
original_blog_categories_dir = files/originals/blog_categories/
resized_blog_categories_dir = files/resized/blog_categories/

;Изображения оригиналов и нарезок фоток авторов
original_authors_dir = files/originals/authors/
resized_authors_dir = files/resized/authors/

;Изображения оригиналов и нарезок фоток доставки
original_deliveries_dir = files/originals/deliveries/
resized_deliveries_dir = files/resized/deliveries/

; Папка изображений преимуществ
original_advantages_dir = files/originals/advantages/
resized_advantages_dir = files/resized/advantages/

;Изображения оригиналов и нарезок фоток способов оплаты
original_payments_dir = files/originals/payments/
resized_payments_dir = files/resized/payments/

; Папка изображений языков
lang_images_dir = files/originals/lang/
lang_resized_dir = files/resized/lang/
