CREATE TABLE IF NOT EXISTS `#__imageshow_source_profile` (
  `external_source_profile_id` int(11) NOT NULL auto_increment,
  `external_source_id` int(11) NOT NULL,
  PRIMARY KEY  (`external_source_profile_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_images` (
  `image_id` int(11) NOT NULL auto_increment,
  `showlist_id` int(11) NOT NULL,
  `image_extid` varchar(255) default NULL,
  `album_extid` varchar(255) default NULL,
  `image_small` varchar(255) default NULL,
  `image_medium` varchar(255) default NULL,
  `image_big` text,
  `image_title` varchar(255) default NULL,
  `image_description` text,
  `image_link` varchar(255) default NULL,
  `ordering` int(11) default '0',
  `custom_data` tinyint(1) default '0',
  `sync` tinyint(1) default '0',
  `image_size` varchar(25) default NULL,
  `exif_data` text,
  PRIMARY KEY  (`image_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_log` (
  `log_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `url` varchar(255) default NULL,
  `result` varchar(255) default NULL,
  `screen` varchar(100) default NULL,
  `action` varchar(50) default NULL,
  `time_created` datetime NULL default null,
  PRIMARY KEY  (`log_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_showcase` (
  `showcase_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `showcase_title` varchar(255) DEFAULT NULL,
  `published` tinyint(1) DEFAULT '0',
  `ordering` int(11) DEFAULT '0',
  `general_overall_width` char(30) DEFAULT NULL,
  `general_overall_height` char(30) DEFAULT NULL,
  `date_created` datetime DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`showcase_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_theme_profile` (
  `theme_id` int(11) NOT NULL default '0',
  `showcase_id` int(11) NOT NULL default '0',
  `theme_name` varchar(255) NOT NULL default '',
  `theme_style_name` varchar(255) NOT NULL default ''
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__imageshow_showlist` (
  `showlist_id` int(11) NOT NULL auto_increment,
  `showlist_title` varchar(255) default NULL,
  `published` tinyint(1) default '0',
  `override_title` tinyint(1) default '0',
  `override_description` tinyint(1) default '0',
  `override_link` tinyint(1) default '0',
  `ordering` int(11) default '0',
  `access` tinyint(3) default NULL,
  `hits` int(11) default NULL,
  `description` text,
  `showlist_link` text,
  `alter_autid` int(11) default '0',
  `date_create` datetime default NULL,
  `image_source_type` varchar(45) default '',
  `image_source_name` varchar(45) default '',
  `image_source_profile_id` int(11) default '0',
  `authorization_status` tinyint(1) default '0',
  `date_modified` datetime default '0000-00-00 00:00:00',
  `image_loading_order` char(30) DEFAULT NULL,
  `show_exif_data` char(100) DEFAULT '',
  PRIMARY KEY  (`showlist_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsn_imageshow_messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_screen` varchar(150) DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT '0',
  PRIMARY KEY (`msg_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsn_imageshow_config` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;