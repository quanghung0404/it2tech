<?php
/**
 * @package         Advanced Module Manager
 * @version         5.3.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tabstate');

if (!JFactory::getUser()->authorise('core.manage', 'com_modules'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_modules', JPATH_ADMINISTRATOR);
NNFrameworkFunctions::loadLanguage('com_advancedmodules');

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');

	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnframework = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnframework->name))
{
	$msg = JText::_('AMM_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('AMM_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDMODULES'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
NNFrameworkFunctions::loadLanguage('plg_system_nnframework');

$controller = JControllerLegacy::getInstance('AdvancedModules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
