<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );
$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
if (!JFile::exists($path)) {
    return;
}

// require ED's engine
require_once ($path);

$config = ED::config();


$component = JFactory::getApplication()->input->get('option', '', 'cmd');
if ($component == 'com_easydiscuss' && !$params->get('show_on_easydiscuss', false)) {
    return;
}

// load ed stuff
ED::init();


$modToolbar = array();
$modToolbar['showToolbar'] = true;
$modToolbar['showHeader'] = $params->get('show_header', 0);
$modToolbar['showSearch'] = $params->get('show_search', 1);
$modToolbar['showRecent'] = $params->get('show_recent', 1);
$modToolbar['showTags'] = $params->get('show_tags', 1);
$modToolbar['showCategories'] = $params->get('show_categories', 1);
$modToolbar['showUsers'] = $params->get('show_users', 1);
$modToolbar['showBadges'] = $params->get('show_badges', 1);
$modToolbar['showSettings'] = $params->get('show_settings', 1);
$modToolbar['showLogin'] = $params->get('show_login', 1);
$modToolbar['showConversation'] = $params->get('show_conversations', 1);
$modToolbar['showNotification'] = $params->get('show_notifications', 1);
$modToolbar['processLogic'] = false;
$modToolbar['renderToolbarModule'] = false;


// since we are loading frontend lib, we will need to load EasyDiscuss frontend language.
JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

require( JModuleHelper::getLayoutPath( 'mod_easydiscuss_toolbar' ) );
