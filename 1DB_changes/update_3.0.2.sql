-- 0
DELETE FROM `ok_currencies` WHERE id=3;
UPDATE `ok_currencies` SET `rate_from`=1.00, `rate_to`=65.00 WHERE id=1;
UPDATE `ok_currencies` SET `rate_from`=1.00, `rate_to`=3.30 WHERE id=4;
UPDATE `ok_comments` SET `name`='Андрей', `text`='Отличный товар. Приобрели около года назад. Ни разу не пожалели о покупке.' WHERE id=1;

-- 1
INSERT INTO `ok_modules` (`vendor`, `module_name`, `position`, `enabled`, `type`, `backend_main_controller`) VALUES
('OkayCMS', 'FAQ', '11', '1', null, 'FAQsAdmin');