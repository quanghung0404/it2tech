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

// Require main framework for EasyBlog
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

// Looks like we need to manually include the router
require_once(__DIR__ . '/router.php');

// Process ajax calls
EB::ajax()->process();

// Start profiling
EB::profiler()->start();

// Load extension languages
EB::loadLanguages();

// Include the main controller
require_once(dirname(__FILE__) . '/controllers/controller.php');

// Execute services
EB::loadServices();

// Get controller name if specified
$app = JFactory::getApplication();
$controllerName	= $app->input->get('controller', 'easyblog', 'cmd');

// Create controller
$controller = JControllerLegacy::getInstance('easyblog');
$task = $app->input->get('task');

$controller->execute($task);
$controller->redirect();
