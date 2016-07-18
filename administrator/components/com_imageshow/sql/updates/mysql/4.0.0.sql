CREATE TABLE IF NOT EXISTS `#__imageshow_source_profile` (
  `external_source_profile_id` int(11) NOT NULL auto_increment,
  `external_source_id` int(11) NOT NULL,
  PRIMARY KEY  (`external_source_profile_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_theme_profile` (
  `theme_id` int(11) NOT NULL default '0',
  `showcase_id` int(11) NOT NULL default '0',
  `theme_name` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8;
