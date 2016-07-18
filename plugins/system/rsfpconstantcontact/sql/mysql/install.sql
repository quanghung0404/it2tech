INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES('cc.api', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES('cc.token', '');

CREATE TABLE IF NOT EXISTS `#__rsform_constantcontact` (
  `form_id` int(11) NOT NULL,
  `cc_list_id` varchar(255) NOT NULL,
  `cc_action` tinyint(1) NOT NULL,
  `cc_action_field` varchar(255) NOT NULL,
  `cc_merge_vars` text NOT NULL,
  `cc_update` tinyint(1) NOT NULL,
  `cc_delete_member` tinyint(1) NOT NULL,
  `cc_published` tinyint(1) NOT NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;