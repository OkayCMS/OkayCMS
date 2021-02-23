ALTER TABLE `ok_okaycms__banners`
ADD `show_all_products` tinyint(1) NULL DEFAULT '0' AFTER `show_all_pages`;

ALTER TABLE `ok_okaycms__banners`
ADD INDEX `show_all_products` (`show_all_products`);