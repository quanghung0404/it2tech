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

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

// Include main engine
$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once($engine);

// Ensure all scripts are loaded
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$app = JFactory::getApplication();
$option = $app->input->get('option', '', 'cmd');
$view = $app->input->get('view', '', 'cmd');
$layout = $app->input->get('layout', '', 'cmd');

if ($option != 'com_easyblog' || $view != 'entry') {
	return;
}

// Get the current post id and author name
$id = $app->input->get('id', '', 'int');
$post = EB::post($id);
$blogger = $post->getAuthor();

// Get the bio character limit
$biolimit = $params->get('biolimit', 100);

require(JModuleHelper::getLayoutPath('mod_easyblogbio'));
