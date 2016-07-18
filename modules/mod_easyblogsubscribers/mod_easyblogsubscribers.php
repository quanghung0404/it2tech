<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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

$input = EB::request();
$option = $input->get('option', '', 'cmd');
$view = $input->get('view', '', 'cmd');
$id = $input->get('id', 0, 'int');

// Allowed views
$allowed = array('entry', 'categories', 'teamblog');

if ($option != 'com_easyblog') {
	return;
}

if (!in_array($view, $allowed)) {
	return;
}

if (!$id) {
	return;
}

$type = 'entry';

if ($view == 'teamblog') {
	$type = 'teamblog';
}

if ($view == 'categories') {
	$type = 'category';
}

// This module will require the main script file since composer needs to be loaded
EB::init('site');

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

// Get a list of subscribers
$model = EB::model('Subscription');
$subscribers = $model->getSubscribers($type, $id);

// Determines if the current user is subscribed
$subscribed = false;
$my = JFactory::getuser();

// Compile the return url
$return = base64_encode(JRequest::getUri());

if (!$my->guest) {
    $subscription = EB::table('Subscriptions');
    $exists = $subscription->load(array('uid' => $id, 'utype' => $type, 'user_id' => $my->id));

    if ($exists) {
        $subscribed = $subscription->id;
    }
}

require(JModuleHelper::getLayoutPath('mod_easyblogsubscribers'));
