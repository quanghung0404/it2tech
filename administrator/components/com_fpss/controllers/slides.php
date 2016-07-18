<?php
/**
 * @version		$Id: slides.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerSlides extends FPSSController
{

	function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', 'slides');
		parent::display();
	}

	function publish()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$ids = JRequest::getVar('id');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$user = JFactory::getUser();
			$counter = 0;
			foreach ($ids as $key => $id)
			{
				if (!$user->authorise('core.edit.state', 'com_fpss.slide.'.$id))
				{
					unset($ids[$key]);
					$counter++;
				}
			}
			if ($counter)
			{
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK'), 'error');
			}
		}
		$model->setState('id', $ids);
		$model->publish();
		$this->setRedirect('index.php?option=com_fpss&view=slides');
	}

	function unpublish()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$ids = JRequest::getVar('id');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$user = JFactory::getUser();
			$counter = 0;
			foreach ($ids as $key => $id)
			{
				if (!$user->authorise('core.edit.state', 'com_fpss.slide.'.$id))
				{
					unset($ids[$key]);
					$counter++;
				}
			}
			if ($counter)
			{
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK'), 'error');
			}
		}
		$model->setState('id', $ids);
		$model->unpublish();
		$this->setRedirect('index.php?option=com_fpss&view=slides');
	}

	function saveorder()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$model->setState('id', JRequest::getVar('id', array(0), 'post', 'array'));
		$model->setState('order', JRequest::getVar('order', array(0), 'post', 'array'));
		$model->saveorder();
		$document = JFactory::getDocument();
		if ($document->getType() == 'html')
		{
			$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_NEW_ORDERING_SAVED'));
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$mainframe->close();
		}
	}

	function featuredsaveorder()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$model->setState('id', JRequest::getVar('id', array(0), 'post', 'array'));
		$model->setState('featuredOrder', JRequest::getVar('featuredOrder', array(0), 'post', 'array'));
		$model->featuredsaveorder();
		$document = JFactory::getDocument();
		if ($document->getType() == 'html')
		{
			$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_NEW_ORDERING_SAVED'));
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$mainframe->close();
		}
	}

	function featured()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$ids = JRequest::getVar('id');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$user = JFactory::getUser();
			$counter = 0;
			foreach ($ids as $key => $id)
			{
				if (!$user->authorise('core.edit.state', 'com_fpss.slide.'.$id))
				{
					unset($ids[$key]);
					$counter++;
				}
			}
			if ($counter)
			{
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK'), 'error');
			}
		}
		$model->setState('id', $ids);
		$model->featured();
		$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_FEATURED_STATE_UPDATED'));
	}

	function accessregistered()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$model->setState('id', JRequest::getVar('id'));
		$model->accessregistered();
		$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_NEW_ACCESS_SETTING_SAVED'));
	}

	function accessspecial()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$model->setState('id', JRequest::getVar('id'));
		$model->accessspecial();
		$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_NEW_ACCESS_SETTING_SAVED'));
	}

	function accesspublic()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$model->setState('id', JRequest::getVar('id'));
		$model->accesspublic();
		$this->setRedirect('index.php?option=com_fpss&view=slides', JText::_('FPSS_NEW_ACCESS_SETTING_SAVED'));
	}

	function remove()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('slides');
		$ids = JRequest::getVar('id');
		$message = JText::_('FPSS_DELETE_COMPLETED');
		$type = 'message';
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$user = JFactory::getUser();
			$counter = 0;
			foreach ($ids as $key => $id)
			{
				if (!$user->authorise('core.delete', 'com_fpss.slide.'.$id))
				{
					unset($ids[$key]);
					$counter++;
				}
			}
			if ($counter)
			{
				$message = JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK');
				$type = 'error';
			}
		}
		if (count($ids))
		{
			$model->setState('id', $ids);
			$model->remove();
			$model->cleanUp();
		}
		$this->setRedirect('index.php?option=com_fpss&view=slides', $message, $type);
	}

	function add()
	{
		$this->setRedirect('index.php?option=com_fpss&view=slide');
	}

	function edit()
	{
		$id = JRequest::getVar('id');
		JArrayHelper::toInteger($id);
		$this->setRedirect('index.php?option=com_fpss&view=slide&id='.$id[0]);
	}

	function stats()
	{
		JLoader::register('FPSSHelperHTML', JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'html.php');
		$model = $this->getModel('slides');
		$model->setState('catid', JRequest::getInt('fpssModuleCategory'));
		$model->setState('timeRange', JRequest::getInt('fpssModuleTimeRange'));
		$model->setState('limit', JRequest::getInt('fpssModuleLimit'));
		$response = $model->stats();
		echo FPSSHelperHTML::getJSON($response);
		$mainframe = JFactory::getApplication();
		$mainframe->close();
	}

	function batch()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$ids = JRequest::getVar('id');
		$vars = JRequest::getVar('batch', array(), 'post', 'array');
		$message = JText::_('FPSS_BATCH_PROCESS_COMPLETED_SUCCESSFULLY');
		$type = 'message';
		$slide = JTable::getInstance('slide', 'FPSS');
		$user = JFactory::getUser();
		$counter = 0;
		foreach ($ids as $key => $id)
		{
			$slide->load($id);
			$action = ($slide->created_by == $user->id) ? 'core.edit.own' : 'core.edit';
			if ($user->authorise($action, 'com_fpss.slide.'.$id))
			{
				if ($vars['assetgroup_id'])
				{
					$slide->access = $vars['assetgroup_id'];
				}
				if ($vars['language_id'])
				{
					$slide->language = $vars['language_id'];
				}
				$slide->store();
			}
			else
			{
				$counter++;
			}
		}
		if ($counter)
		{
			$message = JText::_('FPSS_YOU_ARE_NOT_AUTHORIZED_TO_EXECUTE_THIS_TASK');
			$type = 'error';
		}
		$this->setRedirect('index.php?option=com_fpss&view=slides', $message, $type);
	}

}
