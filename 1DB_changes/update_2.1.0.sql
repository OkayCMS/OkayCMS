ALTER TABLE `ok_variants` ADD `units` VARCHAR(32) NOT NULL DEFAULT '';
ALTER TABLE `ok_lang_variants` ADD `units` VARCHAR(32) NOT NULL DEFAULT '';
ALTER TABLE `ok_purchases` ADD `units` VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE `ok_managers` CHANGE `menu_status` `menu_status` TINYINT(1) NOT NULL DEFAULT '1';