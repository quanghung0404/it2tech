<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die;

if(!class_exists('VmView'))require(JPATH_VM_SITE.DS.'helpers'.DS.'vmview.php');

class VirtueMartViewRaw extends VmView
{

	function display($tpl = 'pdf')
	{
		$type='raw';
		$this->assignRef('type', $type);
		$viewName = jRequest::getWord('view','productdetails');
		$class= 'VirtueMartView'.ucfirst($viewName);
		if(!class_exists($class)) require(JPATH_VM_SITE.DS.'views'.DS.$viewName.DS.'view.html.php');
		$view = new $class ;
	
		$view->display($tpl);
	}

}
