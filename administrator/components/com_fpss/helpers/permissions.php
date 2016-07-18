<?php
/**
 * @version		$Id: permissions.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSHelperPermissions
{

	public static function checkAccess()
	{
		// Set some variables
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		$id = JRequest::getInt('id');

		//Generic manage check
		if (!$user->authorise('core.manage', $option))
		{
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			$mainframe->redirect('index.php');
		}

		//Get the slide to check
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$slide = JTable::getInstance('Slide', 'FPSS');
		$slide->load($id);

		// Determine action for rest checks
		$action = false;
		if ($view == 'slides')
		{
			switch($task)
			{
				case 'add' :
					$action = 'core.create';
					break;
				case 'remove' :
					//$action = 'core.delete';
					break;
				case 'publish' :
				case 'unpublish' :
					//$action = 'core.edit.state';
					break;
			}
		}
		else
		if ($view == 'slide')
		{
			switch($task)
			{
				case '' :
				case 'save' :
				case 'saveAndNew' :
				case 'apply' :
					if ($id)
					{
						$action = ($slide->created_by == $user->id) ? 'core.edit.own' : 'core.edit';
					}
					else
					{
						$action = 'core.create';
					}
					break;
			}

		}

		// Check the determined action
		if ($action)
		{

			//Category check
			if ($slide->catid)
			{
				if (!$user->authorise($action, $option.'.category.'.$slide->catid))
				{
					JError::raiseWarning(403, JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK'));
					$mainframe->redirect('index.php?option=com_fpss');
				}
			}

			//Slide check
			$asset = $option;
			if ($id && $action != 'core.edit.own')
			{
				$asset .= '.slide.'.$id;
			}
			if (!$user->authorise($action, $asset))
			{
				JError::raiseWarning(403, JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK'));
				$mainframe->redirect('index.php?option=com_fpss');
			}
		}
	}

}
