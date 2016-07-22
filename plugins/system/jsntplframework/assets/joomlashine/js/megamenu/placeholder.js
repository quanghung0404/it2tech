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

// define array of placeholders javascript
var $placeholders = new Array();
$placeholders['widget_title']   = '_JSN_TPL_MM_WIDGET_TIGLE_';
$placeholders['extra_class']    = '_JSN_TPL_MM_EXTRA_CLASS_';
$placeholders['index']          = '_JSN_TPL_MM_INDEX_';
$placeholders['custom_style']   = '_JSN_TPL_MM_STYLE_';
$placeholders['standard_value'] = '_JSN_TPL_MM_STD_';
$placeholders['wrapper_append'] = '_JSN_TPL_MM_WRAPPER_TAG_';

// custom sprintf function for javascript: %s
function sprintf(format, etc) {
    var arg = arguments;
    var i = 1;
    return format.replace(/%((%)|s)/g, function (m) { return m[2] || arg[i++] })
}

// custom sprintf function for javascript: {0}, {1}
String.prototype.custom_sprintf = function() {
    var formatted = this;
    for(var arg in arguments)
    {
        formatted = formatted.replace("{" + arg + "}", arguments[arg]);
    }
    return formatted;
};

/**
 * Add placeholder to string
 * Ex:	data.replace(/&lt;/g, '&_WR_WRAPPER_TAG_lt;') => jsn_mm_add_placeholder( data, '&lt;', 'index', '&l{0}t;')
*/
function jsn_mm_add_placeholder( $string, $replace, $placeholder, $expression )
{
	if (!( $placeholders[$placeholder]))
	{	
		return NULL;
	}
	
	$replace = $replace.replace('/', '\\/')
    var regexp = new RegExp($replace, "g");
	
	if (!($expression))
	{	
		return $string.replace(regexp, $placeholders[$placeholder]);
	}
	else
	{
		return $string.replace(regexp, $expression.custom_sprintf($placeholders[$placeholder]));
	}
}

/**
 * Replace placeholder with real value
 * Ex:	html.replace(/_WR_INDEX_/g, value) => jsn_mm_remove_placeholder(html, 'index', value)
*/
function jsn_mm_remove_placeholder( $string, $placeholder, $value)
{
    if (! $string)
    {
        return '';
    }
    
	if (!($placeholders[$placeholder]))
	{	
		return $string;
	}
	
    var regexp = new RegExp($placeholders[$placeholder], "g");
	return $string.replace(regexp, $value);
}

// get placeholder value
function jsn_mm_get_placeholder($placeholder)
{
    if (!($placeholders[$placeholder]))
    {
		return NULL;
    }
    return $placeholders[$placeholder];
}