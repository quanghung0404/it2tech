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


class JSNTplHelperHtmlselectmodulepositionfield extends JSNTplMMHelperHtml {
	/**
	 * Simple Input text
	 * @param type $element
	 * @return string
	 */
	public static function render( $element ) 
	{		
		$element = parent::getExtraInfo( $element );
		$label   = parent::getLabel( $element );
		$type    = ! empty( $element['type_input'] ) ? $element['type_input'] : 'module';
		$output  = "<div id='jsn-tpl-mm-element-module-list-container'>";	
		$output  .= '<input type="text" value="" disabled="disabled" id="jsn-tpl-mm-element-module-position"/>
						<div class="jsn-tpl-mm-element-module-position-wrapper jsn-megamenu-loading" id="jsn-tpl-mm-element-module-position-wrapper"> 	
							<iframe width="100%" height="500px" src="index.php?widget=megamenu&action=get-module-position&shortcode=jsn_tpl_mm_moduleposition&rformat=raw&template=' . JFactory::getApplication()->input->getString('template', '') . '" id="jsn-tpl-mm-element-module-position-iframe-content" class="hidden"></iframe>						
						</div>';
		$output  .= '</div>';
		$output  .= "<input type='$type' class='{$element['class']}' value='{$element['std']}' id='{$element['id']}' name='{$element['id']}' DATA_INFO />";

		return parent::finalElement( $element, $output, $label );
	}
}