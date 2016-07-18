<?php
/**
 * @version		$Id: extension.php 2065 2012-10-25 11:47:56Z lefteris.kavadas $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSHelperLegacy
{

	public static function setup()
	{
		// Define the
		if (!defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}
		// Load the base classes
		//JLoader::register('FPSSTable', JPATH_ADMINISTRATOR.'/components/com_fpss/tables/table.php');
		JLoader::register('FPSSController', JPATH_ADMINISTRATOR.'/components/com_fpss/controllers/controller.php');
		JLoader::register('FPSSModel', JPATH_ADMINISTRATOR.'/components/com_fpss/models/model.php');
		FPSSModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fpss/models', 'FPSSModel');
		JLoader::register('FPSSView', JPATH_ADMINISTRATOR.'/components/com_fpss/views/view.php');
	}

}
