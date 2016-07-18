<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

// Require the base controller

require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'helpers.php');
require_once(JPATH_SITE.DS.'components'.DS.'com_muscol'.DS.'helpers'.DS.'alphabets.php');

require_once( JPATH_COMPONENT.DS.'controller.php' );

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {

	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$albums = false;
$artists = false;
$formats = false;
$genres = false;
$tags = false;
$types = false;
$comments = false;
$ratings = false;
$playlists = false;
$songs = false;
$playlists = false;


switch($controller){
	case "albums": 
	case "album":
		$albums = true;
		$prefix	= 'Albums';
		break;
		
	case "artists": 
	case "artist":
		
		$artists = true;
		$prefix	= 'Artists';
		break;
		
	case "formats": 
	case "format":
		
		$formats = true;
		$prefix	= 'Formats';
		break;
		
	case "genres": 
	case "genre":
		$genres = true;
		$prefix	= 'Genres';
		break;

	case "tags": 
	case "tag":
		$tags = true;
		$prefix	= 'Tags';
		break;

	case "types": 
	case "type":
		$types = true;
		$prefix	= 'Types';
		break;
		
	case "comments": 
	case "comment":
		$comments = true;
		$prefix	= 'Comments';
		break;
		
	case "ratings": 
	case "rating":
		$ratings = true;
		$prefix	= 'Ratings';
		break;
	
	case "playlists": 
	case "playlist":
		$playlists = true;
		$prefix	= 'Playlists';
		break;
		
	case "song": 
	case "songs": 
		$songs = true;
		$prefix	= 'Songs';
		break;

	case "element":
		$prefix	= 'Element';
		break;
	default:
		$albums = true;
		$prefix	= 'Albums';
		break;
}
		
		
JSubMenuHelper::addEntry(JText::_('Albums'), 'index.php?option=com_muscol', $albums );
JSubMenuHelper::addEntry(JText::_('Artists'), 'index.php?option=com_muscol&controller=artists', $artists);
JSubMenuHelper::addEntry(JText::_('Songs'), 'index.php?option=com_muscol&controller=songs', $songs);
JSubMenuHelper::addEntry(JText::_('Formats'), 'index.php?option=com_muscol&controller=formats', $formats);
JSubMenuHelper::addEntry(JText::_('Genres'), 'index.php?option=com_muscol&controller=genres', $genres);
JSubMenuHelper::addEntry(JText::_('Types'), 'index.php?option=com_muscol&controller=types', $types);
JSubMenuHelper::addEntry(JText::_('Tags'), 'index.php?option=com_muscol&controller=tags', $tags);
JSubMenuHelper::addEntry(JText::_('Comments'), 'index.php?option=com_muscol&controller=comments', $comments);
JSubMenuHelper::addEntry(JText::_('Ratings'), 'index.php?option=com_muscol&controller=ratings', $ratings);
JSubMenuHelper::addEntry(JText::_('Playlists'), 'index.php?option=com_muscol&controller=playlists', $playlists);

$lang = JFactory::getLanguage();
$lang->load('com_muscol', JPATH_SITE);

$params =JComponentHelper::getParams( 'com_muscol' );
$mainframe = JFactory::getApplication();

//for ID3
if($params->get('id3')){
	if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()) {
		$mainframe->enqueueMessage('magic_quotes_runtime is enabled, getID3 will not run.', 'warning');
		
	}
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
		$mainframe->enqueueMessage('magic_quotes_gpc is enabled, getID3 will not run.', 'warning');
		
	}
}
				
// Create the controller

$classname	= $prefix.'Controller'.$controller;

$controller	= new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();