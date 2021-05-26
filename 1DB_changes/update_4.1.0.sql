ALTER TABLE `ok_categories`
    ADD `auto_annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `auto_description`;

ALTER TABLE `ok_lang_categories`
    ADD `auto_annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `auto_description`;

ALTER TABLE `ok_seo_filter_patterns`
    ADD `annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `meta_description`;

ALTER TABLE `ok_lang_seo_filter_patterns`
    ADD `annotation` mediumtext COLLATE 'utf8mb4_unicode_ci' NULL AFTER `meta_description`;