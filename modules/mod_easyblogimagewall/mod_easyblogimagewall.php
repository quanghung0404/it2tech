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

// @task: Include module's helper file.
require_once($engine);
require_once(__DIR__ . '/helper.php');

// Load language files from com_easyblog so that other views language strings would still work.
JFactory::getLanguage()->load('mod_easyblogimagewall', JPATH_ROOT);
JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$config = EB::config();
$posts = modImageWallHelper::getPosts($params);

require(JModuleHelper::getLayoutPath('mod_easyblogimagewall'));
