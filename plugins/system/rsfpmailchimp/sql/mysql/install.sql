INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('mailchimp.key', '');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('mailchimp.secure', '0');

CREATE TABLE IF NOT EXISTS `#__rsform_mailchimp` (
  `form_id` int(11) NOT NULL,
  `mc_list_id` varchar(16) NOT NULL,
  `mc_action` tinyint(1) NOT NULL,
  `mc_action_field` varchar(255) NOT NULL,
  `mc_merge_vars` text NOT NULL,
  `mc_interest_groups` text NOT NULL,
  `mc_email_type` varchar(32) NOT NULL,
  `mc_email_type_field` varchar(255) NOT NULL,
  `mc_double_optin` tinyint(1) NOT NULL,
  `mc_update_existing` tinyint(1) NOT NULL,
  `mc_replace_interests` tinyint(1) NOT NULL,
  `mc_send_welcome` tinyint(1) NOT NULL,
  `mc_delete_member` tinyint(1) NOT NULL,
  `mc_send_goodbye` tinyint(1) NOT NULL,
  `mc_send_notify` tinyint(1) NOT NULL,
  `mc_published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;