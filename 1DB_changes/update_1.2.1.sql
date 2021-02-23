ALTER TABLE `s_banners` ADD `group_id` VARCHAR(32) NOT NULL DEFAULT '' AFTER `id`;
UPDATE `s_banners` SET `group_id`=CONCAT('group', `id`);

ALTER TABLE `s_brands` ADD `position` INT(11) NOT NULL AFTER `last_modify`;
UPDATE `s_brands` SET position=id;

ALTER TABLE `s_languages` DROP COLUMN `is_default`;

ALTER TABLE `s_languages` ADD `name_ru` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_uk` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_by` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_en` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_ch` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_de` VARCHAR(255) NOT NULL DEFAULT '',
    ADD `name_fr` VARCHAR(255) NOT NULL DEFAULT '';
UPDATE `s_languages` set name_ru=name, name_uk=name, name_by=name, name_en=name, name_ch=name, name_de=name, name_fr=name;

ALTER TABLE `s_comments` ADD COLUMN `lang_id` INT(11) NOT NULL AFTER `approved`;
ALTER TABLE `s_feedbacks` ADD COLUMN `lang_id` INT(11) NOT NULL AFTER `processed`;