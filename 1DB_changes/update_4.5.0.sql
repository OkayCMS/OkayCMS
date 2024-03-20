INSERT INTO `ok_settings` (`param`, `value`) VALUES ('email_for_module', '');
ALTER TABLE `ok_categories`  ADD `on_main` INT(2) NOT NULL DEFAULT '0'  AFTER `visible`;
