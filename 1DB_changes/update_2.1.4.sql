ALTER TABLE `ok_categories` ADD `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `ok_categories` ADD INDEX `created` (`created`);
