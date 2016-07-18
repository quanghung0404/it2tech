<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Load template framework
if (!defined('JSN_PATH_TPLFRAMEWORK')) {
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/jsntplframework.defines.php';
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/loader.php';
}

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();
$jsn_utils   = JSNTplUtils::getInstance();

// Note. It is important to remove spaces between elements.
?>
<span class="jsn-menu-toggle"><?php echo JText::_('JSN_TPLFW_TOGGLE_MENU'); ?></span>
<ul class="<?php echo $class_sfx;?>"<?php
	$tag = '';
	if ($params->get('tag_id')!=NULL) {
		$tag = $params->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
$count 			= 1;
$menuCount 		= count($list);
$varLastItem 	= 0;
$flag 			= false;
$flag_last 		= false;

foreach ($list as $i => &$item) :
	$class = '';
	if ($item->id == $active_id) {
		$class .= 'current ';
	}

	if (in_array($item->id, $path)) {
		$class .= 'active ';
	}

	if ($item->deeper) {
		$class .= 'parent ';
	}

	if( ($count == 1) || ($flag == true) ) {
		$class .= 'first ';
	}

	if($count == $menuCount || $item->shallower || $jsn_utils->isLastMenu($item)) {
		$class .= 'last ';
	}

	// Check if scrolling effect is enabled for this menu item
	if ($item->params->get('menu-anchor_css', null) != null)
	{
		$suffixes = preg_split('/\s+/', trim($item->params->get('menu-anchor_css')));

		if (in_array('jsn-scroll', $suffixes) AND preg_match('/#[A-Za-z0-9\-_:\.]+$/', $item->link))
		{
			$class .= 'jsn-scroll' . ' ';

			// State that scrolling effect is enabled
			isset($scrolling_effect_enabled) OR $scrolling_effect_enabled = true;
		}
	}

	// Icon menu
	if ($item->anchor_css) {
		$class .= $item->anchor_css.' ';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li '.$class.'>';
	$flag = false;
	$item->title = html_entity_decode($item->title);
	// Render the menu item.
	switch ($item->type) {
		case 'separator':
		case 'component':
			require JModuleHelper::getLayoutPath('mod_menu', 'default_'.$item->type);
			break;

		default:
			require JModuleHelper::getLayoutPath('mod_menu', 'default_url');
			break;
	}

	// The next item is deeper.
	if ($item->deeper) {
		$flag_last = true;
		if ($item->level==1) {
			echo '<span class="jsn-menu-toggle"></span>';
			echo '<ul>';
			$level_1_id = $item->id;
		}
		else echo '<ul>';
		$flag = true;
	}
	// The next item is shallower.
	else if ($item->shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
	$count ++;
endforeach;
?></ul>
<?php
if ( ! defined('JSN_SCROLLING_EFFECT_ENABLED') AND isset($scrolling_effect_enabled) AND $scrolling_effect_enabled)
{
	define('JSN_SCROLLING_EFFECT_ENABLED', 1);

	$tpl_params = $jsn_utils->getTemplateParameters();
?>
<script type="text/javascript">
	typeof JSNUtils == 'undefined' || JSNUtils.initScrollToContent(<?php echo @json_encode($tpl_params->get('menuSticky')); ?>);
</script>
<?php
}
?>