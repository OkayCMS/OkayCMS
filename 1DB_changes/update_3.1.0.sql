-- 0
CREATE TABLE `ok_lessons` (
  `id` INT AUTO_INCREMENT,
  `preview` VARCHAR(255),
  `video` VARCHAR(255),
  `title` VARCHAR(255),
  `description` TEXT,
  `button` VARCHAR(255),
  `target_module` VARCHAR(255),
  `done` TINYINT(1),
  PRIMARY KEY (`id`)
);

INSERT INTO `ok_lessons` (`id`, `preview`, `video`, `title`, `description`, `button`, `target_module`, `done`) VALUES
(1,	'https://img.youtube.com/vi/Ud_B0XGcLFI/mqdefault.jpg',	'https://www.youtube.com/embed/Ud_B0XGcLFI',	'Добавление категорий на сайт',	'Категории используются для построения правильной структуры сайта. Старайтесь не допускать категорий со слишком большим количеством товаров, но и не делать категории в которых будет до десяти товаров.',	'Перейти к добавлению категорий',	'CategoriesAdmin',	NULL),
(2,	'https://img.youtube.com/vi/pAr5CkadzMA/mqdefault.jpg',	'https://www.youtube.com/embed/pAr5CkadzMA',	'Добавление бренда на сайт', 'Бренды используются в фильтре в категориях товаров. Также они важны для покупателей, которые опираются на бренд при выборе товара.',	'Перейти к добавлению бренда',	'BrandsAdmin',	NULL),
(3,	'https://img.youtube.com/vi/X4B2IkQwI5g/mqdefault.jpg',	'https://www.youtube.com/embed/X4B2IkQwI5g',	'Добавление товара на сайт', 'Чем качественнее будет заполнена информация о товаре, тем больше шансов что клиент остановит свой поиск именно у вас. Обязательно укажите в товаре его фото и основные характеристики.',	'Перейти к добавлению товара',	'ProductsAdmin',	NULL),
(4,	'https://img.youtube.com/vi/ccq4Plnqz9M/mqdefault.jpg',	'https://www.youtube.com/embed/ccq4Plnqz9M',	'Настройка валюты',	'Укажите валюту вашей страны основной на сайте. Если же вы используете несколько валют для формирования цен товаров, укажите соответствующие курсы валют в админ. панели.',	'Перейти к настройке валюты',	'CurrencyAdmin',	NULL),
(5,	'https://img.youtube.com/vi/R-VNpOHnk7w/mqdefault.jpg',	'https://www.youtube.com/embed/R-VNpOHnk7w',	'Добавление способа доставки', 'Выбор способов доставки важен для клиентов при оформлении заказа. Некоторые клиенты отказываются от оформления заказа не увидев подходящий вариант доставки. Постарайтесь добавить самые популярные способы доставки на ваш сайт.',	'Перейти к добавлению способа доставки',	'DeliveriesAdmin',	NULL),
(6,	'https://img.youtube.com/vi/CgDtgLfJnCY/mqdefault.jpg',	'https://www.youtube.com/embed/CgDtgLfJnCY',	'Добавление способа оплаты', 'Всё больше клиентов готовы оплатить заказ сразу после оформление. Предоставьте им такую возможность подключив интеграцию с платежной системой на ваш сайт.',	'Перейти к добавлению способа оплаты',	'PaymentMethodsAdmin',	NULL),
(7,	'https://img.youtube.com/vi/W67M4W5uJr4/mqdefault.jpg',	'https://www.youtube.com/embed/W67M4W5uJr4',	'Настройка каталога', 'Задайте водяной знак, настройте работу с товарами не в наличии и сделайте прочие настройки каталога на этой странице.',	'Перейти к настройке каталога',	'SettingsCatalogAdmin',	NULL),
(8,	'https://img.youtube.com/vi/PH6zFFSmU3Q/mqdefault.jpg',	'https://www.youtube.com/embed/PH6zFFSmU3Q',	'Настройка отправки уведомлений', 'Настройте отправку уведомлений с сайта, укажите эмейл администратора и прочие настройки писем на этой странице.',	'Перейти к настройке отправки уведомлений',	'SettingsNotifyAdmin',	NULL),
(9,	'https://img.youtube.com/vi/PZ2xxMaLNS8/mqdefault.jpg',	'https://www.youtube.com/embed/PZ2xxMaLNS8',	'Настройка SEO (синонимы свойства)', 'Этот раздел важен для тонкой SEO оптимизации вашего магазина. К нему стоит приступать после того как вы сделали остальные пункты. С помощью синонимов свойств вы можете гибко настроить заголовки и мета-теги для страниц каталога.',	'Перейти к настройке SEO (синонимы свойств)',	'FeaturesAliasesAdmin',	NULL);

CREATE TABLE `ok_lang_lessons` (
  `lesson_id` INT,
  `lang_id` INT,
  `title` VARCHAR(255),
  `description` TEXT,
  `button` VARCHAR(255),
  PRIMARY KEY (`lesson_id`, `lang_id`)
);

INSERT INTO `ok_lang_lessons` (`lesson_id`, `lang_id`, `title`, `description`, `button`) VALUES
(1,	1,	'Добавление категорий на сайт',	'Категории используются для построения правильной структуры сайта. Старайтесь не допускать категорий со слишком большим количеством товаров.',	'Перейти к добавлению категорий'),
(1,	2,	'Adding Categories To Site', 'Categories are used to build the correct site structure. Try to avoid categories with too many products.',	'Go To Add Categories'),
(1,	3,	'Додавання категорій на сайт', 'Категорії використовуються для побудови правильної структури сайту. Намагайтеся не допускати категорій з дуже великою кількістю товарів.', 'Перейти до додавання категорій'),
(2,	1,	'Добавление бренда на сайт', 'Бренды используются в фильтре в категориях товаров. Также они важны для покупателей, которые опираются на бренд при выборе товара.', 'Перейти к добавлению бренда'),
(2,	2,	'Adding Brand To Site',	'Brands are used in the filter in product categories. They are also important for buyers who rely on the brand when choosing a product.',	'Go To Add Brand'),
(2,	3,	'Додавання бренду на сайт',	'Бренди використовуються в фільтрі в категоріях товарів. Також вони важливі для покупців, які спираються на бренд при виборі товару.',	'Перейти до додавання бренду'),
(3,	1,	'Добавление товара на сайт', 'Чем качественнее будет заполнена информация о товаре, тем больше шансов что клиент остановит свой поиск именно у вас. Обязательно укажите в товаре его фото и основные характеристики.', 'Перейти к добавлению товара'),
(3,	2,	'Adding Product To Site',	'The better the product information is filled out, the more likely it is that the client will stop your search exactly with you. Be sure to include in the product its photo and main characteristics.', 'Go to Add Product'),
(3,	3,	'Додавання товару на сайт',	'Чим якісніше буде заповнена інформація про товар, тим більше шансів що клієнт зупинить свій пошук саме у вас. Обов''язково вкажіть в товарі його фото і основні характеристики.', 'Перейти до додавання товару'),
(4,	1,	'Настройка валюты', 'Укажите валюту вашей страны основной на сайте. Если же вы используете несколько валют для формирования цен товаров, укажите соответствующие курсы валют в админ. панели.', 'Перейти к настройке валюты'),
(4,	2,	'Currency Settings',	'Indicate the currency of your country of primary on the site. If you use several currencies for pricing of goods, specify the appropriate exchange rates in the admin. panels.',	'Go To Currency Settings'),
(4,	3,	'Налаштування валюти',	'Вкажіть валюту вашої країни основний на сайті. Якщо ж ви використовуєте кілька валют для формування цін товарів, вкажіть відповідні курси валют в адмін. панелі.', 'Перейти до налаштування валюти'),
(5,	1,	'Добавление способа доставки', 'Выбор способов доставки важен для клиентов при оформлении заказа. Некоторые клиенты отказываются от оформления заказа не увидев подходящий вариант доставки. Постарайтесь добавить самые популярные способы доставки на ваш сайт.', 'Перейти к добавлению способа доставки'),
(5,	2,	'Adding Delivery Method',	'The choice of delivery methods is important for customers when placing an order. Some customers refuse to place an order without seeing the appropriate delivery option. Try to add the most popular delivery methods to your site.', 'Go To Add Delivery Method'),
(5,	3,	'Додавання способу доставки',	'Вибір способів доставки важливий для клієнтів при оформленні замовлення. Деякі клієнти відмовляються від оформлення замовлення не побачивши підходящий варіант доставки. Постарайтеся додати найпопулярніші способи доставки на ваш сайт.', 'Перейти до додавання способу доставки'),
(6,	1,	'Добавление способа оплаты', 'Всё больше клиентов готовы оплатить заказ сразу после оформление. Предоставьте им такую возможность подключив интеграцию с платежной системой на ваш сайт.', 'Перейти к добавлению способа оплаты'),
(6,	2,	'Adding Payment Method',	'More and more customers are ready to pay for the order immediately after registration. Give them such an opportunity by connecting integration with the payment system to your website.', 'Go To Add Payment Method'),
(6,	3,	'Додавання способу оплати',	'Все більше клієнтів готові оплатити замовлення відразу після оформлення. Необхідно надати їм таку можливість підключивши інтеграцію з платіжною системою на ваш сайт.', 'Перейти до додавання способу оплати'),
(7,	1,	'Настройка каталога', 'Задайте водяной знак, настройте работу с товарами не в наличии и сделайте прочие настройки каталога на этой странице.', 'Перейти к настройке каталога'),
(7,	2,	'Catalog Settings',	'Set a watermark, configure work with out-of-stock goods and make other catalog settings on this page.', 'Go To Catalog Settings'),
(7,	3,	'Налаштування каталога', 'Задайте водяний знак, налаштуйте роботу з товарами не в наявності і зробіть інші настройки каталогу на цій сторінці.', 'Перейти до налаштування каталогу'),
(8,	1,	'Настройка отправки уведомлений', 'Настройте отправку уведомлений с сайта, укажите эмейл администратора и прочие настройки писем на этой странице.', 'Перейти к настройке отправки уведомлений'),
(8,	2,	'Notify Settings', 'Configure sending notifications from the site, specify the administrator''s email address and other settings for letters on this page.', 'Go To Notify Settings'),
(8,	3,	'Налаштування відправки повідомлень',	'Налаштуйте відправку повідомлень з сайту, вкажіть емейл адміністратора та інші настройки листів на цій сторінці.', 'Перейти до налаштування відправки повідомлень'),
(9,	1,	'Настройка SEO (синонимы свойства)', 'Этот раздел важен для тонкой SEO оптимизации вашего магазина. К нему стоит приступать после того как вы сделали остальные пункты. С помощью синонимов свойств вы можете гибко настроить заголовки и мета-теги для страниц каталога.', 'Перейти к настройке SEO (синонимы свойства)'),
(9,	2,	'SEO customization (synonyms for properties)', 'This section is important for fine-tuning SEO of your store. It is worth starting after you have done the rest of the points. Using property synonyms, you can flexibly customize headers and meta tags for catalog pages.', 'Go To SEO customization (synonyms for properties)'),
(9,	3,	'Налаштування SEO (синоніми властивості)', 'Цей розділ важливий для тонкої SEO оптимізації вашого магазину. До нього варто приступати після того як ви зробили інші пункти. За допомогою синонімів властивостей ви можете гнучко налаштувати заголовки і мета-теги для сторінок каталогу.', 'Перейти до налаштування SEO (синоніми властивості)');

ALTER TABLE `ok_deliveries`
ADD `settings` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL,
ADD `module_id` int(11) NULL AFTER `settings`;

ALTER TABLE `ok_deliveries`
CHANGE `free_from` `free_from` decimal(10,2) NULL DEFAULT '0.00' AFTER `description`,
CHANGE `price` `price` decimal(10,2) NULL DEFAULT '0.00' AFTER `free_from`;

ALTER TABLE `ok_orders` MODIFY `ip` varchar(40);
ALTER TABLE `ok_comments` MODIFY `ip` varchar(40);
ALTER TABLE `ok_feedbacks` MODIFY `ip` varchar(40);
ALTER TABLE `ok_users` MODIFY `last_ip` varchar(40);

ALTER TABLE `ok_variants` ADD `volume` decimal(10,5) NULL;

CREATE TABLE `ok_okaycms__np_cost_delivery_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `city_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_term` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redelivery` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`)
VALUES ('OkayCMS', 'NovaposhtaCost', '12', '1', 'delivery', 'NovaposhtaCostAdmin'),
('OkayCMS', 'Banners', '13', '1', NULL, 'BannersAdmin');

CREATE TABLE `ok_lang_okaycms__banners_images` (
  `lang_id` int(11) DEFAULT NULL,
  `banner_image_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  UNIQUE KEY `lang_id_banner_image_id` (`lang_id`,`banner_image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) DEFAULT 0,
  `visible` tinyint(1) DEFAULT 1,
  `show_all_pages` tinyint(1) DEFAULT 1,
  `categories` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pages` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `brands` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `individual_shortcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `visible` (`visible`),
  KEY `show_all_pages` (`show_all_pages`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__banners_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banner_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `position` int(11) DEFAULT 0,
  `visible` tinyint(1) DEFAULT 1,
  `settings` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `banner_id` (`banner_id`),
  KEY `position` (`position`),
  KEY `visible` (`visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__banners` (`id`, `name`, `position`, `visible`, `show_all_pages`, `categories`, `pages`, `brands`, `individual_shortcode`, `settings`) VALUES
(1,	'Home banners',	1,	1,	0,	'0',	'0,1',	'0',	'', 'a:3:{s:9:"as_slider";s:1:"1";s:14:"rotation_speed";s:4:"2500";s:8:"autoplay";s:1:"1";}');

INSERT INTO `ok_okaycms__banners_images` (`id`, `banner_id`, `name`, `alt`, `title`, `url`, `description`, `image`, `position`, `visible`, `settings`) VALUES
(1,	1,	'variant1',	'Коллекция техники LG',	'Коллекция техники LG',	'catalog/tehnika-dlya-doma',	'',	'lg_banner.jpg',	4,	1,	'a:3:{s:12:\"variant_show\";s:7:\"default\";s:7:\"desktop\";a:2:{s:1:\"w\";s:4:\"1067\";s:1:\"h\";s:3:\"400\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:0:\"\";s:1:\"h\";s:0:\"\";}}'),
(2,	1,	'variant2',	'United Colors of Benetton',	'United Colors of Benetton',	'catalog/odezhda-obuv-i-ukrasheniya',	'Легкость, стиль и простота в каждом образе',	'tshirts_banner.jpg',	3,	1,	'a:3:{s:12:\"variant_show\";s:4:\"dark\";s:7:\"desktop\";a:2:{s:1:\"w\";s:4:\"1067\";s:1:\"h\";s:3:\"400\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:0:\"\";s:1:\"h\";s:0:\"\";}}'),
(3,	1,	'variant3',	'Mi 9T Pro',	'Mi 9T Pro',	'catalog/xiaomi',	'Qualcomm® Snapdragon™ 855<br>\r\n6.39 \" AMOLED -екран<br>\r\n48 Мп потрійна основна камера з AI<br>\r\n20 Мп висувна селфі-камера',	'xiaomi_banner.png',	2,	1,	'a:3:{s:12:\"variant_show\";s:10:\"image_left\";s:7:\"desktop\";a:2:{s:1:\"w\";s:3:\"400\";s:1:\"h\";s:3:\"350\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:0:\"\";s:1:\"h\";s:0:\"\";}}'),
(4,	1,	'variant4',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'',	'При покупке игровой консоли PlayStation 4 PRO 1ТБ получите в подарок FIFA 20',	'ps4_banner.jpg',	1,	1,	'a:3:{s:12:\"variant_show\";s:11:\"image_right\";s:7:\"desktop\";a:2:{s:1:\"w\";s:3:\"400\";s:1:\"h\";s:3:\"350\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:3:\"500\";s:1:\"h\";s:3:\"320\";}}');

INSERT INTO `ok_lang_okaycms__banners_images` (`lang_id`, `banner_image_id`, `name`, `alt`, `title`, `url`, `description`) VALUES
(1,	1,	'variant1',	'Коллекция техники LG',	'Коллекция техники LG',	'catalog/tehnika-dlya-doma',	''),
(2,	1,	'variant1',	'LG Technology Collection',	'LG Technology Collection',	'catalog/tehnika-dlya-doma',	''),
(3,	1,	'variant1',	'Колекція техніки LG',	'Колекція техніки LG',	'catalog/tehnika-dlya-doma',	''),
(1,	2,	'variant2',	'United Colors of Benetton',	'United Colors of Benetton',	'catalog/odezhda-obuv-i-ukrasheniya',	'Легкость, стиль и простота в каждом образе'),
(2,	2,	'variant2',	'United Colors of Benetton',	'United Colors of Benetton',	'catalog/odezhda-obuv-i-ukrasheniya',	'Lightness, style and simplicity in every look'),
(3,	2,	'variant2',	'United Colors of Benetton',	'United Colors of Benetton',	'catalog/odezhda-obuv-i-ukrasheniya',	'Легкість, стиль і простота в кожному образі'),
(1,	3,	'variant3',	'Mi 9T Pro',	'Mi 9T Pro',	'catalog/xiaomi',	'Qualcomm® Snapdragon™ 855<br>\r\n6.39 \" AMOLED -екран<br>\r\n48 Мп потрійна основна камера з AI<br>\r\n20 Мп висувна селфі-камера'),
(2,	3,	'variant3',	'Mi 9T Pro',	'Mi 9T Pro',	'catalog/xiaomi',	'Qualcomm® Snapdragon ™ 855 <br>\r\n6.39 \"AMOLED Screen <br>\r\n48 megapixel main camera with AI <br>\r\n20 megapixel visuvna selfie camera'),
(3,	3,	'variant3',	'Mi 9T Pro',	'Mi 9T Pro',	'catalog/xiaomi',	'Qualcomm® Snapdragon ™ 855 <br>\r\n6.39 \"AMOLED -екран <br>\r\n48 Мп потрійна основна камера з AI <br>\r\n20 Мп висувна селфі-камера'),
(1,	4,	'variant4',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'',	'При покупке игровой консоли PlayStation 4 PRO 1ТБ получите в подарок FIFA 20'),
(2,	4,	'variant4',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'',	'When you purchase a PlayStation 4 PRO 1TB game console, you will receive FIFA 20 as a gift'),
(3,	4,	'variant4',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'PlayStation 4 Pro 1TB Black + FIFA 20',	'',	'При покупці ігрової консолі PlayStation 4 PRO 1ТБ отримаєте в подарунок FIFA 20');

DROP TABLE `ok_banners`, `ok_banners_images`, `ok_lang_banners_images`;

INSERT INTO `ok_modules` (`id`, `vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
(14,	'OkayCMS',	'FastOrder',	14,	1,	NULL,	'DescriptionAdmin');

UPDATE `ok_settings` SET `value`='a:5:{i:0;s:7:"twitter";i:1;s:8:"facebook";i:2;s:10:"googleplus";i:3;s:8:"linkedin";i:4;s:13:"odnoklassniki";}' WHERE param='sj_shares';