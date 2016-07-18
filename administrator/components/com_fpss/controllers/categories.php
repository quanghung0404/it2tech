<?php
/**
 * @version		$Id: categories.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerCategories extends FPSSController
{

	function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', 'categories');
		parent::display();
	}

	function publish()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('categories');
		$model->setState('id', JRequest::getVar('id'));
		$model->publish();
		$this->setRedirect('index.php?option=com_fpss&view=categories');
	}

	function unpublish()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('categories');
		$model->setState('id', JRequest::getVar('id'));
		$model->unpublish();
		$this->setRedirect('index.php?option=com_fpss&view=categories');
	}

	function remove()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('categories');
		$model->setState('id', JRequest::getVar('id'));
		$model->remove();
		$this->setRedirect('index.php?option=com_fpss&view=categories', JText::_('FPSS_DELETE_COMPLETED'));
	}

	function add()
	{
		$this->setRedirect('index.php?option=com_fpss&view=category');
	}

	function edit()
	{
		$id = JRequest::getVar('id');
		JArrayHelper::toInteger($id);
		$this->setRedirect('index.php?option=com_fpss&view=category&id='.$id[0]);
	}

	function saveorder()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('categories');
		$model->setState('id', JRequest::getVar('id', array(0), 'post', 'array'));
		$model->setState('order', JRequest::getVar('order', array(0), 'post', 'array'));
		$model->saveorder();
		$document = JFactory::getDocument();
		if ($document->getType() == 'html')
		{
			$this->setRedirect('index.php?option=com_fpss&view=categories', JText::_('FPSS_NEW_ORDERING_SAVED'));
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$mainframe->close();
		}
	}

	function batch()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$ids = JRequest::getVar('id');
		$vars = JRequest::getVar('batch', array(), 'post', 'array');
		$category = JTable::getInstance('category', 'FPSS');
		foreach ($ids as $key => $id)
		{
			$category->load($id);
			if ($vars['language_id'])
			{
				$category->language = $vars['language_id'];
			}
			$category->store();
		}
		$this->setRedirect('index.php?option=com_fpss&view=categories', JText::_('FPSS_BATCH_PROCESS_COMPLETED_SUCCESSFULLY'));
	}

}
