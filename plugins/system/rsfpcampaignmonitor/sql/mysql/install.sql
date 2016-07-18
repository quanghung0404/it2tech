INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('campaignmonitor.api', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('campaignmonitor.client', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('campaignmonitor.usessl', '0');


CREATE TABLE IF NOT EXISTS `#__rsform_campaignmonitor` (
  `form_id` int(11) NOT NULL,
	`enable_campaignmonitor` varchar(16) NOT NULL DEFAULT 0,
	`vars` text NOT NULL,
	`campaignmonitor_list` varchar(255) NOT NULL,
	`campaignmonitor_update` varchar(255) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;