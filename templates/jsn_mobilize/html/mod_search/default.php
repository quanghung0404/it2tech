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
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post">
	<div class="search">
		<?php
		$text = htmlspecialchars($params->get('text', ''));
		$label = htmlspecialchars($params->get('label', ''));

		$output = '<label for="mod-search-searchword">' . $label . '</label><input name="searchword" id="mod-search-searchword" maxlength="' . $maxlength . '"  class="inputbox" type="text" size="' . $width . '" value="' . $text . '"  onblur="if (this.value==\'\') this.value=\'' . $text . '\';" onfocus="if (this.value==\'' . $text . '\') this.value=\'\';" />';

		if ($button) :
			if ($imagebutton) :
				$button = '<input type="image" value="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
			else :
				$button = '<input type="submit" value="' . $button_text . '" class="button" onclick="this.form.searchword.focus();"/>';
			endif;
		endif;

		switch($button_pos) :
			case 'top' :
				if (!empty($button))
				{
					$button = '<p align="center">' . $button . '</p>';
					$output = $button . $output;
				}
				break;

			case 'bottom' :
				if (!empty($button))
				{
					$button = '<p align="center">' . $button . '</p>';
					$output = $output . $button;
				}
				break;

			case 'right' :
				if (!empty($button))
				{
					$output = $output . $button;
				}
				break;

			case 'left' :
			default :
				if (!empty($button))
				{
					$output = $button . $output;
				}

				break;
		endswitch;

		echo $output;
		?>
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="option" value="com_search" />
		<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</div>
</form>
