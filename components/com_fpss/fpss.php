<?php
/**
 * @version		$Id: fpss.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.application.component.view');
jimport('joomla.application.module.helper');
jimport('joomla.filesystem.file');
$language = JFactory::getLanguage();
$language->load('com_fpss', JPATH_ADMINISTRATOR);
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy.php');
FPSSHelperLegacy::setup();
require_once (JPATH_COMPONENT.DS.'controller.php');
$controller = new FPSSControllerSlideshow();
$controller->execute(JRequest::getWord('task'));
$controller->redirect();
