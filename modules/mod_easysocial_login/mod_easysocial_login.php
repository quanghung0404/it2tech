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

// Include the engine file.
require_once( $file );

// Check if Foundry exists
if( !FD::exists() )
{
	FD::language()->loadSite();
	echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
	return;
}

$my 		= FD::user();

// If user is already logged in, there's no point to show the login form.
if( !$params->get( 'show_logout_button' , true ) && $my->id )
{
	return;
}

// Load our own helper file.
require_once( dirname( __FILE__ ) . '/helper.php' );

// Load up the module engine
$modules 	= FD::modules( 'mod_easysocial_login' );

// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency( 'css' , 'javascript' );

// Get the layout to use.
$modulestyle	= $params->get( 'modulestyle' , 'vertical' );
$suffix 		= $params->get( 'suffix' , '' );
$return 		= EasySocialModLoginHelper::getReturnURL( $params );
$config 		= FD::config();

// Facebook codes.
$facebook 	= FD::oauth( 'Facebook' );

require( JModuleHelper::getLayoutPath( 'mod_easysocial_login' , 'default_' . $modulestyle ) );
