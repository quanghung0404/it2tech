<?php
/**
* @package    EasySocial
* @copyright  Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & Sh404sefFactory::getConfig();
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin( $lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// Load up foundry library
$file   = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';
require_once( $file );

// Include common methods
require_once( dirname( __FILE__ )  . '/common.php' );

FD::language()->loadSite();

$config 	= FD::config();

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');

if( !empty( $Itemid ) )
{
	shRemoveFromGETVarsList( 'Itemid' );
}

if( !empty( $limit ) )
{
	shRemoveFromGETVarsList( 'limit' );
}

if( !empty( $limitstart ) )
{
	shRemoveFromGETVarsList( 'limitstart' );
}

// start by inserting the menu element title (just an idea, this is not required at all)
$task			= isset($task) ? $task : null;
$Itemid			= isset($Itemid) ? $Itemid : null;

// Get the component prefix that is configured in SH404
$prefix 	= shGetComponentPrefix( $option );
$prefix 	= empty( $prefix ) ? getMenuTitle( $option , $task , $Itemid , null , $shLangName ) : $prefix;
$prefix 	= empty($prefix) || $prefix == '/' ? JText::_('COM_EASYSOCIAL_SH404_DEFAULT_ALIAS') : $prefix;

// Add the prefix
addPrefix($title, $prefix);

// If view is set, pass the url builders to the view
if( isset( $view ) )
{
	$adapter 	= dirname( __FILE__ ) . '/' . strtolower( $view ) . '.php';

	// Probably the view has some custom stuffs to perform.
	if( JFile::exists( $adapter ) )
	{
		include( $adapter );
	}
	else
	{
		// Add the view to the list of titles
		addView( $title , $view );

		// If layout is set, pass the url builders to the view
		if( isset( $layout ) )
		{
			addLayout( $title , $view , $layout );
		}
	}
}


// Interesting stuffs
// NEW: ask sh404sef to create a short URL for this SEF URL (pageId)
// shMustCreatePageId( 'set', true);


// ------------------  standard plugin finalize function - don't change ---------------------------
if ($dosef)
{
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, (isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), (isset($shLangName) ? @$shLangName : null) );
}
// ------------------  standard plugin finalize function - don't change ---------------------------

