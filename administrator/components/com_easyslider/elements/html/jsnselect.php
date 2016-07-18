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

include_once JPATH_PLATFORM . '/joomla/html/select.php';

/**
 * Utility class for creating HTML select lists
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
abstract class JHtmlJSNSelect extends JHtmlSelect
{
	/**
	 * Generates a yes/no radio list.
	 *
	 * @param   string  $name      The value of the HTML name attribute
	 * @param   array   $attribs   Additional HTML attributes for the <select> tag
	 * @param   string  $selected  The key that is selected
	 * @param   string  $yes       Language key for Yes
	 * @param   string  $no        Language key for no
	 * @param   string  $id        The id for the field
	 *
	 * @return  string  HTML for the radio list
	 */
	public static function booleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false)
	{
		$arr = array (JHtml::_('select.option', '0', JText::_($no)), JHtml::_('select.option', '1', JText::_($yes)));

		return JHtml::_('jsnselect.radiolist', $arr, $name, $attribs, 'value', 'text', (int) $selected, $id);
	}

	/**
	 * Generates an HTML radio list.
	 *
	 * @param   array    $data       An array of objects
	 * @param   string   $name       The value of the HTML name attribute
	 * @param   string   $attribs    Additional HTML attributes for the <select> tag
	 * @param   mixed    $optKey     The key that is selected
	 * @param   string   $optText    The name of the object variable for the option value
	 * @param   string   $selected   The name of the object variable for the option text
	 * @param   boolean  $idtag      Value of the field id or null by default
	 * @param   boolean  $translate  True if options will be translated
	 *
	 * @return  string HTML for the select list
	 *
	 * @since  11.1
	 */
	public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false, $translate = false)
	{
		reset($data);
		$html = '';

		if (is_array($attribs))
		{
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $idtag ? $idtag : $name;

		foreach ($data as $obj)
		{
			$k  = $obj->$optKey;
			$t  = $translate ? JText::_($obj->$optText) : $obj->$optText;
			$id = (isset($obj->id) ? $obj->id : null);

			$extra = '';
			$extra .= $id ? ' id="' . $obj->id . '"' : '';

			if (is_array($selected))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object($val) ? $val->$optKey : $val;

					if ($k == $k2)
					{
						$extra .= ' selected="selected"';
						break;
					}
				}
			}
			else
			{
				$extra .= ((string) $k == (string) $selected ? ' checked="checked"' : '');
			}

			$html .= "\n\t" . '<label for="' . $id_text . $k . '"' . ' id="' . $id_text . $k . '-lbl" class="radio inline">' .
				"\n\t" . '<input type="radio" name="' . $name . '"' . ' id="' . $id_text . $k . '" value="' . $k . '"' . ' ' . $extra . ' '
				. $attribs . '/>' . $t . '</label>';
		}
		$html .= "\n";

		return $html;
	}
}
