<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsnlayout.php 11066 2012-02-07 08:38:03Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JFormFieldJSNLayout extends JFormField
{
	public $type = 'JSNLayout';
	protected function getInput()
	{
		$results = array(
				'0' => array('value' => 'thumbnails', 'text' => JText::_('JSN_FIELD_SELECT_LAYOUT_THUMBNAILS_OPTION')),
				'1' => array('value' => 'details', 'text' => JText::_('JSN_FIELD_SELECT_LAYOUT_DETAILS_OPTION'))
		);
		$html = JHTML::_('select.genericList', $results, $this->name, 'class="inputbox jsn-select-value"', 'value', 'text', $this->value,  $this->id);
		return $html;
	}
}
?>