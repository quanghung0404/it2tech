<?php
/**
 * Main Admin file
 *
 * @package         Snippets
 * @version         4.1.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_snippets'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_snippets');

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$msg = JText::_('SNP_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('SNP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_SNIPPETS'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');

	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnframework = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnframework->name))
{
	$msg = JText::_('SNP_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('SNP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_SNIPPETS'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
NNFrameworkFunctions::loadLanguage('plg_system_nnframework');

// Dependency
require_once JPATH_PLUGINS . '/system/nnframework/fields/dependency.php';
NNFieldDependency::setMessage('/plugins/editors-xtd/snippets/snippets.php', 'SNP_THE_EDITOR_BUTTON_PLUGIN');
NNFieldDependency::setMessage('/plugins/system/snippets/snippets.php', 'SNP_THE_SYSTEM_PLUGIN');

$controller = JControllerLegacy::getInstance('Snippets');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
