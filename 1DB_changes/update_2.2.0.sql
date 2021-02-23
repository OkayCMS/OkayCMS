DROP TABLE IF EXISTS `ok_menu`;
ALTER TABLE `ok_pages` DROP COLUMN `menu_id`;

CREATE TABLE IF NOT EXISTS `ok_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `visible` (`visible`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ok_menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(512) NOT NULL DEFAULT '',
  `is_target_blank` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  KEY `visible` (`visible`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_lang_menu_items` (
  `lang_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`lang_id`,`menu_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE `ok_categories` SET `created` = now() WHERE !`created`;
UPDATE `ok_orders` SET `payment_date` = now() WHERE !`payment_date`;

ALTER TABLE `ok_blog`
CHANGE `date` `date` timestamp NULL DEFAULT NULL AFTER `visible`,
CHANGE `last_modify` `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `image`;

ALTER TABLE `ok_brands`
CHANGE `last_modify` `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `image`;

ALTER TABLE `ok_categories`
CHANGE `last_modify` `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `auto_description`,
CHANGE `created` `created` timestamp NULL DEFAULT NULL AFTER `last_modify`;

ALTER TABLE `ok_callbacks`
CHANGE `date` `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `id`;

ALTER TABLE `ok_comments`
CHANGE `date` `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `parent_id`;

ALTER TABLE `ok_feedbacks`
CHANGE `date` `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `id`;

ALTER TABLE `ok_orders`
CHANGE `modified` `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `separate_delivery`,
CHANGE `payment_date` `payment_date` datetime NULL DEFAULT NULL AFTER `paid`,
CHANGE `date` `date` datetime NULL DEFAULT NULL AFTER `closed`,
CHANGE `payment_details` `payment_details` text COLLATE 'utf8_general_ci' NULL AFTER `url`;

ALTER TABLE `ok_pages`
CHANGE `last_modify` `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `visible`;

ALTER TABLE `ok_products`
CHANGE `created` `created` timestamp NULL DEFAULT NULL AFTER `meta_description`,
CHANGE `last_modify` `last_modify` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `special`;

ALTER TABLE `ok_banners` ENGINE='InnoDB';
ALTER TABLE `ok_banners_images` ENGINE='InnoDB';
ALTER TABLE `ok_blog` ENGINE='InnoDB';
ALTER TABLE `ok_brands` ENGINE='InnoDB';
ALTER TABLE `ok_callbacks` ENGINE='InnoDB';
ALTER TABLE `ok_categories` ENGINE='InnoDB';
ALTER TABLE `ok_categories_features` ENGINE='InnoDB';
ALTER TABLE `ok_comments` ENGINE='InnoDB';
ALTER TABLE `ok_coupons` ENGINE='InnoDB';
ALTER TABLE `ok_currencies` ENGINE='InnoDB';
ALTER TABLE `ok_delivery` ENGINE='InnoDB';
ALTER TABLE `ok_delivery_payment` ENGINE='InnoDB';
ALTER TABLE `ok_features` ENGINE='InnoDB';
ALTER TABLE `ok_feedbacks` ENGINE='InnoDB';
ALTER TABLE `ok_groups` ENGINE='InnoDB';
ALTER TABLE `ok_images` ENGINE='InnoDB';
ALTER TABLE `ok_import_log` ENGINE='InnoDB';
ALTER TABLE `ok_labels` ENGINE='InnoDB';
ALTER TABLE `ok_languages` ENGINE='InnoDB';
ALTER TABLE `ok_lang_banners_images` ENGINE='InnoDB';
ALTER TABLE `ok_lang_blog` ENGINE='InnoDB';
ALTER TABLE `ok_lang_brands` ENGINE='InnoDB';
ALTER TABLE `ok_lang_categories` ENGINE='InnoDB';
ALTER TABLE `ok_lang_currencies` ENGINE='InnoDB';
ALTER TABLE `ok_lang_delivery` ENGINE='InnoDB';
ALTER TABLE `ok_lang_features` ENGINE='InnoDB';
ALTER TABLE `ok_lang_orders_labels` ENGINE='InnoDB';
ALTER TABLE `ok_lang_orders_status` ENGINE='InnoDB';
ALTER TABLE `ok_lang_pages` ENGINE='InnoDB';
ALTER TABLE `ok_lang_payment_methods` ENGINE='InnoDB';
ALTER TABLE `ok_lang_products` ENGINE='InnoDB';
ALTER TABLE `ok_lang_variants` ENGINE='InnoDB';
ALTER TABLE `ok_managers` ENGINE='InnoDB';
ALTER TABLE `ok_menu` ENGINE='InnoDB';
ALTER TABLE `ok_options` ENGINE='InnoDB';
ALTER TABLE `ok_orders` ENGINE='InnoDB';
ALTER TABLE `ok_orders_labels` ENGINE='InnoDB';
ALTER TABLE `ok_orders_status` ENGINE='InnoDB';
ALTER TABLE `ok_pages` ENGINE='InnoDB';
ALTER TABLE `ok_payment_methods` ENGINE='InnoDB';
ALTER TABLE `ok_products` ENGINE='InnoDB';
ALTER TABLE `ok_products_categories` ENGINE='InnoDB';
ALTER TABLE `ok_purchases` ENGINE='InnoDB';
ALTER TABLE `ok_related_blogs` ENGINE='InnoDB';
ALTER TABLE `ok_related_products` ENGINE='InnoDB';
ALTER TABLE `ok_settings` ENGINE='InnoDB';
ALTER TABLE `ok_settings_lang` ENGINE='InnoDB';
ALTER TABLE `ok_spec_img` ENGINE='InnoDB';
ALTER TABLE `ok_subscribe_mailing` ENGINE='InnoDB';
ALTER TABLE `ok_support_info` ENGINE='InnoDB';
ALTER TABLE `ok_users` ENGINE='InnoDB';
ALTER TABLE `ok_variants` ENGINE='InnoDB';

DELIMITER $$
CREATE TRIGGER `categories_date_create` BEFORE INSERT ON `ok_categories` FOR EACH ROW SET NEW.`created` = NOW()$$

CREATE TRIGGER `products_date_create` BEFORE INSERT ON `ok_products` FOR EACH ROW SET NEW.`created` = NOW()$$
DELIMITER ;

ALTER TABLE `ok_orders` CHANGE `separate_delivery` `separate_delivery` tinyint(1) NULL DEFAULT '0' AFTER `coupon_code`;
ALTER TABLE `ok_features` ADD `url_in_product` tinyint(1) NULL DEFAULT '0';

CREATE TABLE `ok_lang_seo_filter_patterns` (
  `lang_id` tinyint(11) NOT NULL,
  `seo_filter_pattern_id` int(11) NOT NULL,
  `h1` varchar(512) DEFAULT '',
  `title` varchar(512) DEFAULT '',
  `keywords` varchar(512) DEFAULT '',
  `meta_description` varchar(512) DEFAULT '',
  `description` text,
  UNIQUE KEY `lang_id_filter_auto_meta_id` (`lang_id`,`seo_filter_pattern_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_seo_filter_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `type` enum('brand','feature') NOT NULL,
  `h1` varchar(512) DEFAULT '',
  `title` varchar(512) DEFAULT '',
  `keywords` varchar(512) DEFAULT '',
  `meta_description` varchar(512) DEFAULT '',
  `description` text,
  `feature_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id_param_type_feature_id` (`category_id`,`type`,`feature_id`),
  KEY `category_id` (`category_id`),
  KEY `feature_id` (`feature_id`),
  KEY `param_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ok_lang_menu_items` (`lang_id`, `menu_item_id`, `name`) VALUES
(1,	2,	'Доставка'),
(1,	3,	'Новости'),
(1,	4,	'Главная'),
(1,	8,	'Блог'),
(1,	9,	'Статьи'),
(1,	11,	'Контакты'),
(1,	12,	'Бренды'),
(1,	13,	'Статьи'),
(1,	14,	'Новости'),
(1,	15,	'Главная'),
(1,	16,	'Контакты'),
(1,	17,	'Оплата'),
(1,	18,	'Доставка'),
(1,	19,	'Новости'),
(1,	20,	'Статьи'),
(1,	22,	'Оплата'),
(1,	23,	'Бренды'),
(1,	24,	'Контакты'),
(2,	2,	'Delivery'),
(2,	3,	'News'),
(2,	4,	'Home'),
(2,	8,	'Blog'),
(2,	9,	'Article'),
(2,	11,	'Contacts'),
(2,	12,	'Brands'),
(2,	13,	'Articles'),
(2,	14,	'News'),
(2,	15,	'Home'),
(2,	16,	'Contacts'),
(2,	17,	'Payment'),
(2,	18,	'Delivery'),
(2,	19,	'News'),
(2,	20,	'Articles'),
(2,	22,	'Payment'),
(2,	23,	'Brands'),
(2,	24,	'Contact'),
(3,	2,	'Доставка'),
(3,	3,	'Новости'),
(3,	4,	'Главная'),
(3,	8,	'Блог'),
(3,	9,	'Статьи'),
(3,	11,	'Контакты'),
(3,	12,	'Бренды'),
(3,	13,	'Статьи'),
(3,	14,	'Новости'),
(3,	15,	'Главная'),
(3,	16,	'Контакты'),
(3,	17,	'Оплата'),
(3,	18,	'Доставка'),
(3,	19,	'Новости'),
(3,	20,	'Статьи'),
(3,	22,	'Оплата'),
(3,	23,	'Бренды'),
(3,	24,	'Контакты');

INSERT INTO `ok_menu` (`id`, `group_id`, `name`, `visible`, `position`) VALUES
(1, 'header', 'Главное меню', 1, 1),
(2, '404', 'Меню на странице 404', 1, 2),
(3, 'footer', 'Меню в footer', 1, 3);

INSERT INTO `ok_menu_items` (`id`, `menu_id`, `parent_id`, `name`, `url`, `is_target_blank`, `visible`, `position`) VALUES
(2,	1,	0,	'Доставка',	'dostavka',	0,	1,	8),
(3,	1,	8,	'Новости',	'news',	0,	1,	23),
(4,	1,	0,	'Главная',	'/',	1,	1,	2),
(8,	1,	0,	'Блог',	'',	0,	1,	3),
(9,	1,	8,	'Статьи',	'blog',	0,	1,	24),
(11,	2,	0,	'Контакты',	'contact',	0,	1,	15),
(12,	2,	0,	'Бренды',	'brands',	0,	1,	12),
(13,	2,	0,	'Статьи',	'blog',	0,	1,	13),
(14,	2,	0,	'Новости',	'news',	0,	1,	14),
(15,	2,	0,	'Главная',	'/',	0,	1,	11),
(16,	3,	0,	'Контакты',	'contact',	0,	1,	20),
(17,	3,	0,	'Оплата',	'oplata',	0,	1,	19),
(18,	3,	0,	'Доставка',	'dostavka',	0,	1,	18),
(19,	3,	0,	'Новости',	'news',	0,	1,	17),
(20,	3,	0,	'Статьи',	'blog',	0,	1,	16),
(22,	1,	0,	'Оплата',	'oplata',	0,	1,	9),
(23,	1,	0,	'Бренды',	'brands',	0,	1,	4),
(24,	1,	0,	'Контакты',	'contact',	0,	1,	22);

ALTER TABLE `ok_lang_pages` ADD `name_h1` VARCHAR (255) NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `ok_pages` ADD `name_h1` VARCHAR (255) NOT NULL DEFAULT '' AFTER `name`;

ALTER TABLE `ok_orders_status` ADD `color` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ffffff' AFTER `is_close`;

CREATE TABLE `ok_features_aliases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `variable` (`variable`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_features_aliases_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feature_alias_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  `feature_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  KEY `feature_alias_id` (`feature_alias_id`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_lang_features_aliases` (
  `lang_id` tinyint(11) NOT NULL,
  `feature_alias_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `lang_id_feature_alias_id` (`lang_id`,`feature_alias_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_lang_features_aliases_values` (
  `lang_id` int(11) NOT NULL,
  `feature_alias_value_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `lang_id_feature_alias_value_id` (`lang_id`,`feature_alias_value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_options_aliases_values` (
  `feature_alias_id` int(11) NOT NULL,
  `translit` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `lang_id` int(11) NOT NULL,
  KEY `feature_alias_id` (`feature_alias_id`),
  KEY `translit` (`translit`),
  KEY `feature_id` (`feature_id`),
  KEY `lang_id` (`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ok_settings`(`setting_id`, `name`, `value`) VALUES ('','captcha_type','default');