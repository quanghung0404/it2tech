<?php
/**
 * @version     $Id: jsnmenubutton.php 17065 2012-10-16 04:06:37Z giangnd $
 * @package     JSN.ImageShow
 * @subpackage  item
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.utilities.utility');
include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'jsn_is_showlist.php';
include_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'jsn_is_showcase.php';

/**
 * Button base class
 *
 * The JButton is the base class for all JButton types
 *
 * @package  JSN.ImageShow
 *
 * @since    2.5
 */
class JButtonJSNMenuButton extends JButton
{

	/**
	 * element name
	 *
	 * This has to be set in the final renderer classes.
	 *
	 * @param   string The name of JButton.
	 */

	protected $_name = 'JSNMenuButton';

	/**
	 * Get the button
	 *
	 * Defined in the final button class
	 *
	 * @param   string  $type  The name of JButton.
	 *
	 * @since   2.5
	 * @return string
	 */

	public function fetchButton($type = 'JSNMenuButton')
	{
		return '';
	}

	/**
	 * fetch Id
	 *
	 * @return string
	 */
	public function fetchId()
	{
		return "jsn-is-menu-button";
	}
}
