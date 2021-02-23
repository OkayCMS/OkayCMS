-- 0
DROP TABLE IF EXISTS `ok_lang_okaycms__faq__faq`;
CREATE TABLE `ok_lang_okaycms__faq__faq` (
  `faq_id` int(11) DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `ok_okaycms__faq__faq`;
CREATE TABLE `ok_okaycms__faq__faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text,
  `visible` tinyint(1) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;