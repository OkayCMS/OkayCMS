-- 0

ALTER TABLE `ok_categories` ADD COLUMN `auto_h1` VARCHAR(255);
ALTER TABLE `ok_lang_categories` ADD COLUMN `auto_h1` VARCHAR(255);

DROP TABLE IF EXISTS `ok_lang_lessons`;
CREATE TABLE `ok_lang_lessons` (
  `lesson_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `button` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`lesson_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ok_lang_lessons` (`lesson_id`, `lang_id`, `title`, `description`, `button`) VALUES
(1,	1,	'Добавление категорий',	'Категории используются для построения правильной структуры сайта. Старайтесь не допускать категорий со слишком большим количеством товаров.',	'Каталог'),
(1,	2,	'Adding Categories',	'Categories are used to build the correct site structure. Try to avoid categories with too many products.',	'Catalog'),
(1,	3,	'Додавання категорій',	'Категорії використовуються для побудови правильної структури сайту. Намагайтеся не допускати категорій з дуже великою кількістю товарів.',	'Каталог'),
(2,	1,	'Добавление бренда',	'Бренды используются в фильтре в категориях товаров. Также они важны для покупателей, которые опираются на бренд при выборе товара.',	'Каталог'),
(2,	2,	'Adding Brand',	'Brands are used in the filter in product categories. They are also important for buyers who rely on the brand when choosing a product.',	'Catalog'),
(2,	3,	'Додавання бренду',	'Бренди використовуються в фільтрі в категоріях товарів. Також вони важливі для покупців, які спираються на бренд при виборі товару.',	'Каталог'),
(3,	1,	'Добавление свойства',	'Этот раздел настроек позволяет управлять свойствами для ваших товаров.Здесь хранятся все свойства, которые вы завели при создании товаров или импортировали вместе со списком товаров.',	'Каталог'),
(3,	2,	'Adding feature',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Catalog'),
(3,	3,	'Додавання властивості',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Каталог'),
(4,	1,	'Добавление товара',	'Чем качественнее будет заполнена информация о товаре, тем больше шансов что клиент остановит свой поиск именно у вас. Обязательно укажите в товаре его фото и основные характеристики.',	'Каталог'),
(4,	2,	'Adding Product',	'The better the product information is filled out, the more likely it is that the client will stop your search exactly with you. Be sure to include in the product its photo and main characteristics.',	'Catalog'),
(4,	3,	'Додавання товару',	'Чим якісніше буде заповнена інформація про товар, тим більше шансів що клієнт зупинить свій пошук саме у вас. Обов\'язково вкажіть в товарі його фото і основні характеристики.',	'Каталог'),
(5,	1,	'Управление заказами',	'Вся основная информация о заказе, его статусе, настройки и  подробности ',	'Заказы'),
(5,	2,	'Order management',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Orders'),
(5,	3,	'Управління замовленнями',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Замовлення'),
(6,	1,	'Добавление статьи в блог',	'Пишите уникальные статьи о новинках или создавайте обзоры вашего товара.',	'Блог'),
(6,	2,	'Adding article to blog',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Blog'),
(6,	3,	'Додавання статті в блог',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Блог'),
(7, 1,	'Настройка отправки уведомлений',	'Настройте отправку уведомлений с сайта, укажите эмейл администратора и прочие настройки писем на этой странице.',	'Настройки сайта'),
(7, 2,	'Notify Settings',	'Configure sending notifications from the site, specify the administrator\'s email address and other settings for letters on this page.',	'Site settings'),
(7, 3,	'Налаштування відправки повідомлень',	'Налаштуйте відправку повідомлень з сайту, вкажіть емейл адміністратора та інші настройки листів на цій сторінці.',	'Налаштування сайту'),
(8, 1,	'Настройка каталога',	'Задайте водяной знак, настройте работу с товарами не в наличии и сделайте прочие настройки каталога на этой странице.',	'Настройки сайта'),
(8, 2,	'Catalog Settings',	   'Set a watermark, configure work with out-of-stock goods and make other catalog settings on this page.',	'Site settings'),
(8, 3,	'Налаштування каталога',	'Задайте водяний знак, налаштуйте роботу з товарами не в наявності і зробіть інші настройки каталогу на цій сторінці.',	'Налаштування сайту'),
(9, 1,	'Настройка валюты',	'Укажите валюту вашей страны основной на сайте. Если же вы используете несколько валют для формирования цен товаров, укажите соответствующие курсы валют в админ. панели.',	'Настройки сайта'),
(9, 2,	'Currency Settings',	'Indicate the currency of your country of primary on the site. If you use several currencies for pricing of goods, specify the appropriate exchange rates in the admin. panels.',	'Site settings'),
(9, 3,	'Налаштування валюти',	'Вкажіть валюту вашої країни основний на сайті. Якщо ж ви використовуєте кілька валют для формування цін товарів, вкажіть відповідні курси валют в адмін. панелі.',	'Налаштування сайту'),
(10, 1,	'Добавление способа доставки',	'Выбор способов доставки важен для клиентов при оформлении заказа. Некоторые клиенты отказываются от оформления заказа не увидев подходящий вариант доставки. Постарайтесь добавить самые популярные способы доставки на ваш сайт.',	'Настройки сайта'),
(10, 2,	'Adding Delivery Method',	'The choice of delivery methods is important for customers when placing an order. Some customers refuse to place an order without seeing the appropriate delivery option. Try to add the most popular delivery methods to your site.',	'Site settings'),
(10, 3,	'Додавання способу доставки',	'Вибір способів доставки важливий для клієнтів при оформленні замовлення. Деякі клієнти відмовляються від оформлення замовлення не побачивши підходящий варіант доставки. Постарайтеся додати найпопулярніші способи доставки на ваш сайт.',	'Налаштування сайту'),
(11, 1,	'Добавление способа оплаты',	'Всё больше клиентов готовы оплатить заказ сразу после оформление. Предоставьте им такую возможность подключив интеграцию с платежной системой на ваш сайт.',	'Настройки сайта'),
(11, 2,	'Adding Payment Method',	'More and more customers are ready to pay for the order immediately after registration. Give them such an opportunity by connecting integration with the payment system to your website.',	'Site settings'),
(11, 3,	'Додавання способу оплати',	'Все більше клієнтів готові оплатити замовлення відразу після оформлення. Необхідно надати їм таку можливість підключивши інтеграцію з платіжною системою на ваш сайт.',	'Налаштування сайту'),
(12, 1,	'SEO товаров',	'Гибкая настрока seo товаров позволит и быстро настроить ваши товары',	'SEO'),
(12, 2,	'SEO products',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'SEO'),
(12, 3,	'SEO товарів',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'SEO');

DROP TABLE IF EXISTS `ok_lessons`;
CREATE TABLE `ok_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preview` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `button` varchar(255) DEFAULT NULL,
  `target_module` varchar(255) DEFAULT NULL,
  `done` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ok_lessons` (`id`, `preview`, `video`, `title`, `description`, `button`, `target_module`, `done`) VALUES
(1,	'https://img.youtube.com/vi/Ud_B0XGcLFI/mqdefault.jpg',	'https://www.youtube.com/embed/Ud_B0XGcLFI',	'Добавление категорий',	'Категории используются для построения правильной структуры сайта. Старайтесь не допускать категорий со слишком большим количеством товаров, но и не делать категории в которых будет до десяти товаров.',	'Каталог',	'CategoriesAdmin',	NULL),
(2,	'https://img.youtube.com/vi/pAr5CkadzMA/mqdefault.jpg',	'https://www.youtube.com/embed/pAr5CkadzMA',	'Добавление бренда',	'Бренды используются в фильтре в категориях товаров. Также они важны для покупателей, которые опираются на бренд при выборе товара.',	'Каталог',	'BrandsAdmin',	NULL),
(3,	'', '',	'Добавление свойства',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Каталог',	'FeaturesAdmin',	NULL),
(4,	'https://img.youtube.com/vi/X4B2IkQwI5g/mqdefault.jpg',	'https://www.youtube.com/embed/X4B2IkQwI5g',	'Добавление товара',	'Чем качественнее будет заполнена информация о товаре, тем больше шансов что клиент остановит свой поиск именно у вас. Обязательно укажите в товаре его фото и основные характеристики.',	'Каталог',	'ProductsAdmin',	NULL),
(5,	'', '',	'Управление заказами',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Заказы',	'OrdersAdmin',	NULL),
(6,	'', '',	'Добавление статьи в блог',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'Блог',	'BlogAdmin',	NULL),
(7, 'https://img.youtube.com/vi/PH6zFFSmU3Q/mqdefault.jpg',	'https://www.youtube.com/embed/PH6zFFSmU3Q',	'Настройка отправки уведомлений',	'Настройте отправку уведомлений с сайта, укажите эмейл администратора и прочие настройки писем на этой странице.',	'Настройки сайта',	'SettingsNotifyAdmin',	NULL),
(8,	'https://img.youtube.com/vi/W67M4W5uJr4/mqdefault.jpg',	'https://www.youtube.com/embed/W67M4W5uJr4',	'Настройка каталога',	'Задайте водяной знак, настройте работу с товарами не в наличии и сделайте прочие настройки каталога на этой странице.',	'Настройки сайта',	'SettingsCatalogAdmin',	NULL),
(9,	'https://img.youtube.com/vi/ccq4Plnqz9M/mqdefault.jpg',	'https://www.youtube.com/embed/ccq4Plnqz9M',	'Настройка валюты',	'Укажите валюту вашей страны основной на сайте. Если же вы используете несколько валют для формирования цен товаров, укажите соответствующие курсы валют в админ. панели.',	'Настройки сайта',	'CurrencyAdmin',	NULL),
(10,	'https://img.youtube.com/vi/R-VNpOHnk7w/mqdefault.jpg',	'https://www.youtube.com/embed/R-VNpOHnk7w',	'Добавление способа доставки',	'Выбор способов доставки важен для клиентов при оформлении заказа. Некоторые клиенты отказываются от оформления заказа не увидев подходящий вариант доставки. Постарайтесь добавить самые популярные способы доставки на ваш сайт.',	'Настройки сайта',	'DeliveriesAdmin',	NULL),
(11,	'https://img.youtube.com/vi/CgDtgLfJnCY/mqdefault.jpg',	'https://www.youtube.com/embed/CgDtgLfJnCY',	'Добавление способа оплаты',	'Всё больше клиентов готовы оплатить заказ сразу после оформление. Предоставьте им такую возможность подключив интеграцию с платежной системой на ваш сайт.',	'Настройки сайта',	'PaymentMethodsAdmin',	NULL),
(12,	'', '',	'SEO товаров',	'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',	'SEO',	'SeoPatternsAdmin',	NULL);

ALTER TABLE `ok_deliveries` ADD COLUMN `hide_front_delivery_price` TINYINT(1);

ALTER TABLE `ok_purchases`
CHANGE `product_name` `product_name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `variant_id`,
CHANGE `variant_name` `variant_name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `product_name`,
CHANGE `sku` `sku` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `amount`,
CHANGE `units` `units` varchar(32) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `sku`;
