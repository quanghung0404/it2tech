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

class SigProControllerMedia extends SigProController
{

	public function connector()
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$path = SigProHelper::getPath('site');
		$url = SigProHelper::getHTTPPath($path);
		JPath::check($path);
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/js/elfinder/php/elFinderConnector.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/js/elfinder/php/elFinder.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/js/elfinder/php/elFinderVolumeDriver.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/js/elfinder/php/elFinderVolumeLocalFileSystem.class.php';
		function access($attr, $path, $data, $volume)
		{
			$mainframe = JFactory::getApplication();
			$user = JFactory::getUser();
			// Hide files and folders starting with .
			if (strpos(basename($path), '.') === 0 && $attr == 'hidden')
			{
				return true;
			}
			// Read only access for front-end. Full access for administration section.
			switch($attr)
			{
				case 'read' :
					return true;
					break;
				case 'write' :
					if ($mainframe->isSite())
					{
						return false;
					}
					else
					{
						return version_compare(JVERSION, '1.6.0', 'ge') ? ($user->authorise('core.create', 'com_sigpro') && $user->authorise('core.edit', 'com_sigpro') && $user->authorise('core.delete', 'com_sigpro')) : true;
					}
					break;
				case 'locked' :
					if ($mainframe->isSite())
					{
						return true;
					}
					else
					{
						return version_compare(JVERSION, '1.6.0', 'ge') ? !($user->authorise('core.create', 'com_sigpro') && $user->authorise('core.edit', 'com_sigpro') && $user->authorise('core.delete', 'com_sigpro')) : false;
					}
					break;
				case 'hidden' :
					return false;
					break;
			}

		}

		if ($mainframe->isAdmin())
		{
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				$write = ($user->authorise('core.create', 'com_sigpro') && $user->authorise('core.edit', 'com_sigpro') && $user->authorise('core.delete', 'com_sigpro'));
			}
			else
			{
				$write = true;
			}
			$permissions = array('read' => true, 'write' => $write);
		}
		else
		{
			$permissions = array('read' => true, 'write' => false);
		}

		$options = array('roots' => array( array('driver' => 'LocalFileSystem', 'path' => $path, 'URL' => $url, 'accessControl' => 'access', 'defaults' => $permissions, 'uploadAllow' => array('image', 'video', 'audio', 'text/plain', 'text/html', 'application/json', 'application/pdf', 'application/zip', 'application/x-7z-compressed', 'application/x-bzip', 'application/x-bzip2', 'text/css', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'), 'uploadOrder' => array('allow', 'deny'))));
		$connector = new elFinderConnector(new elFinder($options));
		$connector->run();
	}

}
