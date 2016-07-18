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

defined('_JEXEC') or die;

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
// Load admin main core language strings
NNFrameworkFunctions::loadLanguage('', JPATH_ADMINISTRATOR);

// Tell the browser not to cache this page.
JFactory::getApplication()->setHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT', true);

$controller = JControllerLegacy::getInstance('AdvancedModules');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
