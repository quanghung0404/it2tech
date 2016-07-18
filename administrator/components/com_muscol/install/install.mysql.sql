CREATE TABLE IF NOT EXISTS `#__muscol_albums` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `artist_id` int(12) NOT NULL,
  `year` year(4) NOT NULL,
  `month` int(2) NOT NULL default '0',
  `edition_year` year(4) NOT NULL,
  `edition_month` int(2) NOT NULL,
  `edition_country` varchar(127) NOT NULL,
  `edition_details` varchar(255) NOT NULL,
  `genre_id` int(3) NOT NULL,
  `catalog_number` varchar(31) NOT NULL,
  `label` varchar(127) NOT NULL,
  `length` varchar(9) NOT NULL,
  `format_id` int(6) NOT NULL,
  `image` varchar(255) NOT NULL default 'no_image.jpg',
  `ndisc` int(11) NOT NULL default '1',
  `types` varchar(31) NOT NULL,
  `review` mediumtext NOT NULL,
  `name2` varchar(255) NOT NULL,
  `artist2` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `subartist` varchar(255) NOT NULL,
  `fisic` varchar(4) NOT NULL default 'si',
  `points` tinyint(1) NOT NULL default '0',
  `added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime NOT NULL,
  `price` decimal(6,2) NOT NULL default '0.00',
  `tags` varchar(63) NOT NULL,
  `part_of_set` int(6) NOT NULL default '0',
  `show_separately` varchar(1) NOT NULL default 'Y',
  `params` mediumtext NOT NULL,
  `keywords` varchar(2048) NOT NULL,
  `user_id` int(6) NOT NULL,
  `hits` INT( 11 ) NOT NULL ,
  `buy_link` VARCHAR( 255 ) NOT NULL ,
  `album_file` VARCHAR( 255 ) NOT NULL ,
  `metakeywords` text NOT NULL,
  `metadescription` text NOT NULL,
  `external_type` varchar(255) NOT NULL,
  `external_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `artist_id` (`artist_id`),
  KEY `genre_id` (`genre_id`),
  KEY `user_id` (`user_id`),
  KEY `format_id` (`format_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_artists` (
  `id` int(11) NOT NULL auto_increment,
  `artist_name` varchar(255) NOT NULL,
  `image` varchar(127) NOT NULL,
  `review` mediumtext NOT NULL,
  `letter` varchar(1) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `related` varchar(63) NOT NULL,
  `keywords` varchar(512) NOT NULL,
  `added` datetime NOT NULL,
  `hits` INT( 11 ) NOT NULL ,
  `country` VARCHAR( 255 ) NOT NULL ,
  `picture` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `metakeywords` text NOT NULL,
  `metadescription` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `years_active` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `tags` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `genre_id` (`genre_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_comments` (
  `id` int(6) NOT NULL auto_increment,
  `album_id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `comment` mediumtext NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `comment_type` VARCHAR( 255 ) NOT NULL ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_format` (
  `id` int(6) NOT NULL auto_increment,
  `format_name` varchar(255) NOT NULL,
  `display_group` int(6) NOT NULL default '0',
  `order_num` int(6) NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_genres` (
  `id` int(6) NOT NULL auto_increment,
  `genre_name` varchar(255) NOT NULL,
  `parents` varchar(63) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_ratings` (
  `id` int(6) NOT NULL auto_increment,
  `album_id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `points` int(2) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_songs` (
  `id` int(6) NOT NULL auto_increment,
  `album_id` int(11) NOT NULL,
  `num` tinyint(3) NOT NULL,
  `disc_num` tinyint(2) NOT NULL,
  `length` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lyrics` text NOT NULL,
  `artist_id` int(11) NOT NULL,
  `composer_id` int(6) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `extension` varchar(5) NOT NULL,
  `added` datetime NOT NULL,
  `review` text NOT NULL,
  `songwriters` tinytext NOT NULL,
  `chords` text NOT NULL,
  `genre_id` int(3) NOT NULL,
  `hits` INT( 11 ) NOT NULL ,
  `buy_link` VARCHAR( 255 ) NOT NULL ,
  `video` VARCHAR( 255 ) NOT NULL ,
  `position` varchar(6) NOT NULL,
  `downloaded` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tags` varchar(255) NOT NULL,
  `external_type` varchar(255) NOT NULL,
  `external_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `artist_id` (`artist_id`),
  KEY `album_id` (`album_id`),
  KEY `genre_id` (`genre_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_tags` (
  `id` int(6) NOT NULL auto_increment,
  `tag_name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_type` (
  `id` int(6) NOT NULL auto_increment,
  `type_name` varchar(127) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_playlists` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `songs` varchar(511) NOT NULL,
  `types` varchar(511) NOT NULL,
  `public` int(2) NOT NULL default '1',
  `added` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `#__muscol_statistics` (
  `id` int(11) NOT NULL auto_increment,
  `type` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `date_event` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ip` varchar(31) NOT NULL,
  `valuestring` varchar(255) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `type` (`type`,`reference_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

INSERT INTO `#__muscol_artists` (`artist_name`, `letter`, `class_name`, `keywords`) VALUES
('Sample artist', 'S', 'Sample artist', ' Sample artist ');

INSERT INTO `#__muscol_format` (`format_name`, `display_group`, `order_num`, `icon`) VALUES
('CD', 0, 1, 'cd.png');

INSERT INTO `#__muscol_genres` (`genre_name`, `parents`) VALUES
('Rock', '0');