<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsndimensionmodal.php 16647 2012-10-03 10:06:41Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldJSNDimensionModal extends JFormField
{
	public $type = 'JSNDimensionModal';
	protected function getInput()
	{
		$msg        = JText::_('JSN_ALLOW_ONLY_DIGITS');
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration("
			var jsn_original_value = '';
			function JSNChangeInputModalDimension()
			{
				var itmes				= new Array();
				var tmp_modal_width 	= $('tmp_width_dimension_modal');
				var tmp_modal_height 	= $('tmp_height_dimension_modal');
				itmes[0] 				= tmp_modal_width.value;
				itmes[1] 				= tmp_modal_height.value;
				var jsn_dimension_modal			= $('".$this->id."');
				jsn_dimension_modal.value = '';
				jsn_dimension_modal.value = itmes.join(',');
			}

			function JSNGetInputValue(object)
			{
				jsn_original_value = object.value;
			}

			function JSNCheckNumberValue(object)
			{
				var patt;
				var msg;
				patt=/^[0-9]+$/;
				msg = '".$msg."';
				if(object.value != '' && !patt.test(object.value))
				{
					alert (msg);
					object.value = jsn_original_value;
					return;
				}
			}

			function setValueWidthHeight(str)
			{
				if (str != '')
				{
					var itmes = str.split(',');
					var tmp_modal_width 	= $('tmp_width_dimension_modal');
					var tmp_modal_height 	= $('tmp_height_dimension_modal');
					if (itmes[0] != undefined)
					{
						tmp_modal_width.value = itmes[0];
					}
					if (itmes[1] != undefined)
					{
						tmp_modal_height.value = itmes[1];
					}
				}
			}
		window.addEvent('domready', function() {
				setValueWidthHeight('".$this->value."');
			});
		");
		$html       = '';
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="jsn-text"';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$postfix	= (isset($this->element['postfix'])) ? '<span class="jsn-postfix">'.$this->element['postfix'].'</span>' : '';
		$html   = '<span style="line-height: 28px; float:left;">'.JText::_('MENU_OVERALL_MODAL_DIMENSION_WIDTH').': </span><span style="float:left; margin:0 5px; line-height: 28px;"><input type="text" onfocus="JSNGetInputValue(this);" onchange="JSNCheckNumberValue(this); JSNChangeInputModalDimension()" name="tmp_width_dimension_modal" id="tmp_width_dimension_modal" value="" size="5" /> px,</span>';
		$html  .= '<span style="line-height: 28px; float:left;">'.JText::_('MENU_OVERALL_MODAL_DIMENSION_HEIGHT').': </span><span style="float:left; margin:0 5px; line-height: 28px;"><input type="text" onfocus="JSNGetInputValue(this);" onchange="JSNCheckNumberValue(this); JSNChangeInputModalDimension()" name="tmp_height_dimension_modal" id="tmp_height_dimension_modal" value="" size="5" /> px</span>';
		$html  .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.$this->value.'"' .
		$class.$size.$disabled.$readonly.$maxLength.'/> '.$postfix;
		return $html;
	}
}