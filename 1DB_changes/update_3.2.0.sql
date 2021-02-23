-- 0
UPDATE `ok_settings` SET `param` = 'captcha_comment' WHERE `param` = 'captcha_product';
DELETE FROM `ok_settings` WHERE `param` = 'captcha_post';

ALTER TABLE `ok_seo_filter_patterns`
CHANGE `title` `meta_title` varchar(512) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `h1`,
CHANGE `keywords` `meta_keywords` varchar(512) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `meta_title`;

ALTER TABLE `ok_lang_seo_filter_patterns`
CHANGE `title` `meta_title` varchar(512) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `h1`,
CHANGE `keywords` `meta_keywords` varchar(512) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT '' AFTER `meta_title`;
