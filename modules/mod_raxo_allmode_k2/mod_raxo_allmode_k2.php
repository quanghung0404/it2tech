<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


defined('_JEXEC') or die;

// Check the type of display page
if ($params->def('hide_option', 0)) {
	if (JFactory::getApplication()->input->get('option') == 'com_k2' && JFactory::getApplication()->input->get('view') == 'item') {
		return;
	}
}

// Include the helper functions only once
require_once __DIR__ . '/helper.php';

// Module cache
$cacheparams = new stdClass;
$cacheparams->cachemode		= ($params->get('ordering') == 'random') ? 'safeuri' : 'itemid';
$cacheparams->class			= 'ModRAXO_Allmode_K2';
$cacheparams->method		= 'getList';
$cacheparams->methodparams	= $params;
$cacheparams->modeparams	= array('id' => 'int', 'Itemid' => 'int');

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);
if (!count($list)) {
	return;
}

// Template name
$tmpl			= $params->def('layout', 'allmode-default');
$tmpl_name		= explode(':', $tmpl);
$tmpl_name		= $tmpl_name[1];

?>
<div class="allmode-box <?php echo $tmpl_name.' '.htmlspecialchars($params->get('moduleclass_sfx')); ?>">
<?php

// Block name
$blockname_text	= trim($params->get('name_text'));
$blockname_link	= trim($params->get('name_link'));
if ($blockname_text && $blockname_link) {
	echo '<h3 class="allmode-name"><a href="'.$blockname_link.'"><span>'.$blockname_text.'</span></a></h3>';
} elseif ($blockname_text) {
	echo '<h3 class="allmode-name"><span>'.$blockname_text.'</span></h3>';
}

// TOP items
$count_top = (int) $params->get('count_top', 2);
$toplist = array();
if ($count_top) {
	$toplist	= array_slice($list, 0, $count_top);
	$list		= array_slice($list, $count_top);
}

// Output template
require JModuleHelper::getLayoutPath('mod_raxo_allmode_k2', $tmpl);

// Show all link
$showall_text	= trim($params->get('showall_text'));
$showall_link	= trim($params->get('showall_link'));
if ($showall_text && $showall_link) {
	echo '<div class="allmode-showall"><a href="'.$showall_link.'">'.$showall_text.'</a></div>';
} elseif ($showall_text) {
	echo '<div class="allmode-showall">'.$showall_text.'</div>';
}
?>
</div>