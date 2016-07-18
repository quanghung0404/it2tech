<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');
class JFormFieldJSNISDimensionThumb extends JFormField
{
	public $type = 'JSNISDimensionThumb';

	protected function getInput()
	{
		$msg        = JText::_('JSN_ALLOW_ONLY_DIGITS');
		$doc 		= JFactory::getDocument();
		$doc->addScriptDeclaration("
			var JSNISOriginalValue = '';
			function JSNISChangeInputThumbDimension()
			{
				var item		= new Array();
				var objWidth 	= document.id('tmp_width_thumb_dimension');
				var objHeight 	= document.id('tmp_height_thumb_dimension');
				item[0] 		= objWidth.value;
				item[1] 		= objHeight.value;
				var JSNISThumbDimensionElement		= document.id('".$this->id."');
				JSNISThumbDimensionElement.value 	= '';
				JSNISThumbDimensionElement.value 	= item.join(',');
			}

			function JSNISGetInputValue(object)
			{
				JSNISOriginalValue = object.value;
			}

			function JSNISCheckNumberValue(object)
			{
				var patt;
				var msg;
				patt = /^[0-9]+$/;
				msg  = '" . $msg . "';
				if(object.value != '' && !patt.test(object.value))
				{
					alert (msg);
					object.value = JSNISOriginalValue;
					return;
				}
			}

			function JSNISSetValueWidthHeight(str)
			{
				var width 	= document.id('tmp_width_thumb_dimension');
				var height 	= document.id('tmp_height_thumb_dimension');

				if (str != '')
				{
					var item = str.split(',');
					if (item[0] != undefined)
					{
						width.value = item[0];
					}

					if (item[1] != undefined)
					{
						height.value = item[1];
					}
				}
				else
				{
					width.value = 250;
					height.value = 150;
				}
			}
			window.addEvent('domready', function() {
				JSNISSetValueWidthHeight('".$this->value."');
			});
		");
		$html       = '';
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="jsn-text"';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$postfix	= (isset($this->element['postfix'])) ? '<span class="jsn-postfix">'.$this->element['postfix'].'</span>' : '';
		$html   = '<span style="float:left; margin:0 5px 0 0; line-height: 28px;"><input placeholder="' . JText::_('MENU_OVERALL_THUMB_DIMENSION_WIDTH') . '" type="text" onfocus="JSNISGetInputValue(this);" onchange="JSNISCheckNumberValue(this); JSNISChangeInputThumbDimension()" name="tmp_width_thumb_dimension" id="tmp_width_thumb_dimension" value="" size="5" /> x </span>';
		$html  .= '<span style="float:left; margin:0 5px 0 0; line-height: 28px;"><input placeholder="'. JText::_('MENU_OVERALL_THUMB_DIMENSION_HEIGHT') . '" type="text" onfocus="JSNISGetInputValue(this);" onchange="JSNISCheckNumberValue(this); JSNISChangeInputThumbDimension()" name="tmp_height_thumb_dimension" id="tmp_height_thumb_dimension" value="" size="5" /></span> px';
		$html  .= '<input type="hidden" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.$this->value.'"' .
				$class.$size.$disabled.$readonly.$maxLength.'/> '.$postfix;

		return $html;
	}
}