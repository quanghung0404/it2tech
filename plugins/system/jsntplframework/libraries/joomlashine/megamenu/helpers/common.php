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

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

class JSNTplMMHelperCommon
{
	/**
	 * Slice to get nth-child first word
	 *
	 * @param type $content
	 */
	static function sliceContent($content)
	{
		$content = urldecode( $content );
		$content = strip_tags( $content );
		$arr     = explode( ' ', $content );
		$arr     = array_slice( $arr, 0, 10 );
	
		return implode( ' ', $arr );
	}
	
	/**
	 * remove ' and " from string
	 *
	 * @param type $str
	 *
	 * @return type
	 */
	static function removeQuotes($str) 
	{
		$str    = stripslashes($str);
		$result = preg_replace("/[\'\"]+/", '', $str);
	
		return $result;
	}
	
	/**
	 * Generate random string
	 *
	 * @param type $length
	 * @param type $is_lower_no_number
	 *
	 * @return string
	 */
	static function randomString( $length = 6, $is_lower_no_number = false ) 
	{
		if ( ! $is_lower_no_number ) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		} else {
			$characters = 'abcdefghijklmnopqrstuvwxyz';
		}
	
		$randomString = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$randomString .= $characters[rand( 0, strlen( $characters ) - 1 )];
		}
	
		return $randomString;
	}
}