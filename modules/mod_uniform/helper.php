<?php
/**
 * @version     $Id:$
 * @package     JSNUniform
 * @subpackage  Modules
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');
/**
 * Modules Uniform controllers 
 * 
 * @package     Controllers
 * @subpackage  Form
 * @since       1.6
 */
class modUniformHelper
{
	/**
	 * Get form
	 * 
	 * @param   Array  &$params  Options params
	 * 
	 * @return Object 
	 */
	public static function getForm(&$params)
	{
		$db	= JFactory::getDBO();
		$id	= $params->get('form_id');
		$items = "";
		$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_forms')->select('*')->where('form_id=' . (int) $id));
		$items = $db->loadObject();
		return $items;
	}

	/**
	 * Get page form
	 * 
	 * @param   Array  &$params  Options params
	 * 
	 * @return Object list
	 */
	public static function getFormPage(&$params)
	{
		$db	= JFactory::getDBO();
		$id	= $params->get('form_id');
		$items = "";
		$db->setQuery($db->getQuery(true)->from('#__jsn_uniform_form_pages')->select('*')->where('form_id=' . (int) $id));
		$items = $db->loadObjectList();
		return $items;
	}
}
