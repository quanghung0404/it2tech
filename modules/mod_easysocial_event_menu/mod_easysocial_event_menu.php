<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Include main engine
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($file)) {
    return;
}

// Include the engine file.
require_once($file);

// Check if Foundry exists
if (!FD::exists()) {
    FD::language()->loadSite();
    echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
    return;
}

// Load up the current user.
$my = FD::user();

// If the user is not logged in, don't show the menu
if (!$my->id) {
    return;
}

// THis module will only appear on group pages
$view = JRequest::getVar('view');
$layout = JRequest::getWord('layout');
$id = JRequest::getInt('id');

if ($view != 'events' || $layout != 'item' || !$id) {
    return;
}

require_once(dirname(__FILE__) . '/helper.php');

// Get the current event object
$event = FD::event($id);

// Load up the module engine
$modules = FD::modules('mod_easysocial_event_menu');

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css');
$modules->loadScript('script.js');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

// Load list of apps for this group
$apps = EasySocialModEventsMenuHelper::getApps($params, $event);

// Get a list of pending members from the event
$pending = EasySocialModEventsMenuHelper::getPendingMembers($params, $event);

require(JModuleHelper::getLayoutPath('mod_easysocial_event_menu', $layout));
