<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2015 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

// Setup the modal
if ($modal) {
	JHtml::_('behavior.modal', 'a.feedback-modal');
}

JFactory::getDocument()->addStyleDeclaration('
#feedback-'.$module->id.' a {
	background-color: '.htmlspecialchars($bg_color).';
	border: solid 2px '.htmlspecialchars($border_color).';
	color: '.htmlspecialchars($text_color).';
	font-size: '.(int) $font_size.'px;
}
')
?>
<div id="feedback-<?php echo $module->id; ?>" class="feedback-container feedback-position-<?php echo $position; ?>">
	<?php echo JHtml::_('link', JRoute::_($form_url), $text, $attribs); ?>
</div>