ALTER TABLE `ok_products` ADD `main_category_id` int(11) NULL;
ALTER TABLE `ok_products` ADD `main_image_id` int(11) NULL;

ALTER TABLE `ok_products` ADD INDEX `main_category_id` (`main_category_id`);
ALTER TABLE `ok_products` ADD INDEX `main_image_id` (`main_image_id`);


UPDATE ok_products p
LEFT JOIN ok_products_categories pc ON p.id = pc.product_id AND pc.position=(SELECT MIN(position) FROM ok_products_categories WHERE product_id=p.id LIMIT 1)
SET p.main_category_id = pc.category_id;

UPDATE ok_products p
LEFT JOIN ok_images i ON p.id = i.product_id AND i.position=(SELECT MIN(position) FROM ok_images WHERE product_id=p.id LIMIT 1)
SET p.main_image_id = i.id;