<?php

/** 
 * @version		2.0.0
 * @package		muscol
 * @copyright	2010 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//new for Joomla 3.0
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

// Require the base controller
require_once (JPATH_COMPONENT.DS.'controller.php');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'helpers.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'route.php');
require_once(JPATH_COMPONENT.DS.'helpers'.DS.'icon.php');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'alphabets.php');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {

	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

switch($controller){
	case "albums":
		$prefix	= 'Albums';
		break;
	case "album":
		$prefix	= 'Albums';
		break;
	case "artists":
		$prefix	= 'Artists';
		break;
	case "artist":
		$prefix	= 'Artists';
		break;
	default:
		$prefix	= 'Artists';
		break;
}

// Create the controller
//$classname	= 'HellosController'.$controller;
$classname	= $prefix.'Controller'.$controller;

$controller	= new $classname( );

$document = JFactory::getDocument();
$uri = JFactory::getURI();

$document->addStyleSheet("media/jui/css/bootstrap.min.css");
JHtml::_('bootstrap.framework');

$document->addScriptDeclaration("jQuery(document).ready(function () {
    jQuery(\"[rel=tooltip]\").tooltip();
  });");
  


// Perform the Request task
$controller->execute( JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();

?>
