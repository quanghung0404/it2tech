RENAME TABLE `#__imageshow_messages` TO `#__jsn_imageshow_messages`;

DROP TABLE IF EXISTS `#__imageshow_parameters`;

CREATE TABLE IF NOT EXISTS `#__jsn_imageshow_config` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;