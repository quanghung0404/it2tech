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

class SigProViewInfo extends SigProView
{

	public function display($tpl = null)
	{
		$info = array();
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$info['plg_content_sigpro'] = JFile::exists(JPATH_PLUGINS.'/content/jw_sigpro/jw_sigpro.php');
			$info['plg_k2_sigpro'] = JFile::exists(JPATH_PLUGINS.'/k2/jw_sigpro/jw_sigpro.php');
			$info['plg_editors-xtd_sigpro'] = JFile::exists(JPATH_PLUGINS.'/editors-xtd/jw_sigpro/jw_sigpro.php');
		}
		else
		{
			$info['plg_content_sigpro'] = JFile::exists(JPATH_PLUGINS.'/content/jw_sigpro.php');
			$info['plg_k2_sigpro'] = JFile::exists(JPATH_PLUGINS.'/k2/jw_sigpro.php');
			$info['plg_editors-xtd_sigpro'] = JFile::exists(JPATH_PLUGINS.'/editors-xtd/jw_sigpro.php');
		}
		$info['plg_content_sigpro_enabled'] = JPluginHelper::isEnabled('content', 'jw_sigpro');
		$info['plg_k2_sigpro_enabled'] = JPluginHelper::isEnabled('k2', 'jw_sigpro');
		$info['plg_editors-xtd_sigpro_enabled'] = JPluginHelper::isEnabled('editors-xtd', 'jw_sigpro');
		$info['php'] = phpversion();
		if (extension_loaded('gd'))
		{
			$gdinfo = gd_info();
			$info['gd'] = $gdinfo["GD Version"];
		}
		else
		{
			$info['gd'] = false;

		}
		$info['upload'] = ini_get('upload_max_filesize');
		$info['memory'] = ini_get('memory_limit');
		$info['permissions'] = array();
		$info['permissions']['cache'] = is_writable(JPATH_SITE.'/cache');
		if (file_exists(JPATH_SITE.'/cache/jw_sigpro'))
		{
			$info['permissions']['cache/jw_sigpro'] = is_writable(JPATH_SITE.'/cache/jw_sigpro');
		}
		if (file_exists(JPATH_SITE.'/media/jw_sigpro/users'))
		{
			$info['permissions']['media/jw_sigpro/users'] = is_writable(JPATH_SITE.'/media/jw_sigpro/users');
		}

		$params = JComponentHelper::getParams('com_sigpro');
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$defaultImagePath = 'images';
		}
		else
		{
			$defaultImagePath = 'images/stories';
		}
		$path = $params->get('galleries_rootfolder', $defaultImagePath);
		if ($path)
		{
			$info['permissions'][$path] = is_writable(SigProHelper::getPath('site'));
		}

		$K2Path = SigProHelper::getPath('k2');
		if (JFolder::exists($K2Path))
		{
			$info['permissions']['media/k2/galleries'] = is_writable($K2Path);
		}
		$this->assignRef('info', $info);
		parent::display($tpl);
	}

}
