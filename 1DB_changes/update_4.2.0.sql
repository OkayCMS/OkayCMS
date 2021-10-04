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

