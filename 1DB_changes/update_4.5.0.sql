alter table ok_categories
    add has_products int default 0 null;

alter table ok_products
    add min_price decimal(14,2) default 0.0 not null;

alter table ok_products
    add max_price decimal(14,2) default 0.0 not null;

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

UPDATE ok_products p
    LEFT JOIN (
        SELECT
            v.product_id,
            floor(min(IF(v.currency_id=0 OR c.id is null, v.price, v.price*c.rate_to/c.rate_from))) AS min_price,
            ceil(max(IF(v.currency_id=0 OR c.id is null, v.price, v.price*c.rate_to/c.rate_from))) AS max_price
        FROM ok_products p
        LEFT JOIN ok_variants v ON v.product_id = p.id
        LEFT JOIN ok_currencies c on c.id = v.currency_id
        GROUP BY p.id
    ) AS prices ON prices.product_id = p.id
    SET p.min_price = prices.min_price,
    p.max_price = prices.max_price
WHERE 1
;