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

// @task: Include module's helper file.
require_once($engine);

// Ensure that all script are loaded in the site.
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$url = rtrim(JURI::root(), '/');
$document = JFactory::getDocument();
$document->addStyleSheet($url . '/components/com_easyblog/assets/css/module.css');
$document->addStyleSheet($url . '/modules/mod_easyblogticker/assets/styles/style.css');
$document->addStyleSheet($url . '/modules/mod_easyblogticker/assets/styles/ticker-style.css');

$config = EB::config();
$model = EB::model('Blog');
$categoryIds = $params->get('catid');
$count = $params->get('count');

if ($categoryIds) {
	$categories	= explode(',', $categoryIds);
	$entries = $model->getBlogsBy('category', $categories, 'latest', $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, false);
} else {
	$entries = $model->getBlogsBy('', '', 'latest', $count, EBLOG_FILTER_PUBLISHED, null, false, array(), false, false, false);
}

// If there's nothing to show at all, don't even display a box.
if (!$entries) {
	return;
}

$items = array();

foreach ($entries as $entry) {

	$row = EB::post($entry->id);
	$row->bind($entry);

	$row->author = EB::user($row->created_by);
	$row->date = EB::date($row->created)->toFormat($config->get('layout_dateformat', JText::_('DATE_FORMAT_LC1')));

	$items[] = $row;
}

require(JModuleHelper::getLayoutPath('mod_easyblogticker'));
