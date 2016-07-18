<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogThemesHelperTree
{
	/**
	 * Renders the user group tree listing.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	The object to check against.
	 * @param	string	The controller to be called.
	 * @param	string	The key for the object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function groups( $name = 'gid' , $selected = '' , $exclude = array() , $checkSuperAdmin = false )
	{
		static $count;

		$count++;

		// If selected value is a string, we assume that it's a json object.
		if (is_string($selected)) {
			$selected 	= json_decode($selected);
		}

		$groups 	= self::getGroups();

		if (!is_array($selected)) {
			$selected 	= array($selected);
		}

		$isSuperAdmin	= JFactory::getUser()->authorise('core.admin');

		$theme 	= EB::template();
		$theme->set('name', $name );
		$theme->set('checkSuperAdmin', $checkSuperAdmin);
		$theme->set('isSuperAdmin', $isSuperAdmin);
		$theme->set('selected', $selected);
		$theme->set('count', $count);
		$theme->set('groups', $groups);

		return $theme->output( 'admin/html/tree.groups' );
	}

	private static function getGroups()
	{
		$db = EB::db();

		$query 	= 'SELECT a.*, COUNT(DISTINCT(b.`id`)) AS `level` FROM ' . $db->quoteName('#__usergroups') . ' AS a';
		$query .= ' LEFT JOIN ' . $db->quoteName('#__usergroups') . ' AS b';
		$query .= ' ON a.`lft` > b.`lft` AND a.`rgt` < b.`rgt`';
		$query .= ' GROUP BY a.`id`, a.`title`, a.`lft`, a.`rgt`, a.`parent_id`';
		$query .= ' ORDER BY a.`lft` ASC';

		$db->setQuery($query);
		$groups 	= $db->loadObjectList();

		return $groups;
	}
}