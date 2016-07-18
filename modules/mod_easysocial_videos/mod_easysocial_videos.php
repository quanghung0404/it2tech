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

jimport('joomla.filesystem.file');

// Include main engine
$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';

if (!JFile::exists($file)) {
    return;
}

// Include the engine file.
require_once($file);

// Check if Foundry exists
if (!ES::exists()) {
	ES::language()->loadSite();
	echo JText::_('COM_EASYSOCIAL_FOUNDRY_DEPENDENCY_MISSING');
	return;
}

// Load up the module engine
$modules = ES::modules('mod_easysocial_videos');

// We need these packages
$modules->loadComponentStylesheets();
$modules->addDependency('css');

// Get the layout to use
$layout = $params->get('layout', 'default');
$suffix = $params->get('suffix', '');

// Module options
$sorting = $params->get('sorting', 'created');
$limit = $params->get('limit', 20);
$filter = $params->get('filter', 'all');
$source = $params->get('source', 'all');

// Determines if we should filter videos by specific category
$category = $params->get('category', null);

$options = array('sort' => $sorting, 'limit' => $limit, 'category' => $category);

// We need to filter by source types if necessary
if ($source != 'all') {
    $options['source'] = $source;
}

// We need to apply specific filters
if ($filter != 'all') {
    $options['filter'] = $filter;
}

$model = ES::model('Videos');
$videos = $model->getVideos($options);

// If there are no videos, do not display anything.
if (!$videos) {
    return;
}

require(JModuleHelper::getLayoutPath('mod_easysocial_videos', $layout));