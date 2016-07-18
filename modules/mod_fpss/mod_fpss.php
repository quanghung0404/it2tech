<?php
/**
 * @version		$Id: mod_fpss.php 2192 2012-11-16 13:10:11Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

defined('_JEXEC') or die('Restricted access');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/helpers/legacy.php');
FPSSHelperLegacy::setup();

// JoomlaWorks reference parameters
$mod_copyrights_start = "\n\n<!-- JoomlaWorks \"Frontpage Slideshow\" (v3.5.1) starts here -->\n";
$mod_copyrights_end = "\n<!-- JoomlaWorks \"Frontpage Slideshow\" (v3.5.1) ends here -->\n\n";

jimport('joomla.filesystem.folder');

if (!JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'))
{
	JError::raiseWarning('', JText::_('FPSS_YOU_NEED_TO_INSTALL_THE_FRONTPAGE_SLIDESHOW_COMPONENT_AS_WELL'));
	return;
}
require_once (JPATH_SITE.DS.'components'.DS.'com_fpss'.DS.'helpers'.DS.'slideshow.php');
$slides = FPSSHelperSlideshow::render($params, 'module', $module->id);

$moduleTitle = $module->title;

if (!count($slides))
	return;

$document = JFactory::getDocument();

if ($document->getType() == 'html')
{
	$document->addHeadLink(JRoute::_('index.php?option=com_fpss&task=module&id='.$module->id.'&format=feed&type=rss'), 'alternate', 'rel', array(
		'type' => 'application/rss+xml',
		'title' => $moduleTitle.' '.JText::_('FPSS_MOD_RSS_FEED')
	));
	$document->addHeadLink(JRoute::_('index.php?option=com_fpss&task=module&id='.$module->id.'&format=feed&type=atom'), 'alternate', 'rel', array(
		'type' => 'application/atom+xml',
		'title' => $moduleTitle.' '.JText::_('FPSS_MOD_ATOM_FEED')
	));
}

// Output content with template
echo $mod_copyrights_start;
require (JModuleHelper::getLayoutPath('mod_fpss', $params->get('template', 'Default').DS.'default'));
echo FPSSHelperSlideshow::setCrd();
echo $mod_copyrights_end;
