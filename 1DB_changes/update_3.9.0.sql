ALTER TABLE `ok_seo_filter_patterns`
CHANGE `type` `type` enum('brand','feature','brand_feature') COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `category_id`;

ALTER TABLE `ok_payment_methods`
ADD `auto_submit` tinyint(1) DEFAULT 0 AFTER `settings`;

ALTER TABLE `ok_users`
ADD `last_name` varchar(255) NULL AFTER `name`;

ALTER TABLE `ok_orders`
ADD `last_name` varchar(255) NULL AFTER `name`;

ALTER TABLE `ok_managers`
ADD `email` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `login`;

UPDATE `ok_managers` SET `email` = (SELECT `value` FROM `ok_settings` WHERE `param` = 'admin_email');

DELETE FROM `ok_settings` WHERE `param` = 'admin_email';

CREATE TABLE `ok_user_cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_variant_id` (`user_id`,`variant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_user_wishlist_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_user_comparison_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_user_browsed_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ok_users`
ADD `preferred_delivery_id` int(11) NULL,
ADD `preferred_payment_method_id` int(11) NULL AFTER `preferred_delivery_id`;

ALTER TABLE `ok_comments`
ADD `user_id` int(11) NULL;

ALTER TABLE `ok_modules`
ADD `version` varchar(16) NULL;

UPDATE `ok_modules` SET `version` = '1.0.0';

CREATE TABLE `ok_discounts`
(
    `id`                 INT(11)        NOT NULL AUTO_INCREMENT,
    `entity`             VARCHAR(255)   NOT NULL,
    `entity_id`          INT(11)        NOT NULL,
    `type`               VARCHAR(255)   NOT NULL,
    `value`              DECIMAL(10, 2) NOT NULL,
    `from_last_discount` TINYINT(1) DEFAULT 1,
    `name`               VARCHAR(255)   NOT NULL,
    `description`        TEXT,
    `position`           INT(11),
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE `ok_lang_discounts`
(
    `lang_id`     INT(11)      NOT NULL,
    `discount_id` INT(11)      NOT NULL,
    `name`        VARCHAR(255) NOT NULL,
    `description` TEXT,
    UNIQUE KEY `lang_id` (`lang_id`, `discount_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO `ok_discounts`
(`entity`, `entity_id`, `type`, `value`, `from_last_discount`, `name`, `description`, `position`)
    (SELECT 'order',
            `id`,
            'absolute',
            `coupon_discount`,
            1,
            'Coupon',
            `coupon_code`,
            0
     FROM `ok_orders`);

INSERT INTO `ok_discounts`
(`entity`, `entity_id`, `type`, `value`, `from_last_discount`, `name`, `position`)
    (SELECT 'order', `id`, 'percent', `discount`, 0, 'User group', 0 FROM `ok_orders`);

DELETE
FROM `ok_discounts`
WHERE `value` = 0.00;

UPDATE `ok_discounts`
SET `position` = `id`
WHERE 1;

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`, `description`)
    (SELECT 1, `id`, 'Купон', `description` FROM `ok_discounts` WHERE `name` = 'Coupon');

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`)
    (SELECT 1, `id`, 'Группа покупателя' FROM `ok_discounts` WHERE `name` = 'User group');

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`, `description`)
    (SELECT 2, `id`, 'Coupon', `description` FROM `ok_discounts` WHERE `name` = 'Coupon');

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`)
    (SELECT 2, `id`, 'User group' FROM `ok_discounts` WHERE `name` = 'User group');

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`, `description`)
    (SELECT 3, `id`, 'Купон', `description` FROM `ok_discounts` WHERE `name` = 'Coupon');

INSERT INTO `ok_lang_discounts`
    (`lang_id`, `discount_id`, `name`)
    (SELECT 3, `id`, 'Група покупця' FROM `ok_discounts` WHERE `name` = 'User group');

ALTER TABLE `ok_orders`
    DROP COLUMN `discount`,
    DROP COLUMN `coupon_discount`,
    DROP COLUMN `coupon_code`,
    ADD `undiscounted_total_price` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' AFTER `ip`;

UPDATE `ok_orders` AS o
SET o.`undiscounted_total_price` =
        IFNULL((SELECT SUM(p.price * p.amount)
                FROM ok_purchases AS p
                WHERE p.order_id = o.id), 0)
WHERE 1;

ALTER TABLE `ok_purchases`
    ADD `undiscounted_price` DECIMAL(10, 2) NOT NULL DEFAULT '0.00' AFTER `variant_name`;

UPDATE `ok_purchases`
SET `undiscounted_price` = `price`
WHERE 1;

INSERT INTO `ok_settings` (`param`, `value`)
values ('cart_discount_sets', 'a:3:{i:0;s:17:"$<ok_coup $<ok_gr";i:1;s:9:"$<ok_coup";i:2;s:7:"$<ok_gr";}');

ALTER TABLE `ok_seo_filter_patterns`
ADD `second_feature_id` INT NULL DEFAULT NULL AFTER `feature_id`;

ALTER TABLE `ok_seo_filter_patterns`
CHANGE `type` `type` enum('brand','feature','brand_feature','feature_feature') COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `category_id`;

-- 1

ALTER TABLE `ok_seo_filter_patterns`
    ADD UNIQUE `category_id_type_feature_id_second_feature_id` (`category_id`, `type`, `feature_id`, `second_feature_id`),
DROP INDEX `category_id_param_type_feature_id`;

UPDATE `ok_settings`
SET `value` = 'a:1:{i:0;O:8:"stdClass":2:{s:3:"set";s:17:"$<ok_coup $<ok_gr";s:7:"partial";b:1;}}'
WHERE `param` = 'cart_discount_sets';

-- 2

UPDATE `ok_modules` SET `module_name` = 'YooKassa' WHERE `module_name` = 'YandexMoneyApi';