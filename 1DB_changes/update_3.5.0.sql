CREATE TABLE `ok_order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `new_status_id` int(11) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ok_variants`
ADD INDEX `sku` (`sku`(100)),
DROP INDEX `sku`;

ALTER TABLE `ok_orders`
ADD COLUMN `formatted_phone` VARCHAR(20),
ADD INDEX `formatted_phone` (`formatted_phone`);

-- проставим текущим заказам отформатированный телефон
DELIMITER $$
 CREATE FUNCTION `regex_replace`(pattern VARCHAR(1000),replacement VARCHAR(1000),original VARCHAR(1000))
RETURNS VARCHAR(1000)
 DETERMINISTIC
 BEGIN 
  DECLARE temp VARCHAR(1000); 
  DECLARE ch VARCHAR(1); 
  DECLARE i INT;
  SET i = 1;
  SET temp = '';
  IF original REGEXP pattern THEN 
   loop_label: LOOP 
    IF i>CHAR_LENGTH(original) THEN
     LEAVE loop_label;  
    END IF;
    SET ch = SUBSTRING(original,i,1);
    IF NOT ch REGEXP pattern THEN
     SET temp = CONCAT(temp,ch);

   ELSE
     SET temp = CONCAT(temp,replacement);
    END IF;
    SET i=i+1;
   END LOOP;
  ELSE
   SET temp = original;
  END IF;
  RETURN temp;
 END$$
 DELIMITER ;
UPDATE `ok_orders` SET `formatted_phone` = regex_replace('[^0-9]', '', phone);
DROP FUNCTION `regex_replace`;

ALTER TABLE `ok_orders`
ADD `referer_channel` enum('email','search','social','referral','unknown') COLLATE 'utf8mb4_unicode_ci' NULL,
ADD `referer_source` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `referer_channel`;

ALTER TABLE `ok_okaycms__banners`
ADD `group_name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `name`;

UPDATE `ok_okaycms__banners` SET
`group_name` = concat('group_', `id`);

ALTER TABLE `ok_payment_methods`
ADD `novaposhta_cost__cash_on_delivery` TINYINT(1) COLLATE 'utf8mb4_unicode_ci' NULL;

INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`, `system`) VALUES
('OkayCMS',	'Hotline',	15,	1,	NULL,	'Description1CAdmin',	NULL);

ALTER TABLE `ok_products` ADD COLUMN `to__okaycms__hotline` TINYINT(1);
ALTER TABLE `ok_products` ADD COLUMN `not_to__okaycms__hotline` TINYINT(1);
ALTER TABLE `ok_brands` ADD COLUMN `to__okaycms__hotline` TINYINT(1);
ALTER TABLE `ok_categories` ADD COLUMN `to__okaycms__hotline` TINYINT(1);