<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: imageshow.php 6505 2011-05-31 11:01:31Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
// Set the directory separator define if necessary
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
require_once (JPATH_COMPONENT.DS.'controller.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'jsn_is_factory.php');
global $mainframe;
$mainframe  = JFactory::getApplication();
$controllerName = JRequest::getCmd( 'controller' );

if ($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$classname	= 'ImageShowController'.$controller;
$controller	= new $classname();
$controller->execute( JRequest::getVar( 'task' ) );
$controller->redirect();