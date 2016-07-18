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

class EasyBlogModelBlogger extends EasyBlogAdminModel
{
	public $_total = null;
	public $_pagination = null;
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$mainframe	= JFactory::getApplication();

		$limit = EB::call('Pagination', 'getLimit');
		$limitstart = $this->input->get('limitstart', '0', 'int');

		// In case limit has been changed, adjust it
		$limitstart = (int) ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * This method reduces the number of query hit on the server
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preload($authorIds = array())
	{
		if (!$authorIds) {
			return $authorIds;
		}

		$db = EB::db();

		$query = array();

		$query[] = 'SELECT b.* FROM ' . $db->qn('#__users') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__easyblog_users') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . '= b.' . $db->qn('id');
		$query[] = ' where a.`id` IN (' . implode(',', $authorIds) . ')';

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$authors = array();

		foreach ($result as $item) {
			$author = EB::table('Profile');
			$author->bind($item);

			$authors[$item->id] = $author;
		}

		return $authors;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	public function getData($sort = 'latest', $limit = 0, $filter='showallblogger')
	{
		$db 				= EB::db();
		$config				= EB::config();
		$nameDisplayFormat	= $config->get('layout_nameformat');
		$limitSQL			= '';

		if( !is_null( $limit ) )
		{
			$limit		= ($limit == 0) ? $this->getState('limit') : $limit;
			$limitstart = $this->getState('limitstart');
			$limitSQL	= ' LIMIT ' . $limitstart . ',' . $limit;
		}

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		$query  = 'select SQL_CALC_FOUND_ROWS count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT( DISTINCT(g.content_id) ) as `featured`,';
		$query  .= ' a.`id`, b.`nickname`, b.avatar, b.description, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`';
		$query .= '	from `#__users` as a';
		$query .= ' 	left join `#__easyblog_post` as p on a.`id` = p.`created_by`';
		$query .= ' 		and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query .= ' 		and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query .= ' 	inner JOIN `#__easyblog_users` AS `b` ON p.`created_by` = b.`id`';
		$query .= ' 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query .= ' where (' . $aclQuery . ')';
		$query .= ' group by a.`id`';
		if ($filter == 'showbloggerwithpost') {
			$query .= ' having (count(p.id) > 0)';
		}

		switch($sort)
		{
			case 'latestpost' :
				$query .= '	ORDER BY `latestPostDate` DESC';
				break;
			case 'latest' :
				$query .= '	ORDER BY a.`registerDate` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY a.`lastvisitDate` DESC';
				break;
			case 'alphabet' :
				if($nameDisplayFormat == 'name')
					$query .= '	ORDER BY a.`name` ASC';
				else if($nameDisplayFormat == 'username')
					$query .= '	ORDER BY a.`username` ASC';
				else
					$query .= '	ORDER BY b.`nickname` ASC';
				break;
			default	:
				break;
		}

		$query	.= 	$limitSQL;

		$db->setQuery( $query );
		$results	= $db->loadObjectList();

		// now execute found_row() to get the number of records found.
		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery( $cntQuery );
		$this->_total	= $db->loadResult();

		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$limitstart = $this->getState('limitstart');
			$this->_pagination	= new JPagination( $this->_total , $limitstart , $limit );
		}

		return $results;
	}

    public function isBloggerSubscribedUser($bloggerId, $userId, $email = null)
    {
		$db	= EB::db();

        $query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
        $query  .= ' WHERE `uid` = ' . $db->Quote($bloggerId);
        $query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

        if ($email) {
			$query  .= ' AND (`user_id` = ' . $db->Quote($userId);
			$query  .= ' OR `email` = ' . $db->Quote($email) .')';
        } else {
			$query  .= ' AND `user_id` = ' . $db->Quote($userId);
        }

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    public function isBloggerSubscribedEmail($bloggerId, $email)
    {
		$db	= EB::db();

		//lets check if this blogger data cached or not.
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['subs'])) {
				return $data['subs'];
			} else {
				return false;
			}
		}

        $query  = 'SELECT `id` FROM `#__easyblog_subscriptions`';
        $query  .= ' WHERE `uid` = ' . $db->Quote($bloggerId);
        $query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);
        $query  .= ' AND `email` = ' . $db->Quote($email);

        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function addBloggerSubscription($bloggerId, $email, $userId = '0', $fullname = '')
    {
    	$config = EasyBlogHelper::getConfig();
    	$acl = EB::acl();
		$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$date       = EB::date();
			$subscriber = EB::table('Subscriptions');

	        $subscriber->uid = $bloggerId;
	        $subscriber->utype = EBLOG_SUBSCRIPTION_BLOGGER;


	        $subscriber->email    	= $email;
	        if($userId != '0')
	            $subscriber->user_id    = $userId;

			$subscriber->fullname	= $fullname;
			$subscriber->created  	= $date->toMySQL();
	        $state = $subscriber->store();

			if ($state) {
				$profile = EB::user($bloggerId);

				// lets send confirmation email to subscriber.
				$helper 	= EB::subscription();
				$template 	= $helper->getTemplate();

				$template->uid 			= $subscriber->id;
				$template->utype 		= 'bloggersubscription';
				$template->user_id 		= $subscriber->user_id;
				$template->uemail 		= $email;
				$template->ufullname 	= $fullname;
				$template->ucreated 	= $subscriber->created;
				$template->targetname 	= $profile->getName();
				$template->targetlink	= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $bloggerId, false, true );

				$helper->addMailQueue( $template );
			}
		}
    }

    function updateBloggerSubscriptionEmail($sid, $userid, $email)
    {
    	$config = EasyBlogHelper::getConfig();
    	$acl = EB::acl();
    	$my = JFactory::getUser();

		if ($acl->get('allow_subscription') || (empty($my->id) && $config->get('main_allowguestsubscribe'))) {
			$subscriber = EB::table('Subscriptions');
			$subscriber->load($sid);
			$subscriber->email = $email;
			$subscriber->user_id = $userid;
			$subscriber->store();
		}
    }

    function getBlogggerSubscribers($bloggerId)
    {
        $db = EB::db();

        $query  = "SELECT *. 'bloggersubscription' as `type` FROM `#__easyblog_subscriptions`";
        $query .= " WHERE `uid` = " . $db->Quote($bloggerId);
        $query .= " AND `utype` = " . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);

        //echo $query . '<br/><br/>';

        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }

	/**
	 * Retrieves a list of bloggers from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSiteAuthors($limit = 0, $limitstart = 0)
	{
		$db = EB::db();
		$limit = ($limit == 0) ? $this->getState('limit') : $limit;

		if (!$limitstart) {
			$limitstart = $this->getState('limitstart');
		}

		// Generate the acl query
		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		// Build the pagination query
		$query = array();
		$query[] = 'SELECT COUNT(1)';
		$query[] = 'FROM `#__users` as a';
		$query[] = 'inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query[] = 'LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query[] = 'WHERE (' . $aclQuery . ')';
		$query = implode(' ', $query);

		$db->setQuery($query);
		$total = (int) $db->loadResult();


		// Retrieve the total count
		$query = array();
		$query[] = 'SELECT a.`id`, b.`nickname`, a.`name`, a.`username`, a.`registerDate`, a.`lastvisitDate`, b.`permalink`';
		$query[] = 'FROM `#__users` as a';
		$query[] = 'inner JOIN `#__easyblog_users` AS `b` ON a.`id` = b.`id`';
		$query[] = 'LEFT JOIN `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');
		$query[] = 'WHERE (' . $aclQuery . ')';
		$query[] = 'ORDER BY a.`name` ASC';
		$query[] = 'LIMIT ' . $limitstart . ',' . $limit;
		$query = implode(' ', $query);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		$this->_pagination = EB::pagination($total, $limitstart, $limit);

		return $result;
	}

	/**
	 * Retrieves a list of bloggers from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBloggers($sort = 'latest', $limit = 0, $filter='showallblogger' , $search = '', $inclusion = array(), $exclusion = array(), $featuredOnly = '')
	{
		$db = EB::db();
		$config	= EB::config();


		$nameDisplayFormat = $config->get('layout_nameformat');

		$limit = ($limit == 0) ? $this->getState('limit') : $limit;
		$limitstart = $this->input->get('limitstart', $this->getState('limitstart'), 'int');

		$limitSQL = ' LIMIT ' . $limitstart . ',' . $limit;

		$excludedQuery = '';
		$excluded = $config->get('layout_exclude_bloggers');

		// check if there is exclusion from the backend settings OR from the parameter
		if (!empty($excluded) || !empty($exclusion)) {
			$tmp	= explode( ',' , $excluded );

 			if (!empty($excluded) && !empty($exclusion)) {
				$tmp = array_merge($tmp, $exclusion);
			}

			$values	= array();

			foreach ($tmp as $id) {
				$values[]	= $db->Quote( $id );
			}
			$excludedQuery	= ' AND a.`id` NOT IN (' . implode( ',' , $values ) . ')';
		}

		//inclusion blogger
		$includedQuery = '';
		if (!empty($inclusion)) {

			$values	= array();

			foreach ($inclusion as $id) {
				$values[] = $db->Quote($id);
			}

			$includedQuery = ' AND a.id IN (' . implode(',', $values) . ')';

		}

		$searchQuery = '';
		if (!empty($search)) {
			$searchQuery	.= ' AND ';

			switch( $nameDisplayFormat )
			{
				case 'name':
					$searchQuery	.= '`name` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
				case 'username':
					$searchQuery	.= '`username` LIKE ' . $db->Quote( '%' . $search . '%');
				break;
				default:
					$searchQuery	.= '`nickname` LIKE ' . $db->Quote( '%' . $search . '%' );
				break;
			}
		}

		$aclQuery = EB::AclHelper()->genIsbloggerSQL();

		// $query  = 'select count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT( DISTINCT(g.content_id) ) as `featured`,';
		$query  = 'select SQL_CALC_FOUND_ROWS count( p.id ) as `totalPost`, MAX(p.`created`) as `latestPostDate`, COUNT( DISTINCT(g.content_id) ) as `featured`,';
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

		$query .= ' 	left join `#__easyblog_featured` AS `g` ON a.`id`= g.`content_id` AND g.`type`= ' . $db->Quote('blogger');

		$query .= ' where (' . $aclQuery . ')';

		if ($filter == 'showbloggerwithpost') {
			$query .= ' and `p`.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= ' and p.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		}

		$query .= $excludedQuery;
		$query .= $includedQuery;
		$query .= $searchQuery;

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
			case 'latestpost' :
				$query .= '	ORDER BY `latestPostDate` DESC';
				break;
			case 'latest' :
				// $query .= '	ORDER BY a.`registerDate` DESC';
				$query .= '	ORDER BY a.`id` DESC';
				break;
			case 'postcount' :
				$query .= '	ORDER BY `totalPost` DESC';
				break;
			case 'active' :
				$query	.= ' ORDER BY a.`lastvisitDate` DESC';
				break;
			case 'alphabet' :
				if($nameDisplayFormat == 'name')
					$query .= '	ORDER BY a.`name` ASC';
				else if($nameDisplayFormat == 'username')
					$query .= '	ORDER BY a.`username` ASC';
				else
					$query .= '	ORDER BY b.`nickname` ASC';
				break;
			default	:
				break;
		}

		$query .= $limitSQL;

		$db->setQuery($query);
		$results = $db->loadObjectList();

		// now execute found_row() to get the number of records found.
		$cntQuery = 'select FOUND_ROWS()';
		$db->setQuery( $cntQuery );
		$this->_total	= $db->loadResult();

		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = EB::pagination($this->_total, $limitstart, $limit);
		}

		return $results;
	}

	public function getTagUsed($bloggerId)
	{
		$db = EB::db();

		//lets check if this blogger data cached or not.
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['tag'])) {
				return $data['tag'];
			} else {
				return array();
			}
		}

		$query  = 'select distinct a.* from `#__easyblog_tag` as a';
		$query  .= ' inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query  .= ' inner join `#__easyblog_post` as c on b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	public function getCategoryUsed($bloggerId)
	{
		$db = EB::db();

		//lets check if this blogger data cached or not.
		if (EB::cache()->exists($bloggerId, 'bloggers')) {
			$data = EB::cache()->get($bloggerId, 'bloggers');

			if (isset($data['category'])) {
				return $data['category'];
			} else {
				return array();
			}
		}

		$query  = 'select distinct a.*, count(b.`id`) as `post_count` from `#__easyblog_category` as a';
		$query  .= ' inner join `#__easyblog_post_category` as b ON a.`id` = b.`category_id`';
		$query  .= ' inner join `#__easyblog_post` as c ON b.`post_id` = c.`id`';
		$query  .= ' where c.`created_by` = ' . $db->Quote($bloggerId);
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query  .= ' group by a.id';
		$query  .= ' order by null';

		$db->setQuery($query);

		$result	= $db->loadObjectList();
		return $result;
	}

	/**
	 * Retrieves the total number of posts created by an author.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalBlogCreated($id)
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post') . ' AS a';
		$query[] = 'WHERE a.' . $db->qn('created_by') . '=' . $db->Quote($id);
		$query[] = 'AND a.' . $db->qn('published') . '=' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query[] = 'AND a.' . $db->qn('state') . '=' . $db->Quote(EASYBLOG_POST_NORMAL);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result	= $db->loadResult();
		return $result;
	}

	/**
	 * preload tag used by bloggers.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function preloadTagUsed($bloggerIds) {

		$db = EB::db();

		$query  = 'select distinct a.*, c.`created_by` as `author_id`';
		$query 	.= ' from `#__easyblog_tag` as a';
		$query  .= ' 	inner join `#__easyblog_post_tag` as b on a.`id` = b.`tag_id`';
		$query  .= ' 	inner join `#__easyblog_post` as c on b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` IN (' . implode(',', $bloggerIds) . ')';
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

		$db->setQuery($query);

		$results	= $db->loadObjectList();

		$tags = array();

		if ($results) {
			foreach($results as $result) {
				$tags[$result->author_id][$result->id] = $result;
			}
		}

		return $tags;
	}

	public function preloadCategoryUsed($bloggerIds)
	{
		$db = EB::db();

		$query  = 'select distinct a.*, count(b.`id`) as `post_count`, c.`created_by` as `author_id`';
		$query 	.= ' from `#__easyblog_category` as a';
		$query  .= ' 	inner join `#__easyblog_post_category` as b ON a.`id` = b.`category_id`';
		$query  .= ' 	inner join `#__easyblog_post` as c ON b.`post_id` = c.`id`';
		$query	.= ' where c.`created_by` IN (' . implode(',', $bloggerIds) . ')';
		$query  .= ' and c.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
		$query  .= ' and c.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
		$query  .= ' group by a.`id`, c.`created_by`';
		$query 	.= ' order by null';

		$db->setQuery($query);

		$results	= $db->loadObjectList();

		$categories = array();

		if ($results) {
			foreach($results as $result) {
				$categories[$result->author_id][$result->id] = $result;
			}
		}

		return $categories;
	}

    public function preloadBlogggerSubscribers($bloggerIds)
    {
        $db = EB::db();

        $my = JFactory::getUser();

        $email = $my->email;

        $query  = 'SELECT `id`, `uid` FROM `#__easyblog_subscriptions`';
        $query  .= ' WHERE `uid` IN (' . implode(',', $bloggerIds) . ')';
        $query  .= ' AND `utype` = ' . $db->Quote(EBLOG_SUBSCRIPTION_BLOGGER);
        $query  .= ' AND `email` = ' . $db->Quote($email);

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $subs = array();

        if ($results) {
	        foreach($results as $result) {
	        	$subs[$result->uid] = $result->id;
	        }
    	}

        return $subs;
    }


}
