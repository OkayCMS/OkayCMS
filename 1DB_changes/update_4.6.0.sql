CREATE TABLE IF NOT EXISTS `ok_user_password_recovery_tokens` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `token_digest` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `consumed_at` DATETIME NULL DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `created_ip` VARCHAR(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_digest` (`token_digest`),
  KEY `user_id` (`user_id`),
  KEY `expires_at` (`expires_at`),
  KEY `consumed_at` (`consumed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ok_user_auth_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `action` VARCHAR(32) NOT NULL,
  `email_hash` CHAR(64) NOT NULL,
  `ip_hash` CHAR(64) NOT NULL,
  `attempts` INT(11) NOT NULL DEFAULT 0,
  `first_attempt` DATETIME NOT NULL,
  `last_attempt` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `action_email_ip` (`action`, `email_hash`, `ip_hash`),
  KEY `first_attempt` (`first_attempt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

UPDATE `ok_settings`
SET `value` = 'default'
WHERE `param` = 'social_share_theme'
  AND (`value` = '' OR `value` IN ('flat', 'classic', 'minima', 'plain'));

UPDATE `ok_settings`
SET `value` = 'a:4:{i:0;s:8:"copy_url";i:1;s:8:"facebook";i:2;s:9:"x-twitter";i:3;s:8:"linkedin";}'
WHERE `param` = 'sj_shares'
  AND `value` IN (
    'a:4:{i:0;s:7:"twitter";i:1;s:8:"facebook";i:2;s:9:"vkontakte";i:3;s:13:"odnoklassniki";}',
    'a:4:{i:0;s:7:"twitter";i:1;s:8:"facebook";i:2;s:10:"googleplus";i:3;s:8:"linkedin";}',
    'a:5:{i:0;s:7:"twitter";i:1;s:8:"facebook";i:2;s:10:"googleplus";i:3;s:8:"linkedin";i:4;s:13:"odnoklassniki";}'
  );

INSERT IGNORE INTO `ok_settings` (`param`, `value`) VALUES ('use_backorder_status', '0');
