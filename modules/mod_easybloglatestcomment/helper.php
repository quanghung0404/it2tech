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

class modEasyBlogLatestCommentHelper
{
	static function getLatestComment(&$params)
	{
		$mainframe = JFactory::getApplication();
		$db = EB::db();
		$count = (INT)trim($params->get('count', 5));
		$showprivate = $params->get('showprivate', true);

		$query = 'SELECT ' . $db->qn('b.title') . ' as `blog_title`, ' . $db->qn('b.created_by') . ' as `author_id`, ' . $db->qn('b.category_id') . ' as `category_id`, a.*';
		$query .= ' from `#__easyblog_comment` as a';
		$query .= '   left join `#__easyblog_post` as b';
		$query .= '   on a.`post_id` = b.`id`';
		$query .= ' where b.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' and b.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query .= ' and a.`published`=' . $db->Quote( '1' );
		$query .= ' and b.`source_id` = ' . $db->Quote('0');

		if (!$showprivate) {
			$query .= ' and b.`access` = ' . $db->Quote('0');
		}

		$query .= ' order by a.`created` desc';
		$query .= ' limit ' . $count;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (count($result) > 0) {
			for ($i = 0; $i < count($result); $i++) {
				$row =& $result[ $i ];
				$row->author = EB::user($row->created_by);

				$date = EB::date($row->created)->dateWithOffSet();
				$row->dateString = EB::date($row->created)->toFormat(JText::_('DATE_FORMAT_LC3'));
			}
		}
		return $result;
	}

	static function getJComment(&$params)
	{
		$db = EB::db();
		$query = 'SELECT * FROM ' . $db->nameQuote('#__jcomments') . ' '
				. 'WHERE ' . $db->nameQuote('published') . '=' . $db->Quote(1) . ' '
				. 'AND ' . $db->nameQuote('object_group') . '=' . $db->Quote('com_easyblog') . ' '
				. 'ORDER BY `date` '
				. 'LIMIT 0,' . $params->get('count');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$comments = array();

		if ($rows) {
			foreach ($rows as $row) {
				$row->author = EB::user($row->userid);
				$row->created_by = $row->userid;
				$row->post_id = $row->object_id;

				$blog = EB::table('Blog');
				$blog->load($row->object_id);

				$row->blog_title = $blog->title;
				$row->created = $row->date;
				$comments[] = $row;
			}
		}

		return $comments;
	}

}
