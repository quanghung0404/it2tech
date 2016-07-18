<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2BreadcrumbsBlock">
	<?php
	$output = '';
	if ($params->get('home')) {
		$output .= '<span class="bcTitle">'.JText::_('K2_YOU_ARE_HERE').'</span>';
		$output .= '<a href="'.JURI::root().'">'.$params->get('home',JText::_('K2_HOME')).'</a>';
		if (count($path)) {
			foreach ($path as $link) {
				$output .= '<span class="bcSeparator">'.$params->get('seperator','&raquo;').'</span>'.$link;
			}
		}
		if($title){
			$output .= '<span class="bcSeparator">'.$params->get('seperator','&raquo;').'</span>'.$title;
		}
	} else {
		if($title){
			$output .= '<span class="bcTitle">'.JText::_('K2_YOU_ARE_HERE').'</span>';
		}
		if (count($path)) {
			foreach ($path as $link) {
				$output .= $link.'<span class="bcSeparator">'.$params->get('seperator','&raquo;').'</span>';
			}
		}
		$output .= $title;
	}

	echo $output;
	?>
</div>
