INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('verticalresponse.key', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('verticalresponse.secret', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('verticalresponse.token', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('verticalresponse.usessl', '0');


CREATE TABLE IF NOT EXISTS `#__rsform_verticalresponse` (
  `form_id` int(11) NOT NULL,
	`enable_verticalresponse` varchar(16) NOT NULL DEFAULT 0,
	`vars` text NOT NULL,
	`verticalresponse_list` varchar(255) NOT NULL,
	`verticalresponse_update` varchar(255) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;