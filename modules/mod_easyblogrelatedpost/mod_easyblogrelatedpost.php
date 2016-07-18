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
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
    return;
}

// @task: Include module's helper file.
require_once($engine);
require_once(__DIR__ . '/helper.php');

// Get joomla's app
$app = JFactory::getApplication();

$view = $app->input->get('view');
$id = $app->input->get('id');

// We do not want to display anything other than the entry view.
if ($view != 'entry' || !$id) {
	return;
}

$config = EB::config();

if ($config->get('main_ratings')) {
    EB::init('module');
}

// Attach modules stylesheet
EB::stylesheet('module')->attach();

// @task: Load component's language file.
JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);
JFactory::getLanguage()->load('mod_easyblogrelatedpost', JPATH_ROOT);

// Some custom properties that the user can define in the back end.
$count = $params->get('count', 5);
$posts = modRelatedPostHelper::getData($params, $id, $count);

$disabled = $params->get('enableratings') ? false : true;

// Get the photo layout option
$photoLayout = $params->get('photo_layout');
$photoSize = $params->get('photo_size', 'medium');

$photoAlignment = $params->get('alignment', 'center');
$photoAlignment = ($photoAlignment == 'default') ? 'center' : $photoAlignment;

require(JModuleHelper::getLayoutPath('mod_easyblogrelatedpost'));

