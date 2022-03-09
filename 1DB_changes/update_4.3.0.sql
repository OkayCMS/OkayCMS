DELETE FROM ok_lang_pages WHERE page_id IN (SELECT id FROM ok_pages WHERE url IN ('bestsellers', 'discounted'));
DELETE FROM ok_pages WHERE url IN ('bestsellers', 'discounted');
DELETE FROM ok_currencies WHERE code = 'RUB';
DELETE FROM ok_lang_currencies WHERE sign = 'руб';
UPDATE ok_currencies t SET t.position = 1 WHERE t.code = 'UAH';

INSERT INTO ok_settings (param, value) VALUES('features_max_count_products', 10);