CREATE TABLE IF NOT EXISTS `#__jsn_easyslider_config` (
	`name` varchar( 255 ) NOT NULL ,
	`value` text NOT NULL ,
	UNIQUE KEY `name` ( `name` )
)DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsn_easyslider_messages` (
	`msg_id` int(11) NOT NULL AUTO_INCREMENT,
	`msg_screen` varchar(150) DEFAULT NULL,
	`published` tinyint(1) DEFAULT '1',
	`ordering` int(11) DEFAULT '0',
	PRIMARY KEY (`msg_id`),
	UNIQUE KEY `message` (`msg_screen`,`ordering`)
)DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__jsn_easyslider_sliders` (
  `slider_id` int(11) NOT NULL AUTO_INCREMENT,
  `slider_title` varchar(255) NOT NULL,
  `slider_data` longtext DEFAULT '',
  `published` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  PRIMARY KEY (`slider_id`)
)DEFAULT CHARSET=utf8;
