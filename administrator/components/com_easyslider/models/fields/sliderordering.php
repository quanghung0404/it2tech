<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('JPATH_BASE') or die;

/**
 * slider ordering field renderer.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JFormFieldSliderOrdering extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'SliderOrdering';

	/**
	 * Method to get the field input markup.
	 *
	 * @return   string	The field input markup.
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array ();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Get some field values from the form.
		$sliderId = (int) $this->form->getValue('slider_id');

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, slider_title AS text' .
			' FROM #__jsn_easyslider_sliders' .
			' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $sliderId ? 0 : 1);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value) . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $sliderId ? 0 : 1);
		}

		return implode($html);
	}
}
