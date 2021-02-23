/**
WARNING!!! ВНИМАНИЕ!!! Перез запуском данного SQL скрипта, нужно запустить скрипт update_features.php который указан в инструкции по обновлению.
Скрипт должен будет исправить дублирующие значения свойств.
Данный SQL скрипт переносит значения свойств только для основного языка, переводы значений нужно будет выполнять самостоятельно.
 */

SELECT @main_lang_id:=`id` FROM `ok_languages` ORDER BY `position` ASC LIMIT 1;

CREATE TABLE `ok_products_features_values` (
  `product_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  UNIQUE KEY `product_id_value_id` (`product_id`,`value_id`),
  KEY `product_id` (`product_id`),
  KEY `value_id` (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ok_lang_features_values` (
  `lang_id` int(11) NOT NULL,
  `feature_value_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `value` varchar(1024) NOT NULL,
  `translit` varchar(255) NOT NULL,
  KEY `translit_feature_id_lang_id` (`translit`,`feature_id`,`lang_id`),
  KEY `lang_id` (`lang_id`),
  KEY `feature_value_id` (`feature_value_id`),
  KEY `translit` (`translit`),
  KEY `value` (`value`(64))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Создаем таблицу значений свойств*/
CREATE TABLE `ok_features_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_id` int(11) NOT NULL,
  `feature_id` int(11) NOT NULL,
  `value` varchar(1024) NOT NULL DEFAULT '',
  `translit` varchar(255) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT 0,
  `to_index` tinyint(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `feature_id` (`feature_id`),
  KEY `position` (`position`),
  UNIQUE KEY `feature_id_translit` (`feature_id`,`translit`),
  KEY `value` (`value`(64))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Копируем данные в новую таблицу*/
INSERT INTO `ok_features_values` (`lang_id`, `feature_id`, `value`, `translit`) SELECT MAX(`lang_id`), MAX(`feature_id`), MAX(`value`), MAX(`translit`) FROM `ok_options` as `o` WHERE `o`.`lang_id`=@main_lang_id GROUP BY `value`, `feature_id`;

UPDATE `ok_features_values` SET `position`=`id`;

/*Заполняем таблицу значений свойств товаров*/
INSERT INTO `ok_products_features_values` (`product_id`, `value_id`)
  SELECT `o`.`product_id`, `fv`.`id`
  FROM `ok_features_values` AS `fv`
  LEFT JOIN `ok_options` AS `o` ON `o`.`lang_id`=`fv`.`lang_id` AND `o`.`feature_id`=`fv`.`feature_id` AND `o`.`value`=`fv`.`value` AND `o`.`lang_id`=@main_lang_id;

/*Заполняем сразу мультиязычную таблицу для всех языков значениями дефолтного языка*/
INSERT INTO `ok_lang_features_values` (`lang_id`, `feature_value_id`, `value`, `translit`, `feature_id`)
  SELECT `l`.`id`, `f`.`id`, `f`.`value`, `f`.`translit`, `f`.`feature_id`
  FROM `ok_features_values` AS `f`
  LEFT JOIN `ok_languages` AS `l` ON 1 WHERE `lang_id`=@main_lang_id;

ALTER TABLE `ok_features` ADD `to_index_new_value` tinyint(1) NULL DEFAULT '0';

ALTER TABLE `ok_features_values`
DROP `lang_id`;

DROP TRIGGER IF EXISTS `categories_date_create`;
DROP TRIGGER IF EXISTS `products_date_create`;

/*Удаляем старую таблицу значений свойств*/
DROP TABLE `ok_options`;

INSERT INTO `ok_settings` (`name`, `value`) VALUES
('max_filter_brands',	'1'),
('max_filter_filter',	'1'),
('max_filter_features_values',	'1'),
('max_filter_features',	'1'),
('max_filter_depth',	'1');

ALTER TABLE `ok_managers` ADD `menu` text NULL;

ALTER TABLE `ok_categories`
CHANGE `annotation` `annotation` text COLLATE 'utf8_general_ci' NULL AFTER `meta_description`,
CHANGE `description` `description` text COLLATE 'utf8_general_ci' NULL AFTER `annotation`,
CHANGE `auto_description` `auto_description` text COLLATE 'utf8_general_ci' NULL AFTER `auto_meta_desc`;

ALTER TABLE `ok_lang_categories`
CHANGE `annotation` `annotation` text COLLATE 'utf8_general_ci' NULL AFTER `meta_description`,
CHANGE `description` `description` text COLLATE 'utf8_general_ci' NULL AFTER `annotation`,
CHANGE `auto_description` `auto_description` text COLLATE 'utf8_general_ci' NULL AFTER `auto_meta_desc`;

ALTER TABLE `ok_brands`
CHANGE `annotation` `annotation` text COLLATE 'utf8_general_ci' NULL AFTER `meta_description`,
CHANGE `description` `description` text COLLATE 'utf8_general_ci' NULL AFTER `annotation`,
CHANGE `image` `image` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `description`;

ALTER TABLE `ok_lang_brands`
CHANGE `annotation` `annotation` text COLLATE 'utf8_general_ci' NULL AFTER `meta_description`,
CHANGE `description` `description` text COLLATE 'utf8_general_ci' NULL AFTER `annotation`;
