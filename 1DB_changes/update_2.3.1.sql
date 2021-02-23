ALTER TABLE `ok_settings` CHANGE `name` `param` varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '' AFTER `setting_id`;
ALTER TABLE `ok_settings_lang` CHANGE `name` `param` varchar(128) COLLATE 'utf8_general_ci' NOT NULL FIRST;
/*Где-то пошел сбой, и у некоторых пользователей нет такой колонки, запрос выполнять если её нет*/
ALTER TABLE `ok_support_info` ADD COLUMN `accesses` VARCHAR(2048) NULL DEFAULT '';
INSERT INTO `ok_settings` (`param`, `value`) VALUES ('products_image_sizes	', '200x200|50x50|1800x1200w|600x340|75x75|330x300|800x600|55x55|300x120|35x35');
ALTER TABLE `ok_orders_status` ADD `status_1c` varchar(32) NULL DEFAULT '';
