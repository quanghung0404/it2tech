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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

$engine = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($engine)) {
	return;
}

require_once($engine);

// Ensure that all script are loaded
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

// @task: Load component's language file.
JFactory::getLanguage()->load('mod_easyblogarchive', JPATH_ROOT);

$model = EB::model( 'Archive' );
$year = $model->getArchiveMinMaxYear();
$catid = '?';

if (!$year) {
	return;
}

$currentMonth = (int) EB::date()->toFormat('%m');
$currentYear = (int) EB::date()->toFormat('%Y');

// @task: Get the count from the module parameters.
$count = $params->get('count', 0);

if (!empty($count)) {
	if (($year['maxyear'] - $year['minyear']) > $count) {
		$year['minyear'] = $year['maxyear'] - $count;
	}
}

//set default year
$defaultYear = JRequest::getVar('archiveyear', $year['maxyear'], 'REQUEST');

//set default month
$defaultMonth = JRequest::getVar('archivemonth', 0, 'REQUEST');

$menuitemid	= $params->get('menuitemid', '');
$menuitemid	= (!empty($menuitemid)) ? '&Itemid='.$menuitemid : '';

//@task: Get the parameter
$showEmptyMonth= $params->get('showempty', 1);
$showEmptyYear = $params->get('showemptyyear', false);

// @task: Get excluded/included categories
$excludeCats = $params->get('excatid', array());
$includeCats = $params->get('catid', array());

$includeCats = EB::getCategoryInclusion($includeCats);

$catUrl = '';

if (is_array($includeCats)) {
	foreach ($includeCats as $includeCat) {
		$catUrl .= '&category[]='.$includeCat;
	}
}

$filter = $params->get( 'filter', '');
$filterId = '';

// Get filter if any
if ($filter == 'blogger') {
	$filterId = $params->get('bloggerId', '');
} else {
	$filterId = $params->get('teamId', '');
}

$postCounts	= $model->getArchivePostCounts( $year['minyear'] , $year['maxyear'], $excludeCats, $includeCats , $filter, $filterId);

require(JModuleHelper::getLayoutPath('mod_easyblogarchive'));
