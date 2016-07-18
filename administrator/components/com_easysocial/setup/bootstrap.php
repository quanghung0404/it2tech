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

$app = JFactory::getApplication();
$input = $app->input;

// Ensure that the Joomla sections don't appear.
$input->set('tmpl', 'component');

// Determines if the current mode is re-install
$reinstall = $input->get('reinstall', false, 'bool') || $input->get('install', false, 'bool');

// If the mode is update, we need to get the latest version
$update = $input->get('launchInstaller', false, 'bool') || $input->get('update', false, 'bool');

// Determines if we are now in developer mode.
$developer = $input->get('developer', false, 'bool');

if ($developer) {
	$session = JFactory::getSession();
	$session->set('easysocial.developer', true);
}

############################################################
#### Constants
############################################################
define('ES_PACKAGES', dirname( __FILE__ ) . '/packages' );
define('ES_CONFIG', dirname( __FILE__ ) . '/config' );
define('ES_THEMES', dirname( __FILE__ ) . '/themes' );
define('ES_LIB', dirname( __FILE__ ) . '/libraries' );
define('ES_CONTROLLERS', dirname( __FILE__ ) . '/controllers' );
define('ES_SERVER', 'http://stackideas.com' );
define('ES_VERIFIER', 'http://stackideas.com/updater/verify' );
define('ES_MANIFEST', 'http://stackideas.com/updater/manifests/easysocial' );
define('ES_TMP', dirname(__FILE__) . '/tmp');
define('ES_BETA', false);
define('ES_SETUP_URL', rtrim(JURI::root(), '/') . '/administrator/components/com_easysocial/setup');

############################################################
#### Process ajax calls
############################################################
if ($input->get('ajax', false, 'bool')) {

	$controller = $input->get('controller', '', 'cmd');
	$task = $input->get('task', '', 'cmd');

	$controllerFile = ES_CONTROLLERS . '/' . strtolower( $controller ) . '.php';

	require_once($controllerFile);

	$controllerName = 'EasySocialController' . ucfirst( $controller );
	$controller = new $controllerName();

	return $controller->$task();
}

############################################################
#### Process controller
############################################################
$controller = $input->get('controller', '', 'cmd');

if (!empty($controller)) {
	$controllerFile = ES_CONTROLLERS . '/' . strtolower($controller) . '.php';

	require_once($controllerFile);

	$controllerName = 'EasySocialController' . ucfirst( $controller );
	$controller = new $controllerName();
	return $controller->execute();
}

############################################################
#### Initialization
############################################################
$contents = JFile::read(ES_CONFIG . '/installation.json');
$steps = json_decode($contents);

############################################################
#### Workflow
############################################################
$active = $input->get('active', 0, 'int');

if ($active == 0) {
	$active = 1;
	$stepIndex = 0;
} else {
	$active += 1;
	$stepIndex = $active - 1;
}

if ($active > count($steps)) {
	$active = 'complete';
	$activeStep = new stdClass();

	$activeStep->title = JText::_( 'COM_EASYSOCIAL_INSTALLATION_COMPLETED' );
	$activeStep->template = 'complete';

	// Assign class names to the step items.
	if ($steps) {
		foreach ($steps as $step) {
			$step->className = ' current done';
		}
	}
} else {
	// Get the active step object.
	$activeStep = $steps[$stepIndex];

	// Assign class names to the step items.
	foreach ($steps as $step) {
		$step->className = $step->index == $active || $step->index < $active ? ' current' : '';
		$step->className .= $step->index < $active ? ' done' : '';
	}
}

require(ES_THEMES . '/default.php');
