<?php
/**
 * @version     3.1.x
 * @package     Simple Image Gallery Pro
 * @author      JoomlaWorks - http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license     http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

// Get application
$application = JFactory::getApplication();

// Check user is logged in
$user = JFactory::getUser();
if ($user->guest)
{
	JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
	$application->redirect('index.php');
}

// Load admin language
$language = JFactory::getLanguage();
$language->load('com_sigpro', JPATH_ADMINISTRATOR);

// Load the helper and initialize
JLoader::register('SigProHelper', JPATH_COMPONENT_ADMINISTRATOR.'/helper.php');
SigProHelper::initialize();

// Add model path
if (version_compare(JVERSION, '3.0', 'ge'))
{
	JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

}
else
{
	JModel::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

}

// Check some variables for security reasons
$view = JRequest::getCmd('view', 'galleries');
{
	if ($view == 'media' || $view == 'info' || $view == 'settings')
	{
		JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		$application->redirect('index.php');
	}
}
$type = JRequest::getCmd('type');
if ($type != 'site' && $type != 'k2')
{
	JRequest::setVar('type', 'site');
}

// K2 galleries permissions check
if ($application->isSite() && $type == 'k2')
{
	$canAccess = true;
	$task = JRequest::getWord('task');
	$folder = ($view == 'galleries' && $task == 'create') ? JRequest::getInt('newFolder') : JRequest::getInt('folder');
	require_once JPATH_SITE.'/components/com_k2/helpers/permissions.php';
	K2HelperPermissions::setPermissions();
	JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/tables');
	$row = JTable::getInstance('K2Item', 'Table');
	$row->load($folder);

	$isNew = is_null($row->id);

	if ($view == 'galleries')
	{
		if ($task == 'create')
		{
			if ($isNew && !K2HelperPermissions::canAddItem())
			{
				$canAccess = false;
			}
			if (!$isNew && !K2HelperPermissions::canEditItem($row->created_by, $row->catid))
			{
				$canAccess = false;
			}
		}
		else
		{
			$canAccess = false;
		}
	}
	else if ($view == 'gallery')
	{
		if ($isNew && !K2HelperPermissions::canAddItem())
		{
			$canAccess = false;
		}

		if (!$isNew && !K2HelperPermissions::canEditItem($row->created_by, $row->catid))
		{
			$canAccess = false;
		}
	}

	if (!$canAccess)
	{
		JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
		$application->redirect('index.php');
	}

}

// Bootstrap
if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$view.'.php'))
{
	JRequest::setVar('view', $view);
	require_once JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.$view.'.php';
	$class = 'SigProController'.ucfirst($view);
	$controller = new $class();
	$controller->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.'/views');
	$controller->execute(JRequest::getWord('task'));
	$controller->redirect();
}
