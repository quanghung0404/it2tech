<?php
/**
 * @version		$Id: view.feed.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSViewSlideshow extends FPSSView
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = (version_compare(JVERSION, '1.6.0', 'ge')) ? $mainframe->getParams('com_fpss') : JComponentHelper::getParams('com_fpss');
		$this->loadHelper('slideshow');
		$slides = FPSSHelperSlideshow::getSlides($params);
		foreach ($slides as $slide)
		{
			$slide->title = $this->escape($slide->title);
			$slide->title = html_entity_decode($slide->title);
			$feedItem = new JFeedItem();
			$feedItem->title = $slide->title;
			$feedItem->link = $slide->link;
			$feedItem->description = '<div><img src="'.JURI::root().'media/com_fpss/cache/'.$slide->id.'_'.md5('Image'.$slide->id).'_m.jpg" alt="'.$slide->title.'" /></div>'.$slide->text;
			$feedItem->date = $slide->created;
			$document->addItem($feedItem);
		}
	}

	function module($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		$document = JFactory::getDocument();
		$id = JRequest::getInt('id');
		$status = true;
		$module = JTable::getInstance('module');
		$module->load($id);
		if (!$module->published || $module->module != 'mod_fpss')
		{
			$status = false;
		}
		if (version_compare(JVERSION, '1.6.0', 'ge') && !in_array($module->access, $user->getAuthorisedViewLevels()))
		{
			$status = false;
		}
		if (version_compare(JVERSION, '1.6.0', 'lt') && $module->access > $user->get('aid'))
		{
			$status = false;
		}
		if (!$status)
		{
			JError::raiseError(404, JText::_('FPSS_PAGE_NOT_FOUND'));
		}
		jimport('joomla.html.parameter');
		$params = version_compare(JVERSION, '1.6.0', 'ge') ? new JRegistry($module->params) : new JParameter($module->params);
		$document->setTitle($module->title);
		$this->loadHelper('slideshow');
		$slides = FPSSHelperSlideshow::getSlides($params);
		foreach ($slides as $slide)
		{
			$slide->title = $this->escape($slide->title);
			$slide->title = html_entity_decode($slide->title);
			$feedItem = new JFeedItem();
			$feedItem->title = $slide->title;
			$feedItem->link = $slide->link;
			$feedItem->description = '<div><img src="'.JURI::root().'media/com_fpss/cache/'.$slide->id.'_'.md5('Image'.$slide->id).'_m.jpg" alt="'.$slide->title.'" /></div>'.$slide->text;
			$feedItem->date = $slide->created;
			$document->addItem($feedItem);
		}
	}

}
