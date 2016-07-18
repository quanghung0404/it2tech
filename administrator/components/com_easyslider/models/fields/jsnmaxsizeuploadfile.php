<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN EasySlider
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.form.formfield');

class JFormFieldJSNMaxSizeUploadFile extends JFormField
{
    public $type = 'JSNMaxSizeUploadFile';

    protected function getInput()
    {
        $doc = JFactory::getDocument();
        $msg = JText::_('JSN ALLOW ONLY DIGITS AND < CONFIG ON PHP.INI (' . ini_get('upload_max_filesize') . ')');
        $doc->addScriptDeclaration("
			var original_value = '';

			function getInputValue(object)
			{
				original_value = object.value;
				if( parseInt(object.value) > parseInt(" . $this->parse_size(ini_get('upload_max_filesize')) . ") ){
					original_value = parseInt(" . $this->parse_size(ini_get('upload_max_filesize')) . ");
				}
			}

			function checkNumberValue(object)
			{
				var msg;
				msg = '" . $msg . "';
				if(object.value != '' && parseInt(object.value) > parseInt(" . $this->parse_size(ini_get('upload_max_filesize')) . ") )
				{
					alert (msg);
					object.value = original_value;
					return;
				}
			}

		");
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="jsn-text"';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$postfix	= (isset($this->element['postfix'])) ? '<span class="jsn-postfix">'.$this->element['postfix'].'</span>' : '';
		$value      = htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');

		$html = '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
			' value="'.($value != ''? (int) $value:'').'" onfocus="getInputValue(this);" onchange="checkNumberValue(this);"' .
			$class.$size.$disabled.$readonly.$maxLength.'/> '.$postfix;

		return $html;
	}

	protected function getLabel(){
		return '<label class="control-label" original-title="' . JText::_($this->description, true) . '">' . JText::_($this->element['label'], true) . '</label>';
	}
	public function parse_size($size) {
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		return $size;
	}
}