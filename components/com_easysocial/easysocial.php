<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Engine is required anywhere EasySocial is used.
require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

// Check if Foundry exists
if (!ES::exists()) {
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Start collecting page objects.
ES::page()->start();

// Get app
$app = JFactory::getApplication();
$input = $app->input;

// Load foundry configuration
$config = ES::config();

// Dispatch emails if necessary
if ($config->get('email.pageload')) {
	$cron = ES::cron();
	$cron->dispatchEmails();
}

// Process cron service here.
if ($input->get('cron', false, 'bool') == true) {
	$cron = ES::cron();
	$cron->execute();
	exit;
}

// Get the current view
$view = $input->get('view', '', 'word');

// Try to get the task from query string.
$task = $input->get('task', 'display', 'cmd');

// We treat the view as the controller. Load other controller if there is any.
$controller	= $input->get('controller', '', 'word');

// Listen for ajax calls.
ES::ajax()->listen();

// We need the base controller
ES::import('site:/controllers/controller');

if (!empty($controller)) {
	$controller	= JString::strtolower($controller);

	// Import controller
	$state = ES::import('site:/controllers/' . $controller);

	if (!$state) {
		JError::raiseError(500 , JText::sprintf('COM_EASYSOCIAL_INVALID_CONTROLLER', $controller));
	}
}

$class	= 'EasySocialController' . JString::ucfirst($controller);

// Test if the object really exists in the current context
if (!class_exists($class)) {
	JError::raiseError( 500 , JText::sprintf( 'COM_EASYSOCIAL_INVALID_CONTROLLER_CLASS_ERROR' , $class ) );
}

$controller = new $class();

// Task's are methods of the controller. Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

ES::page()->end();

