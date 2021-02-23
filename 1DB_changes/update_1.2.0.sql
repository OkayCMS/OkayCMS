ALTER TABLE `s_users` ADD `phone` VARCHAR(32) NOT NULL DEFAULT '' AFTER `name`,
    ADD `address` VARCHAR(255) NOT NULL DEFAULT '' AFTER `phone`;
ALTER TABLE `s_orders` CHANGE `phone` `phone` VARCHAR(32) NOT NULL DEFAULT '';

ALTER TABLE `s_delivery` ADD `image` VARCHAR(255) NOT NULL DEFAULT '' AFTER `separate_payment`;
ALTER TABLE `s_payment_methods` ADD `image` VARCHAR(255) NOT NULL DEFAULT '' AFTER `position`;

ALTER TABLE `s_comments` ADD `email` VARCHAR(255) NOT NULL DEFAULT '' AFTER `name`;
ALTER TABLE `s_comments` ADD `parent_id` INT(11) NOT NULL DEFAULT '0' AFTER `id`,
    ADD INDEX `parent_id` (`parent_id`);
ALTER TABLE `s_feedbacks`ADD COLUMN `processed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `message`;
CREATE TABLE `s_related_blogs` (
  `post_id` INT(11) NOT NULL,
  `related_id` INT(11) NOT NULL,
  `position` INT(11) NOT NULL,
  PRIMARY KEY (`post_id`, `related_id`),
  INDEX `position` (`position`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;
