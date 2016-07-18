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

// no direct access
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
    return;
}
// @task: Include module's helper file.
require_once($engine);
require_once(__DIR__ . '/helper.php');

$config = EB::config();

if ($config->get('main_ratings')) {
    EB::init('module');
}

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$language = JFactory::getLanguage();
$language->load('mod_easyblogtopblogs', JPATH_ROOT);

$config = EB::config();
$result = modTopBlogsHelper::getPosts($params);
$disabled = $params->get('enableratings') ? false : true;
$layout = $params->get('module_layout');
$columnCount = $params->get('column');

// Get the photo layout option
$photoLayout = $params->get('photo_layout');
$photoSize = $params->get('photo_size', 'medium');

$photoAlignment = $params->get('alignment', 'center');
$photoAlignment = ($photoAlignment == 'default') ? 'center' : $photoAlignment;

if(!$result) {
    return;
}

require(JModuleHelper::getLayoutPath('mod_easyblogtopblogs'));
