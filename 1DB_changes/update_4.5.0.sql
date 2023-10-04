alter table ok_categories
    add has_products int default 0 null;

UPDATE ok_categories c
    LEFT JOIN (
        SELECT pc.category_id, COUNT(DISTINCT p.id) > 0 AS has_products
        FROM  ok_products_categories pc
                  LEFT JOIN ok_products p ON p.id = pc.product_id
        WHERE p.visible
        GROUP BY pc.category_id
    ) AS products_count ON products_count.category_id = c.id
SET c.has_products = IFNULL(products_count.has_products, 0)
WHERE 1;

