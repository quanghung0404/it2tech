INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('getresponse.key', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('getresponse.usessl', '0');

CREATE TABLE IF NOT EXISTS `#__rsform_getresponse` (
  `form_id` int(11) NOT NULL,
	`enable_getresponse` varchar(16) NOT NULL DEFAULT 0,
	`vars` text NOT NULL,
	`getresponse_list` varchar(255) NOT NULL,
	`getresponse_update` varchar(255) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;