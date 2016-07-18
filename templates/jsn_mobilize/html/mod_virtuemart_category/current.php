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
//JHTML::stylesheet ( 'menucss.css', 'modules/mod_virtuemart_category/css/', false );
$ID = str_replace('.', '_', substr(microtime(true), -8, 8));
?>

<ul class="VMmenu <?php echo $class_sfx ?>" id="<?php echo "VMmenu".$ID ?>" >
<?php foreach ($categories as $category) {
		 $active_menu = 'class="VmClose"';
		$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
		$cattext = $category->category_name;
		//if ($active_category_id == $category->virtuemart_category_id) $active_menu = 'class="active"';
		if (in_array( $category->virtuemart_category_id, $parentCategories)) $active_menu = 'class="VmOpen"';

		?>

<li <?php echo $active_menu ?>>
		<?php echo JHTML::link($caturl, $cattext);
		if ($category->childs) {
			?>
			<span class="VmArrowdown"> </span>
			<?php
		}
		?>
<?php if ($active_menu=='class="VmOpen"') {


	?>
	<ul class="menu">
	<?php
		foreach ($category->childs as $child) {

			$caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$child->virtuemart_category_id);
			$cattext = $child->category_name;
			?>

			<li>
				<?php echo JHTML::link($caturl, $cattext); ?>
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}
?>
</li>
<?php
	} ?>
</ul>
