INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('zohocrm.token', '');

CREATE TABLE IF NOT EXISTS `#__rsform_zohocrm` (
  `form_id` int(11) NOT NULL,
  `zh_wf_trigger` tinyint(1) NOT NULL,
  `zh_duplicate_check` tinyint(1) NOT NULL,
  `zh_is_approval` tinyint(1) NOT NULL,
  `zh_format` tinyint(1) NOT NULL,
  `zh_merge_vars` text NOT NULL,
  `zh_debug` tinyint(1) NOT NULL,
  `zh_published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`form_id`)
) DEFAULT CHARSET=utf8;