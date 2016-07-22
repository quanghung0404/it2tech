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
include_once JPATH_ROOT . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/helpers/html.php';

class JSNTplHelperHtmltextfield extends JSNTplMMHelperHtml {
	/**
	 * Simple Input text
	 * @param type $element
	 * @return string
	 */
	public static function render( $element ) 
	{
		$element = parent::getExtraInfo( $element );
		$label   = parent::getLabel( $element );
		$type    = ! empty( $element['type_input'] ) ? $element['type_input'] : 'text';
		$output  = "<input type='$type' class='{$element['class']}' value=\"" . $element['std'] . "\" id='{$element['id']}' name='{$element['id']}' DATA_INFO />";

		return parent::finalElement( $element, $output, $label );
	}
}