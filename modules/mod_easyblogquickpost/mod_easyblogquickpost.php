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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

$my = JFactory::getUser();
$config = EB::config();

if (!JFile::exists($engine) || $my->guest || !$config->get('main_microblog')) {
    return;
}

require_once($engine);

// Ensure that all script are loaded
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

require(JModuleHelper::getLayoutPath('mod_easyblogquickpost'));

