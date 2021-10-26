ALTER TABLE `ok_features_values` 
    ADD `external_id` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL;

ALTER TABLE `ok_features` 
    ADD `visible` tinyint(1) NULL DEFAULT '1';

ALTER TABLE `ok_categories`
    ADD `auto_annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `auto_description`;

ALTER TABLE `ok_lang_categories`
    ADD `auto_annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `auto_description`;

ALTER TABLE `ok_seo_filter_patterns`
    ADD `annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `meta_description`;

ALTER TABLE `ok_lang_seo_filter_patterns`
    ADD `annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `meta_description`;