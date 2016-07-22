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

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES .'/shortcodes/common.php';

class JSNTplMMShortcodeLayout extends JSNTplMMShortcodeCommon
{
	/**
	 * Constructor
	 *
	 * @return type
	 */
	public function __construct() 
	{
		$this->type = 'layout';
		$this->config['el_type'] = 'element';
		
		$this->elementConfig();
		$this->elementItems();
		$this->shortcodeData();
	}
	
	/**
	 * get params & structure of shortcode
	 */
	public function shortcodeData() 
	{
	
	}
	
	public function elementConfig() 
	{
	
	}
	
	public function elementItems()
	{
	
	}	
}