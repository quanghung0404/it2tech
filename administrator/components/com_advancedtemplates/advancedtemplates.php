<?php
/**
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
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

$app  = JFactory::getApplication();
$user = JFactory::getUser();

// ACL for hardening the access to the template manager.
if (!$user->authorise('core.manage', 'com_templates'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('com_templates', JPATH_ADMINISTRATOR);
NNFrameworkFunctions::loadLanguage('com_advancedtemplates');

jimport('joomla.filesystem.file');

// return if NoNumber Framework plugin is not installed
if (!JFile::exists(JPATH_PLUGINS . '/system/nnframework/nnframework.php'))
{
	$msg = JText::_('ATP_NONUMBER_FRAMEWORK_NOT_INSTALLED')
		. ' ' . JText::sprintf('ATP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDTEMPLATES'));
	JFactory::getApplication()->enqueueMessage($msg, 'error');

	return;
}

// give notice if NoNumber Framework plugin is not enabled
$nnep = JPluginHelper::getPlugin('system', 'nnframework');
if (!isset($nnep->name))
{
	$msg = JText::_('ATP_NONUMBER_FRAMEWORK_NOT_ENABLED')
		. ' ' . JText::sprintf('ATP_EXTENSION_CAN_NOT_FUNCTION', JText::_('COM_ADVANCEDTEMPLATES'));
	JFactory::getApplication()->enqueueMessage($msg, 'notice');
}

// load the NoNumber Framework language file
NNFrameworkFunctions::loadLanguage('plg_system_nnframework');

JLoader::register('AdvancedTemplatesHelper', __DIR__ . '/helpers/templates.php');

$controller = JControllerLegacy::getInstance('AdvancedTemplates');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
