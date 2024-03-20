ALTER TABLE `ok_categories`  ADD `on_main` INT(2) NOT NULL DEFAULT '0'  AFTER `visible`;
INSERT INTO `ok_settings` (`param`, `value`) VALUES
 ('open_ai_temperature', '1'),
 ('open_ai_presence_penalty', '0'),
 ('open_ai_frequency_penalty', '0'),
 ('open_ai_max_tokens', '4096');

INSERT INTO `ok_settings_lang` (`lang_id`, `param`, `value`) VALUES
 (3, 'ai_system_message', 'Ти контент менеджер. Пиши тільки метадані, які привертатимуть увагу користувачів. Використовуй emoji. Розписуй по максимуму все в деталях'),
 (2, 'ai_system_message', 'You are a content manager. Write only metadata that will attract the attention of users. Use emoji. Paint everything in detail as much as possible'),
 (1, 'ai_system_message', 'Ты контент менеджер. Пиши только метаданные, которые будут привлекать внимание пользователей. Используй emoji. Расписывай по максимуму все в деталях');
