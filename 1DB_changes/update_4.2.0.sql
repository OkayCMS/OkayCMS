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