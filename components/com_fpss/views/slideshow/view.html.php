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

class FPSSViewSlideshow extends FPSSView
{

	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$params = (version_compare(JVERSION, '1.6.0', 'ge')) ? $mainframe->getParams('com_fpss') : JComponentHelper::getParams('com_fpss');
		$this->loadHelper('slideshow');
		$slides = FPSSHelperSlideshow::render($params, 'component');
		$document->addHeadLink(JRoute::_('&format=feed&type=rss'), 'alternate', 'rel', array(
			'type' => 'application/rss+xml',
			'title' => $params->get('page_title').' '.JText::_('FPSS_MOD_RSS_FEED')
		));
		$document->addHeadLink(JRoute::_('&format=feed&type=atom'), 'alternate', 'rel', array(
			'type' => 'application/atom+xml',
			'title' => $params->get('page_title').' '.JText::_('FPSS_MOD_ATOM_FEED')
		));
		$this->assignRef('slides', $slides);
		$this->assignRef('params', $params);

		ob_start();
		$module = new JObject;
		$module->id = 0;
		$output = $slides;
		require (JModuleHelper::getLayoutPath('mod_fpss', $params->get('template', 'Movies').DS.'default'));
		$slideshow = ob_get_contents();
		ob_end_clean();

		$this->assignRef('slideshow', $slideshow);
		parent::display($tpl);
	}

	function module($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$user = JFactory::getUser();
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
		$pathway = $mainframe->getPathWay();
		$pathway->addItem($module->title, '');
		$this->loadHelper('slideshow');
		$slides = FPSSHelperSlideshow::render($params, 'component');
		$document->addHeadLink(JRoute::_('&format=feed&type=rss'), 'alternate', 'rel', array(
			'type' => 'application/rss+xml',
			'title' => 'RSS 2.0'
		));
		$document->addHeadLink(JRoute::_('&format=feed&type=atom'), 'alternate', 'rel', array(
			'type' => 'application/atom+xml',
			'title' => 'Atom 1.0'
		));
		$this->assignRef('slides', $slides);
		$this->assignRef('params', $params);
		parent::display($tpl);
	}

}
