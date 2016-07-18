<?php
/**
 * @version		$Id: view.html.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSViewInfo extends FPSSView
{
	function display($tpl = null)
	{
		$db = JFactory::getDBO();
		$db_version = $db->getVersion();
		$php_version = phpversion();
		$server = $this->get_server_software();
		$gd_check = extension_loaded('gd');
		$media_folder_check = is_writable(JPATH_ROOT.DS.'media'.DS.'com_fpss');
		$cache_folder_check = is_writable(JPATH_ROOT.DS.'cache');
		$this->assignRef('server', $server);
		$this->assignRef('php_version', $php_version);
		$this->assignRef('db_version', $db_version);
		$this->assignRef('gd_check', $gd_check);
		$this->assignRef('media_folder_check', $media_folder_check);
		$this->assignRef('cache_folder_check', $cache_folder_check);
		$title = JText::_('FPSS_INFORMATION');
		$this->assignRef('title', $title);
		$this->loadHelper('html');
		FPSSHelperHTML::title($title);
		FPSSHelperHTML::toolbar();
		FPSSHelperHTML::subMenu();
		parent::display($tpl);

	}

	function get_server_software()
	{
		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			return $_SERVER['SERVER_SOFTWARE'];
		}
		else
		if (($sf = getenv('SERVER_SOFTWARE')))
		{
			return $sf;
		}
		else
		{
			return JText::_('FPSS_NA');
		}
	}

}
