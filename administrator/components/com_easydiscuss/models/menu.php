<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelMenu extends EasyDiscussAdminModel
{
	/**
	 * Retrieves all menus associated with EasyDiscuss
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssociatedMenus()
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->qn('#__menu');
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('client_id') . '=' . $db->Quote(0);
		$query[] = 'AND ' . $db->qn('link') . ' LIKE ' . $db->Quote('index.php?option=com_easydiscuss%');

        $query = implode(' ', $query);

        $db->setQuery($query);

        $result = $db->loadObjectList();

        return $result;
	}


	public function getCategoryTreeIds($parentId)
	{
		$db = ED::db();

		$query = "select b.`id`, b.`title`, b.`alias`, b.`lft`, b.`rgt`";
		$query .= " from `#__discuss_category` as a";
		$query .= "		inner join `#__discuss_category` as b on b.`lft` >= a.`lft` and b.`lft` <= a.`rgt`";
		$query .= " where a.`published` = 1";
		$query .= " and a.`id` = " . $db->Quote($parentId);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
