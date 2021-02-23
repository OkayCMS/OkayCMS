ALTER TABLE `ok_router_cache`
CHANGE `type` `type` enum('category','product','blog_category','post') COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `slug_url`;

CREATE TABLE `ok_blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name_h1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `annotation` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `external_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `level_depth` tinyint(1) NOT NULL DEFAULT 1,
  `last_modify` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `position` (`position`),
  KEY `visible` (`visible`),
  KEY `external_id` (`external_id`),
  KEY `created` (`created`),
  KEY `url` (`url`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_blog_categories` (`id`, `parent_id`, `name`, `name_h1`, `meta_title`, `meta_keywords`, `meta_description`, `annotation`, `description`, `url`, `image`, `position`, `visible`, `external_id`, `level_depth`, `last_modify`, `created`) VALUES
(1,	0,	'Новости',	'',	'Новости',	'Новости',	'',	'',	'',	'news',	'',	1,	1,	'',	1,	'2020-05-19 08:06:47',	NULL),
(2,	0,	'Статьи',	'',	'Статьи',	'Статьи',	'',	'',	'',	'blog',	'',	2,	1,	'',	1,	'2020-05-19 08:07:03',	NULL);

CREATE TABLE `ok_lang_blog_categories` (
  `lang_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name_h1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `annotation` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `lang_id` (`lang_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_lang_blog_categories` (`lang_id`, `category_id`, `name`, `name_h1`, `meta_title`, `meta_keywords`, `meta_description`, `annotation`, `description`) VALUES
(1,	1,	'Новости',	'',	'Новости',	'Новости',	'',	'',	''),
(1,	2,	'Статьи',	'',	'Статьи',	'Статьи',	'',	'',	''),
(2,	1,	'Новости',	'',	'Новости',	'Новости',	'',	'',	''),
(2,	2,	'Статьи',	'',	'Статьи',	'Статьи',	'',	'',	''),
(3,	1,	'Новости',	'',	'Новости',	'Новости',	'',	'',	''),
(3,	2,	'Статьи',	'',	'Статьи',	'Статьи',	'',	'',	'');

CREATE TABLE `ok_blog_categories_relation` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`post_id`,`category_id`),
  KEY `position` (`position`),
  KEY `post_id` (`post_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ok_blog` ADD `main_category_id` int(11) NULL;

UPDATE `ok_pages` SET `url` = 'all-posts' WHERE `url` = 'blog';
UPDATE `ok_settings` SET `value` = 'all-posts' WHERE `param` = 'all_blog_routes_template__default';

DELETE FROM `ok_settings` WHERE `param` = 'lastModifyNews'
OR `param` = 'lastModifyPosts'
OR `param` = 'all_news_routes_template__default'
OR `param` = 'news_item_routes_template__default'
OR `param` = 'news_item_routes_template_slash_end'
OR `param` = 'blog_item_routes_template'
OR `param` = 'blog_item_routes_template__default'
OR `param` = 'blog_item_routes_template_slash_end'
OR `param` = 'news_item_routes_template';

UPDATE ok_blog b
LEFT JOIN ok_blog_categories_relation bc ON b.id = bc.post_id AND bc.position=(SELECT MIN(position) FROM ok_blog_categories_relation WHERE post_id=b.id LIMIT 1)
SET b.main_category_id = bc.category_id;

ALTER TABLE `ok_comments`
CHANGE `type` `type` enum('product','blog','news','post') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'product' AFTER `text`;

UPDATE `ok_comments` SET `type` = 'post' WHERE `type` = 'blog' OR `type` = 'news';

ALTER TABLE `ok_comments`
CHANGE `type` `type` enum('product','post') COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'product' AFTER `text`;

/*Переносим блог по категориям новости и статьи*/
UPDATE `ok_blog` SET `main_category_id` = 1 WHERE `type_post` = 'news';
UPDATE `ok_blog` SET `main_category_id` = 2 WHERE `type_post` = 'blog';

INSERT INTO `ok_blog_categories_relation` (`post_id`, `category_id`)
SELECT `id`, 1 FROM `ok_blog` WHERE `type_post` = 'news';

INSERT INTO `ok_blog_categories_relation` (`post_id`, `category_id`)
SELECT `id`, 2 FROM `ok_blog` WHERE `type_post` = 'blog';

ALTER TABLE `ok_blog` DROP `type_post`;

INSERT INTO `ok_settings` (`param`, `value`) VALUES 
('blog_category_routes_template', 'no_prefix'),
('post_routes_template', 'no_prefix_and_path');

CREATE TABLE `ok_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_modify` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `position` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `url` (`url`(100)),
  KEY `name` (`name`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ok_lang_authors` (
  `lang_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_title` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_keywords` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `meta_description` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `lang_id` (`lang_id`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ok_blog` ADD `author_id` int(11) NULL AFTER `id`;
ALTER TABLE `ok_blog` ADD `read_time` int(11) NULL;
ALTER TABLE `ok_blog` ADD `updated_date` date NULL;

ALTER TABLE `ok_blog`
ADD `rating` float(3,1) NULL DEFAULT '0.00',
ADD `votes` int(11) NULL DEFAULT '0' AFTER `rating`;

ALTER TABLE `ok_authors`
ADD `position_name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`;

ALTER TABLE `ok_lang_authors`
ADD `position_name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`;

ALTER TABLE `ok_blog` ADD `show_table_content` tinyint(1) NOT NULL DEFAULT '0' AFTER `visible`;

ALTER TABLE `ok_authors` ADD `socials` text NULL;

ALTER TABLE `ok_okaycms__banners`
ADD `as_individual_shortcode` tinyint(1) NULL DEFAULT '0' AFTER `individual_shortcode`;

UPDATE `ok_okaycms__banners` SET `as_individual_shortcode` = 1 WHERE `individual_shortcode` != '';

ALTER TABLE `ok_okaycms__banners` DROP `individual_shortcode`;
