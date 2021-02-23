DROP TABLE IF EXISTS `s_import_log`;
CREATE TABLE IF NOT EXISTS `s_import_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `status` varchar(8) NOT NULL DEFAULT '',
    `product_name` varchar(255) NOT NULL DEFAULT '',
    `variant_name` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`),
    KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;