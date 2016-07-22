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

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/shortcodes/element.php';

class JSNTplMMShortcodeChild extends JSNTplMMShortcodeElement
{
	public function elementInMegamenu( $content = '', $shortcode_data = '', $el_title = '', $index = '' )
	{
		$this->config['sub_element'] = true;
		return parent::elementInMegamenu( $content, $shortcode_data, $el_title, $index );
	}
}