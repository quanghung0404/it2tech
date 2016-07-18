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

// Include main engine
$file 	= JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

jimport( 'joomla.filesystem.file' );

if( !JFile::exists( $file ) )
{
	return;
}

// If user is logged in, skip this
if( JFactory::getUser()->id )
{
	return;
}

// Include the engine file.
require_once( $file );

FD::language()->loadSite();

// Check if Foundry exists
if( !FD::exists() )
{
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

$my 		= FD::user();

// Load our own helper file.
require_once( dirname( __FILE__ ) . '/helper.php' );

// Load up the module engine
$modules 	= FD::modules( 'mod_easysocial_register' );

// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency( 'css' , 'javascript' );

$modules->loadScript('script.js');

// Get the layout to use.
$layout 	= $params->get( 'layout' , 'default' );
$suffix 	= $params->get( 'suffix' , '' );
$config 	= FD::config();

// Get the profile id
$profileId 	= $params->get( 'profile_id' );

$registerType = $params->get('register_type', 'quick');

// If there's no profile id, then we automatically assign the default profile id
if (empty($profileId))
{
	$profileModel = FD::model('profiles');
	$defaultProfile = $profileModel->getDefaultProfile();
	$profileId = $defaultProfile->id;
}

$fieldsModel = FD::model('fields');

$options = array(
	'visible' => SOCIAL_PROFILES_VIEW_MINI_REGISTRATION,
	'profile_id' => $profileId
);

$fields = $fieldsModel->getCustomFields($options);

if (!empty($fields)) {
	FD::language()->loadAdmin();

	$fieldsLib		= FD::fields();

	$session		= JFactory::getSession();
	$registration	= FD::table('Registration');
	$registration->load( $session->getId() );

	$data			= $registration->getValues();

	$args = array(&$data, &$registration);

	$fieldsLib->trigger('onRegisterMini', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
}

// Get the splash image url.
$splashImage	= $params->get( 'splash_image_url' , JURI::root() . 'modules/mod_easysocial_register/images/splash.jpg' );

require( JModuleHelper::getLayoutPath( 'mod_easysocial_register' , $layout ) );
