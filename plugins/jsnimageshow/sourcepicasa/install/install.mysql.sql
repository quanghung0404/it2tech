CREATE TABLE IF NOT EXISTS `#__imageshow_external_source_picasa` (
  `external_source_id` int(11) unsigned NOT NULL auto_increment,
  `external_source_profile_title` varchar(255) default NULL,
  `picasa_username` varchar(255) default '',
  `picasa_thumbnail_size` char(30) default '144',
  `picasa_image_size` char(30) default '1024',
  PRIMARY KEY  (`external_source_id`)
) DEFAULT CHARSET=utf8;
