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

class SigProModelGallery extends SigProModel
{

	public function getData()
	{
		$mainframe = JFactory::getApplication();
		$path = SigProHelper::getPath($this->getState('type', 'site'));
		$folder = $this->getState('folder');
		$language = $this->getState('language');
		if (!$language)
		{
			$params = JComponentHelper::getParams('com_languages');
			$language = $params->get('site');
		}
		if (!$folder || !JFolder::exists($path.'/'.$folder))
		{
			$mainframe->redirect('index.php?option=com_sigpro', JText::_('COM_SIGPRO_THE_SPECIFIED_FOLDER_DOES_NOT_EXIST'), 'error');
		}

		$labels = array();
		$labelsFile = false;
		if (JFile::exists($path.'/'.$folder.'/'.$language.'.labels.txt'))
		{
			$labelsFile = $language.'.labels.txt';
		}
		elseif (JFile::exists($path.'/'.$folder.'/labels.txt'))
		{
			$labelsFile = 'labels.txt';
		}
		if ($labelsFile)
		{
			$contents = JFile::read($path.'/'.$folder.'/'.$labelsFile);
			$lines = preg_split('/(?:\r\n?|\n)/', $contents);
			if ($lines)
			{
				foreach ($lines as $line)
				{
					$data = explode('|', $line);
					$label = new JObject;
					$label->set('title', @$data[1]);
					$label->set('description', @$data[2]);
					$labels[@$data[0]] = $label;
				}
			}
		}
		$images = array();
		$files = JFolder::files($path.'/'.$folder, '.jpg|.jpeg|.png|.gif|.JPG|.JPEG|.PNG|.GIF');
		$httpPath = SigProHelper::getHTTPPath($path.'/'.$folder);
		foreach ($files as $file)
		{
			$image = new JObject();
			$image->set('name', $file);
			$image->set('path', $httpPath.'/'.$file);
			if (isset($labels[$file]))
			{
				$image->set('title', $labels[$file]->title);
				$image->set('description', $labels[$file]->description);
			}
			else
			{
				$image->set('title', '');
				$image->set('description', '');
			}
			$info = @getimagesize($path.'/'.$folder.'/'.$image->name);
			if (isset($info[0]))
			{
				$image->dimensions = $info[0].'x'.$info[1];
			}
			else
			{
				$image->dimensions = '';
			}
			$image->size = round((filesize($path.'/'.$folder.'/'.$image->name) / 1024), 2);
			$image->url = SigProHelper::getImageURL($image->path);
			$images[] = $image;
		}
		$gallery = new JObject;
		if ($this->getState('type') == 'k2')
		{
			if (is_numeric($folder))
			{
				$db = JFactory::getDBO();
				$db->setQuery("SELECT title FROM #__k2_items WHERE id = ".(int)$folder);
				$title = $db->loadResult();
				$gallery->set('name', $title);
			}
			else
			{
				$gallery->set('name', null);
			}

		}
		else
		{
			$gallery->set('name', $folder);
		}
		$gallery->set('folder', $folder);
		$gallery->set('images', $images);
		return $gallery;
	}

	public function upload()
	{
		jimport('joomla.filesystem.file');
		$file = $this->getState('file');
		$filename = $this->getState('filename');
		$response = array(
			'status' => 0,
			'error' => ''
		);

		//Check if file is uploaded
		if (is_null($file) || !is_uploaded_file($file['tmp_name']))
		{
			$response['error'] = JText::_('COM_SIGPRO_FILE_WAS_NOT_UPLOADED');
		}
		//Check if file is an image
		elseif (!($info = @getimagesize($file['tmp_name'])))
		{
			$response['error'] = JText::_('COM_SIGPRO_FILE_IS_NOT_AN_IMAGE');
		}
		//Check image type
		elseif (!in_array($info[2], array(
			1,
			2,
			3,
			7,
			8
		)))
		{
			$response['error'] = JText::_('COM_SIGPRO_THIS_IMAGE_IS_NOT_SUPPORTED');
		}
		//Perform the upload
		else
		{
			$type = $this->getState('type');
			$folder = $this->getState('folder');
			$path = SigProHelper::getPath($type);
			if ($folder && JFolder::exists($path.'/'.$folder))
			{

				$result = JFile::upload($file['tmp_name'], $path.'/'.$folder.'/'.JFile::makeSafe($filename));
				if ($result)
				{
					$response['status'] = 1;
					$response['name'] = JFile::makeSafe($filename);
					$response['width'] = $info[0];
					$response['height'] = $info[1];
					$response['mime'] = $info['mime'];
					$response['size'] = round((filesize($path.'/'.$folder.'/'.JFile::makeSafe($filename)) / 1024), 2);
					$httpPath = SigProHelper::getHTTPPath($path.'/'.$folder);
					$response['path'] = $httpPath.'/'.$response['name'];
					$response['url'] = SigProHelper::getImageURL($response['path']);
				}
				else
				{
					$response['error'] = JText::_('COM_SIGPRO_COULD_NOT_UPLOAD_FILE');
				}
			}
			else
			{
				$response['error'] = JText::_('COM_SIGPRO_TARGET_FOLDER_DOES_NOT_EXIST');
			}
		}
		return $response;
	}

	public function save()
	{
		jimport('joomla.filesystem.file');
		$type = $this->getState('type');
		$path = SigProHelper::getPath($type);
		$folder = $this->getState('folder');
		$files = $this->getState('files');
		$titles = $this->getState('titles');
		$descriptions = $this->getState('descriptions');
		$language = $this->getState('language');
		if (!$language)
		{
			$params = JComponentHelper::getParams('com_languages');
			$language = $params->get('site');
		}
		$labels = '';
		foreach ($files as $key => $file)
		{
			if (JFile::exists($path.'/'.$folder.'/'.$file))
			{
				$labels .= $file.'|'.$titles[$key].'|'.$descriptions[$key]."\n";
			}
		}
		JFile::write($path.'/'.$folder.'/'.$language.'.labels.txt', $labels);
		return true;
	}

	public function add()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDBO();
		$type = $this->getState('type');
		$path = SigProHelper::getPath($type);
		$folder = $this->getState('folder');
		$response = $this->validate();
		if ($response->status == 0)
		{
			$this->setState('message', $response->message);
			return false;
		}
		$params = JComponentHelper::getParams('com_languages');
		$language = $params->get('site');
		if (JFolder::exists($path.'/'.$folder))
		{
			$this->setState('message', JText::_('COM_SIGPRO_FOLDER_ALREADY_EXISTS'));
			return false;
		}
		else
		{
			if (JFolder::create($path.'/'.$folder))
			{
				$buffer = '';
				JFile::write($path.'/'.$folder.'/'.$language.'.labels.txt', $buffer);
				$this->setState('message', JText::_('COM_SIGPRO_GALLERY_CREATED'));
				$this->setState('folder', $folder);
				$gallery = '{gallery}'.$folder.'{/gallery}';
				if (!SigProHelper::isK2v3())
				{
					if ($this->getState('type') == 'k2')
					{
						$db->setQuery('UPDATE #__k2_items SET gallery = '.$db->quote($gallery).'  WHERE id = '.(int)$folder);
						$db->query();
					}
				}
				return true;
			}
			else
			{
				$this->setState('message', JText::_('COM_SIGPRO_ERROR_CREATING_THE_FOLDER'));
				return false;
			}
		}
	}

	public function delete()
	{
		jimport('joomla.filesystem.file');
		$type = $this->getState('type');
		$path = SigProHelper::getPath($type);
		$folder = $this->getState('folder');
		$file = $this->getState('file');
		$response = new stdClass;
		$response->status = 0;
		$response->message = '';
		if (!JFile::exists($path.'/'.$folder.'/'.$file))
		{
			$response->message = JText::_('COM_SIGPRO_FILE_DOES_NOT_EXIST');
		}
		else
		{
			$result = JFile::delete($path.'/'.$folder.'/'.$file);
			if ($result)
			{
				$response->status = 1;
				$response->message = JText::_('COM_SIGPRO_FILE_DELETED');
			}
			else
			{
				$response->message = JText::_('COM_SIGPRO_DELETE_FAILED');
			}
		}
		return $response;
	}

	public function validate()
	{
		jimport('joomla.filesystem.folder');
		$type = $this->getState('type');
		$path = SigProHelper::getPath($type);
		$folder = $this->getState('folder');
		$response = new stdClass;
		$response->status = 0;
		$response->message = '';
		if (JString::trim($folder) == '')
		{
			$response->status = 0;
			$response->message = JText::_('COM_SIGPRO_INVALID_FOLDER_NAME');
		}
		else if (JFolder::exists($path.'/'.$folder))
		{
			$response->status = 0;
			$response->message = JText::_('COM_SIGPRO_FOLDER_ALREADY_EXISTS');
		}
		else
		{
			$response->status = 1;
			$response->message = JText::_('COM_SIGPRO_FOLDER_DOES_NOT_EXIST');
		}
		return $response;
	}

}
