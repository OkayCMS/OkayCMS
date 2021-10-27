DROP TABLE IF EXISTS `ok_advantages`;
DROP TABLE IF EXISTS `ok_lang_advantages`;

INSERT INTO `ok_okaycms__banners` (`name`, `group_name`, `position`, `visible`, `show_all_pages`, `show_all_products`, `categories`, `pages`, `brands`, `as_individual_shortcode`, `settings`) VALUES
('Advantage',	'advantage',	2,	1,	0,	0,	'0',	'1',	'0',	1,	'a:2:{s:9:\"as_slider\";s:1:\"1\";s:14:\"rotation_speed\";s:4:\"2500\";}');
SET @banner_id = LAST_INSERT_ID();

INSERT INTO `ok_okaycms__banners_images` (`banner_id`, `name`, `alt`, `title`, `url`, `description`, `image`, `position`, `visible`, `settings`, `is_lang_banner`) VALUES
(@banner_id,	'advantage1',	'Доставка',	'Доставка по всей стране',	'',	'',	'advantage1_icon.jpg',	5,	1,	'a:3:{s:12:\"variant_show\";s:7:\"default\";s:7:\"desktop\";a:2:{s:1:\"w\";s:0:\"\";s:1:\"h\";s:0:\"\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:0:\"\";s:1:\"h\";s:0:\"\";}}',	0);
SET @banner_image_id = LAST_INSERT_ID();
INSERT INTO `ok_lang_okaycms__banners_images` (`lang_id`, `banner_image_id`, `name`, `alt`, `title`, `url`, `description`, `image`) VALUES
(1,	@banner_image_id,	'advantage1',	'Доставка',	'Доставка по всей стране',	'',	'',	'advantage1_icon.jpg'),
(2,	@banner_image_id,	'advantage1',	'Delivery',	'Nationwide delivery',	'',	'',	'advantage1_icon.jpg'),
(3,	@banner_image_id,	'advantage1',	'Доставка',	'Доставка по всій країні',	'',	'',	'advantage1_icon.jpg');

INSERT INTO `ok_okaycms__banners_images` (`banner_id`, `name`, `alt`, `title`, `url`, `description`, `image`, `position`, `visible`, `settings`, `is_lang_banner`) VALUES
(@banner_id,	'advantage2',	'Гарантия',	'100% гарантия качества',	'',	'',	'advantage2_icon.jpg',	6,	1,	'a:3:{s:12:\"variant_show\";s:7:\"default\";s:7:\"desktop\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}}',	0);
SET @banner_image_id = LAST_INSERT_ID();
INSERT INTO `ok_lang_okaycms__banners_images` (`lang_id`, `banner_image_id`, `name`, `alt`, `title`, `url`, `description`, `image`) VALUES
(1,	@banner_image_id,	'advantage2',	'Гарантия',	'100% гарантия качества',	'',	'',	'advantage2_icon.jpg'),
(2,	@banner_image_id,	'advantage2',	'Guarantee',	'100% quality guarantee',	'',	'',	'advantage2_icon.jpg'),
(3,	@banner_image_id,	'advantage2',	'Гарантія',	'100% гарантія якості 100% гарантія якості',	'',	'',	'advantage2_icon.jpg');

INSERT INTO `ok_okaycms__banners_images` (`banner_id`, `name`, `alt`, `title`, `url`, `description`, `image`, `position`, `visible`, `settings`, `is_lang_banner`) VALUES
(@banner_id,	'advantage3',	'Возврат',	'14 дней на возврат товара',	'',	'',	'advantage3_icon.jpg',	7,	1,	'a:3:{s:12:\"variant_show\";s:7:\"default\";s:7:\"desktop\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}}',	0);
SET @banner_image_id = LAST_INSERT_ID();
INSERT INTO `ok_lang_okaycms__banners_images` (`lang_id`, `banner_image_id`, `name`, `alt`, `title`, `url`, `description`, `image`) VALUES
(1,	@banner_image_id,	'advantage3',	'Возврат',	'14 дней на возврат товара',	'',	'',	'advantage3_icon.jpg'),
(2,	@banner_image_id,	'advantage3',	'Return',	'14 days for return',	'',	'',	'advantage3_icon.jpg'),
(3,	@banner_image_id,	'advantage3',	'Повернення',	'14 днів на повернення товару',	'',	'',	'advantage3_icon.jpg');

INSERT INTO `ok_okaycms__banners_images` (`banner_id`, `name`, `alt`, `title`, `url`, `description`, `image`, `position`, `visible`, `settings`, `is_lang_banner`) VALUES
(@banner_id,	'advantage4',	'Самовывоз',	'Самовывоз из магазина',	'',	'',	'advantage4_icon.jpg',	8,	1,	'a:3:{s:12:\"variant_show\";s:7:\"default\";s:7:\"desktop\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}s:6:\"mobile\";a:2:{s:1:\"w\";s:3:\"100\";s:1:\"h\";s:3:\"100\";}}',	0);
SET @banner_image_id = LAST_INSERT_ID();
INSERT INTO `ok_lang_okaycms__banners_images` (`lang_id`, `banner_image_id`, `name`, `alt`, `title`, `url`, `description`, `image`) VALUES
(1,	@banner_image_id,	'advantage4',	'Самовывоз',	'Самовывоз из магазина',	'',	'',	'advantage4_icon.jpg'),
(2,	@banner_image_id,	'advantage4',	'Pickup',	'Pickup',	'',	'',	'advantage4_icon.jpg'),
(3,	@banner_image_id,	'advantage4',	'Самовивіз',	'Самовивіз з магазину',	'',	'',	'advantage4_icon.jpg');

ALTER TABLE ok_features_values_aliases_values
    ADD COLUMN feature_value_id INT(11) NOT NULL;

UPDATE ok_features_values_aliases_values AS fvav
    LEFT JOIN ok_lang_features_values AS lfv ON lfv.translit = fvav.translit AND lfv.lang_id = fvav.lang_id
    SET fvav.feature_value_id = IF(lfv.feature_value_id IS NOT NULL, lfv.feature_value_id, 0)
WHERE 1;

DELETE
FROM ok_features_values_aliases_values
WHERE feature_value_id = 0;

ALTER TABLE ok_features_values_aliases_values
DROP COLUMN translit;

UPDATE ok_pages AS p
    LEFT JOIN ok_lang_pages AS lp ON lp.page_id = p.id
    SET p.description       = '',
        p.meta_description  = '',
        lp.description      = '',
        lp.meta_description = ''
WHERE url = 'user/register';

DELETE FROM `ok_pages` WHERE `id` = '5';
INSERT INTO `ok_pages` (`id`, `url`, `name`, `name_h1`, `meta_title`, `meta_description`, `meta_keywords`, `description`, `position`, `visible`, `last_modify`)
VALUES
(5,	'404',	'Страница не найдена',	'',	'Страница не найдена',	'Страница не найдена',	'Страница не найдена',	'<p><span data-language=\"page404_text\">Страница, которую вы запрашиваете, не существует, или она удалена. Возможно, вы набрали неправильный адрес. Попробуйте ввести еще раз.</span></p>',	5,	1,	'2019-06-24 11:33:00');

DELETE FROM `ok_lang_pages` WHERE `page_id` = '5';
INSERT INTO `ok_lang_pages` (`lang_id`, `page_id`, `name`, `name_h1`, `meta_title`, `meta_description`, `meta_keywords`, `description`)
VALUES
(1,	5,	'Страница не найдена',	'',	'Страница не найдена',	'Страница не найдена',	'Страница не найдена',	'<p><span data-language=\"page404_text\">Страница, которую вы запрашиваете, не существует, или она удалена. Возможно, вы набрали неправильный адрес. Попробуйте ввести еще раз.</span></p>'),
(2,	5,	'Page not found',	'',	'Page not found',	'Page not found',	'Page not found',	'<p><span data-language=\"page404_text\">The page you are requesting does not exist or has been deleted. You may have typed the wrong address. Try entering again.</span></p>'),
(3,	5,	'Сторінку не знайдено',	'',	'Сторінку не знайдено',	'Сторінку не знайдено',	'Сторінку не знайдено',	'<p><span data-language=\"page404_text\">Сторінка, яку ви запитуєте, не існує, або вона видалена. Можливо, ви набрали неправильний адресу. Спробуйте ввести ще раз.</span></p>');

ALTER TABLE `ok_brands`
    ADD `name_h1` varchar(255) DEFAULT '';

ALTER TABLE `ok_lang_brands`
    ADD `name_h1` varchar(255) DEFAULT '';