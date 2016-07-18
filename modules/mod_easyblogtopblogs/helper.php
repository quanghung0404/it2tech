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

class modTopBlogsHelper
{
	static function getPosts( $params )
	{
		$db = EB::db();
		$order = trim($params->get('order', 'postcount_desc'));
		$count = (int) trim($params->get('count', 0));
		$showprivate = $params->get('showprivate', true);
		$config = EB::config();

		$query = 'SELECT a.* , SUM(b.value) AS ratings FROM ' . $db->nameQuote('#__easyblog_post') . ' AS a '
				. 'LEFT JOIN ' . $db->nameQuote('#__easyblog_ratings') . ' AS b '
				. 'ON a.id=b.uid '
				. 'AND b.type=' . $db->Quote('entry') . ' '
				. 'INNER JOIN ' . $db->quoteName('#__easyblog_post_category') . ' as c ON a.`id` = c.`post_id` INNER JOIN ' . $db->quoteName('#__easyblog_category') . ' as d ON c.`category_id` = d.`id` '
				. 'WHERE a.' . $db->nameQuote('published') .'=' . $db->Quote(EASYBLOG_POST_PUBLISHED);

		$query .= 'AND a.' . $db->nameQuote('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL) . ' ';

		if (!$showprivate) {
			$query .= ' AND a.' . $db->nameQuote('access') . '=' . $db->Quote(0);
		}

		// Respect inclusion categories
		$categories	= $params->get('catid');

		if (!empty($categories)) {
			$categories = explode(',', $categories);

			$query	.= ' AND d.`id` IN(';

			if (!is_array($categories)) {
				$categories	= array($categories);
			}

			for ($i = 0; $i < count($categories); $i++) {
				$query	.= $db->Quote($categories[$i]);

				if (next($categories) !== false) {
					$query	.= ',';
				}
			}
			$query	.= ')';
		}

		$query .= ' AND a.' . $db->nameQuote('source_id') . '=' . $db->Quote('0');
		$query .= 'GROUP BY b.uid ';
		$query .= 'ORDER BY ' . $db->nameQuote('ratings') . ' DESC ';

		if (!empty($count)) {
			$query .= ' LIMIT ' . $count;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$posts = EB::modules()->processItems($result, $params);

		return $posts;
	}

}
