ALTER TABLE `s_spec_img` ADD COLUMN `position` INT(10) NOT NULL DEFAULT '0' AFTER `filename`;
UPDATE s_spec_img SET position=id;

ALTER TABLE `s_lang_products` ADD COLUMN `special` VARCHAR(255) NULL DEFAULT NULL AFTER `meta_description`;
UPDATE s_lang_products lp LEFT JOIN s_products p ON(p.id=lp.product_id) SET lp.special=p.special;

ALTER TABLE `s_categories` ADD COLUMN `name_h1` VARCHAR(255) NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `s_lang_categories` ADD COLUMN `name_h1` VARCHAR(255) NOT NULL AFTER `name`;

CREATE TABLE `s_managers` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`login` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`permissions` VARCHAR(1024) NULL DEFAULT NULL,
	`cnt_try` TINYINT NOT NULL DEFAULT '0',
	`last_try` DATE NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `login` (`login`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
ROW_FORMAT=DEFAULT;
INSERT INTO `s_managers` (`login`, `password`) VALUES ('admin', '$apr1$t13co40k$3r2S6/MpFwliaixrvvR5N.');
/* Логин "admin", пароль "1234" */
