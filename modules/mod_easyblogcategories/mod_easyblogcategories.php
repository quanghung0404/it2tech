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

if (!JFile::exists($engine)) {
    return;
}

require_once($engine);
require_once(__DIR__ . '/helper.php');

// Ensure that all script are loaded
EB::init('module');

// Attach modules stylesheet
EB::stylesheet('module')->attach();

$layoutType = $params->get('layouttype');
$sort = $params->get('order', 'latest');
$count = (INT)trim($params->get('count', 0));
$hideEmptyPost = $params->get('hideemptypost', '0');
$onlyTheseCatIds = $params->get('catid', '');

$filterCats = array();

if (!empty($onlyTheseCatIds)) {
    $filterStr = '';
    $filterCats = explode(',', $onlyTheseCatIds);
}

// Get all the parent categories
$model = EB::model('Category');
$results = $model->getCategories($sort, $hideEmptyPost, $count, $filterCats, false);

// For toggle-able layout
if ($layoutType == 'toggle') {

    // // Now we get the child categories for each parent
    $top_level = 1;
    $categories = array();
    modEasyBlogCategoriesHelper::getChildCategories($results, $params, $categories, ++$top_level);

    require(JModuleHelper::getLayoutPath('mod_easyblogcategories'));

} else {

    $app = JFactory::getApplication();
    $top_level = 1;

    $view = $app->input->get('view');
    $layout = $app->input->get('layout');
    $selected = '';

    if ($view=='categories' && $layout=='listings') {
        $selected = $app->input->get('id');
    }

    $categories = array();

    // Get nested child category
    modEasyBlogCategoriesHelper::getChildCategories($results, $params, $categories, ++$top_level);

    require(JModuleHelper::getLayoutPath('mod_easyblogcategories', 'nested'));
}




