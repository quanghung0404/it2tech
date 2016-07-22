<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 *
 * JSNTPLFramework class
 *
 */

class JSNTPLFramework
{
	/**
	 * initialize
	 */

	public static function initial()
	{
		$app = JFactory::getApplication();

		if ($app->isSite())
		{
			$jsnUtils   			= JSNTplUtils::getInstance();
			$isInstalledSh404Sef 	= $jsnUtils->checkSH404SEF();
			
			if ($jsnUtils->isJoomla3())
			{
				// JViewLegacy
				JLoader::register('JViewLegacy', JSN_PATH_TPLFRAMEWORK . '/includes/core/j3x/jsntplviewlegacy.php');

				// JModuleHelper
				JLoader::register ('JModuleHelper', JSN_PATH_TPLFRAMEWORK . '/includes/core/j3x/jsntplmodulehelper.php');

				//Check if SH404Sef is installed or not. If yes, then do not load jsntplpagination.php
				if (!$isInstalledSh404Sef)
				{	
					// JPagination
					JLoader::register('JPagination', JSN_PATH_TPLFRAMEWORK . '/includes/core/j3x/jsntplpagination.php');
				}
			}
			else
			{
				// JView
				jimport('joomla.application.component.view');
				JLoader::register('JView', JSN_PATH_TPLFRAMEWORK . '/includes/core/j25/jsntplview.php');

				// JModuleHelper
				jimport('joomla.application.module.helper');
				JLoader::register('JModuleHelper', JSN_PATH_TPLFRAMEWORK . '/includes/core/j25/jsntplmodulehelper.php');
				
				//Check if SH404Sef is installed or not. If yes, then do not load jsntplpagination.php
				if (!$isInstalledSh404Sef)
				{
					// JPagination
					jimport('joomla.html.pagination');
					JLoader::register('JPagination', JSN_PATH_TPLFRAMEWORK . '/includes/core/j25/jsntplpagination.php');
				}
			}
		}
	}
}
