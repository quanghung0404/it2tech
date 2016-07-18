<?php
/**
 * @package     Joomla.Platform
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Modifed by JoomlaShine Team
 */

defined('_JEXEC') or die('Restricted access');
//include_once JPATH_PLATFORM.DS.'joomla'.DS.'html'.DS.'select.php';
/**
 * Utility class for creating HTML select lists
 *
 * @package     Joomla.Platform
 * @subpackage  HTML
 * @since       11.1
 */
abstract class JHtmlJSNSelect extends JHtmlSelect
{

	public static function booleanlist($name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false, $dyes = '1', $dno = '0')
	{
		$arr = array(JHtml::_('select.option', $dno, JText::_($no)), JHtml::_('select.option', $dyes, JText::_($yes)));
		return JHtml::_('jsnselect.radiolist', $arr, $name, $attribs, 'value', 'text', $selected, $id);
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
	public static function radiolist($data, $name, $attribs = null, $optKey = 'value', $optText = 'text', $selected = null, $idtag = false,
	$translate = false)
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
			$k = $obj->$optKey;
			$t = $translate ? JText::_($obj->$optText) : $obj->$optText;
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
