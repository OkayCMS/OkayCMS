CREATE TABLE IF NOT EXISTS `ok_settings_lang` (
  `name` varchar(128) NOT NULL,
  `lang_id` int(11) NOT NULL DEFAULT '0',
  `value` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY (`lang_id`,`name`),
  KEY `name` (`name`),
  KEY `lang_id` (`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `ok_comments` CHANGE `type` `type` ENUM('product','blog','news') NOT NULL DEFAULT 'product';
