<?php
/**
 * @version		$Id: category.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerCategory extends FPSSController
{

	function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', 'category');
		parent::display();
	}

	function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('category');
		$model->setState('data', JRequest::get('post'));
		if (!$model->save())
		{
			$this->setRedirect('index.php?option=com_fpss&view=categories', $model->getError(), 'error');
			return false;
		}
		$this->setRedirect('index.php?option=com_fpss&view=categories', JText::_('FPSS_CATEGORY_SAVED'));
	}

	function apply()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('category');
		$model->setState('data', JRequest::get('post'));
		if (!$model->save())
		{
			$this->setRedirect('index.php?option=com_fpss&view=category&id='.$model->getError(), 'error');
			return false;
		}
		$this->setRedirect('index.php?option=com_fpss&view=category&id='.$model->getState('id'));
	}

	function cancel()
	{
		$this->setRedirect('index.php?option=com_fpss&view=categories');
	}

}
