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

// Get joomla's app
$app = JFactory::getApplication();

// Test for installation requests.
jimport('joomla.filesystem.file');

// Installation file
$file = JPATH_ROOT . '/tmp/easyblog.installation';

// Cancel setup file
$cancelSetup = $app->input->get('cancelSetup', false, 'bool');

if ($cancelSetup && JFile::exists($file)) {

    // Delete the tmp file
    JFile::delete($file);

    // Redirect the user back to EasyBlog
    return $app->redirect('index.php?option=com_easyblog');
}

// If manual installation is invoked, we need to create the installer file
$install = $app->input->get('setup', false, 'bool');

if ($install) {

    $obj = new stdClass();
    $obj->new = false;
    $obj->step = 1;
    $obj->status = 'installing';

    $contents = json_encode($obj);

    JFile::write($file, $contents);
}


// Check if there's a file initiated for installation
$installCompleted = $app->input->get('active') == 'complete';

if (JFile::exists($file) || $installCompleted) {
    require_once(dirname(__FILE__) . '/setup/bootstrap.php');
    exit;
}

// Load up EasyBlog's framework
require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');


// Process ajax calls
EB::ajax()->process();

// Test for user access
if(!JFactory::getUser()->authorise('core.manage', 'com_easyblog')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = JControllerLegacy::getInstance('easyblog');
$task = $app->input->get('task');

$controller->execute($task);
$controller->redirect();