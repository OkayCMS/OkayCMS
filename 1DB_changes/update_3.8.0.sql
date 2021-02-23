ALTER TABLE `ok_okaycms__np_cost_delivery_data`
ADD `city_name` varchar(255) NULL,
ADD `area_name` varchar(255) NULL AFTER `city_name`,
ADD `region_name` varchar(255) NULL AFTER `area_name`,
ADD `street` varchar(255) NULL AFTER `region_name`,
ADD `house` varchar(255) NULL AFTER `street`,
ADD `apartment` varchar(255) NULL AFTER `house`;

CREATE TABLE `ok_okaycms__yandex_xml__feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__yandex_xml__relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('product','category','brand') COLLATE utf8mb4_unicode_ci NOT NULL,
  `include` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_id_entity_type_include_entity_id` (`feed_id`,`entity_type`,`include`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__yandex_xml__feeds` (`id`, `name`, `url`, `enabled`) VALUES
(1,	'New Feed',	'feed', 1);

INSERT INTO `ok_okaycms__yandex_xml__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 1
FROM ok_products
WHERE to__okaycms__yandex_xml = 1;

INSERT INTO `ok_okaycms__yandex_xml__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 0
FROM ok_products
WHERE not_to__okaycms__yandex_xml = 1;

ALTER TABLE `ok_products`
DROP COLUMN `to__okaycms__yandex_xml`;

ALTER TABLE `ok_products`
DROP COLUMN `not_to__okaycms__yandex_xml`;

INSERT INTO `ok_okaycms__yandex_xml__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'category', 1
FROM ok_categories
WHERE to__okaycms__yandex_xml = 1;

ALTER TABLE `ok_categories`
DROP COLUMN `to__okaycms__yandex_xml`;

INSERT INTO `ok_okaycms__yandex_xml__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'brand', 1
FROM ok_brands
WHERE to__okaycms__yandex_xml = 1;

ALTER TABLE `ok_brands`
DROP COLUMN `to__okaycms__yandex_xml`;


CREATE TABLE `ok_okaycms__rozetka__feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__rozetka__relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('product','category','brand') COLLATE utf8mb4_unicode_ci NOT NULL,
  `include` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_id_entity_type_include_entity_id` (`feed_id`,`entity_type`,`include`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__rozetka__feeds` (`id`, `name`, `url`, `enabled`) VALUES
(1,	'New Feed',	'feed', 1);

INSERT INTO `ok_okaycms__rozetka__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 1
FROM ok_products
WHERE to_rozetka = 1;

INSERT INTO `ok_okaycms__rozetka__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 0
FROM ok_products
WHERE not_to_rozetka = 1;

ALTER TABLE `ok_products`
DROP COLUMN `to_rozetka`;

ALTER TABLE `ok_products`
DROP COLUMN `not_to_rozetka`;

INSERT INTO `ok_okaycms__rozetka__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'category', 1
FROM ok_categories
WHERE to_rozetka = 1;

ALTER TABLE `ok_categories`
DROP COLUMN `to_rozetka`;

INSERT INTO `ok_okaycms__rozetka__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'brand', 1
FROM ok_brands
WHERE to_rozetka = 1;

ALTER TABLE `ok_brands`
DROP COLUMN `to_rozetka`;


CREATE TABLE `ok_okaycms__hotline__feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__hotline__relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('product','category','brand') COLLATE utf8mb4_unicode_ci NOT NULL,
  `include` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_id_entity_type_include_entity_id` (`feed_id`,`entity_type`,`include`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__hotline__feeds` (`id`, `name`, `url`, `enabled`) VALUES
(1,	'New Feed',	'feed', 1);

INSERT INTO `ok_okaycms__hotline__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 1
FROM ok_products
WHERE to__okaycms__hotline = 1;

INSERT INTO `ok_okaycms__hotline__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 0
FROM ok_products
WHERE not_to__okaycms__hotline = 1;

ALTER TABLE `ok_products`
DROP COLUMN `to__okaycms__hotline`;

ALTER TABLE `ok_products`
DROP COLUMN `not_to__okaycms__hotline`;

INSERT INTO `ok_okaycms__hotline__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'category', 1
FROM ok_categories
WHERE to__okaycms__hotline = 1;

ALTER TABLE `ok_categories`
DROP COLUMN `to__okaycms__hotline`;

INSERT INTO `ok_okaycms__hotline__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'brand', 1
FROM ok_brands
WHERE to__okaycms__hotline = 1;

ALTER TABLE `ok_brands`
DROP COLUMN `to__okaycms__hotline`;


CREATE TABLE `ok_okaycms__yandex_xml_vendor_model__feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__yandex_xml_vendor_model__relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('product','category','brand') COLLATE utf8mb4_unicode_ci NOT NULL,
  `include` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_id_entity_type_include_entity_id` (`feed_id`,`entity_type`,`include`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__yandex_xml_vendor_model__feeds` (`id`, `name`, `url`, `enabled`) VALUES
(1,	'New Feed',	'feed', 1);

INSERT INTO `ok_okaycms__yandex_xml_vendor_model__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 1
FROM ok_products
WHERE to__okaycms__yandex_xml_vendor_model = 1;

INSERT INTO `ok_okaycms__yandex_xml_vendor_model__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 0
FROM ok_products
WHERE not_to__okaycms__yandex_xml_vendor_model = 1;

ALTER TABLE `ok_products`
DROP COLUMN `to__okaycms__yandex_xml_vendor_model`;

ALTER TABLE `ok_products`
DROP COLUMN `not_to__okaycms__yandex_xml_vendor_model`;

INSERT INTO `ok_okaycms__yandex_xml_vendor_model__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'category', 1
FROM ok_categories
WHERE to__okaycms__yandex_xml_vendor_model = 1;

ALTER TABLE `ok_categories`
DROP COLUMN `to__okaycms__yandex_xml_vendor_model`;

INSERT INTO `ok_okaycms__yandex_xml_vendor_model__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'brand', 1
FROM ok_brands
WHERE to__okaycms__yandex_xml_vendor_model = 1;

ALTER TABLE `ok_brands`
DROP COLUMN `to__okaycms__yandex_xml_vendor_model`;


CREATE TABLE `ok_okaycms__google_merchant__feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_okaycms__google_merchant__relations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) NOT NULL,
  `entity_id` int(11) NOT NULL,
  `entity_type` enum('product','category','brand') COLLATE utf8mb4_unicode_ci NOT NULL,
  `include` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_id_entity_type_include_entity_id` (`feed_id`,`entity_type`,`include`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_okaycms__google_merchant__feeds` (`id`, `name`, `url`, `enabled`) VALUES
(1,	'New Feed',	'feed', 1);

INSERT INTO `ok_okaycms__google_merchant__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 1
FROM ok_products
WHERE to__okaycms__google_merchant = 1;

INSERT INTO `ok_okaycms__google_merchant__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'product', 0
FROM ok_products
WHERE not_to__okaycms__google_merchant = 1;

ALTER TABLE `ok_products`
DROP COLUMN `to__okaycms__google_merchant`;

ALTER TABLE `ok_products`
DROP COLUMN `not_to__okaycms__google_merchant`;

INSERT INTO `ok_okaycms__google_merchant__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'category', 1
FROM ok_categories
WHERE to__okaycms__google_merchant = 1;

ALTER TABLE `ok_categories`
DROP COLUMN `to__okaycms__google_merchant`;

INSERT INTO `ok_okaycms__google_merchant__relations` (`feed_id`, `entity_id`, `entity_type`, `include`)
SELECT 1, id, 'brand', 1
FROM ok_brands
WHERE to__okaycms__google_merchant = 1;

ALTER TABLE `ok_brands`
DROP COLUMN `to__okaycms__google_merchant`;