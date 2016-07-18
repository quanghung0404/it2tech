INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES('aki.key', '');

CREATE TABLE IF NOT EXISTS `#__rsform_akismet` (
  `form_id` int(11) NOT NULL,
  `aki_merge_vars` text NOT NULL,
  `aki_published` tinyint(1) NOT NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;