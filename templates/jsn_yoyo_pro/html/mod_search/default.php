<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_search
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
if (!defined('JSN_PATH_TPLFRAMEWORK')) {
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/jsntplframework.defines.php';
	require_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/loader.php';
}

JHtml::_('behavior.framework');
JHtml::addIncludePath(JPATH_SITE . '/components/com_search/models');

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();
$jsnUtils   = JSNTplUtils::getInstance();

// Load the smart search component language file.
$lang = JFactory::getLanguage();
$lang->load('com_finder', JPATH_SITE);
$suffix = $params->get('moduleclass_sfx');
?>
<?php if ($jsnUtils->isJoomla3()): ?>
	<div class="search<?php echo $moduleclass_sfx ?>">
		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="form-inline">
			<?php
				$output = '<label for="mod-search-searchword" class="element-invisible">' . $label . '</label> ';
				$output .= '<input name="searchword" id="mod-search-searchword" maxlength="' . $maxlength . '"  class="inputbox search-query" type="text" size="' . $width . '" value="' . $text . '"  onblur="if (this.value==\'\') this.value=\'' . $text . '\';" onfocus="if (this.value==\'' . $text . '\') this.value=\'\';" />';

				if ($button) :
					if ($imagebutton) :
						$btn_output = ' <input type="image" value="' . $button_text . '" class="button" src="' . $img . '" onclick="this.form.searchword.focus();"/>';
					else :
						$btn_output = ' <button class="button btn btn-primary" onclick="this.form.searchword.focus();">' . $button_text . '</button>';
					endif;

					switch ($button_pos) :
						case 'top' :
							$output = $btn_output . '<br />' . $output;
							break;

						case 'bottom' :
							$output .= '<br />' . $btn_output;
							break;

						case 'right' :
							$output .= $btn_output;
							break;

						case 'left' :
						default :
							$output = $btn_output . $output;
							break;
					endswitch;

				endif;

				echo $output;
			?>
			<input type="hidden" name="task" value="search" />
			<input type="hidden" name="option" value="com_search" />
			<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
		</form>
	</div>

<?php else : ?>
<form action="<?php echo JRoute::_('index.php');?>" method="post">
	<div class="search">
		<?php
			$text			= htmlspecialchars($params->get('text', ''));
			$label			= htmlspecialchars($params->get('label', ''));
			
			$output = '<label for="mod-search-searchword">'.$label.'</label><input name="searchword" id="mod-search-searchword" maxlength="'.$maxlength.'"  class="inputbox" type="text" size="'.$width.'" value="'.$text.'"  onblur="if (this.value==\'\') this.value=\''.$text.'\';" onfocus="if (this.value==\''.$text.'\') this.value=\'\';" />';

			if ($button) :
				if ($imagebutton) :
					$button = '<input type="image" value="'.$button_text.'" class="button" src="'.$img.'" onclick="this.form.searchword.focus();"/>';
				else :
					$button = '<input type="submit" value="'.$button_text.'" class="button" onclick="this.form.searchword.focus();"/>';
				endif;
			endif;

			switch ($button_pos) :
				case 'top' :
					$button = '<p align="center">'.$button.'</p>';
					$output = $button.$output;
					break;

				case 'bottom' :
					$button = '<p align="center">'.$button.'</p>';
					$output = $output.$button;
					break;

				case 'right' :
					$output = $output.$button;
					break;

				case 'left' :
				default :
					$output = $button.$output;
					break;
			endswitch;

			echo $output;
		?>
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="Itemid" value="<?php echo $mitemid; ?>" />
	</div>
</form>

<?php endif ?>
