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

class JSNTplMMHelperBuilder
{
	/**
	 * Generate megamenu page header
	 * @param string $header_str
	 */
	public function generateHeader($headerStr = 'JSN TPL MegaMenu Builder', $iconClass = '')
	{
		$header  = '<div clas"icon32 ' . $iconClass . '"></div>';
		$header .= '<h2>' . $headerStr . '</h2>';
		return $header;
	}	
}