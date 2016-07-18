<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

$path = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

if (!JFile::exists($path)) {
    return;
}

require_once ($path);
//ED::loadStylesheet("module", "mod_easydiscuss_your_discussions");

$my = ED::user();
$config = ED::config();

$model= ED::model('Posts');

$count = $params->get('count', 5);

$posts = array();
$posts = $model->getPostsBy('user', $my->id, 'latest', '0', '', '', $count);

if (!$posts) {
	return;
}

$posts = ED::modules()->format($posts);

require(JModuleHelper::getLayoutPath('mod_easydiscuss_your_discussions'));



