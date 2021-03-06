<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once($engine);
require_once(__DIR__ . '/helper.php');

// Ensure that all script are loaded
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

JTable::addIncludePath(EBLOG_TABLES);

$config = EB::config();

$jCommentFile = JPATH_ROOT . '/components/com_jcomments/jcomments.php';

if ($config->get('comment_jcomments') && JFile::exists($jCommentFile)) {
	$comments = modEasyBlogLatestCommentHelper::getJComment($params);
} else {
	// Use default comments
	$comments = modEasyBlogLatestCommentHelper::getLatestComment($params);
}

$maxCharacter = $params->get('maxcommenttext', 100);

require(JModuleHelper::getLayoutPath('mod_easybloglatestcomment'));
