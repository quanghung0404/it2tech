<?php
/**
 * @version		$Id: controller.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSControllerSlideshow extends FPSSController
{

	function display($cachable = false, $urlparams = array())
	{
		JRequest::setVar('view', 'slideshow');
		parent::display();
	}

	function module()
	{
		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$viewLayout = JRequest::getCmd('layout', 'default');
		$view = $this->getView('slideshow', $viewType);
		$view->setLayout($viewLayout);
		//if ($viewType != 'feed')
		//{
		//	$cache = JFactory::getCache('com_fpss', 'view');
		//	$cache->get($view, 'module');
		//}
		//else
		//{
			$view->module();
		//}
	}

	function track()
	{
		$params = JComponentHelper::getParams('com_fpss');
		if (!$params->get('stats', 1))
		{
			JError::raiseError(404, JText::_('FPSS_PAGE_NOT_FOUND'));
		}
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$mainframe = JFactory::getApplication();
		$id = JRequest::getInt('id');
		$url = JRequest::getString('url');
		$url = JString::str_ireplace(':', '-', $url);
		$url = base64_decode(strtr($url, '-_,', '+/='));
		$slide = JTable::getInstance('slide', 'FPSS');
		$slide->load($id);
		if (!$slide->id)
		{
			$mainframe->redirect(JURI::root());
		}
		if ($slide->referenceType == 'custom')
		{
			$url = $slide->custom;
		}
		else
		{
			if (!JURI::isInternal($url))
			{
				$mainframe->redirect(JURI::root());
			}
		}
		$slide->hit();
		$date = JFactory::getDate();
		$now = version_compare(JVERSION, '1.6.0', '<') ? $date->toMySQL() : $date->toSql();
		$db = JFactory::getDBO();
		$query = "INSERT INTO #__fpss_stats VALUES('',{$id}, ".$db->Quote($now).")";
		$db->setQuery($query);
		$db->query();
		$mainframe->redirect($url);
	}

}
