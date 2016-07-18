<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

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

$option = JRequest::getVar('option');

if ($option == 'com_easysocial' && !$params->get('show_on_easysocial', false)) {
	return;
}

// Load our css
FD::document()->init();

// Load up EasySocial's language file
FD::language()->loadSite();

// Load up the module engine
$modules = FD::modules('mod_easysocial_toolbar');

$modules->loadComponentScripts();

// We need these packages
$modules->addDependency('css', 'javascript');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

$toolbar = FD::get('Toolbar');

$options = array(
						'forceoption' => true,
						'toolbar' => true,
						'dashboard' => $params->get('show_dashboard', true),
						'friends' => $params->get('show_friends', true),
						'conversations' => $params->get('show_conversations', true),
						'notifications'	=> $params->get('show_notifications', true),
						'search' => $params->get('show_search', true),
						'login'	=> $params->get('show_login', true),
						'profile' => $params->get('show_profile', true),
						'responsive' => $params->get('responsive', true),
						'modulePopboxPosition' => $params->get('module_popbox_position', true),
						'modulePopboxCollision' => $params->get('module_popbox_collision', true)
					);

require(JModuleHelper::getLayoutPath('mod_easysocial_toolbar', $layout));
