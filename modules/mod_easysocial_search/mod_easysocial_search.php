<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main engine
jimport('joomla.filesystem.file');
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

if (!JFile::exists($file)) {
    return;
}

require_once($file);

// Load frontend's language file
ES::language()->loadSite();

// Check if Foundry exists
if (!FD::exists()){
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Load up the module engine
$modules = ES::modules('mod_easysocial_search');

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css', 'javascript');
$modules->loadScript('script.js');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

$searchAdapter = ES::get('Search');
$filterTypes = $searchAdapter->getTaxonomyTypes();

require(JModuleHelper::getLayoutPath('mod_easysocial_search', $layout));
