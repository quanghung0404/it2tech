<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Test for installation requests.
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$app = JFactory::getApplication();
$input = $app->input;

$exitInstallation = $input->get('exitInstallation', false, 'bool');

// Check if there's a file initiated for installation
$file = JPATH_ROOT . '/tmp/easysocial.installation';

if ($exitInstallation) {
	if (JFile::exists($file)) {
		JFile::delete($file);

		return $app->redirect('index.php?option=com_easysocial');
	}
}

$launchInstaller = $input->get('launchInstaller', false, 'bool');

if ($launchInstaller) {
	// Determines if the installation is a new installation or old installation.
	$obj = new stdClass();
	$obj->new = false;
	$obj->step = 1;
	$obj->status = 'installing';

	$contents = json_encode($obj);

	if (!JFile::exists($file)) {
		JFile::write($file, $contents);
	}	
}

$active = $input->get('active', 0, 'int');

if (JFile::exists($file) || $active) {
    require_once(dirname(__FILE__) . '/setup/bootstrap.php');
    exit;
}

// Check if we need to synchronize the database columns
$sync = $input->get('sync', false, 'bool');

if ($sync) {
	$input->set('task', 'sync');
	$input->set('controller', 'easysocial');
}

// Engine is required anywhere EasySocial is used.
require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

// Check if Foundry exists
if (!FD::exists()) {
	FD::language()->loadSite();
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Toggle super mode
$superdev = $input->get('superdev', null, 'int');

if (isset($superdev)) {
	$super = (bool) $superdev;

	$config = FD::config();
	$config->set( 'general.super', $super );

	$jsonString = $config->toString();

	$configTable = FD::table('Config');

	if (!$configTable->load('site')) {
		$configTable->type = 'site';
	}

	$configTable->set('value', $jsonString);
	$configTable->store();

	echo 'Super developer mode: ' . (($super) ? 'ON' : 'OFF');
	return;
}

// Load language.
FD::language()->loadAdmin();

// Start collecting page objects.
FD::page()->start();

// @rule: Process AJAX calls
FD::ajax()->listen();

// Get the task
$task = $input->get('task', 'display', 'cmd');

// We treat the view as the controller. Load other controller if there is any.
$controller = $input->get('controller', '', 'word');

// We need the base controller
FD::import('admin:/controllers/controller');

if (!empty($controller)) {

	$controller = JString::strtolower($controller);

	$state = FD::import('admin:/controllers/' . $controller);

	if (!$state) {
		JError::raiseError(500, JText::sprintf('COM_EASYSOCIAL_INVALID_CONTROLLER', $controller));
	}
}

$class = 'EasySocialController' . JString::ucfirst($controller);

// Test if the object really exists in the current context
if (!class_exists($class)) {
	JError::raiseError(500, JText::sprintf('COM_EASYSOCIAL_INVALID_CONTROLLER_CLASS_ERROR', $class));
}

$controller	= new $class();

// Task's are methods of the controller. Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

// End page
ES::page()->end();