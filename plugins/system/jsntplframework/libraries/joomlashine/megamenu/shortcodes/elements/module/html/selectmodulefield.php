<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers/html.php';


class JSNTplHelperHtmlselectmodulefield extends JSNTplMMHelperHtml {
	/**
	 * Simple Input text
	 * @param type $element
	 * @return string
	 */
	public static function render( $element ) 
	{		
		$moduleInfo = self::getModuleInfoByID($element['std']);
		$element = parent::getExtraInfo( $element );
		$label   = parent::getLabel( $element );
		$type    = ! empty( $element['type_input'] ) ? $element['type_input'] : 'module';
		$output  = "<div id='jsn-tpl-mm-element-module-list-container' data-start='0' data-module-total='0'>";
		
		$output  .= '<input type="text" value="' . (string) @$moduleInfo->title . '" disabled="disabled" id="jsn-tpl-mm-element-module-name"/>
					<div class="jsn-tpl-mm-element-module-load-more">
						<div id="jsn-tpl-mm-element-module-btn-group-container">
							<button type="button" class="btn btn-default jsn-tpl-mm-element-module-btn-reset" id="jsn-tpl-mm-element-module-btn-reset">Reset</button>
							<input type="text" value="" class="input-sm pull-right" placeholder="Search..." id="jsn-tpl-mm-element-module-input-search" />
						</div>
						<div id="jsn-tpl-mm-element-module-content"></div>
						<a href="javascript:void(0);" class="jsn-add-more" id="jsn-tpl-mm-element-module-load-more-btn">' . JText::_('JSN_TPLFW_MEGAMENU_LOAD_MORE', true) . ' <i id="jsn-tpl-mm-element-module-icon-loading" class="jsn-icon16 jsn-icon-loading hidden"></i></a>						
					</div>';
		$output  .= '</div>';
		$output  .= "<input type='$type' class='{$element['class']}' value='{$element['std']}' id='{$element['id']}' name='{$element['id']}' DATA_INFO />";

		return parent::finalElement( $element, $output, $label );
	}
	
	public static function getModuleInfoByID($id)
	{
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__modules'));
		$query->where('client_id = 0 AND id = ' . (int) $id);
		$db->setQuery($query);
		return $db->loadObject();
	}

}