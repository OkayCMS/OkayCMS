ALTER TABLE `ok_features_values` ADD `external_id` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL;
ALTER TABLE `ok_features` ADD `visible` tinyint(1) NULL DEFAULT '1';
