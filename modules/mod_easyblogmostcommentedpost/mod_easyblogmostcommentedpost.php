<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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

// Include the syndicate functions only once
require_once(__DIR__ . '/helper.php');
require_once($engine);

$config = EB::config();

if ($config->get('main_ratings')) {
    EB::init('module');
}

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$config = EB::config();
$posts = modEasyBlogMostCommentedPostHelper::getMostCommentedPost($params);
$textcount = $params->get('textcount', 150);
$layout = $params->get('module_layout');
$columnCount = $params->get('column');

$disabled = $params->get('enableratings') ? false : true;

if(!$posts) {
    return;
}

require(JModuleHelper::getLayoutPath('mod_easyblogmostcommentedpost'));
