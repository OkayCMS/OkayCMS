ALTER TABLE `ok_lang_delivery` RENAME TO `ok_lang_deliveries`;
ALTER TABLE `ok_delivery` RENAME TO `ok_deliveries`;
ALTER TABLE `ok_banners` DROP `description`;

ALTER TABLE `ok_options_aliases_values` RENAME TO `ok_features_values_aliases_values`;

ALTER TABLE `ok_orders_status` ADD `status_1c` enum('new','accepted','to_delete', 'not_use') NULL DEFAULT 'not_use';

-- 1
ALTER TABLE `ok_languages`
DROP `name_ru`,
DROP `name_ua`,
DROP `name_en`;

CREATE TABLE `ok_lang_languages` (
  `lang_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `lang_id` (`lang_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ok_lang_languages` (`lang_id`, `language_id`, `name`) VALUES
(1,	1,	'Русский'),
(1,	2,	'Английский'),
(1,	3,	'Украинский'),
(2,	1,	'Russian'),
(2,	2,	'English'),
(2,	3,	'Ukrainian'),
(3,	1,	'Російська'),
(3,	2,	'Англійська'),
(3,	3,	'Українська');

-- 2
ALTER TABLE `ok_lang_features_values` DROP `feature_id`;