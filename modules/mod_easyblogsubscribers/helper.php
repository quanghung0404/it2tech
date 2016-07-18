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
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');

class modEasyBlogSubscribers
{

	public static function getUsers()
	{
	    $id     = JRequest::getVar('id');
	    $view	= JRequest::getVar('view');
	    $db     = EB::db();

		$query		= '';

		if ($view == 'entry') {

			$query = 'select disctinct a.`user_id` from `#__easyblog_subscriptions` as a';
			$query .= ' inner join `#__users` as b on a.`user_id` = b.`id`';
			$query .= ' where (';

			// entry
			$query .= ' (a.`uid` = ' . $db->Quote($id) . ' AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY) . ') OR';

			// category
			$query .= ' (a.`uid` IN (select pc.`category_id` from `#__easyblog_post_category` as pc where pc.`post_id` = ' . $db->Quote($id) . ' ) AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY) . ') OR';

			// teamblog
			$query .= ' (a.`uid` IN (select pc.`source_id` from `#__easyblog_post` as p where p.`id` = ' . $db->Quote($id) . ' and p.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' ) AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG) . ')';

			$query .= ')';

		} else if ($view == 'categories' && $id) {

			$query = 'select disctinct a.`user_id` from `#__easyblog_subscriptions` as a';
			$query .= ' inner join `#__users` as b on a.`user_id` = b.`id`';
			$query .= ' where a.`uid` = ' . $db->Quote($id);
			$query .= ' and a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);

		} else if ($view == 'teamblog') {

			$query = 'select disctinct a.`user_id` from `#__easyblog_subscriptions` as a';
			$query .= ' inner join `#__users` as b on a.`user_id` = b.`id`';
			$query .= ' where a.`uid` = ' . $db->Quote($id);
			$query .= ' and a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);

		}

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
		    return false;
		}

		//preload users
		$ids = array();
		foreach( $result as $row ) {
		    $ids[]  = $row->user_id;
		}

		EB::user($ids);

		$subscribers    = array();
		foreach( $result as $row ) {
		    $subscribers[]  = EB::user($row->user_id);
		}

		return $subscribers;
	}


	public static function getGuestCount()
	{
	    $id     = JRequest::getVar('id');
	    $view	= JRequest::getVar('view');
	    $db     = EB::db();

		$query		= '';

		if ($view == 'entry') {

			$query = 'select cont(1) from `#__easyblog_subscriptions` as a';
			$query .= ' where (';

			// entry
			$query .= ' (a.`uid` = ' . $db->Quote($id) . ' AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY) . ') OR';

			// category
			$query .= ' (a.`uid` IN (select pc.`category_id` from `#__easyblog_post_category` as pc where pc.`post_id` = ' . $db->Quote($id) . ' ) AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY) . ') OR';

			// teamblog
			$query .= ' (a.`uid` IN (select pc.`source_id` from `#__easyblog_post` as p where p.`id` = ' . $db->Quote($id) . ' and p.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_TEAM) . ' ) AND a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG) . ')';

			$query .= ')';
			$query .= ' AND a.`user_id` = ' . $db->Quote('0');

		} else if ($view == 'categories' && $id) {

			$query = 'select count(1) from `#__easyblog_subscriptions` as a';
			$query .= ' where a.`uid` = ' . $db->Quote($id);
			$query .= ' and a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_CATEGORY);
			$query .= ' AND a.`user_id` = ' . $db->Quote('0');


		} else if ($view == 'teamblog') {

			$query = 'select count(1) from `#__easyblog_subscriptions` as a';
			$query .= ' where a.`uid` = ' . $db->Quote($id);
			$query .= ' and a.`utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_TEAMBLOG);
			$query .= ' AND a.`user_id` = ' . $db->Quote('0');


		}

		$db->setQuery( $query );

		return (int) $db->loadResult();
	}

}
