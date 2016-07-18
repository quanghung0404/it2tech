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

class SigProControllerGalleries extends SigProController
{

	public function delete()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('galleries');
		$type = JRequest::getCmd('type', 'site');
		$folders = JRequest::getVar('folder');
		$model->setState('type', $type);
		$model->setState('folders', $folders);
		$model->delete();
		$this->setRedirect('index.php?option=com_sigpro&view=galleries', JText::_('COM_SIGPRO_FOLDERS_DELETED'));
	}

	public function add()
	{
		$view = $this->getView('gallery', 'html');
		$view->add();
	}

	public function create()
	{
		$folder = JRequest::getCmd('newFolder');
		$type = JRequest::getCmd('type', 'site');
		if (!$folder)
		{
			$this->setRedirect('index.php?option=com_sigpro');
			return $this;
		}
		if (!JFolder::exists(JPATH_SITE.'/media/k2/galleries/'.$folder))
		{
			$model = $this->getModel('gallery');
			$model->setState('type', $type);
			$model->setState('folder', $folder);
			$model->add();
		}
		$this->setRedirect('index.php?option=com_sigpro&view=gallery&type=k2&folder='.$folder);
		return $this;
	}

}
