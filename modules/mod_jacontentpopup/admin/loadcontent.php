<?php
/**
 * ------------------------------------------------------------------------
 * JA Content Popup Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));

if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
	//Apache CGI
	$_SERVER['PHP_SELF'] = rtrim(dirname(dirname(dirname(dirname($_SERVER['PHP_SELF'])))), '/\\').'/index.php';	
	$_SERVER['SCRIPT_NAME'] = rtrim(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/\\').'/index.php';
} else {
    //Others
	$_SERVER['SCRIPT_NAME'] = rtrim(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))), '/\\').'/index.php';	
}

require_once JPATH_BASE.'/includes/defines.php';
require_once JPATH_BASE.'/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$mainframe = JFactory::getApplication('site');

// Initialise the application.
$mainframe->initialise();

jimport('joomla.application.component.model');
jimport('joomla.application.module.helper'); 
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$language = JFactory::getLanguage();
// add force load current language.
if ($lag = JRequest::getVar('lang')) {
	$db		= JFactory::getDbo();
	$query	= $db->getQuery(true);
	$query->select('l.`lang_code`');
	$query->from('#__languages AS l');
	$query->where('l.`sef` = "'.$lag.'"');
	$query->where('l.`published` = 1');
	$db->setQuery($query);
	$items = $db->loadRow();
	if ($items != NULL) $language->setLanguage($items[0]);
}
$language->load('mod_jacontentpopup');

$jacppath = dirname(dirname(__FILE__));

require_once ($jacppath . '/helpers/helper.php');
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

if (version_compare(JVERSION, '3.0', 'ge')){
	JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
} else {
	JModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
}

$adapters = JFolder::files($jacppath . '/helpers/adapter', '.php');
if (count($adapters)) {
    foreach ($adapters as $adapter) {
		require_once ($jacppath . '/helpers/adapter/' . $adapter);
    }
}

$module_position = JRequest::getVar('position');
$module_modulesid = JRequest::getInt('modulesid');

$modules = JModuleHelper::getModules($module_position);
foreach ($modules AS $m){
	if($m->id == $module_modulesid){
		$module  = $m;
	}
}

$params = new JRegistry;
$params->loadString($module->params, 'JSON');

$helper = new modJaNewsHelper($module,$params);
//$source = 'JANewsHelper';
$source = $params->get('source', 'JANewsHelper');

/*Get layout*/
$layout = $params->get('layout', 'default');
/*Group by categories*/
$group  = $params->get('group_categories', 0);
$show_popup		= $params->get('show_popup',1);
$show_titles	= $params->get('show_titles', 1);
$show_introtext = $params->get('show_introtext', 1);

$jacphelper =  new $source();
$using_ajax = 1;
$lists = null;

if ($group == 1){
	$lists = $jacphelper->getCategories($params, $helper);
}else {
	$lists = $jacphelper->getList($params, $helper);
}

$pagination = null;

require JModuleHelper::getLayoutPath('mod_jacontentpopup', $layout . '_item');
?>