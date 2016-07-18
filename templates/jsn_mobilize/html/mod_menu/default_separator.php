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
// No direct access.
defined('_JEXEC') or die;
global $jsn_richmenu_separator;
$menus		= JFactory::getApplication()->getMenu();
$menu 		= $menus->getActive();
$class 		= '';

// Note. It is important to remove spaces between elements.
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ?
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
}
else { $linktype = $item->title;
}

?><a href="javascript: void(0)">
	<span>
		<?php
		if ($item->anchor_title) {
			echo '<span class="jsn-menutitle">'.$linktype.'</span>';
			echo '<span class="jsn-menudescription">'.$item->anchor_title.'</span>';
		} else {
			echo $linktype;
		}
		?>
	</span>
  </a>
