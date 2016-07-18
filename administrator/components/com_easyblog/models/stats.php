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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelStats extends EasyBlogAdminModel
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieve stats for blog posts created the past week
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentsHistory()
	{
		// Get dbo
		$db = EB::db();

		// Get the past 7 days
		$today = EB::date();
		$dates = array();

		for ($i = 0 ; $i < 7; $i++) {

			$date = EB::date('-' . $i . ' day');
			$dates[] = $date->format('Y-m-d');
		}

		// Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;
		foreach ($dates as $date) {

			$query   = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_comment');
			$query[] = 'WHERE DATE_FORMAT(' . $db->quoteName('created') . ', GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(1);

			$query = implode(' ', $query);

			$db->setQuery($query);

			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

	/**
	 * Retrieve stats for blog posts created the past week
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostsHistory()
	{
		// Get dbo
		$db = EB::db();

		// Get the past 7 days
		$today = EB::date();
		$dates = array();

		for ($i = 0 ; $i < 7; $i++) {

			$date = EB::date('-' . $i . ' day');
			$dates[] = $date->format('Y-m-d');
		}

		// Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;
		foreach ($dates as $date) {

			$query   = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_post');
			$query[] = 'WHERE DATE_FORMAT(' . $db->quoteName('created') . ', GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


			$query = implode(' ', $query);

			$db->setQuery($query);

			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

	/**
	 * Retrieve the total number of posts created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPosts()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('published') . '!=' . $db->Quote(EASYBLOG_POST_BLANK);
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);


		$query = implode(' ', $query);

		$db->setQuery($query);

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve the total number of tags created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalTags()
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_tag');

		$db->setQuery($query);

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve the total number of tags created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalTeams()
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_team');

		$db->setQuery($query);

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieve the total number of comments created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalComments()
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_comment');

		$db->setQuery($query);

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves the total number of authors
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalAuthors()
	{
		$db  = EB::db();

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		$query = 'select count(1)';
		$query .= ' from `#__users` as a';
		$query .= ' where a.`block` = ' . $db->Quote('0');
		$query .= ' and (' . $aclQuery . ')';

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of recent blog posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingPosts($limit = 5)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('published') . '=' . $db->Quote(EASYBLOG_POST_PENDING);
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = 'ORDER BY ' . $db->quoteName('created') . ' DESC';
		$query[] = 'LIMIT ' . $limit;

		$query = implode(' ', $query);

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$posts = array();

		foreach ($rows as $row) {
			$post = EB::post($row->id);
			$posts[] = $post;
		}

		return $posts;
	}

	/**
	 * Retrieves a list of recent blog posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRecentComments($limit = 5)
	{
		$db = EB::db();

		$query = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_comment') . ' ';
		$query .= ' WHERE ' . $db->nameQuote('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' ORDER BY ' . $db->nameQuote('created') . ' DESC';
		$query .= ' LIMIT ' . $limit;

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$comments = array();

		foreach ($rows as $row) {
			$comment = EB::table('Comment');
			$comment->bind($row);

			$comments[] = $comment;
		}

		return $comments;
	}

	/**
	 * Retrieves a list of recent blog posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRecentPosts($limit = 5)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->nameQuote('#__easyblog_post') . ' ';
		$query[] = ' WHERE ' . $db->nameQuote('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND ' . $db->quoteName('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query[] = ' ORDER BY ' . $db->nameQuote('created') . ' DESC';
		$query[] = ' LIMIT ' . $limit;

		$query = implode(' ', $query);
		$db->setQuery($query);

		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$posts = array();

		foreach ($rows as $row) {

			$post = EB::post($row->id);

			$posts[] = $post;
		}

		return $posts;
	}


	/**
	 * Retrieve the total number of comments created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalCategories()
	{
		$db = EB::db();

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_category');

		$db->setQuery($query);

		$total 	= $db->loadResult();

		return $total;
	}
}
