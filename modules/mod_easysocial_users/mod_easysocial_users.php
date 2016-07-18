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

// Load up the module engine
$modules = FD::modules('mod_easysocial_users');

// We need these packages
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();
$modules->addDependency('css', 'javascript');

// Get the layout to use.
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');


// Get the layout to use.
$model = FD::model('Users');
$options = array( 'ordering' => 'a.' . $params->get( 'ordering' , 'registerDate' ) , 'direction' => $params->get( 'direction' , 'desc' ) , 'limit' => $params->get( 'total' , 10 ) );

// Check filter type
if ($params->get('filter' , 'recent' ) == 'online') {
	$options[ 'login' ]	= true;
	$options[ 'frontend' ] = true;
}

if ($params->get('profileId')) {

	$profileId = (int) $params->get('profileId');

	$options['profile'] = $profileId;
}


// Determine if admins should be included in the user's listings.
$config = FD::config();
$admin = $config->get('users.listings.admin');

$options['includeAdmin'] = $admin ? true : false;

// Check if we should only include user's with avatar.
if ($params->get('hasavatar', false) == true) {
	$options['picture']	= true;
}

// we only want published user.
$options[ 'published' ]	= 1;

// exclude users that blocked the current logged in user
$options['excludeblocked'] = 1;

$inclusion = trim($params->get('user_inclusion'));

if ($inclusion) {
    $options['inclusion'] = explode(',', $inclusion);
}

$result = $model->getUsers($options);
$users = array();

if ($result) {
	foreach ($result as $row) {
		$users[] = FD::user($row->id);
	}
}

require(JModuleHelper::getLayoutPath('mod_easysocial_users', $layout));
