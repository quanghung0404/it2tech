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

$path = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($path)) {
	return;
}

require_once($path);

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$app = JFactory::getApplication();

$view = $app->input->get('view', '', 'var');

// Return if this is not entry view
if ($view !== 'entry') {
	return;
}

JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
JFactory::getLanguage()->load('mod_easyblogpostmeta', JPATH_ROOT);

$id = $app->input->get('id', '', 'var');
$my = JFactory::getUser();
$config = EB::config();
$post = EB::post($id);
$categories = $post->getCategories();
$blogger = EB::user($post->created_by);

require(JModuleHelper::getLayoutPath('mod_easyblogpostmeta'));

