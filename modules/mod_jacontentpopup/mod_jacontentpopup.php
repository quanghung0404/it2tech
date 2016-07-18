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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


//INCLUDING ASSET
require_once(dirname(__FILE__).'/assets/behavior.php');
JHTML::_('behavior.framework', true);
JHTML::_('behavior.modal');
JHTML::_('JABehavior.jquery');
//JHTML::_('JABehavior.jqueryeasing');

include_once(dirname(__FILE__).'/assets/asset.php');


$jacppath = dirname(__FILE__);
$jacpurl = JURI::base(true) . '/modules/mod_jacontentpopup';

require_once ($jacppath . '/helpers/helper.php');
require_once JPATH_SITE . '/components/com_content/helpers/route.php';

if (version_compare(JVERSION, '3.0', 'ge')) {
	JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models');
} else {
	JModel::addIncludePath(JPATH_SITE . '/components/com_content/models');
}
$source = $params->get('source', 'JANewsHelper');
if($source=='JANewsHelper'){
	require_once ($jacppath . '/helpers/adapter/content.php');
}else if($source=='JAK2CHelper'){
	require_once ($jacppath . '/helpers/adapter/k2.php');
}

$mainframe = JFactory::getApplication();
$document  = JFactory::getDocument();

$helper = new modJaNewsHelper($module,$params);


/*Get layout*/
$layout = $params->get('layout', 'default');
/*Group by categories*/
$group  = $params->get('group_categories', 0);

$show_titles	= $params->get('show_titles', 1);
$show_introtext = $params->get('show_introtext', 1);
$anim_type		= $params->get('anim_type', 'jafade');
$show_nav_control = $params->get('show_nav_control', 0);
$show_paging 	= $params->get('show_paging', 1);
$show_popup		= $params->get('show_popup',1);
$jacphelper =  new $source();

$using_ajax = null;
$lists = null;

if ($group == 1){
	$lists = $jacphelper->getCategories($params,$helper);
}else {
	$lists = $jacphelper->getList($params,$helper);
}

if($lists){
	if($show_popup){
		$document->addScriptDeclaration('
			var jabaseurl = "'.JURI::base().'";
			jQuery(document).ready(function($){
				
				jarefs = function(modulesid){
					var refs = [];
					$(\'#ja-cp-\'+modulesid+\' .ja-cp-group.active\').find("a").each(function(){
						ref = $(this).attr("data-ref");
						if(ref && $(\'.\'+ref)){
						    refs.push(ref);
						}
					});
					refs = $.unique(refs);
					return refs;
				};

				var jascroller = function(){
					if((\'ontouchstart\' in window) || window.DocumentTouch && document instanceof DocumentTouch){

						var activeview = $(\'.yoxview_ja_slideshow:visible\');
						if(!activeview.length){
							return;
						}

						var panel = activeview.find(\'.yoxview_mediaPanel\'),
							ifm = panel.find(\'iframe:visible\');

						var iscroll = ifm.data(\'iscroll\') || false;
						if (iscroll) {
							iscroll.refresh();
						}
					}
				};

				var cleanup = function(){
					if((\'ontouchstart\' in window) || window.DocumentTouch && document instanceof DocumentTouch){

						$(\'#yoxview_popupWrap iframe\').each(function(){
							var iscroll = $(this).data(\'iscroll\') || false;
							if (iscroll) {
								iscroll.destroy();
							}
						});
					}
				};
				
				jayoxview'.$module->id.' = function(refs,modulesid){	    	
					$(\'#ja-cp-\'+modulesid+\' .\'+refs).yoxview({
						textLinksSelector:\'[data-ref="\'+refs+\'"]\',
						defaultDimensions: { iframe: { width: '.$params->get('iframewidth','500').',height:'.$params->get('iframeheight','500').' }},
						skin:\'ja_slideshow\',
						onOpen: jascroller,
						onSelect: jascroller,
						onClose: cleanup
					});
				};
				    
				jaloadyoxview = function(modulesid){
					refs = jarefs(modulesid);
					for(i=0;i<refs.length;i++){
						eval(eval("jayoxview"+modulesid)(refs[i],modulesid));	
					}
				};
				jQuery(document).ready(function($){
					jaloadyoxview("'.$module->id.'");	
				});	
			});
		');
	}
	$document->addScriptDeclaration('
			jQuery(document).ready(function($){
			    $(\'#ja-cp-' . $module->id . '\').jacp({
			    	baseurl: \'' . JURI::base() . '\',
			    	animation: \'' . $anim_type . '\',
			    	navButtons: \'' . $show_nav_control . '\'
			    });	
			});	
		');
	$pagination = $helper->getPagination($params, $module);
	
	require JModuleHelper::getLayoutPath('mod_jacontentpopup', $layout);
} else {
	return '';
}