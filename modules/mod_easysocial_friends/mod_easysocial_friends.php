<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

$engine = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

jimport('joomla.filesystem.file');

if (!JFile::exists($engine)) {
	return;
}

// Include the engine file.
require_once($engine);

// Check if Foundry exists
if (!FD::exists()) {
	return;
}

// Ensure that this is a logged in user, otherwise there is no way to get their friends
$my = FD::user();

if ($my->guest) {
	return;
}

// Load up the module engine
$lib = FD::modules('mod_easysocial_friends');

// We need these packages
$lib->loadComponentScripts();
$lib->loadComponentStylesheets();
$lib->addDependency('css', 'javascript');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

// Retrieve the user's friends now.
$model = FD::model('Friends');
$limit = $params->get('limit', 6);
$options = array('limit' => $limit);

// Retrieve the list of friends
$friends = $model->getFriends($my->id, $options);

if (!$friends) {
	return;
}

require(JModuleHelper::getLayoutPath('mod_easysocial_friends', $layout));
