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