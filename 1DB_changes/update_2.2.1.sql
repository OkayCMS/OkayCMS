ALTER TABLE `ok_support_info` ADD COLUMN `accesses` VARCHAR(2048) NULL DEFAULT '';
INSERT INTO `ok_settings`(`name`, `value`) VALUES ('captcha_type','default');