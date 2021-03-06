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

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
    return;
}
require_once ($path);
require_once dirname( __FILE__ ) . '/helper.php';


ED::init();

JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

$posts = modRecentDiscussionsHelper::getData($params);
$config = ED::config();

require(JModuleHelper::getLayoutPath('mod_easydiscuss_recentdiscussions'));
