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

class JSNTplMMHelperType 
{
	/**
	 * Border style options
	 *
	 * @return array
	 */
	static function getBorderStyles() 
	{
		return array(
				'solid'  => JText::_( 'Solid' ),
				'dotted' => JText::_( 'Dotted' ),
				'dashed' => JText::_( 'Dashed' ),
				'double' => JText::_( 'Double' ),
				'groove' => JText::_( 'Groove' ),
				'ridge'  => JText::_( 'Ridge' ),
				'inset'  => JText::_( 'Inset' ),
				'outset' => JText::_( 'Outset' )
		);
	}
}

