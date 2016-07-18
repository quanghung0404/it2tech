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

class SigProHelper
{

	public static $isK2v3Var = null;

	public static function copyrights()
	{
		$document = JFactory::getDocument();
		if ($document->getType() == 'html' && JRequest::getCmd('tmpl', 'index') == 'index')
		{
			echo '<div id="sigProAdminFooter"><a href="http://www.joomlaworks.net/simple-image-gallery-pro" target="_blank">Simple Image Gallery Pro v3.1.0</a><br />Copyright &copy; 2006-'.date('Y').' <a href="http://www.joomlaworks.net/" target="_blank">JoomlaWorks Ltd.</a></div>';
		}
	}

	public static function initialize()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		JLoader::register('SigProController', JPATH_ADMINISTRATOR.'/components/com_sigpro/controllers/controller.php');
		JLoader::register('SigProView', JPATH_ADMINISTRATOR.'/components/com_sigpro/views/view.php');
		JLoader::register('SigProModel', JPATH_ADMINISTRATOR.'/components/com_sigpro/models/model.php');
		SigProModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_sigpro/models');
	}

	public static function getLanguagesList()
	{
		$language = JFactory::getLanguage();
		$languages = $language->getKnownLanguages(JPATH_SITE);
		$active = JRequest::getCmd('sigLang');
		if (!$active)
		{
			$params = JComponentHelper::getParams('com_languages');
			$active = $params->get('site');
		}

		return JHTML::_('select.genericlist', $languages, 'sigLang', '', 'tag', 'name', $active);
	}

	public static function getPath($type = 'site')
	{
		jimport('joomla.filesystem.file');
		$application = JFactory::getApplication();

		if ($type == 'k2')
		{
			$path = JPATH_SITE.'/media/k2/galleries';
		}
		else if ($type == 'site')
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
			if ($application->isAdmin() || $isAdmin)
			{
				if (version_compare(JVERSION, '1.6.0', 'ge'))
				{
					$defaultImagePath = 'images';
				}
				else
				{
					$defaultImagePath = 'images/stories';
				}

				$params = JComponentHelper::getParams('com_sigpro');
				$path = JPATH_SITE.'/'.$params->get('galleries_rootfolder', $defaultImagePath);
			}
			else
			{
				$folder = self::getUserFolder();
				$path = JPATH_SITE.'/media/jw_sigpro/users/'.$folder;
				if (!JFolder::exists($path))
				{
					JFolder::create($path);
				}
			}
		}
		else if ($type == 'users')
		{
			$path = JPATH_SITE.'/media/jw_sigpro/users';
		}

		$path = JPath::clean($path);
		JPath::check($path);
		if (JString::substr($path, -1, 1) == DIRECTORY_SEPARATOR)
		{
			$path = JString::rtrim($path, DIRECTORY_SEPARATOR);
		}
		return $path;
	}

	public static function getHTTPPath($path)
	{
		$httpPath = JString::str_ireplace(JPATH_SITE, JURI::root(true), $path);
		$httpPath = JString::str_ireplace(DIRECTORY_SEPARATOR, '/', $httpPath);
		return $httpPath;
	}

	public static function getImageURL($url)
	{
		$url = JString::str_ireplace(' ', '%20', $url);
		$imgResizingService = true;
		//if (in_array($_SERVER['HTTP_HOST'], array('localhost', '127.0.0.1')))
		if (preg_match("#localhost#s", $_SERVER['HTTP_HOST'], $matches) !== false || preg_match("#127\.0\.0\.1#s", $_SERVER['HTTP_HOST'], $matches) !== false)
		{
			$imgResizingService = false;
		}
		if (JURI::root(true).'/' == '/')
		{
			$absoluteURL = substr(JURI::root(false), 0, -1).$url;
		}
		else
		{
			$absoluteURL = JString::str_ireplace(JURI::root(true).'/', JURI::root(false), $url);
		}
		$preview = $imgResizingService ? '//ir0.mobify.com/550/'.$absoluteURL : $url;
		return $preview;
	}

	public static function getVar($name)
	{
		$source = JRequest::getVar($name);
		return self::cleanPath($source);
	}

	public static function cleanPath($source)
	{
		$pattern = '/^[A-Za-z0-9_-]+[A-Za-z0-9_\.\x20-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.\x20-]*)*$/';
		preg_match($pattern, (string)$source, $matches);
		$result = @(string)$matches[0];
		$result = JString::str_ireplace('\'', '', $result);
		return $result;
	}

	public static function getJSON($array = array())
	{

		if (function_exists('json_encode'))
		{
			return json_encode($array);
		}

		$object = '{';
		foreach ((array)$array as $k => $v)
		{
			if (is_null($v))
			{
				continue;
			}
			if (!is_array($v) && !is_object($v))
			{
				$object .= ' "'.$k.'": ';
				$object .= (is_numeric($v) || strpos($v, '\\') === 0) ? (is_numeric($v)) ? $v : substr($v, 1) : '"'.$v.'"';
				$object .= ',';
			}
			else
			{
				$object .= ' '.$k.': '.SigProHelper::getJSON($v).',';
			}
		}
		if (substr($object, -1) == ',')
		{
			$object = substr($object, 0, -1);
		}
		$object .= '}';

		return $object;
	}

	public static function getUserFolder()
	{
		$user = JFactory::getUser();
		$folder = str_pad($user->id, 10, '0', STR_PAD_LEFT);
		return $folder;
	}

	public static function checkPermissions($task)
	{
		$application = JFactory::getApplication();
		$user = JFactory::getUser();
		$result = true;
		$view = JRequest::getCmd('view', 'galleries');
		$type = JRequest::getCmd('type', 'site');
		if ($application->isAdmin() && version_compare(JVERSION, '1.6.0', 'ge') && $type != 'k2')
		{
			if ($view == 'galleries')
			{
				switch($task)
				{
					case 'add' :
					case 'create' :
						$action = 'create';
						break;
					case 'delete' :
						$action = 'delete';
						break;
				}
			}
			else
			{
				switch($task)
				{
					case 'create' :
						$action = 'create';
						break;
					case 'save' :
					case 'apply' :
					case 'upload' :
					case 'delete' :
						$action = 'edit';
						break;
				}
			}
			if (isset($action))
			{
				$result = $user->authorise('core.'.$action, 'com_sigpro');
			}
		}
		return $result;
	}

	public static function isK2v3()
	{
		if (self::$isK2v3Var === null)
		{
			$isK2v3 = false;
			if (version_compare(JVERSION, '3.2', 'ge'))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('manifest_cache'))->from($db->quoteName('#__extensions'))->where($db->quoteName('name').' = '.$db->quote('com_k2'));
				$db->setQuery($query);
				$manifest = json_decode($db->loadResult());
				$installedVersion = $manifest->version;
				if (version_compare($installedVersion, '3.0.0', 'ge'))
				{
					$isK2v3 = true;
				}
			}
			self::$isK2v3Var = $isK2v3;
		}
		return self::$isK2v3Var;
	}

}
