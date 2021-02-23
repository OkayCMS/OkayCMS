-- 0
INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
('OkayCMS', 'PayKeeper', '10', '1', 'payment', 'DescriptionAdmin');

ALTER TABLE `ok_banners` ADD INDEX `category` (`categories`(100)), DROP INDEX `category`;
ALTER TABLE `ok_banners` ADD INDEX `pages` (`pages`(100)), DROP INDEX `pages`;
ALTER TABLE `ok_banners` ADD INDEX `brands` (`brands`(100)), DROP INDEX `brands`;
ALTER TABLE `ok_blog` ADD INDEX `url` (`url`(100)), DROP INDEX `url`;
ALTER TABLE `ok_brands` ADD INDEX `url` (`url`(100)), DROP INDEX `url`;
ALTER TABLE `ok_brands` ADD INDEX `name` (`name`(100)), DROP INDEX `name`;
ALTER TABLE `ok_categories` ADD INDEX `url` (`url`(100)), DROP INDEX `url`;
ALTER TABLE `ok_features_aliases` ADD UNIQUE`variable` (`variable`(100)), DROP INDEX `variable`;
ALTER TABLE `ok_features_aliases_values` ADD INDEX `value` (`value`(100)), DROP INDEX `value`;
ALTER TABLE `ok_features_values` ADD UNIQUE`feature_id_translit` (`feature_id`,`translit`(100)), DROP INDEX `feature_id_translit`;
ALTER TABLE `ok_features_values_aliases_values` ADD INDEX `translit` (`translit`(100)), DROP INDEX `translit`;
ALTER TABLE `ok_images` ADD INDEX `filename` (`filename`(100)), DROP INDEX `filename`;
ALTER TABLE `ok_lang_features_values` ADD INDEX `value` (`value`(100)), DROP INDEX `value`;
ALTER TABLE `ok_lang_features_values` ADD INDEX `translit_feature_id_lang_id` (`translit`(100),`lang_id`), DROP INDEX `translit_feature_id_lang_id`;
ALTER TABLE `ok_lang_features_values` ADD INDEX `translit` (`translit`(100)), DROP INDEX `translit`;
ALTER TABLE `ok_managers` ADD INDEX `login` (`login`(100)), DROP INDEX `login`;
ALTER TABLE `ok_pages` ADD INDEX `url` (`url`(100)), DROP INDEX `url`;
ALTER TABLE `ok_products` ADD INDEX `url` (`url`(100)), DROP INDEX `url`;
ALTER TABLE `ok_products` ADD INDEX `name` (`name`(100)), DROP INDEX `name`;
ALTER TABLE `ok_settings` ADD UNIQUE `param` (`param`(100)), DROP INDEX `param`;
ALTER TABLE `ok_settings_lang` ADD INDEX `name` (`param`(128)), DROP INDEX `name`;
ALTER TABLE `ok_subscribe_mailing` ADD INDEX `email` (`email`(100)), DROP INDEX `email`;
ALTER TABLE `ok_users` ADD INDEX `email` (`email`(100)), DROP INDEX `email`;
ALTER TABLE `ok_orders` ADD INDEX `code` (`url`(100)), DROP INDEX `code`;

ALTER TABLE `ok_features` DROP `yandex`;