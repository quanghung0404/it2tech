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

$class 	= ''; 

// Note. It is important to remove spaces between elements.
if ($item->id == $active_id) {
	$class .= ' current';
}
$title = $item->anchor_title ? 'title="'.$item->anchor_title.'" ' : '';
if ($item->menu_image) {
		$item->params->get('menu_text', 1 ) ? 
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" /><span class="image-title">'.$item->title.'</span> ' :
		$linktype = '<img src="'.$item->menu_image.'" alt="'.$item->title.'" />';
} else { 
	$linktype = $item->title;
}

switch ($item->browserNav) :
	default:
	case 0:
?>
	<a <?php echo ($class)? 'class="'.$class.'"' : ''; ?> href="<?php echo $item->flink; ?>" <?php echo $title; ?>>
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
	<?php
		break;
	case 1:
		// _blank
?>
		<a class="<?php echo $class; ?>" href="<?php echo $item->flink; ?>" target="_blank" <?php echo $title; ?>>
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
<?php
		break;
	case 2:
		// window.open
		$attrib = 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,';
?>
		<a class="<?php echo $class; ?>" href="<?php echo $item->flink; ?>" onclick="window.open(this.href,'targetWindow','<?php echo $attrib;?>');return false;" <?php echo $title; ?>>
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
<?php
		break;
endswitch;
