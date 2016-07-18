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

// If user is not logged in, skip this
if (JFactory::getUser()->guest) {
    return;
}

// Include main engine
$file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

if (!JFile::exists($file)) {
    return;
}

require_once($file);

FD::language()->loadSite();

if (!FD::exists()) {
    echo JText::_( 'COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING' );
    return;
}

$modules = FD::modules('mod_easysocial_profile_completeness');

// We need foundryjs here
$modules->loadComponentScripts();
$modules->loadComponentStylesheets();

// We need these packages
$modules->addDependency('css', 'javascript');

$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

$my = FD::user();

$total = $my->getProfile()->getTotalFields(SOCIAL_PROFILES_VIEW_EDIT);
$filled = (int) $my->completed_fields;

// Avoid using maintenance script to do this because it is possible that a site might have >1000 users
// Using this method instead so that every user will at least get executed once during login
// Won't happen on subsequent logins
if (empty($filled)) {
    $fields = FD::model('Fields')->getCustomFields(array(
        'profile_id' => $my->getProfile()->id,
        'data' => true,
        'dataId' => $my->id,
        'dataType' => SOCIAL_TYPE_USER,
        'visible' => SOCIAL_PROFILES_VIEW_EDIT,
        'group' => SOCIAL_FIELDS_GROUP_USER
    ));

    $args = array(&$my);
    $completedFields = FD::fields()->trigger('onProfileCompleteCheck', SOCIAL_FIELDS_GROUP_USER, $fields, $args);
    $table = FD::table('Users');
    $table->load(array('user_id' => $my->id));
    $table->completed_fields = count($completedFields);
    $table->store();

    $filled = $table->completed_fields;
}

$percentage = (int) (($filled / $total) * 100);

if ($percentage >= 100) {
    return;
}

require(JModuleHelper::getLayoutPath('mod_easysocial_profile_completeness', $layout));
