<?php
/**
 * @version		$Id: extension.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerExtension extends FPSSController
{

	function com_menus()
	{
		JRequest::setVar('tmpl', 'component');
		$model = $this->getModel('menus');
		$view = $this->getView('extension', 'html');
		$view->setModel($model);
		$view->com_menus();
	}

	function com_virtuemart()
	{
		JRequest::setVar('tmpl', 'component');
		if (JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'classes'.DS.'ps_database.php'))
		{
			$model = $this->getModel('virtuemart');
			define('FPSS_VM_VERSION', '1');
		}
		else
		{
			require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
			VmConfig::loadConfig();
			$model = &$this->getModel('virtuemart2');
			define('FPSS_VM_VERSION', '2');
		}
		$view = &$this->getView('extension', 'html');
		$view->setModel($model, true);
		$view->com_virtuemart();
	}

	function com_tienda()
	{
		JRequest::setVar('tmpl', 'component');
		$view = $this->getView('extension', 'html');
		$view->com_tienda();
	}

	function com_users()
	{
		JRequest::setVar('tmpl', 'component');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$this->setRedirect('index.php?option=com_users&view=users&layout=modal&tmpl=component&field=FPSS_created_by');
			return true;
		}
		$model = $this->getModel('users');
		$view = $this->getView('extension', 'html');
		$view->setModel($model, true);
		$view->com_users();
	}

}
