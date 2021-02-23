-- 0
DROP TABLE IF EXISTS `ok_advantages`;
CREATE TABLE `ok_advantages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  `position` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_advantages` (`id`, `filename`, `text`, `position`) VALUES
(1,	'advantage1_icon_1.jpg',	'Доставка по всей стране',	2),
(2,	'advantage2_icon_1.jpg',	'100% гарантия качества',	0),
(3,	'advantage3_icon_1.jpg',	'14 дней на возврат товара',	1),
(4,	'advantage4_icon_1.jpg',	'Самовывоз из магазина',	3);

DROP TABLE IF EXISTS `ok_lang_advantages`;
CREATE TABLE `ok_lang_advantages` (
  `advantage_id` int(11) DEFAULT NULL,
  `lang_id` int(11) NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci,
  UNIQUE KEY `lang_id` (`advantage_id`,`lang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ok_lang_advantages` (`advantage_id`, `lang_id`, `text`) VALUES
(1,	1,	'Доставка по всей стране'),
(1,	2,	'Nationwide delivery'),
(1,	3,	'Доставка по всій країні'),
(2,	1,	'100% гарантия качества'),
(2,	2,	'100% quality guarantee'),
(2,	3,	'100% гарантія якості'),
(3,	1,	'14 дней на возврат товара'),
(3,	2,	'14 days for return'),
(3,	3,	'14 днів на повернення товару'),
(4,	1,	'Самовывоз из магазина'),
(4,	2,	'Pickup'),
(4,	3,	'Самовивіз з магазину');