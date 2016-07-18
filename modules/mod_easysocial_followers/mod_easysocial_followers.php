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

$my = FD::user();

// If user is not loagged in, just return
if (!$my->id) {
	return;
}

// Load up the module engine
$modules = FD::modules('mod_easysocial_followers');

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css', 'javascript');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

$options = array();

if ($params->get('total')) {
    $options['limit'] = $params->get('total');
}

// Check filter type
$filter = $params->get('filter', 'followedBy');

$model = FD::model('Followers');

if ($filter == 'following') {
	$results = $model->getFollowing($my->id, $options);
} else {
    $results = $model->getFollowers($my->id, $options);
}

require(JModuleHelper::getLayoutPath('mod_easysocial_followers', $layout));
