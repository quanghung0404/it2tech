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

if (file_exists(JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php'))
{
	require_once JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php';
}

class plgK2Jw_SigPro extends K2Plugin
{

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->pluginName = 'jw_sigpro';
		$this->loadLanguage();
	}

	function onAfterK2Save(&$item, $isNew)
	{
		jimport('joomla.filesystem.folder');
		require_once JPATH_ADMINISTRATOR.'/components/com_sigpro/helper.php';

		if (SigProHelper::isK2v3())
		{
			$path = SigProHelper::getPath('k2');
			if ($item->galleries)
			{
				$galleries = json_decode($item->galleries);
			}
			foreach ($galleries as $gallery)
			{
				if (isset($gallery->folder) && $gallery->folder)
				{
					if (!JFolder::exists($path.'/'.$item->id))
					{
						JFolder::create($path.'/'.$item->id);
					}
					if (JFolder::exists($path.'/'.$gallery->folder))
					{
						$gallery->upload = uniqid();
						JFolder::move($path.'/'.$gallery->folder, $path.'/'.$item->id.'/'.$gallery->upload);
					}
					unset($gallery->folder);
				}
			}

			$item->galleries = json_encode($galleries);
			$item->store();
		}
		else
		{
			$path = SigProHelper::getPath('k2');
			$folder = JRequest::getCmd('sigProFolder');
			if ($isNew && $folder && $folder != $item->id && JFolder::exists($path.'/'.$folder))
			{
				JFolder::move($path.'/'.$folder, $path.'/'.$item->id);
			}

			// Get item gallery value from the database
			$item->load($item->id);

			// Handle non Flickr galleries
			if (!strpos($item->gallery, 'flickr.com'))
			{
				$exists = JFolder::exists($path.'/'.$item->id);
				$isValid = $exists && count(JFolder::files($path.'/'.$item->id, '.jpg|.jpeg|.png|.gif|.JPG|.JPEG|.PNG|.GIF')) > 0;
				if ($isValid && ($item->gallery == null || trim($item->gallery) == ''))
				{
					$item->gallery = '{gallery}'.$item->id.'{/gallery}';
					$item->store();
				}
				if (!$isValid)
				{
					if ($exists)
					{
						JFolder::delete($path.'/'.$item->id);
					}
					if ($item->gallery)
					{
						$item->gallery = '';
						$item->store();
					}
				}
			}
		}

	}

}
