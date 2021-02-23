-- 0
ALTER TABLE ok_features ADD COLUMN `description` TEXT;
ALTER TABLE ok_lang_features ADD COLUMN `description` TEXT;

CREATE TABLE ok_modules (
  `id` INT AUTO_INCREMENT,
  `vendor` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `position` INT NOT NULL DEFAULT 0,
  `enabled` TINYINT,
  `type` VARCHAR(255),
  PRIMARY KEY (`id`)
);

UPDATE `ok_payment_methods` SET `module` = 'OkayCMS/LiqPay' WHERE `module` = 'Liqpay';
UPDATE `ok_payment_methods` SET `module` = 'OkayCMS/Yandex' WHERE `module` = 'Yandex';

ALTER TABLE `ok_modules` ADD `backend_main_controller` varchar(255) COLLATE 'utf8_general_ci' NULL;

INSERT INTO `ok_modules` (`vendor`, `name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
('OkayCMS', 'Integration1C', '1', '1', NULL, NULL),
('OkayCMS', 'Yandex', '2', '1', NULL, NULL),
('OkayCMS', 'LiqPay', '3', '1', NULL, NULL),
('OkayCMS', 'Rozetka', '4', '1', 'xml', 'RozetkaXmlAdmin');

-- 1

UPDATE `ok_modules` SET `type` = 'payment' WHERE `id` = '2';
UPDATE `ok_modules` SET `type` = 'payment' WHERE `id` = '3';

-- 2

ALTER TABLE `ok_payment_methods`
CHANGE `module` `module` varchar(255) COLLATE 'utf8_general_ci' NULL DEFAULT '' AFTER `id`;

-- 3

ALTER TABLE `ok_modules`
CHANGE `name` `module_name` varchar(255) COLLATE 'utf8_general_ci' NOT NULL AFTER `vendor`;

INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
('OkayCMS', 'WayForPay', '5', '1', 'payment', NULL);

ALTER TABLE `ok_variants` DROP `attachment`;

ALTER TABLE `ok_lang_features_values` DROP `feature_id`;

-- выполнить и удалить скрипт update_3.0.4_Beta.php

ALTER TABLE `ok_variants` DROP COLUMN `feed`;
ALTER TABLE `ok_categories` DROP COLUMN `yandex_name`;
UPDATE `ok_modules` SET `backend_main_controller`='DescriptionAdmin' WHERE `id`=3;

INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
('OkayCMS', 'YandexXMLVendorModel', '6', '1', 'xml', 'YandexXmlAdmin'),
('OkayCMS', 'YandexXML', '7', '1', 'xml', 'YandexXmlAdmin'),
('OkayCMS', 'GoogleMerchant', '8', '1', 'xml', 'GoogleMerchantAdmin'),
('OkayCMS', 'YandexMoneyApi', '9', '1', 'payment', 'DescriptionAdmin');

DELETE FROM `ok_modules` WHERE ((`id` = '2'));
UPDATE `ok_payment_methods` SET `module` = '', `settings` = '' WHERE `module` = 'OkayCMS/Yandex';

UPDATE `ok_modules` SET `backend_main_controller` = 'Description1CAdmin' WHERE `id` = '1';
UPDATE `ok_modules` SET `backend_main_controller` = 'DescriptionAdmin' WHERE `id` = '5';