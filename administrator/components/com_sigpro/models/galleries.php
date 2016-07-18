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

class SigProModelGalleries extends SigProModel
{

	public function getData()
	{
		$path = SigProHelper::getPath($this->getState('type'));
		$galleries = array();
		if (!JFolder::exists($path))
		{
			$this->setState('total', 0);
			return $galleries;
		}
		$folders = JFolder::folders($path, '.', true, true, array(
			'.svn',
			'CVS',
			'.DS_Store',
			'__MACOSX'
		));
		foreach ($folders as $folder)
		{
			$basename = basename($folder);
			if (JString::strpos($basename, '.') === 0)
			{
				continue;
			}
			$images = JFolder::files($folder, '.jpg|.jpeg|.png|.gif|.JPG|.JPEG|.PNG|.GIF');
			$files = JFolder::files($folder, '.txt');
			$labels = false;
			foreach ($files as $file)
			{
				if (JString::strpos($file, 'labels.txt') !== false)
				{
					$labels = true;
				}
			}

			if (count($images) || $labels)
			{
				$relativeFolder = JString::str_ireplace(array(
					$path.DIRECTORY_SEPARATOR,
					$path.'/'
				), '', $folder);
				// Replace forward slashes when we are under Windows Servers
				$relativeFolder = JString::str_ireplace(DIRECTORY_SEPARATOR, '/', $relativeFolder);
				$gallery = new JObject;
				$gallery->set('folder', $relativeFolder);
				$gallery->set('insertPath', $relativeFolder);
				$application = JFactory::getApplication();
				if ($application->isSite() && $this->getState('type') == 'site')
				{
					$user = JFactory::getUser();
					if (version_compare(JVERSION, '2.5', 'ge'))
					{
						$isAdmin = $user->authorise('core.admin', 'com_sigpro');
					}
					else
					{
						$isAdmin = $user->gid == 25;
					}
					if (!$isAdmin)
					{
						$gallery->set('insertPath', '/media/jw_sigpro/users/'.SigProHelper::getUserFolder().'/'.$relativeFolder);
					}
				}
				$gallery->set('path', SigProHelper::getHTTPPath($folder));
				$gallery->set('title', basename($folder));
				$gallery->set('titleLower', JString::strtolower($gallery->get('title')));
				$gallery->set('images', $images);
				$gallery->set('created', filemtime($folder));
				if (count($images))
				{
					$preview = SigProHelper::getHTTPPath($folder.'/'.$images[0]);
					$url = SigProHelper::getImageURL($preview);
				}
				else
				{
					$url = JURI::root(true).'/administrator/components/com_sigpro/images/unavailable.png';
				}
				$gallery->set('preview', $url);
				$gallery->set('url', $url);
				$link = 'index.php?option=com_sigpro&view=gallery&type='.$this->getState('type', 'site').'&folder='.$gallery->folder.'&tmpl='.JRequest::getCmd('tmpl', 'index').'&editorName='.JRequest::getCmd('editorName');
				$template = JRequest::getCmd('template');
				if ($template)
				{
					$link .= '&template='.$template;
				}
				$gallery->set('link', JRoute::_($link));
				$gallery->set('numOfImages', count($gallery->images));
				$gallery->set('checked_out', '');
				$galleries[JString::strtolower($folder)] = $gallery;
			}
		}
		$this->setState('total', count($galleries));
		$galleries = $this->sortGalleries($galleries);
		if ($this->getState('limit'))
		{
			$galleries = array_slice($galleries, $this->getState('limitstart'), $this->getState('limit'), true);
		}
		if ($this->getState('type') == 'k2')
		{
			$db = JFactory::getDBO();
			$ItemIds = array();
			foreach ($galleries as $gallery)
			{
				$ItemIds[] = $gallery->title;
			}
			JArrayHelper::toInteger($ItemIds);
			$ItemIds = array_filter($ItemIds);
			if (count($ItemIds))
			{
				$db->setQuery("SELECT id, title FROM #__k2_items WHERE id IN (".implode(',', $ItemIds).")");
				$items = $db->loadObjectList();
				foreach ($items as $item)
				{
					foreach ($galleries as $gallery)
					{
						if ($gallery->title == $item->id)
						{
							$gallery->title = $item->title;
						}
					}

				}
			}

		}
		return $galleries;
	}

	public function delete()
	{
		$db = JFactory::getDBO();
		$path = SigProHelper::getPath($this->getState('type', 'site'));
		$folders = $this->getState('folders', array());
		foreach ($folders as $folder)
		{
			$folder = SigProHelper::cleanPath($folder);
			$folder = JString::str_ireplace(DIRECTORY_SEPARATOR, '', $folder);
			if ($folder && JFolder::exists($path.'/'.$folder))
			{
				JFolder::delete($path.'/'.$folder);
				if ($this->getState('type') == 'k2')
				{
					$db->setQuery("UPDATE #__k2_items SET gallery = '' WHERE id = ".(int)$folder);
					$db->query();
				}
			}
		}
		return true;
	}

	public function sortGalleries($galleries)
	{
		$sorted = array();
		$options = explode(' ', $this->getState('sorting'));
		$this->setState('ordering', $options[0]);
		$this->setState('orderingDir', $options[1]);
		if ($this->getState('ordering') == 'folder')
		{
			usort($galleries, array(
				$this,
				'sortByTitle'
			));
		}
		else
		{
			usort($galleries, array(
				$this,
				'sortByDate'
			));
		}
		if ($this->getState('orderingDir') == 'DESC')
		{
			$galleries = array_reverse($galleries);
		}
		return $galleries;
	}

	public function sortByTitle($a, $b)
	{
		return strcmp($a->titleLower, $b->titleLower);
	}

	public function sortByDate($a, $b)
	{
		if ($a->created == $b->created)
		{
			return 0;
		}
		return ($a->created < $b->created) ? -1 : 1;
	}

}
