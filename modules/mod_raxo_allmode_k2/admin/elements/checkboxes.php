<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Element
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldCheckboxes extends JFormField
{
	protected $type = 'Checkboxes';
	protected $forceMultiple = true;

	protected function getInput()
	{
		$html = array();
		$class = $this->element['class'] ? ' class="checkboxes '.(string) $this->element['class'].'"' : ' class="checkboxes"';
		$options = $this->getOptions();

		$this->value = is_string($this->value) && !empty($this->value) ? explode(',', $this->value) : $this->value;

		$html[] = '<fieldset id="'.$this->id.'"'.$class.'>';

		foreach ($options as $i => $option) {
			$checked	= (in_array((string)$option->value,(array)$this->value) ? ' checked="checked"' : '');
			$class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
			$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';
			$onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

			$html[] = '<label class="checkbox inline btn">';
			$html[] = '<input type="checkbox" id="'.$this->id.$i.'" name="'.$this->name.'" value="'.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'.$checked.$class.$onclick.$disabled.'/>';
			$html[] = '<span>'.JText::_($option->text).'</span>';
			$html[] = '</label>';
		}

		$i = $i+1;
		$html[] = '<input type="hidden" id="'.$this->id.$i.'" name="'.$this->name.'" value="fix" />';
		$html[] = '</fieldset>';

		return implode($html);
	}

	protected function getOptions()
	{
		$options = array();

		foreach ($this->element->children() as $option) {
			if ($option->getName() != 'option') {
				continue;
			}

			$tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option), 'value', 'text', ((string) $option['disabled']=='true'));
			$tmp->class = (string) $option['class'];
			$tmp->onclick = (string) $option['onclick'];

			$options[] = $tmp;
		}

		reset($options);
		return $options;
	}
}