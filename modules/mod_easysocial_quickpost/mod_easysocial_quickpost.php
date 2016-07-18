<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
if (!ES::exists()) {
    ES::language()->loadSite();
    echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
    return;
}

$my = ES::user();

if (!$params->get('show_public') && $my->guest) {
	return;
}

// Load up the module engine
$modules = ES::modules('mod_easysocial_quickpost');

// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency('javascript');
$modules->loadScript('script.js');

// Load front end language file
ES::language()->loadSite();

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

require(JModuleHelper::getLayoutPath('mod_easysocial_quickpost', $layout));
