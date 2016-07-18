DROP TABLE IF EXISTS `#__jsn_mobilize_config`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_config` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__jsn_mobilize_config`
--
-- --------------------------------------------------------

--
-- Table structure for table `#__jsn_mobilize_design`
--

DROP TABLE IF EXISTS `#__jsn_mobilize_design`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_design` (
  `design_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`design_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `#__jsn_mobilize_design`
--
-- --------------------------------------------------------

--
-- Table structure for table `#__jsn_mobilize_messages`
--

DROP TABLE IF EXISTS `#__jsn_mobilize_messages`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_screen` varchar(150) DEFAULT NULL,
  `published` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT '0',
  PRIMARY KEY (`msg_id`),
  UNIQUE KEY `message` (`msg_screen`,`ordering`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__jsn_mobilize_os`
--

DROP TABLE IF EXISTS `#__jsn_mobilize_os`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_os` (
  `os_id` int(11) NOT NULL AUTO_INCREMENT,
  `os_value` varchar(255) NOT NULL,
  `os_type` varchar(50) NOT NULL,
  `os_title` varchar(255) NOT NULL,
  `os_order` int(11) NOT NULL,
  PRIMARY KEY (`os_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `#__jsn_mobilize_os`
--

INSERT INTO `#__jsn_mobilize_os` (`os_id`, `os_value`, `os_type`, `os_title`, `os_order`) VALUES
(1, '{"ios":["6","<"]}', 'ios', 'iOS 6.x and bellow', 1),
(2, '{"ios":["7"]}', 'ios', 'iOS 7.x', 2),
(3, '{"android":["2"]}', 'android', 'Android 2.2 - 2.3', 4),
(4, '{"android":["4"]}', 'android', 'Android 4.x', 5),
(5, '{"wmobilie":["6","<"]}', 'wmobilie', 'Windows Mobile 6.x and bellow', 6),
(6, '{"wmobilie":["7"]}', 'wmobilie', 'Windows Mobile 7.x', 7),
(7, '{"wmobilie":["8"]}', 'wmobilie', 'Windows Mobile 8.x', 8),
(8, '{"blackberry":["5","<"]}', 'blackberry', 'BlackBerry 5.x and bellow', 9),
(9, '{"blackberry":["6","7"]}', 'blackberry', 'BlackBerry 6x - 7x', 10),
(10, '{"blackberry":["10"]}', 'blackberry', 'BlackBerry 10x', 11),
(11, 'other', 'other', 'Other', 12),
(12, '{"ios":["8"]}', 'ios', 'iOS 8.x', 3);

-- --------------------------------------------------------

--
-- Table structure for table `#__jsn_mobilize_os_support`
--

DROP TABLE IF EXISTS `#__jsn_mobilize_os_support`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_os_support` (
  `support_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_id` int(11) NOT NULL,
  `os_id` int(11) NOT NULL,
  PRIMARY KEY (`support_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `#__jsn_mobilize_os_support`
--
-- --------------------------------------------------------

--
-- Table structure for table `#__jsn_mobilize_profiles`
--

DROP TABLE IF EXISTS `#__jsn_mobilize_profiles`;
CREATE TABLE IF NOT EXISTS `#__jsn_mobilize_profiles` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_title` varchar(255) NOT NULL,
  `profile_description` text NOT NULL,
  `profile_state` int(11) NOT NULL,
  `profile_minify` varchar(50) NOT NULL,
  `profile_optimize_images` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `profile_device` varchar(10) NOT NULL,
  PRIMARY KEY (`profile_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
