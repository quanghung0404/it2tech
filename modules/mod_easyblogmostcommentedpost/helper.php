<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php' );

class modEasyBlogMostCommentedPostHelper
{
	public static function getMostCommentedPost(&$params)
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();
		$my = JFactory::getUser();

		$config = EB::config();
		$count = (INT)trim($params->get('count', 0));
		$categories = $params->get('catid');

		$catAccess = array();

		// Get the category ID if any from the module setting
		if (!empty($categories)) {
			$categories = explode(',' , $categories);
		}

		// Respect inclusion categories
		if (!empty($categories)) {

			if ( !is_array( $categories ) ) {
				$categories	= array( $categories );
			}

			$catAccess['include'] = $categories;
		}

		$showprivate = $params->get('showprivate', true);
		$showcomment = $params->get('showlatestcomment', true);
		
		$query  = 'SELECT a.*, count(b.' . $db->quoteName('id') . ') as ' . $db->quoteName('comment_count');

		if ($showcomment) {
			$query.= ', c.' . $db->quoteName('id') . ' as ' . $db->quoteName('comment_id') . ', c.' . $db->quoteName('comment') . ', c.' . $db->quoteName('created_by') . ' as ' . $db->quoteName('commentor') . ', c.' . $db->quoteName('title') . ' as ' . $db->quoteName('comment_title') . ', c.' . $db->quoteName('name') . ' as ' . $db->quoteName('commentor_name');
		}

		$query.= ' FROM ' . $db->quoteName('#__easyblog_post') . ' AS a';
		$query.= '  LEFT JOIN ' . $db->quoteName('#__easyblog_comment') . ' AS b ON a.' . $db->quoteName('id') . ' = b.' . $db->quoteName('post_id');

		if ($showcomment) {
			$query.= '  LEFT JOIN ' . $db->quoteName('#__easyblog_comment') . ' AS c ON a.' . $db->quoteName('id') . ' = c.' . $db->quoteName('post_id');
			$query.= '    AND c.' . $db->quoteName('id') . ' = (SELECT MAX(d.' . $db->quoteName('id') . ') FROM ' . $db->quoteName('#__easyblog_comment') . ' AS d WHERE c.' . $db->quoteName('post_id') . ' = d.' . $db->quoteName('post_id') . ')';
		}

		$query.= ' WHERE a.' . $db->quoteName('published') . ' = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query.= ' AND a.' . $db->quoteName('state') . ' = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		if(! $showprivate)
		    $query .= ' AND a.' . $db->quoteName('access') . ' = ' . $db->Quote('0');

		// get teamblogs id.
	    // contribution type sql
	    $contributor = EB::contributor();
	    $contributeSQL = ' AND ( (a.`source_type` = ' . $db->Quote(EASYBLOG_POST_SOURCE_SITEWIDE) . ') ';
	    
	    if ($config->get('main_includeteamblogpost')) {
	      $contributeSQL .= $contributor::genAccessSQL(EASYBLOG_POST_SOURCE_TEAM, 'a');
	    }
	    
	    $contributeSQL .= ')';
		
		$query .= $contributeSQL;

		// category access here
		$catLib = EB::category();
		$catAccessSQL = $catLib->genAccessSQL( 'a.`id`', $catAccess);
		$query .= ' AND (' . $catAccessSQL . ')';

		$query.= ' GROUP BY a.' . $db->quoteName('id');
		$query.= ' HAVING (' . $db->quoteName('comment_count') . ' > 0)';
		$query.= ' ORDER BY ' . $db->quoteName('comment_count') . ' DESC';

		if ($count > 0) {
			$query .= ' LIMIT ' . $count;
		}

		$db->setQuery($query);

		$posts = $db->loadObjectList();

		// process item
		$posts = EB::modules()->processItems($posts, $params);

		return $posts;
	}

	public static function processComment($data, $row, $params) {

		$row->comment_id = isset($data->comment_id) ? $data->comment_id : '';
		$row->comment = isset($data->comment) ? $data->comment : '';
		$row->commentor = isset($data->commentor) ? $data->commentor : '0';
		$row->comment_title	= isset($data->comment_title) ? $data->comment_title : '';
		$row->commentor_name = isset($data->commentor_name) ? $data->commentor_name : '';

		if ($params->get('showlatestcomment', true)) {
			if ($row->commentor != 0) {
				$commentor	= EB::user($row->commentor);
				$row->commentor	= $commentor;
			} else {
				$obj = new stdClass();
				$obj->id = '0';
				$obj->nickname = (!empty($row->commentor_name))? $row->commentor_name : JText::_('COM_EASYBLOG_GUEST');

				$commentor = EB::table('Profile');
				$commentor->bind($obj);

				$row->commentor = $commentor;
			}
		}

		$comment = strip_tags($row->comment);
		$commentLength = JString::strlen($comment);

		$comment = '"' . $comment . '"';
		$comment = EB::comment()->parseBBCode($comment);

		$row->comment = ($commentLength > 150) ? JString::substr($comment, 0, 150) . '...' : $comment;

		return $row;

		}
}
