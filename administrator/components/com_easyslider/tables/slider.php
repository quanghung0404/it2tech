<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.tableasset');

/**
 * Class for working with item table.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
class JSNEasySliderTableSlider extends JTable
{
	/**
	 * Object constructor to set table and key fields.  In most cases this will
	 * be overridden by child classes to explicitly set the table and key fields
	 * for a particular database table.
	 *
	 * @param   JDatabase  &$db  JDatabase connector object.
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jsn_easyslider_sliders', 'slider_id', $db);
	}
}
