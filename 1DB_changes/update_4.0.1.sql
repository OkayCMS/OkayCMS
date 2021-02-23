UPDATE `ok_modules` SET `type` = 'xml' WHERE `vendor` = 'OkayCMS' AND `module_name` = 'Hotline';
UPDATE `ok_settings` SET `param` = 'max_brands_filter_depth' WHERE `param` = 'max_filter_brands';
UPDATE `ok_settings` SET `param` = 'max_other_filter_depth' WHERE `param` = 'max_filter_filter';
UPDATE `ok_settings` SET `param` = 'max_features_filter_depth' WHERE `param` = 'max_filter_features';
UPDATE `ok_settings` SET `param` = 'max_features_values_filter_depth' WHERE `param` = 'max_filter_features_values';

DELETE FROM `ok_settings` WHERE `param` = 'filter_canonical_type';

-- Удалим старые, не используемые директивы
DELETE FROM `ok_settings` WHERE `param` = 'yandex_export_not_in_stock';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_available_for_retail_store';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_available_for_reservation';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_short_description';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_has_manufacturer_warranty';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_has_seller_warranty';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_sales_notes';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_metrika_token';
DELETE FROM `ok_settings` WHERE `param` = 'topvisor_key';
DELETE FROM `ok_settings` WHERE `param` = 'y_metric';
DELETE FROM `ok_settings` WHERE `param` = 'yandex_metrika_app_id';

INSERT INTO `ok_settings` (`param`, `value`) VALUES 
('canonical_catalog_pagination', 3),
('canonical_catalog_page_all', 3),
('canonical_category_brand', 6),
('canonical_category_features', 6),
('canonical_catalog_other_filter', 6),
('canonical_catalog_filter_pagination', 7),
('robots_catalog_pagination', 1),
('robots_catalog_page_all', 1),
('robots_category_brand', 1),
('robots_category_features', 1),
('robots_catalog_other_filter', 1),
('robots_catalog_filter_pagination', 1);
