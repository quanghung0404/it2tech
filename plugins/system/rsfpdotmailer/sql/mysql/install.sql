INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('dotmailer.username', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('dotmailer.password', '');

CREATE TABLE IF NOT EXISTS `#__rsform_dotmailer` (
  `form_id` int(11) NOT NULL,
  `dm_list_id` varchar(16) NOT NULL,
  `dm_action` varchar(32) NOT NULL,
  `dm_action_field` varchar(255) NOT NULL,
  `dm_merge_vars` text NOT NULL,
  `dm_audience_type` varchar(32) NOT NULL,
  `dm_audience_type_field` varchar(255) NOT NULL,
  `dm_optin_type` varchar(32) NOT NULL,
  `dm_optin_type_field` varchar(255) NOT NULL,
  `dm_email_type` varchar(32) NOT NULL,
  `dm_email_type_field` varchar(255) NOT NULL,
  `dm_email` varchar(255) NOT NULL,
  `dm_published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;