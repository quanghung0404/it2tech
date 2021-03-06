<?php
/**
 * @version		$Id: mod_fpss_stats.php 2089 2012-10-30 11:13:27Z lefteris.kavadas $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');
jimport('joomla.application.component.model');

require_once (JPATH_ADMINISTRATOR.'/components/com_fpss/helpers/legacy.php');
FPSSHelperLegacy::setup();

JLoader::register('FPSSHelperHTML', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'.DS.'helpers'.DS.'html.php');

$mainframe = JFactory::getApplication();
$componentParams = JComponentHelper::getParams('com_fpss');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base(true).'/modules/mod_fpss_stats/tmpl/css/style.css');

if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	JHtml::_('behavior.framework');
}
else
{
	JHTML::_('behavior.mootools');
}

if(version_compare(JVERSION, '3.0', '<'))
{
	$document->addScript(JURI::base(true).'/components/com_fpss/js/jquery.min.js');
}
$document->addScript(JURI::base(true).'/components/com_fpss/js/highcharts.js');
$document->addScript(JURI::base(true).'/components/com_fpss/js/fpss.js');

FPSSModel::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fpss'.DS.'models');
$model = FPSSModel::getInstance('slides', 'FPSSModel');

if ($params->get('fpssCache', 1) && $mainframe->getCfg('caching'))
{
	$cache = JFactory::getCache('mod_fpss_stats');
	$cache->setLifeTime($params->get('cache_time', 900));
	$chart = $cache->call(array(
		$model,
		'stats'
	));
	$categoryFilter = $cache->call(array(
		'FPSSHelperHTML',
		'getCategoryFilter'
	), 'fpssModuleCategory');
}
else
{
	$model->setState('catid', 0);
	$model->setState('timeRange', 0);
	$model->setState('limit', 0);
	$chart = $model->stats();
	$categoryFilter = FPSSHelperHTML::getCategoryFilter('fpssModuleCategory');
}
foreach ($chart->categories as &$category)
{
	$category = addslashes($category);
	$category = "'".$category."'";
}

$document->addScriptDeclaration("
	var FPSSChartData = [".implode(',', $chart->data)."];
	var FPSSChartCategories = [".implode(',', $chart->categories)."];
");
$document->addScriptDeclaration("
	\$FPSS(document).ready(function(){
		loadFPSSChart('fpssChart', FPSSChartData,'".JText::_('FPSS_STATISTICS')."' ,'".JText::_('FPSS_HITS')."', FPSSChartCategories);
	});
");

require (JModuleHelper::getLayoutPath('mod_fpss_stats'));
