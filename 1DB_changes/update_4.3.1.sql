UPDATE `ok_payment_methods` SET `image` = 'liqpay_logo.png' WHERE `ok_payment_methods`.`id` = 6;

ALTER TABLE `ok_categories` ADD `show_table_content` tinyint(1) NOT NULL DEFAULT '0' AFTER `visible`;
UPDATE `ok_settings` SET `value` = 'fda37584a5eed426c352a431d4852e01' WHERE `ok_settings`.`param` = 'newpost_key';

ALTER TABLE `ok_features`  ADD `show_in_product` INT(2) NOT NULL DEFAULT '1'  AFTER `visible`;