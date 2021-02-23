ALTER TABLE `s_managers` ADD `lang` VARCHAR(2) NOT NULL DEFAULT 'ru' AFTER `id`;
DROP TABLE s_translations;

ALTER TABLE `s_lang_pages` DROP COLUMN `header`;
ALTER TABLE `s_pages` DROP COLUMN `header`;
ALTER TABLE `s_blog` CHANGE COLUMN `text` `description` LONGTEXT NOT NULL AFTER `annotation`;
ALTER TABLE `s_lang_blog` CHANGE COLUMN `text` `description` TEXT NOT NULL AFTER `annotation`;
ALTER TABLE `s_pages` CHANGE COLUMN `body` `description` LONGTEXT NOT NULL AFTER `meta_keywords`;
ALTER TABLE `s_lang_pages` CHANGE COLUMN `body` `description` LONGTEXT NOT NULL AFTER `meta_keywords`;
ALTER TABLE `s_products` CHANGE COLUMN `body` `description` LONGTEXT NOT NULL AFTER `annotation`;
ALTER TABLE `s_lang_products` CHANGE COLUMN `body` `description` TEXT NOT NULL AFTER `annotation`;
ALTER TABLE `s_categories` CHANGE COLUMN `auto_body` `auto_description` TEXT NOT NULL AFTER `auto_meta_desc`;
ALTER TABLE `s_lang_categories` CHANGE COLUMN `auto_body` `auto_description` TEXT NOT NULL AFTER `auto_meta_desc`;
ALTER TABLE `s_banners` DROP COLUMN `products`;
ALTER TABLE `s_spec_img` DROP COLUMN `name`;
ALTER TABLE `s_brands` ADD COLUMN `visible` TINYINT(1) NOT NULL DEFAULT '0' AFTER `position`;
ALTER TABLE `s_managers` ADD COLUMN `comment` VARCHAR(500) NULL DEFAULT '' AFTER `last_try`;
ALTER TABLE `s_users` DROP COLUMN `enabled`;

CREATE TABLE `s_lang_orders_labels` (
  `lang_id` int(11) NOT NULL,
  `lang_label` varchar(4) NOT NULL,
  `order_labels_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `lang_id` (`lang_id`,`order_labels_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `s_lang_orders_status` (
  `lang_id` int(11) NOT NULL,
  `lang_label` varchar(4) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  UNIQUE KEY `lang_id` (`lang_id`,`order_status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `s_orders_status` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `is_close` TINYINT(1) NOT NULL DEFAULT '0',
  `position` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*support*/
DROP TABLE IF EXISTS `s_tech_info`;
CREATE TABLE IF NOT EXISTS `s_tech_info` (
  `id` tinyint(1) NOT NULL AUTO_INCREMENT,
  `temp_key` varchar(32) DEFAULT NULL,
  `temp_time` timestamp NULL DEFAULT NULL,
  `new_messages` int(11) NOT NULL DEFAULT '0',
  `private_key` varchar(2048) DEFAULT NULL,
  `public_key` varchar(2048) DEFAULT NULL,
  `okay_public_key` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `s_tech_info` ADD `balance` INT(11) NOT NULL DEFAULT '0' AFTER `new_messages`;
RENAME TABLE `s_tech_info` TO `s_support_info`;
ALTER TABLE `s_support_info` ADD `is_auto` TINYINT(1) NOT NULL DEFAULT '1' AFTER `okay_public_key`;
/*support END*/

ALTER TABLE `s_variants` CHANGE COLUMN `yandex` `feed` INT(1) NULL DEFAULT '0' AFTER `currency_id`;
ALTER TABLE `s_managers` ADD COLUMN `menu_status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `comment`;
ALTER TABLE `s_orders` CHANGE COLUMN `status` `status_id` INT(11) NOT NULL DEFAULT '0' AFTER `comment`;
ALTER TABLE `s_blog` ADD COLUMN `type_post` ENUM('blog','news') NOT NULL DEFAULT 'blog' AFTER `last_modify`;
ALTER TABLE `s_feedbacks` ADD COLUMN `is_admin` TINYINT(1) NOT NULL DEFAULT '0' AFTER `lang_id`;
ALTER TABLE `s_feedbacks` ADD COLUMN `parent_id` INT(11) NOT NULL DEFAULT '0' AFTER `is_admin`;

ALTER TABLE `s_languages` DROP COLUMN `name_by`;
ALTER TABLE `s_languages` DROP COLUMN `name_ch`;
ALTER TABLE `s_languages` DROP COLUMN `name_de`;
ALTER TABLE `s_languages` DROP COLUMN `name_fr`;
ALTER TABLE `s_languages` ADD `href_lang` VARCHAR(10) NOT NULL AFTER `label`;
UPDATE `s_languages` SET `href_lang`=`label`;
/* !!! WARNING ЕСЛИ ОБНОВЛЯЕМ РАБОЧИЙ САЙТ так же следует обновить label языков у которых отличается label от href_lang */
UPDATE `s_languages` SET `label`='ua' WHERE `label`='uk';
ALTER TABLE `s_languages` CHANGE `name_uk` `name_ua` VARCHAR(255) NOT NULL DEFAULT '';
/* WARNING END */

ALTER TABLE `s_lang_banners_images` DROP `lang_label`;
ALTER TABLE `s_lang_blog` DROP `lang_label`;
ALTER TABLE `s_lang_brands` DROP `lang_label`;
ALTER TABLE `s_lang_categories` DROP `lang_label`;
ALTER TABLE `s_lang_currencies` DROP `lang_label`;
ALTER TABLE `s_lang_delivery` DROP `lang_label`;
ALTER TABLE `s_lang_features` DROP `lang_label`;
ALTER TABLE `s_lang_orders_labels` DROP `lang_label`;
ALTER TABLE `s_lang_orders_status` DROP `lang_label`;
ALTER TABLE `s_lang_pages` DROP `lang_label`;
ALTER TABLE `s_lang_payment_methods` DROP `lang_label`;
ALTER TABLE `s_lang_products` DROP `lang_label`;
ALTER TABLE `s_lang_variants` DROP `lang_label`;



ALTER TABLE `s_banners_images` CHANGE `alt` `alt` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_banners_images` CHANGE `title` `title` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_banners_images` CHANGE `alt` `alt` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_banners_images` CHANGE `title` `title` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_blog` CHANGE `name` `name` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_blog` CHANGE `url` `url` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_blog` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_blog` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_blog` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_blog` CHANGE `description` `description` TEXT NOT NULL;
ALTER TABLE `s_lang_blog` CHANGE `name` `name` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_blog` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_blog` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_blog` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';

ALTER TABLE `s_brands` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_brands` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_brands` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_brands` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_lang_brands` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_brands` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_brands` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_brands` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';

ALTER TABLE `s_categories` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `external_id` `external_id` VARCHAR(36) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `auto_meta_title` `auto_meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `auto_meta_keywords` `auto_meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_categories` CHANGE `auto_meta_desc` `auto_meta_desc` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `name_h1` `name_h1` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `auto_meta_title` `auto_meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `auto_meta_keywords` `auto_meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_categories` CHANGE `auto_meta_desc` `auto_meta_desc` VARCHAR(512) NOT NULL DEFAULT '';

ALTER TABLE `s_currencies` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_currencies` CHANGE `sign` `sign` VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE `s_currencies` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_currencies` CHANGE `enabled` `enabled` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `s_lang_currencies` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_currencies` CHANGE `sign` `sign` VARCHAR(20) NOT NULL DEFAULT '';

ALTER TABLE `s_delivery` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_delivery` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_delivery` CHANGE `separate_payment` `separate_payment` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `s_lang_delivery` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_features` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_features` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_features` CHANGE `in_filter` `in_filter` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `s_features` CHANGE `yandex` `yandex` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `s_lang_features` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_labels` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_labels` CHANGE `color` `color` VARCHAR(6) NOT NULL DEFAULT '';
ALTER TABLE `s_labels` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_lang_orders_labels` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_lang_orders_status` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_pages` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_pages` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_pages` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_pages` CHANGE `description` `description` TEXT NOT NULL;
ALTER TABLE `s_lang_pages` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_pages` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_pages` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_pages` CHANGE `description` `description` TEXT NOT NULL;

ALTER TABLE `s_payment_methods` CHANGE `module` `module` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_payment_methods` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_payment_methods` CHANGE `currency_id` `currency_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_payment_methods` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_lang_payment_methods` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_products` CHANGE `brand_id` `brand_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `s_products` CHANGE `name` `name` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_products` CHANGE `description` `description` TEXT NOT NULL;
ALTER TABLE `s_products` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_products` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_products` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_products` CHANGE `featured` `featured` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `s_products` CHANGE `external_id` `external_id` VARCHAR(36) NOT NULL DEFAULT '';
ALTER TABLE `s_products` CHANGE `special` `special` VARCHAR(255) NULL DEFAULT '';
ALTER TABLE `s_lang_products` CHANGE `name` `name` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_products` CHANGE `meta_title` `meta_title` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_products` CHANGE `meta_keywords` `meta_keywords` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_products` CHANGE `meta_description` `meta_description` VARCHAR(512) NOT NULL DEFAULT '';
ALTER TABLE `s_lang_products` CHANGE `special` `special` VARCHAR(255) NULL DEFAULT '';

ALTER TABLE `s_variants` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_variants` CHANGE `sku` `sku` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_variants` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_variants` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_variants` CHANGE `attachment` `attachment` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_variants` CHANGE `external_id` `external_id` VARCHAR(36) NOT NULL DEFAULT '';
ALTER TABLE `s_variants` CHANGE `feed` `feed` TINYINT(1) NULL DEFAULT '0';
ALTER TABLE `s_lang_variants` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_banners` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_banners` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_banners` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_callbacks` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_callbacks` CHANGE `date` `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `s_callbacks` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_callbacks` CHANGE `phone` `phone` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_comments` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_comments` CHANGE `ip` `ip` VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE `s_comments` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_comments` CHANGE `type` `type` ENUM('product','blog') NOT NULL DEFAULT 'product';
ALTER TABLE `s_comments` CHANGE `lang_id` `lang_id` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_coupons` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_coupons` CHANGE `code` `code` VARCHAR(256) NOT NULL DEFAULT '';
ALTER TABLE `s_coupons` CHANGE `single` `single` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `s_feedbacks` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_feedbacks` CHANGE `date` `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `s_feedbacks` CHANGE `ip` `ip` VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE `s_feedbacks` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_feedbacks` CHANGE `email` `email` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_feedbacks` CHANGE `lang_id` `lang_id` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_images` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_images` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_languages` CHANGE `name` `name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_languages` CHANGE `enabled` `enabled` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `s_languages` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_orders` CHANGE `delivery_id` `delivery_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `s_orders` CHANGE `payment_method_id` `payment_method_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `s_orders` CHANGE `paid` `paid` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `s_orders` CHANGE `payment_date` `payment_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `s_orders` CHANGE `closed` `closed` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `s_orders` CHANGE `date` `date` DATETIME NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `s_orders` CHANGE `email` `email` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `comment` `comment` VARCHAR(1024) NOT NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `url` `url` VARCHAR(255) NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `ip` `ip` VARCHAR(20) NOT NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `note` `note` VARCHAR(1024) NOT NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `coupon_code` `coupon_code` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_orders` CHANGE `separate_delivery` `separate_delivery` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `s_purchases` CHANGE `variant_id` `variant_id` INT(11) NULL DEFAULT '0';
ALTER TABLE `s_purchases` CHANGE `variant_name` `variant_name` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_purchases` CHANGE `sku` `sku` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_spec_img` CHANGE `filename` `filename` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_spec_img` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_subscribe_mailing` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `s_subscribe_mailing` CHANGE `email` `email` VARCHAR(255) NOT NULL DEFAULT '';

ALTER TABLE `s_users` CHANGE `email` `email` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `s_users` CHANGE `last_ip` `last_ip` VARCHAR(20) NULL DEFAULT NULL;

ALTER TABLE `s_managers` CHANGE `comment` `comment` VARCHAR(512) NULL DEFAULT '';
ALTER TABLE `s_menu` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_options` CHANGE `value` `value` VARCHAR(1024) NOT NULL DEFAULT '';
ALTER TABLE `s_products_categories` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_related_blogs` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `s_related_products` CHANGE `position` `position` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `s_variants` ADD COLUMN `weight` DECIMAL(10,2) NULL DEFAULT '0' AFTER `name`;


RENAME TABLE `s_banners` TO `ok_banners`;
RENAME TABLE `s_banners_images` TO `ok_banners_images`;
RENAME TABLE `s_blog` TO `ok_blog`;
RENAME TABLE `s_brands` TO `ok_brands`;
RENAME TABLE `s_callbacks` TO `ok_callbacks`;
RENAME TABLE `s_categories` TO `ok_categories`;
RENAME TABLE `s_categories_features` TO `ok_categories_features`;
RENAME TABLE `s_comments` TO `ok_comments`;
RENAME TABLE `s_coupons` TO `ok_coupons`;
RENAME TABLE `s_currencies` TO `ok_currencies`;
RENAME TABLE `s_delivery` TO `ok_delivery`;
RENAME TABLE `s_delivery_payment` TO `ok_delivery_payment`;
RENAME TABLE `s_features` TO `ok_features`;
RENAME TABLE `s_feedbacks` TO `ok_feedbacks`;
RENAME TABLE `s_groups` TO `ok_groups`;
RENAME TABLE `s_images` TO `ok_images`;
RENAME TABLE `s_import_log` TO `ok_import_log`;
RENAME TABLE `s_labels` TO `ok_labels`;
RENAME TABLE `s_lang_banners_images` TO `ok_lang_banners_images`;
RENAME TABLE `s_lang_blog` TO `ok_lang_blog`;
RENAME TABLE `s_lang_brands` TO `ok_lang_brands`;
RENAME TABLE `s_lang_categories` TO `ok_lang_categories`;
RENAME TABLE `s_lang_currencies` TO `ok_lang_currencies`;
RENAME TABLE `s_lang_delivery` TO `ok_lang_delivery`;
RENAME TABLE `s_lang_features` TO `ok_lang_features`;
RENAME TABLE `s_lang_orders_labels` TO `ok_lang_orders_labels`;
RENAME TABLE `s_lang_orders_status` TO `ok_lang_orders_status`;
RENAME TABLE `s_lang_pages` TO `ok_lang_pages`;
RENAME TABLE `s_lang_payment_methods` TO `ok_lang_payment_methods`;
RENAME TABLE `s_lang_products` TO `ok_lang_products`;
RENAME TABLE `s_lang_variants` TO `ok_lang_variants`;
RENAME TABLE `s_languages` TO `ok_languages`;
RENAME TABLE `s_managers` TO `ok_managers`;
RENAME TABLE `s_menu` TO `ok_menu`;
RENAME TABLE `s_options` TO `ok_options`;
RENAME TABLE `s_orders` TO `ok_orders`;
RENAME TABLE `s_orders_labels` TO `ok_orders_labels`;
RENAME TABLE `s_orders_status` TO `ok_orders_status`;
RENAME TABLE `s_pages` TO `ok_pages`;
RENAME TABLE `s_payment_methods` TO `ok_payment_methods`;
RENAME TABLE `s_products` TO `ok_products`;
RENAME TABLE `s_products_categories` TO `ok_products_categories`;
RENAME TABLE `s_purchases` TO `ok_purchases`;
RENAME TABLE `s_related_blogs` TO `ok_related_blogs`;
RENAME TABLE `s_related_products` TO `ok_related_products`;
RENAME TABLE `s_settings` TO `ok_settings`;
RENAME TABLE `s_spec_img` TO `ok_spec_img`;
RENAME TABLE `s_subscribe_mailing` TO `ok_subscribe_mailing`;
RENAME TABLE `s_support_info` TO `ok_support_info`;
RENAME TABLE `s_users` TO `ok_users`;
RENAME TABLE `s_variants` TO `ok_variants`;
