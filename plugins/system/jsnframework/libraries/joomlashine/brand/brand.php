<?php
/**
 * @version    $Id$
 * @package    JSN_Framework
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Prevent this file from being included twice
if (class_exists('JSNBrand'))
{
	return;
}

/**
 * Class JSN Brand
 *
 * @package  JSN_Framework
 * @since    1.1.0
 */
class JSNBrand
{
	/**
	 * Check if Plg JSNTplBrand is installed or not
	 * 
	 * @return True on success
	 */
	public static function checkPlgJSNBrand()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear();
		$query->select('enabled');
		$query->from('#__extensions');
		$query->where('type = ' . $db->quote('plugin') . ' AND element = ' . $db->quote('jsnbrand') . ' AND folder = ' . $db->quote('system'));
		$db->setQuery($query);
		return (int) $db->loadResult();
	}
}
