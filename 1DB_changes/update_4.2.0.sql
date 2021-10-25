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