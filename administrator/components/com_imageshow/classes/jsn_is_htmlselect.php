<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsn_is_htmlselect.php 8418 2011-09-22 08:18:02Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
//require_once JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'select.php';
class JSNISHTMLSelect extends JHtmlSelect
{
	public static function booleanlist(
	$name, $attribs = null, $selected = null, $yes = 'JYES', $no = 'JNO', $id = false
	) {
		$arr = array(
		JHtml::_('select.option', 'no', JText::_($no)),
		JHtml::_('select.option', 'yes', JText::_($yes))
		);
		return JHtml::_('select.radiolist', $arr, $name, $attribs, 'value', 'text', $selected, $id);
	}
}