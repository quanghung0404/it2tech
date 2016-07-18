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

class SigProControllerGallery extends SigProController
{

	public function upload()
	{
		JRequest::checkToken('get') or jexit('Invalid Token');
		$params = JComponentHelper::getParams('com_sigpro');
		$memoryLimit = (int)$params->get('memoryLimit');
		if ($memoryLimit > (int)ini_get('memory_limit'))
		{
			ini_set('memory_limit', $memoryLimit.'M');
		}
		$file = JRequest::getVar('file', null, 'files');
		$filename = JRequest::getVar('name');
		$type = JRequest::getCmd('type', 'site');
		$folder = SigProHelper::getVar('folder');
		$model = $this->getModel('gallery');
		$model->setState('file', $file);
		$model->setState('filename', $filename);
		$model->setState('type', $type);
		$model->setState('folder', $folder);
		$response = $model->upload();
		echo SigProHelper::getJSON($response);
		@header ("Connection: close");
		exit ;
	}

	public function save()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$type = JRequest::getCmd('type', 'site');
		$folder = SigProHelper::getVar('folder');
		$files = JRequest::getVar('filenames');
		$titles = JRequest::getVar('titles');
		$descriptions = JRequest::getVar('descriptions', array(), 'default', 'none', 4);
		$language = JRequest::getCmd('sigLang');
		$model = $this->getModel('gallery');
		$model->setState('type', $type);
		$model->setState('folder', $folder);
		$model->setState('files', $files);
		$model->setState('titles', $titles);
		$model->setState('descriptions', $descriptions);
		$model->setState('language', $language);
		$model->save();
		if (JRequest::getCmd('task') == 'save')
		{
			$link = 'index.php?option=com_sigpro&view=galleries';
		}
		else
		{
			$link = 'index.php?option=com_sigpro&view=gallery&folder='.$folder;
		}
		$this->setRedirect($link, JText::_('COM_SIGPRO_GALLERY_SAVED'));
	}

	public function apply()
	{
		$this->save();
	}

	public function cancel()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$this->setRedirect('index.php?option=com_sigpro&view=galleries');
	}

	public function create()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$type = JRequest::getCmd('type', 'site');
		$folder = SigProHelper::getVar('folder');
		$model = $this->getModel('gallery');
		$model->setState('type', $type);
		$model->setState('folder', $folder);
		if ($model->add())
		{
			$this->setRedirect('index.php?option=com_sigpro&view=gallery&folder='.$model->getState('folder'), $model->getState('message'));
		}
		else
		{
			$this->setRedirect('index.php?option=com_sigpro&view=galleries&task=add', $model->getState('message'), 'error');
		}
	}

	public function delete()
	{
		JRequest::checkToken() or jexit('Invalid Token');
		$type = JRequest::getCmd('type', 'site');
		$folder = JRequest::getVar('folder');
		$file = JRequest::getVar('file');
		$files = JRequest::getVar('image');
		$model = $this->getModel('gallery');
		$model->setState('type', $type);
		$model->setState('folder', $folder);
		if (is_array($files) && count($files))
		{
			foreach ($files as $image)
			{
				$model->setState('file', $image);
				$response = $model->delete();
			}
			$this->setRedirect('index.php?option=com_sigpro&view=gallery&type='.$type.'&folder='.$folder, JText::_('COM_SIGPRO_IMAGES_DELETED'));
		}
		elseif ($file)
		{
			$file = basename($file);
			$model->setState('file', $file);
			$response = $model->delete();
			echo SigProHelper::getJSON($response);
			exit ;
		}

	}

	public function validate()
	{
		$type = JRequest::getCmd('type', 'site');
		$folder = SigProHelper::getVar('folder');
		$model = $this->getModel('gallery');
		$model->setState('type', $type);
		$model->setState('folder', $folder);
		$response = $model->validate();
		echo SigProHelper::getJSON($response);
		exit ;
	}

}
