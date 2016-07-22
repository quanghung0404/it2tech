CREATE TABLE IF NOT EXISTS `#__jsn_tplframework_megamenu` (
  `megamenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `style_id` int(11) DEFAULT NULL,
  `language_code` varchar(250) DEFAULT NULL,
  `menu_type` varchar(250) DEFAULT NULL,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `params` mediumtext,
   PRIMARY KEY (`megamenu_id`)
) DEFAULT CHARSET=utf8;