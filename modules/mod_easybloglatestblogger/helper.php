<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.system.file');
jimport('joomla.system.folder');

class modLatestBloggerHelper
{
	static public function getBloggers($sort = 'latest', $limit = 5, $filter='showallblogger', $featuredOnly = '')
	{
		$db = EB::db();

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();


		$query  = 'select count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT(g.content_id) as `featured`,';
		$query  .= ' a.`id`, b.`nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`, b.`permalink`';
		$query .= '	from `#__users` as a';

		$query .= ' 	inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';

		if ($filter == 'showallblogger') {
			$query .= ' 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`';
			$query .= ' 		and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= ' 		and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		} else {
			$query .= ' 	inner join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		}


		if ($featuredOnly) {
			$query .= ' 	inner join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		} else {
			$query .= ' 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		}

		$query .= ' where (' . $aclQuery . ')';

		if ($filter == 'showbloggerwithpost') {
			$query .= ' and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= ' and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		}

		$query .= ' group by a.`id`';

		if ($filter == 'showbloggerwithpost' && $featuredOnly) {
			$query .= ' having (count(p.id) > 0 and count(g.content_id) > 0)';
		} else if ($filter == 'showbloggerwithpost' && !$featuredOnly) {
			$query .= ' having (count(p.id) > 0)';
		} else if ($filter != 'showbloggerwithpost' && $featuredOnly) {
			$query .= ' having (count(g.content_id) > 0)';
		}

		switch($sort)
		{
			case 'featured':
				$query	.= ' ORDER BY `featured` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY a.`id` DESC';
				break;
			case 'postcount' :
				$query .= '	ORDER BY `totalPost` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY a.`lastvisitDate` DESC';
				break;
			default	:
				break;
		}

		$query .= ' LIMIT ' . $limit;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}
}
