<?php
/**
 * Main Admin file
 *
 * @package         DB Replacer
 * @version         4.0.6PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_dbreplacer'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_dbreplacer');

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$msg = JText::_('DBR_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('DBR_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_DBREPLACER'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');

	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnep = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnep->name))
{
	$msg = JText::_('DBR_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('DBR_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_DBREPLACER'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
NNFrameworkFunctions::loadLanguage('plg_system_nnframework');

$controller = JControllerLegacy::getInstance('DBReplacer');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
