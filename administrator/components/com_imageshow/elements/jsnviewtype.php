<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: jsnviewtype.php 10490 2011-12-26 07:28:53Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
class JFormFieldJSNViewType extends JFormField
{
	public $type = 'JSNViewType';
	protected function getInput()
	{
		$doc =& JFactory::getDocument();
		$doc->addScriptDeclaration("
			function JSNChangeViewType()
			{
				var view_type 				= $('".$this->id."');
				var value					= view_type.value;
				var lbl_modal_dimenstion 	= $('jform_params_dimension_modal-lbl');
				var elementParent = lbl_modal_dimenstion.getParent();
				if (value == 'modal-window')
				{
					elementParent.setStyle('display', 'list-item');
				}
				else
				{
					elementParent.setStyle('display', 'none');
				}
			}
			window.addEvent('domready', function() {
				JSNChangeViewType();
			});
		");
		$results = array(
		//'0' => array('value' => '',
		//'text' =>'- '.JText::_('JSN_FIELD_SELECT_VIEW_TYPE'). ' -'),
				'0' => array('value' => 'new-page',
				'text' => JText::_('JSN_FIELD_SELECT_VIEW_NEW_PAGE_OPTION')),
				'1' => array('value' => 'modal-window',
				'text' => JText::_('JSN_FIELD_SELECT_VIEW_MODAL_WINDOW_OPTION'))
		);
		$html 		= JHTML::_('select.genericList', $results, $this->name, 'class="inputbox jsn-select-value" onchange="JSNChangeViewType();"', 'value', 'text', $this->value,  $this->id);
		return $html;
	}
}
?>