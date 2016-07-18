<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
    return;
}

require_once($engine);

// This module will require the main script file since composer needs to be loaded
EB::init('site');

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$width  = $params->get('avatarwidth', '50');
$height = $params->get('avatarheight', '50');
$width  = empty( $width ) ? '50' : $width;
$height  = empty( $height ) ? '50' : $height;

$model = EB::model('TeamBlogs');
$teams = $model->getTeamBlogs();

require(JModuleHelper::getLayoutPath('mod_easyblogteamblogs'));
