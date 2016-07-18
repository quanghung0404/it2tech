CREATE TABLE IF NOT EXISTS `#__rsform_vtiger` (
  `form_id` int(11) NOT NULL,
  `vt_accesskey` varchar(255) NOT NULL,
  `vt_username` varchar(255) NOT NULL,
  `vt_hostname` varchar(255) NOT NULL,
  `vt_fields` mediumtext NOT NULL,
  `vt_published` tinyint(1) NOT NULL,
  PRIMARY KEY  (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;