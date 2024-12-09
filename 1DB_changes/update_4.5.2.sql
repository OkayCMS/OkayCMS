ALTER TABLE `ok_lang_features_values` ADD `position` INT(11) NOT NULL DEFAULT 0 AFTER `translit`, ADD INDEX `position` (`position`);
ALTER TABLE `ok_features_values` DROP INDEX `feature_id_translit`, ADD INDEX `feature_id_translit` (`feature_id`, `translit`) USING BTREE;

SET @rownum := 0;
    SET @current_feature_id := 0;

    CREATE TEMPORARY TABLE temp_positions AS
SELECT feature_value_id, new_position
FROM (
         SELECT fv.id AS feature_value_id,
                (@rownum := IF(@current_feature_id = fv.feature_id, @rownum + 1, 1)) AS new_position,
                @current_feature_id := fv.feature_id
         FROM ok_features_values fv
         ORDER BY fv.feature_id, fv.position
     ) AS t;

UPDATE ok_features_values fv
    JOIN temp_positions tp ON fv.id = tp.feature_value_id
    SET fv.position = tp.new_position;

DROP TEMPORARY TABLE temp_positions;

UPDATE `ok_lang_features_values` `lfv` INNER JOIN `ok_features_values` `fv` ON `lfv`.`feature_value_id` = `fv`.`id` SET `lfv`.`position` = `fv`.`position`;

ALTER TABLE `ok_lang_features_values` DROP INDEX `translit_feature_id_lang_id`, ADD UNIQUE `lang_id_feature_value_id_translit` (`lang_id`, `feature_value_id`, `translit`) USING BTREE;

ALTER TABLE `ok_managers` CHANGE `permissions` `permissions` TEXT NULL DEFAULT NULL;

ALTER TABLE `ok_okaycms__banners` CHANGE `categories` `categories` VARCHAR (1024) NOT NULL DEFAULT '', CHANGE `pages` `pages` VARCHAR (1024) NOT NULL DEFAULT '', CHANGE `brands` `brands` VARCHAR (1024) NOT NULL DEFAULT '';

INSERT INTO `ok_settings` (`param`, `value`) VALUES ('sort_feature_values_individually_each_lang', '1');