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

// define array of placeholders php
global $JSNTplMMPlaceholders;
$JSNTplMMPlaceholders                     = array();
//$JSNTplMMPlaceholders[ 'widget_title' ]   = '_WR_WIDGET_TIGLE_';
$JSNTplMMPlaceholders[ 'extra_class' ]    = '_JSN_TPL_MM_EXTRA_CLASS_';
$JSNTplMMPlaceholders[ 'index' ]          = '_JSN_TPL_MM_INDEX_';
$JSNTplMMPlaceholders[ 'custom_style' ]   = '_JSN_TPL_MM_STYLE_';
$JSNTplMMPlaceholders[ 'standard_value' ] = '_JSN_TPL_MM_STD_';
$JSNTplMMPlaceholders[ 'wrapper_append' ] = '_JSN_TPL_MM_WRAPPER_TAG_';

class JSNTplMMHelperPlaceholder 
{
	static function addPlaceholder($string, $placeholder, $expression = '')
	{
		global $JSNTplMMPlaceholders;
		
		if (!isset( $JSNTplMMPlaceholders[$placeholder]))
		{	
			return NULL;
		}
		
		if (empty($expression))
		{	
			return sprintf($string, $JSNTplMMPlaceholders[$placeholder]);
		}
		else
		{
			return sprintf($string, sprintf($expression, $JSNTplMMPlaceholders[$placeholder]));
		}
	}
	
	static function removePlaceholder($string, $placeholder, $value)
	{
		global $JSNTplMMPlaceholders;
		
		if (! isset($JSNTplMMPlaceholders[$placeholder]))
		{
			return $string;
		}
		
		return str_replace($JSNTplMMPlaceholders[$placeholder], $value, $string);
	}
	
	static function getPlaceholder($placeholder)
	{
		global $JSNTplMMPlaceholders;
		
		if (! isset( $JSNTplMMPlaceholders[$placeholder]))
		{	
			return NULL;
		}
		
		return $JSNTplMMPlaceholders[$placeholder];
	}
}