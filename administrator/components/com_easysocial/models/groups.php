<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelGroups extends EasySocialModel
{
	public function __construct($config = array())
	{
		parent::__construct('groups', $config);
	}

	/**
	 * Initializes all the generic states from the form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initStates()
	{
		$filter 	= $this->getUserStateFromRequest('state', 'all');
		$ordering 	= $this->getUserStateFromRequest('ordering', 'id');
		$direction	= $this->getUserStateFromRequest('direction', 'DESC');
		$type		= $this->getUserStateFromRequest('type', 'all');

		$this->setState('type', $type);
		$this->setState('state', $filter);

		parent::initStates();

		// Override the ordering behavior
		$this->setState('ordering', $ordering);
		$this->setState('direction', $direction);
	}

	/**
	 * Saves the ordering of profiles
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveOrder($ids, $ordering)
	{
		$table 	= FD::table('Profile');
		$table->reorder();
	}

	/**
	 * Retrieves group creation stats for a particular category
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The category id
	 * @return
	 */
	public function getCreationStats($categoryId)
	{
		$db			= FD::db();
		$sql 		= $db->sql();
		$dates 		= array();

		// Get the past 7 days
		$curDate 	= FD::date();
		for($i = 0 ; $i < 7; $i++)
		{
			$obj 		= new stdClass();

			if ($i == 0) {
				$obj->date 	= $curDate->toMySQL();
			}
			else
			{
				$unixdate 		= $curDate->toUnix();
				$new_unixdate 	= $unixdate - ($i * 86400);
				$newdate  		= FD::date($new_unixdate);

				$obj->date 		= $newdate->toSql();
			}

			$dates[]	= $obj;
		}

		// Reverse the dates
		$dates 		= array_reverse($dates);
		$result		= array();

		foreach ($dates as &$row) {
			// Registration date should be Y, n, j
			$date	= FD::date($row->date)->format('Y-m-d');

			$query 		= array();
			$query[]	= 'SELECT COUNT(1) AS `cnt` FROM ' . $db->nameQuote('#__social_clusters') . ' AS a';
			$query[]	= 'WHERE DATE_FORMAT(a.created, GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[]	= 'AND a.`category_id` = ' . $db->Quote($categoryId);
			$query[]	= 'AND a.`type` = ' . $db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE);
			$query[]    = 'group by a.`category_id`';

			$query 		= implode(' ', $query);
			$sql->raw($query);


			$db->setQuery($sql);

			$total		= $db->loadResult();

			$result[]	= (int) $total;
		}

		return $result;
	}

	/**
	 * Retrieves the total number of announcements
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalNews($groupId, $options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_news', 'a');
		$sql->column('COUNT(1)');
		$sql->where('a.cluster_id', $groupId);

		// If we should exclude specific items
		$exclude = isset($options['exclude']) ? $options['exclude'] : '';

		if ($exclude) {
			$sql->where('a.id', $exclude, 'NOT IN');
		}

		$db->setQuery($sql);
		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of news item from a particular group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getNews($groupId, $options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters_news', 'a');
		$sql->where('a.cluster_id', $groupId);

		// If we should exclude specific items
		$exclude 	= isset($options[ 'exclude' ]) ? $options[ 'exclude' ] : '';

		if ($exclude) {
			$sql->where('a.id', $exclude, 'NOT IN');
		}

		$sql->order('created', 'DESC');

		$limit 	= isset($options[ 'limit' ]) ? $options[ 'limit' ] : '';

		if ($limit) {
			$this->setState('limit', $limit);

			// Get the limitstart.
			$limitstart 	= $this->getUserStateFromRequest('limitstart', 0);
			$limitstart 	= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$this->setState('limitstart', $limitstart);

			// Run pagination here.
			$this->setTotal($sql->getTotalSql());

			$result		= $this->getData($sql->getSql());
		}
		else
		{
			$db->setQuery($sql);
			$result 	= $db->loadObjectList();
		}

		$result 	= $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$items 	= array();

		foreach ($result as $row) {
			$news 	= FD::table('GroupNews');
			$news->bind($row);

			$items[]	= $news;
		}

		return $items;
	}

	/**
	 * Retrieves a list of random members from a particular category
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRandomCategoryMembers($categoryId, $limit = false)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters_nodes', 'a');
		$sql->column('DISTINCT(a.uid)');
		$sql->innerjoin('#__social_clusters', 'b');
		$sql->on('a.cluster_id', 'b.id');

		// exclude esad users
		$sql->innerjoin('#__social_profiles_maps', 'upm');
		$sql->on('a.uid', 'upm.user_id');

		$sql->innerjoin('#__social_profiles', 'up');
		$sql->on('upm.profile_id', 'up.id');
		$sql->on('up.community_access', '1');


		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.uid' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where('b.category_id', $categoryId);

		$state 	= isset($options[ 'state' ]) ? $options[ 'state' ] : '';

		if ($state) {
			$sql->where('state', $state);
		}

		// Determine if we should retrieve admins only
		$adminOnly 	= isset($options[ 'admin' ]) ? $options[ 'admin' ] : '';

		if ($adminOnly) {
			$sql->where('admin', SOCIAL_STATE_PUBLISHED);
		}

		// Only fetch open group details
		$sql->where('b.type', SOCIAL_GROUPS_PUBLIC_TYPE);

		$sql->order('', 'ASC', 'RAND');

		if ($limit) {
			$sql->limit($limit);
		}

		// Set the total records for pagination.
		// $this->setTotal($sql->getTotalSql());

		// echo $sql;

		// Run the main query to get the list of users
		$db->setQuery($sql);

		// Get the final result
		$result		= $this->getData($sql->getSql());

		if (!$result) {
			return $result;
		}

		$users 		= array();

		foreach ($result as $row) {
			$user 	= FD::user($row->uid);

			$users[]	= $user;
		}

		return $users;
	}

	public function getFilters($groupId, $userId = '')
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select * from `#__social_stream_filter`';
		$query .= ' where `uid` = ' . $db->Quote($groupId);
		$query .= ' and `utype` = ' . $db->Quote(SOCIAL_TYPE_GROUP);
		if ($userId)
			$query .= ' and `user_id` = ' . $db->Quote($userId);

		$sql->raw($query);
		$db->setQuery($sql);

		$result = $db->loadObjectList();

		$items = array();

		if ($result) {
			foreach ($result as $row) {
				$sf = FD::table('StreamFilter');
				$sf->bind($row);

				$items[] = $sf;
			}
		}

		return $items;
	}

	/**
	 * Retrieves a list of members from a particular group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMembers($groupId, $options = array())
	{
		static $cache = array();

		ksort($options);

		$optionskey = serialize($options);


		$load = array();
		if (is_array($groupId)) {
			foreach($groupId as $gid) {
				if (! isset($cache[$gid][$optionskey])) {
					$load[] = $gid;
				}
			}
		} else {

			if (! isset($cache[$groupId][$optionskey])) {
				$load[] = $groupId;
			}
		}


		if ($load) {

			// prefill empty array
			if (count($load) > 1) {
				foreach($load as $ld) {
					$cache[$ld][$optionskey] = array();
				}
			} else {
				$cache[$load[0]][$optionskey] = array();
			}

			$db 	= FD::db();
			$sql 	= $db->sql();

			$sql->select('#__social_clusters_nodes', 'a');
			$sql->column('a.*');

			if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    $sql->leftjoin( '#__social_block_users' , 'bus');
			    $sql->on( 'a.uid' , 'bus.user_id' );
			    $sql->on( 'bus.target_id', JFactory::getUser()->id );
			    $sql->isnull('bus.id');
			}

			// We should not fetch banned users
			$sql->join('#__users', 'u');
			$sql->on('a.uid', 'u.id');

			// exclude esad users
			$sql->innerjoin('#__social_profiles_maps', 'upm');
			$sql->on('u.id', 'upm.user_id');

			$sql->innerjoin('#__social_profiles', 'up');
			$sql->on('upm.profile_id', 'up.id');
			$sql->on('up.community_access', '1');

			// By specific groups
			if (count($load) > 1) {
				$sql->where('a.cluster_id', $load, 'IN');
			} else {
				$sql->where('a.cluster_id', $load[0]);
			}

			// Whe the user isn't blocked
			$sql->where('u.block', 0);

			$state = isset($options['state']) ? $options['state'] : '';

			if ($state) {
				$sql->where('a.state', $state);
			}

			// Determine if we should retrieve admins only
			$adminOnly = isset($options['admin']) ? $options['admin'] : '';

			if ($adminOnly) {
				$sql->where('a.admin', SOCIAL_STATE_PUBLISHED);
			}

			// Determine if we want to exclude this.
			$exclude 	= isset($options[ 'exclude' ]) ? $options[ 'exclude' ] : '';

			if ($exclude) {
				$sql->where('a.uid', $exclude, '<>');
			}

			if (!empty($options['ordering'])) {
				$direction = !empty($options['direction']) ? $options['direction'] : 'asc';

				$sql->order($options['ordering'], $direction);
			}

			// Should we apply pagination
			$limit = isset($options['limit']) ? $options['limit'] : '';

			// echo $sql->debug();exit;

			if ($limit) {

				$this->setState( 'limit' , $limit );

				// Get the limitstart.
				$limitstart 	= $this->getUserStateFromRequest( 'limitstart' , 0 );
				$limitstart 	= ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

				$this->setState( 'limitstart' , $limitstart );


				// Set the total records for pagination.
				// $this->setTotal($sql->getTotalSql());

				$this->setTotal( $sql->getSql() , true );

				// $this->setLimit($limit);

				// Get the final result
				$result = $this->getData($sql->getSql());
			} else {
				// Run the main query to get the list of users
				$db->setQuery($sql);

				$result = $db->loadObjectList();
			}

			if ($result) {
				foreach($result as $item) {
					$cache[$item->cluster_id][$optionskey][] = $item;
				}
			}

		}

		if (is_array($groupId)) {
			// when this is an array of group ids, we know we are doign preload. lets return true.
			return true;
		}


		$result = $cache[$groupId][$optionskey];

		$usersObject 	= isset($options[ 'users' ]) ? $options[ 'users' ] : true;

		$users 		= array();

		if ($usersObject) {
			//preload users
			$userIds = array();
			foreach ($result as $row) {
				$userIds[] = $row->uid;
			}
			FD::user($userIds);

			//
			foreach ($result as $row) {
				$user 	= FD::user($row->uid);
				$users[]	= $user;
			}
		} else {
			// return plain object lists since we no longer need to bind to jtable for members checking.
			$users = $result;
		}

		return $users;
	}

	/**
	 * Retrieves random albums
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRandomAlbums($options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_albums', 'a');
		$sql->column('a.*');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.user_id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// Filter by category id
		$category 	= isset($options[ 'category_id' ]) ? $options[ 'category_id' ] : '';

		if ($category) {
			$sql->join('#__social_clusters', 'b');
			$sql->on('a.uid', 'b.id');
			$sql->join('#__social_clusters_categories', 'c');
			$sql->on('c.id', 'b.category_id');

			$sql->where('c.id', (int) $category);
			$sql->where('a.type', SOCIAL_TYPE_GROUP);
			$sql->where('b.type', SOCIAL_GROUPS_PUBLIC_TYPE);
		}

		// Determine if we should include the core albums
		$coreAlbums 	= isset($options[ 'core' ]) ? $options[ 'core' ] : true;

		if (!$coreAlbums) {
			$sql->where('a.core', 0);
		}

		$coreAlbumsOnly	= isset($options[ 'coreAlbumsOnly' ]) ? $options[ 'coreAlbumsOnly' ] : '';

		if ($coreAlbumsOnly) {
			$sql->where('a.core', 0, '>');
		}

		$withCoversOnly	= isset($options[ 'withCovers' ]) ? $options[ 'withCovers' ] : '';

		if ($withCoversOnly) {
			$sql->join('#__social_photos', 'b', 'INNER');
			$sql->on('a.cover_id', 'b.id');
		}

		$ordering 		= isset($options[ 'order' ]) ? $options[ 'order' ] : '';

		if ($ordering) {
			$direction 	= isset($options[ 'direction' ]) ? $options[ 'direction' ] : 'desc';

			$sql->order($ordering, $direction);
		}


		$pagination 	= isset($options[ 'pagination' ]) ? $options[ 'pagination' ] : false;


		$result = array();

		if ($pagination) {
			// Set the total number of items.
			$totalSql 		= $sql->getSql();
			$this->setTotal($totalSql, true);

			$result			= $this->getData($sql->getSql());
		}
		else
		{
			$limit 		= isset($options[ 'limit' ]) ? $options[ 'limit' ] : '';
			if ($limit) {
				$sql->limit($limit);
			}

			$db->setQuery($sql);
			$result 	= $db->loadObjectList();
		}



		if (!$result) {
			return $result;
		}

		$albums 	= array();

		foreach ($result as $row) {
			$album 	= FD::table('Album');
			$album->bind($row);

			$albums[]	= $album;
		}

		return $albums;
	}

	/**
	 * Retrieves the total number of groups in the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of profiles.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalGroups($options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters', 'a');
        $sql->column('a.id', 'id', 'count distinct');

        // Check for blocked users
		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.creator_uid' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where('a.state', SOCIAL_CLUSTER_PUBLISHED);
		$sql->where('a.cluster_type', SOCIAL_TYPE_GROUP);

		$types = isset($options['types']) ? $options['types'] : '';

		if ($types != 'all') {
			if ($types === 'user') {
				$userid = isset($options['userid']) ? $options['userid'] : FD::user()->id;

				$sql->leftjoin('#__social_clusters_nodes', 'nodes');
				$sql->on('a.id', 'nodes.cluster_id');

				$sql->where('(');
				$sql->where('a.type', array(SOCIAL_GROUPS_PRIVATE_TYPE, SOCIAL_GROUPS_PUBLIC_TYPE), 'IN');
				$sql->where('(', '', '', 'OR');
				$sql->where('a.type', SOCIAL_GROUPS_INVITE_TYPE);
				$sql->where('nodes.uid', $userid);
				$sql->where(')');
				$sql->where(')');
			} else {

				// Get the current logged in user
				$my = FD::user();

				if (!$my->isSiteAdmin()) {
					$sql->where('a.type', array(SOCIAL_GROUPS_PRIVATE_TYPE, SOCIAL_GROUPS_PUBLIC_TYPE), 'IN');
				}
			}
		}

		// Test to check against category id
		$category 	= isset($options[ 'category_id' ]) ? $options[ 'category_id' ] : '';

		if ($category) {
			$sql->where('a.category_id', $category);
		}

		// Test to filter featured items
		$featured 	= isset($options[ 'featured' ]) ? $options[ 'featured' ] : '';

		if ($featured !== '') {
			$sql->where('a.featured', $featured);
		}

		$db->setQuery($sql);
		$count = (int) $db->loadResult();

		return $count;
	}

	/**
	 * Retrieves the total number of groups in the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of profiles.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalAlbums($options = array())
	{
		$config = FD::config();
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select count(1)';
		$query .= ' from `#__social_albums` as a';

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON a.' . $db->nameQuote( 'user_id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$query .= ' inner join `#__social_clusters` as b on a.`uid` = b.`id`';
		$query .= ' where a.`type` = ' . $db->Quote(SOCIAL_TYPE_GROUP);
		$query .= ' and a.`core` = ' . $db->Quote('0'); // do not get core album
		$query .= ' and b.`state` = ' . $db->Quote(SOCIAL_CLUSTER_PUBLISHED);
		$query .= ' and b.`type` != ' . $db->Quote(SOCIAL_GROUPS_INVITE_TYPE);


		// Test to check against category id
		$category 	= isset($options[ 'category_id' ]) ? $options[ 'category_id' ] : '';

		if ($category) {
			$query .= ' and b.`category_id` = ' . $db->Quote($category);
		}

		// Test to filter featured items
		$featured 	= isset($options[ 'featured' ]) ? $options[ 'featured' ] : '';

		if ($featured !== '') {
			$query .= ' and b.`featured` = ' . $db->Quote($featured);
		}

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		$sql->raw($query);
		$db->setQuery($sql);
		$count 		= (int) $db->loadResult();

		return $count;
	}

	/**
	 * Retrieves a list of online users from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOnlineMembers($groupId)
	{
		$db = FD::db();
		$sql = $db->sql();

		// Get the session life time so we can know who is really online.
		$jConfig 	= FD::jConfig();
		$lifespan 	= $jConfig->getValue('lifetime');
		$online 	= time() - ($lifespan * 60);

		$sql->select('#__session', 'a');
		$sql->column('b.id');
		$sql->join('#__users', 'b', 'INNER');
		$sql->on('a.userid', 'b.id');
		$sql->join('#__social_clusters_nodes', 'c', 'INNER');
		$sql->on('c.uid', 'b.id');
		$sql->on('c.type', SOCIAL_TYPE_USER);

        // exclude esad users
        $sql->innerjoin('#__social_profiles_maps', 'upm');
        $sql->on('c.uid', 'upm.user_id');

        $sql->innerjoin('#__social_profiles', 'up');
        $sql->on('upm.profile_id', 'up.id');
        $sql->on('up.community_access', '1');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'b.id' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where('a.time', $online, '>=');
		$sql->where('b.block', 0);
		$sql->where('c.cluster_id', $groupId);
		$sql->group('a.userid');

		$db->setQuery($sql);

		$result = $db->loadColumn();

		if (!$result) {
			return array();
		}

		$users	= FD::user($result);

		return $users;
	}

	/**
	 * Retrieves the total number of profiles in the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of profiles.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalInvites($userId)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();


		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('state', SOCIAL_GROUPS_MEMBER_INVITED);
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);

		$db->setQuery($sql);
		$count 		= (int) $db->loadResult();

		return $count;
	}

	/**
	 * Retrieves the total number of profiles in the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	int		The total number of profiles.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalMembers($clusterId)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();


		$sql->select('#__social_clusters_nodes', 'a');
		$sql->column('COUNT(1)');

        // exclude esad users
        $sql->innerjoin('#__social_profiles_maps', 'upm');
        $sql->on('a.uid', 'upm.user_id');

        $sql->innerjoin('#__social_profiles', 'up');
        $sql->on('upm.profile_id', 'up.id');
        $sql->on('up.community_access', '1');

		$sql->where('a.cluster_id', $clusterId);
		$sql->where('a.state', SOCIAL_STATE_PUBLISHED);

		$db->setQuery($sql);
		$count 		= (int) $db->loadResult();

		return $count;
	}

	/**
	 * Dprecated since 1.2. Use EasySocialModelGroupCategories::getItems() instead.
	 * Retrieves a list of cluster categories on the site.
	 *
	 * @since	1.0
	 * @deprecated  1.2
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getCategoriesWithState($options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters_categories');

		// Check for search
		$search 	= $this->getState('search');

		if ($search) {
			$sql->where('title', '%' . $search . '%', 'LIKE');
		}

		// Check for state
		$state 		= $this->getState('state');

		if ($state != 'all') {
			$sql->where('state', $state);
		}

		// This must always be checked
		$sql->where('type', SOCIAL_TYPE_GROUP);

		$ordering = $this->getState('ordering', 'ordering');
		$direction = $this->getState('direction', 'asc');

		$sql->order($ordering, $direction);

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$db->setQuery($sql);

		$result		= $this->getData($sql->getSql());

		if (!$result) {
			return false;
		}

		$categories	= array();
		$total      = count($result);

		for($i = 0; $i < $total; $i++)
		{
			$category       = FD::table('GroupCategory');
			$category->bind($result[ $i ]);

			$categories[]    = $category;
		}

		return $categories;
	}

	/**
	 * Deprecated. Use EasySocialModelGroupCategories::getCategories() instead.
	 * Retrieves a list of cluster categories on the site.
	 *
	 * @since	1.0
	 * @deprecated 1.2
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getCategories($options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters_categories', 'a');
		$sql->column('a.*');

		// Check for search
		$search 	= isset($options[ 'search' ]) ? $options[ 'search' ] : '';

		if ($search) {
			$sql->where('a.title', '%' . $search . '%', 'LIKE');
		}

		// Check for state
		$state 		= isset($options[ 'state' ]) ? $options[ 'state' ] : '';

		if ($state != 'all') {
			$sql->where('a.state', $state);
		}

		// Check for profile access
		$profileId 	= isset($options['profile_id']) ? $options['profile_id'] : '';

		if ($profileId) {

			$sql->join('#__social_clusters_categories_access', 'c');
			$sql->on('a.id', 'c.category_id');
			$sql->on('c.type', 'create');

			$sql->where('c.profile_id', $profileId);
		}

		// This must always be checked
		$sql->where('a.type', SOCIAL_TYPE_GROUP);

		// Determine the ordering
		$ordering 	= isset($options[ 'ordering' ]) ? $options[ 'ordering' ] : 'ordering';

		if ($ordering == 'title') {
			$sql->order('a.title', 'ASC');
		}

		// Order by total number of groups
		if ($ordering == 'groups') {
			$sql->join('#__social_clusters', 'b');
			$sql->on('b.category_id', 'a.id');
			$sql->on('b.state', SOCIAL_CLUSTER_PUBLISHED);
			$sql->order('COUNT(b.id)', 'DESC');
			$sql->group('a.id');
		}

		if ($ordering == 'ordering') {
			$sql->order('a.ordering');
		}

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$db->setQuery($sql);

		$result		= $this->getData($sql->getSql());

		if (!$result) {
			return false;
		}

		$categories	= array();
		$total      = count($result);

		for($i = 0; $i < $total; $i++)
		{
			$category       = FD::table('GroupCategory');
			$category->bind($result[ $i ]);

			$categories[]    = $category;
		}

		return $categories;
	}

	/**
	 * Retrieves a list of cluster categories on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getCreatableCategories($profileId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = array();

		$query[] = "SELECT DISTINCT `a`.* FROM `#__social_clusters_categories` AS `a`";
		$query[] = "LEFT JOIN `#__social_clusters_categories_access` AS `b`";
		$query[] = "ON `a`.`id` = `b`.`category_id`";
		$query[] = "WHERE `a`.`type` = 'group'";
        $query[] = "AND `a`.`state` = '1'";

		if (!FD::user()->isSiteAdmin()) {
			$query[] = "AND (`b`.`profile_id` = " . $profileId;
			$query[] = "OR `a`.`id` NOT IN (SELECT `category_id` FROM `#__social_clusters_categories_access`))";
		}

		$query[] = "ORDER BY `a`.`ordering`";

		$db->setQuery($sql->raw(implode(' ', $query)));


		$result = $db->loadObjectList();

		$categories = $this->bindTable('EventCategory', $result);

		return $categories;
	}

	/**
	 * Retrieves a list of custom profiles from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getItemsWithState($options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters', 'a');
		$sql->column('a.*');
		$sql->column('b.title', 'categoryTitle');

		// Check for search
		$search = $this->getState('search');

		if ($search) {
			$sql->where('a.title', '%' . $search . '%', 'LIKE');
		}

		// Determines if we should load pending groups
		$pending = $this->normalize($options, 'pending', false);

		if ($pending) {
			$sql->where('a.state', SOCIAL_CLUSTER_PENDING, '=');
		} else {
			$sql->where('a.state', SOCIAL_CLUSTER_PENDING, '!=');
		}

		$state = $this->getState('state');

		if ($state != 'all') {
			$sql->where('a.state', $state);
		}

		$type = $this->getState('type');

		if ($type != 'all') {
			$sql->where('a.type', $type);
		}

		$ordering = $this->getState('ordering');

		if ($ordering) {
			$direction	= $this->getState('direction');

			$sql->order($ordering, $direction);
		}

		// Join with the category as we need to order by category
		$sql->join('#__social_clusters_categories', 'b');
		$sql->on('b.id', 'a.category_id');

		// This must always be checked
		$sql->where('a.cluster_type', SOCIAL_TYPE_GROUP);

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$db->setQuery($sql);

		$result		= $this->getData($sql->getSql());

		if (!$result) {
			return false;
		}

		$groups		= array();

		foreach ($result as $row) {
			$group 		= FD::group($row->id);
			$groups[]	= $group;
		}

		return $groups;
	}

	/**
	 * Retrieves a list of custom profiles from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	Array	An array list of SocialTableProfile
	 *
	 */
	public function getItems($options = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters');

		// Check for search
		$search 	= $this->getState('search');

		if ($search) {
			$sql->where('title', '%' . $search . '%', 'LIKE');
		}

		// Determines if we should load pending groups
		$pending 	= isset($options[ 'pending' ]) ? $options[ 'pending' ] : false;

		if ($pending) {
			$sql->where('state', SOCIAL_CLUSTER_PENDING, '=');
		}
		else
		{
			$sql->where('state', SOCIAL_CLUSTER_PENDING, '!=');
		}

		// This must always be checked
		$sql->where('cluster_type', SOCIAL_TYPE_GROUP);

		// Set the total records for pagination.
		$this->setTotal($sql->getTotalSql());

		$db->setQuery($sql);

		$result		= $this->getData($sql->getSql());

		if (!$result) {
			return false;
		}

		$groups		= array();

		foreach ($result as $row) {
			$group 		= FD::group($row->id);
			$groups[]	= $group;
		}

		return $groups;
	}

	/**
	 * Generates a unique alias for the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function getUniqueAlias($title, $exclude = null)
	{
		// Pass this back to Joomla to ensure that the permalink would be safe.
		$alias = JFilterOutput::stringURLSafe($title);

		$model = FD::model('Clusters');

		$i = 2;

		// Set this to a temporary alias
		$tmp = $alias;

		do {
			$exists = $model->clusterAliasExists($alias, $exclude, SOCIAL_TYPE_GROUP);

			if ($exists) {
				$alias	= $tmp . '-' . $i++;
			}

		} while ($exists);

		return $alias;
	}

	/**
	 * Retrieves the total number of groups a user is participating in.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalParticipatedGroups($userId, $filter = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters_nodes', 'a');
		$sql->column('COUNT(1)');

		// Ensure that the group is published
		$sql->join('#__social_clusters', 'b', 'INNER');
		$sql->on('b.id', 'a.cluster_id');

		$sql->where('a.uid', $userId);
		$sql->where('a.type', SOCIAL_TYPE_USER);
		$sql->where('a.state', SOCIAL_STATE_PUBLISHED);
		$sql->where('b.state', SOCIAL_STATE_PUBLISHED);
		$sql->where('b.cluster_type', SOCIAL_TYPE_GROUP);

		$types 		= isset($filter[ 'types' ]) ? $filter[ 'types' ] : '';
		if ($types) {
			if($types == 'invited') {
				$sql->where('b.type', SOCIAL_GROUPS_INVITE_TYPE);
			} else {
				$sql->where('b.type', array(SOCIAL_GROUPS_PRIVATE_TYPE, SOCIAL_GROUPS_PUBLIC_TYPE), 'IN');
			}
		}


		$db->setQuery($sql);

		$total 	= (int) $db->loadResult();

		return $total;
	}

	/**
	 * Get a list of groups from this user
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return groups
	 */
	public function getUserGroups( $userId )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'select a.`cluster_id` from `#__social_clusters_nodes` as a';
		$query .= '	inner join `#__social_clusters` as b on a.`cluster_id` = b.`id`';
		$query .= '		and b.`cluster_type` = ' . $db->Quote( SOCIAL_TYPE_GROUP ) . ' and b.`state` = ' . $db->Quote( SOCIAL_STATE_PUBLISHED );
		$query .= ' where a.`uid` = ' . $db->Quote($userId);
		$query .= ' and a.`state` = ' . $db->Quote( SOCIAL_GROUPS_MEMBER_PUBLISHED );
		$query .= ' ORDER BY `a`.`created` DESC';

		$sql->raw($query);
		$db->setQuery($sql);

		$ids = $db->loadColumn();

		$groups 	= array();
		if ($ids) {
			// foreach ($ids as $id) {
				// $groups[]	= FD::group($id->cluster_id);
				// $groups[]	= FD::group($id);

			// }
			$groups = FD::group()->loadGroups($ids);
		}
		return $groups;
	}



	/**
	 * Get a list of groups from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups($filter = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters', 'a');
		$sql->column('DISTINCT(a.id)');

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.creator_uid' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where('a.cluster_type', SOCIAL_TYPE_GROUP);

		// Test to filter by category
		$category = $this->normalize($filter, 'category', '');

		if ($category) {
			$sql->where('a.category_id', $category);
		}

		// Test to filter by creator
		$uid = $this->normalize($filter, 'uid', '');

		if ($uid) {
			$sql->join('#__social_clusters_nodes', 'c', 'INNER');
			$sql->on('a.id', 'c.cluster_id');

			$sql->where('c.uid', $uid);
			$sql->where('c.state', SOCIAL_GROUPS_MEMBER_PUBLISHED);
		}

		// Test to filter by invitation
		$invited = $this->normalize($filter, 'invited', '');

		if ($invited) {

			$sql->join('#__social_clusters_nodes', 'b', 'INNER');
			$sql->on('b.cluster_id', 'a.id');

			$sql->where('b.state', SOCIAL_GROUPS_MEMBER_INVITED);
			$sql->where('b.uid', $invited);
		}

		// Test to filter featured items
		$featured = $this->normalize($filter, 'featured', '');

		if ($featured !== '') {
			$sql->where('a.featured', $featured);
		}

		// Test if there is an inclusion
		$inclusion = $this->normalize($filter, 'inclusion', '');

		if ($inclusion !== '') {
			$groupId = explode(',',$inclusion);
			$sql->where( 'a.id', $groupId, 'in' );
		}

		// Test to filter all group types
		$types = isset($filter['types']) ? $filter['types'] : '';

		if ($types != 'all') {

			$userid = isset($filter['userid']) ? $filter['userid'] : FD::user()->id;

			// currentuser type is currently used in groups module.
			if ($types === 'currentuser' && $userid) {

				$sql->innerjoin('#__social_clusters_nodes', 'nodes');
				$sql->on('a.id', 'nodes.cluster_id');
				$sql->where('nodes.uid', $userid);

			} else if ($types === 'user' && $userid) {

				$sql->leftjoin('#__social_clusters_nodes', 'nodes');
				$sql->on('a.id', 'nodes.cluster_id');
				$sql->where('(');
				$sql->where('a.type', array(SOCIAL_GROUPS_PRIVATE_TYPE, SOCIAL_GROUPS_PUBLIC_TYPE), 'IN');
				$sql->where('(', '', '', 'OR');
				$sql->where('a.type', SOCIAL_GROUPS_INVITE_TYPE);
				$sql->where('nodes.uid', $userid);
				$sql->where(')');
				$sql->where(')');

			} else {
				$sql->where('a.type', array(SOCIAL_GROUPS_PRIVATE_TYPE, SOCIAL_GROUPS_PUBLIC_TYPE), 'IN');
			}
		}

		// Test to filter published / unpublished groups
		$state = isset($filter[ 'state' ]) ? $filter[ 'state' ] : '';

		if ($state) {
			$sql->where('a.state', $state);
		}

		// Determines if there are ordering options supplied
		$ordering 	= isset($filter[ 'ordering' ]) ? $filter[ 'ordering' ] : 'latest';

		$cntSQL = '';

		if ($ordering == 'members') {
			$sql->join('#__social_clusters_nodes', 'f', 'INNER');
			$sql->on('f.cluster_id', 'a.id');
			$sql->on('f.state', SOCIAL_GROUPS_MEMBER_PUBLISHED);

			// lets get the sql without the order by condition.
			$cntSQL = $sql->getSql();

			$sql->order('COUNT(f.`id`)', 'DESC');
			$sql->group('a.id');
		} else {

			// lets get the sql without the order by condition.
			$cntSQL = $sql->getSql();

			if ($ordering == 'popular') {
				$sql->order('a.hits', 'DESC');
			}

			if ($ordering == 'latest') {
				$sql->order('a.created', 'DESC');
			}

			if ($ordering == 'random') {
				$sql->order('', 'DESC', 'RAND');
			}

			if ($ordering == 'name') {
				$sql->order('a.title', 'ASC');
			}
		}

		$limit = $this->normalize($filter, 'limit', '');

		if ($limit) {
			$this->setState('limit', $limit);

			// Get the limitstart.
			//$limitstart = $this->getUserStateFromRequest('limitstart', 0);
			$limitstart = JRequest::getInt('limitstart', 0);

			$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

			$this->setState('limitstart', $limitstart);

			// Set the total records for pagination.
			$this->setTotal($cntSQL, true);

			$query = $sql->getSql();


			// Get the list of ids
			$ids = $this->getData($query);
		} else {
			$db->setQuery($sql);

			$ids = $db->loadObjectList();
		}

		if (!$ids) {
			return $ids;
		}

		$groups = array();

		foreach ($ids as $id) {
			$groups[] = FD::group($id->id);
		}

		return $groups;
	}

	/**
	 * Retrieves the meta data of a list of groups
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMeta($ids = array())
	{
		static $loaded = array();

		// Store items that needs to be loaded
		$loadItems 	= array();

		foreach ($ids as $id) {
			$id 	= (int) $id;

			if (!isset($loaded[ $id ])) {
				$loadItems[]	= $id;

				// Initialize this with a false value first.
				$loaded[ $id ]	= false;
			}
		}

		// Determines if there is new items to be loaded
		if ($loadItems) {
			$db		= FD::db();
			$sql 	= $db->sql();

			$sql->select('#__social_clusters', 'a');
			$sql->column('a.*');
			$sql->column('b.small');
			$sql->column('b.medium');
			$sql->column('b.large');
			$sql->column('b.square');
			$sql->column('b.avatar_id');
			$sql->column('b.photo_id');
			$sql->column('b.storage', 'avatarStorage');
			$sql->column('f.id', 'cover_id');
			$sql->column('f.uid', 'cover_uid');
			$sql->column('f.type', 'cover_type');
			$sql->column('f.photo_id', 'cover_photo_id');
			$sql->column('f.cover_id'	, 'cover_cover_id');
			$sql->column('f.x', 'cover_x');
			$sql->column('f.y', 'cover_y');
			$sql->column('f.modified', 'cover_modified');
			$sql->join('#__social_avatars', 'b');
			$sql->on('b.uid', 'a.id');
			$sql->on('b.type', 'a.cluster_type');
			$sql->join('#__social_covers', 'f');
			$sql->on('f.uid', 'a.id');
			$sql->on('f.type', 'a.cluster_type');

			if (count($loadItems) > 1) {
				$sql->where('a.id', $loadItems, 'IN');
				$sql->group('a.id');
			}
			else
			{
				$sql->where('a.id', $loadItems[0]);
			}

			$sql->where('a.cluster_type', SOCIAL_TYPE_GROUP);

			// Debugging mode
			// echo $sql->debug();

			$db->setQuery($sql);

			$groups 	= $db->loadObjectList();

			if ($groups) {
				foreach ($groups as $group) {
					$loaded[ $group->id ]	= $group;
				}
			}
		}

		// Format the return result
		$data		= array();

		foreach ($ids as $id) {
			$data[] 	= $loaded[ $id ];
		}

		return $data;
	}


	/**
	 * Retrieves the total number of pending groups from the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPendingCount()
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select('#__social_clusters', 'a');
		$sql->column('COUNT(1)', 'count');
		$sql->where('a.cluster_type', SOCIAL_TYPE_GROUP);
		$sql->where('a.state', SOCIAL_CLUSTER_PENDING);

		$db->setQuery($sql);

		$total 		= (int) $db->loadResult();

		return $total;
	}

	/**
	 * Returns the total number of clusters created by a given node
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The unique id of the creator.
	 * @param	string 		The unique type of the creator.
	 * @return
	 */
	public function getTotalCreated($uid, $type)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_clusters');
		$sql->column('count(1)');
		$sql->where('creator_uid', $uid);
		$sql->where('creator_type', $type);
		$sql->where('cluster_type', SOCIAL_TYPE_GROUP);

		$db->setQuery($sql);
		$total 	= $db->loadResult();

		if (!$total) {
			return 0;
		}

		return $total;
	}

	/**
	 * Determines if the user is an admin of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAdmin($userId, $groupId)
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);
		$sql->where('cluster_id', $groupId);
		$sql->where('admin', SOCIAL_STATE_PUBLISHED);

		$db->setQuery($sql);

		$isAdmin 	= $db->loadResult() > 0;

		return $isAdmin;
	}

	/**
	 * Determines if the user is an owner of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isOwner($userId, $groupId)
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);
		$sql->where('cluster_id', $groupId);
		$sql->where('owner', SOCIAL_STATE_PUBLISHED);

		$db->setQuery($sql);

		$isOwner 	= $db->loadResult() > 0;

		return $isOwner;
	}

	/**
	 * Determines if the user is a member of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isInvited($userId, $groupId)
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);
		$sql->where('cluster_id', $groupId);
		$sql->where('state', SOCIAL_GROUPS_MEMBER_INVITED);

		$db->setQuery($sql);

		$isMember 	= $db->loadResult() > 0;

		return $isMember;
	}

	/**
	 * Retrieves total number of friends in the group
	 *
	 * @since	1.4
	 * @access	public
	 * @param	int		The group id
	 * @param	Array	An array of options
	 * @return
	 */
	public function getTotalFriendsInGroup($groupId, $options = array())
	{
		$db 	= FD::db();
		$query	= array();

		$query[]	= 'SELECT COUNT(DISTINCT(a.uid)) FROM ' . $db->nameQuote('#__social_clusters_nodes') . ' AS ' . $db->nameQuote('a');
		$query[]	= 'INNER JOIN ' . $db->nameQuote('#__social_friends') . ' AS ' . $db->nameQuote('b');
		$query[]	= 'ON(';

		$query[]	= '(';
		$query[]	= 'a.' . $db->nameQuote('uid') . ' = b.' . $db->nameQuote('actor_id') . ' AND b.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($options[ 'userId' ]);
		$query[]	= ')';
		$query[]	= 'OR';
		$query[]	= '(';
		$query[]	= 'a.' . $db->nameQuote('uid') . ' = b.' . $db->nameQuote('target_id') . ' AND b.' . $db->nameQuote('actor_id') . ' = ' . $db->Quote($options[ 'userId' ]);
		$query[]	= ')';

		$query[]	= ')';
		$query[]	= 'AND b.' . $db->nameQuote('state') . ' = ' . $db->Quote(SOCIAL_STATE_PUBLISHED);
		$query[]	= 'WHERE a.' . $db->nameQuote('cluster_id') . '=' . $db->Quote($groupId);

		$publishedOnly 	= isset($options['published']) ? $options['published'] : false;

		if ($publishedOnly) {
			$query[]	= 'AND (';
			$query[]	= 'a.' . $db->nameQuote('state') . '=' . $db->Quote(SOCIAL_GROUPS_MEMBER_PUBLISHED);
			$query[]	= ')';
		}

		$db->setQuery($query);
		$total = $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of friends from a particular group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The group id
	 * @param	Array	An array of options
	 * @return
	 */
	public function getFriendsInGroup($groupId, $options = array())
	{
		$db 	= FD::db();
		$query	= array();


		$query[]	= 'SELECT DISTINCT(a.uid) FROM ' . $db->nameQuote('#__social_clusters_nodes') . ' AS ' . $db->nameQuote('a');
		$query[]	= 'INNER JOIN ' . $db->nameQuote('#__social_friends') . ' AS ' . $db->nameQuote('b');
		$query[]	= 'ON(';

		$query[]	= '(';
		$query[]	= 'a.' . $db->nameQuote('uid') . ' = b.' . $db->nameQuote('actor_id') . ' AND b.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($options[ 'userId' ]);
		$query[]	= ')';
		$query[]	= 'OR';
		$query[]	= '(';
		$query[]	= 'a.' . $db->nameQuote('uid') . ' = b.' . $db->nameQuote('target_id') . ' AND b.' . $db->nameQuote('actor_id') . ' = ' . $db->Quote($options[ 'userId' ]);
		$query[]	= ')';

		$query[]	= ')';
		$query[]	= 'AND b.' . $db->nameQuote('state') . ' = ' . $db->Quote(SOCIAL_STATE_PUBLISHED);
		$query[]	= 'WHERE a.' . $db->nameQuote('cluster_id') . '=' . $db->Quote($groupId);

		$publishedOnly 	= isset($options['published']) ? $options['published'] : false;

		if ($publishedOnly) {
			$query[]	= 'AND (';
			$query[]	= 'a.' . $db->nameQuote('state') . '=' . $db->Quote(SOCIAL_GROUPS_MEMBER_PUBLISHED);
			$query[]	= ')';
		}

		$db->setQuery($query);
		$result	= $db->loadColumn();

		if (!$result) {
			return $result;
		}

		$users 	= array();
		foreach ($result as $id) {
			$user 		= FD::user($id);

			$users[]	= $user;
		}

		return $users;
	}

	/**
	 * Determines if the user is a pending member of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPendingMember($userId, $groupId)
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);
		$sql->where('cluster_id', $groupId);
		$sql->where('state', SOCIAL_GROUPS_MEMBER_PENDING);

		$db->setQuery($sql);

		$pending 	= $db->loadResult() > 0;

		return $pending;
	}

	/**
	 * Determines if the user is a member of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isMember($userId, $groupId)
	{
		$db 	= FD::db();

		$sql	= $db->sql();

		$sql->select('#__social_clusters_nodes');
		$sql->column('COUNT(1)');
		$sql->where('uid', $userId);
		$sql->where('type', SOCIAL_TYPE_USER);
		$sql->where('cluster_id', $groupId);
		$sql->where('state', SOCIAL_GROUPS_MEMBER_PUBLISHED);

		$db->setQuery($sql);

		$isMember 	= $db->loadResult() > 0;

		return $isMember;
	}

	/**
	 * Create new group on the site
	 *
	 * @since	1.2
	 * @access	public
	 * @param	SocialTableStepSession		The table mapping for step session
	 * @return
	 */
	public function createGroup(SocialTableStepSession &$session)
	{
		$config 	= FD::config();

		// Set the basic group details here.
		// Other group details should be fulfilled by the respective custom fields.
		FD::import('admin:/includes/group/group');

		$group 					= new SocialGroup();
		$group->creator_uid 	= FD::user()->id;
		$group->creator_type 	= SOCIAL_TYPE_USER;
		$group->category_id 	= $session->uid;
		$group->cluster_type 	= SOCIAL_TYPE_GROUP;
		$group->hits 			= 0;
		$group->created 		= FD::date()->toSql();

		// Generate a unique key for this group which serves as a password
		$group->key 			= md5(JFactory::getDate()->toSql() . FD::user()->password . uniqid());

		// Load up the values which the user inputs
		$param 		= FD::get('Registry');

		// Bind the JSON values.
		$param->bind($session->values);

		// Convert the data into an array of result.
		$data       = $param->toArray();

		$model 		= FD::model('Fields');

		// Get all published fields for the group.
		// $fields 	= $model->getCustomFieldsForNode($session->uid, SOCIAL_TYPE_CLUSTERS);
		$fields = $model->getCustomFields(array('uid' => $session->uid, 'group' => SOCIAL_TYPE_GROUP, 'visible' => SOCIAL_GROUPS_VIEW_REGISTRATION));

		// Pass in data and new user object by reference for fields to manipulate
		$args       = array(&$data, &$group);

		// Perform field validations here. Validation should only trigger apps that are loaded on the form
		// @trigger onRegisterBeforeSave
		$lib 		= FD::getInstance('Fields');

		// Get the trigger handler
		$handler	= $lib->getHandler();

		// Trigger onRegisterBeforeSave
		$errors 	= $lib->trigger('onRegisterBeforeSave', SOCIAL_FIELDS_GROUP_GROUP, $fields, $args, array($handler, 'beforeSave'));

		// If there are any errors, throw them on screen.
		if (is_array($errors)) {
			if (in_array(false, $errors, true)) {
				$this->setError($errors);
				return false;
			}
		}

		// If groups required to be moderated, unpublish it first.
		$my 			= FD::user();
		$group->state 	= $my->getAccess()->get('groups.moderate') ? SOCIAL_CLUSTER_PENDING : SOCIAL_CLUSTER_PUBLISHED;

		// If the creator is a super admin, they should not need to be moderated
		if ($my->isSiteAdmin()) {
			$group->state 	= SOCIAL_CLUSTER_PUBLISHED;
		}

        $dispatcher  = FD::dispatcher();
        $triggerArgs = array(&$group, &$my, true);

        // @trigger: onGroupBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onGroupBeforeSave', $triggerArgs);

		// Let's try to save the user now.
		$state 		= $group->save();

		// If there's a problem saving the user object, set error message.
		if (!$state) {
			$this->setError($group->getError());
			return false;
		}

		// Send e-mail notification to site admin to approve / reject the group.
		if ($my->getAccess()->get('groups.moderate') && !$my->isSiteAdmin()) {
			$this->notifyAdminsModeration($group);
		}
		else
		{
			// If the creator is a site admin, we don't need to notify the admins
			if (!$my->isSiteAdmin()) {
				$this->notifyAdmins($group);
			}
		}

		// Once the group is stored, we just re-load it with the proper data
		$group 	= FD::group($group->id);

		// After the group is created, assign the current user as the node item
		$group->createOwner($my->id);

		// Reform the args with the binded custom field data in the user object
		$args 	= array(&$data, &$group);

		// Allow fields app to make necessary changes if necessary. At this point, we wouldn't want to allow
		// the field to stop the registration process already.
		// @trigger onRegisterAfterSave
		$lib->trigger('onRegisterAfterSave', SOCIAL_FIELDS_GROUP_GROUP, $fields, $args);

		// Bind custom fields for this user.
		$group->bindCustomFields($data);

		// @trigger onRegisterAfterSaveFields
		$lib->trigger('onRegisterAfterSaveFields', SOCIAL_FIELDS_GROUP_GROUP, $fields, $args);

        // @trigger: onGroupAfterSave
        $triggerArgs = array(&$group, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onGroupAfterSave', $triggerArgs);

		// We need to set the "data" back to the registration table
		$newData 			= FD::json()->encode($data);
		$session->values 	= $newData;

		// If there is still no alias generated, we need to automatically build one for the group
		if (!$group->alias) {
			$group->alias	= $this->getUniqueAlias($group->getName());

			$group->save();
		}

		return $group;
	}

	/**
	 * Notify site admins that a group is created
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyAdmins(SocialGroup $group)
	{
		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'title'			=> $group->getName(),
								'creatorName'	=> $group->getCreator()->getName(),
								'creatorLink'	=> $group->getCreator()->getPermalink(false, true),
								'categoryTitle'	=> $group->getCategory()->get('title'),
								'avatar'		=> $group->getAvatar(SOCIAL_AVATAR_LARGE),
								'permalink'		=> $group->getPermalink(true, true),
								'alerts'		=> false
						);

		// Set the e-mail title
		$title 		= JText::sprintf('COM_EASYSOCIAL_EMAILS_GROUP_CREATED_MODERATOR_EMAIL_TITLE', $group->getName());

		// Get a list of super admins on the site.
		$usersModel = FD::model('Users');
		$admins 	= $usersModel->getSiteAdmins();

		foreach ($admins as $admin) {
			// Ensure that the user is a site admin or the Receive System email is turned off
			if (!$admin->isSiteAdmin() || !$admin->sendEmail) {
				continue;
			}

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Set the admin's name.
			$params[ 'adminName' ]	= $admin->getName();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient($admin->getName(), $admin->email);

			// Set title
			$mailTemplate->setTitle($title);

			// Set the template
			$mailTemplate->setTemplate('site/group/created', $params);

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

			// Try to send out email to the admin now.
			$state 		= $mailer->create($mailTemplate);
		}

		return true;
	}

	/**
	 * Searches for groups
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function search($keyword, $options = array())
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters');
		$sql->where('cluster_type', SOCIAL_TYPE_GROUP);
		$sql->where('title', '%' . $keyword . '%', 'LIKE');

		// Determines if we should search for unpublished groups as well
		$unpublished = isset($options['unpublished']) && $options['unpublished'] ? true : false;

		if (!$unpublished) {
			$sql->where('state', SOCIAL_STATE_PUBLISHED);
		}

		// Determines if we should exclude specific group ids
		$exclusion = isset($options['exclusion']) && $options['exclusion'] ? $options['exclusion'] : false;

		if ($exclusion) {
			$exclusion = ES::makeArray($exclusion);

			$sql->where('id', $exclusion, 'NOT IN');
		}

		$db->setQuery($sql);
		$result = $db->loadObjectList();
		$groups = array();

		if (!$result) {
			return $groups;
		}

		foreach ($result as $row) {
			$group = FD::group();
			$group->bind($row);

			$groups[] = $group;
		}

		return $groups;
	}

	/**
	 * Notify site admins that a group is created and it is pending moderation.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyAdminsModeration(SocialGroup $group)
	{
		// Push arguments to template variables so users can use these arguments
		$params 	= array(
								'title'			=> $group->getName(),
								'creatorName'	=> $group->getCreator()->getName(),
								'categoryTitle'	=> $group->getCategory()->get('title'),
								'avatar'		=> $group->getAvatar(SOCIAL_AVATAR_LARGE),
								'permalink'		=> JURI::root() . 'administrator/index.php?option=com_easysocial&view=groups&layout=pending',
								'reject'		=> FRoute::controller('groups', array('external' => true, 'task' => 'rejectGroup', 'id' => $group->id, 'key' => $group->key)),
								'approve'		=> FRoute::controller('groups', array('external' => true, 'task' => 'approveGroup', 'id' => $group->id, 'key' => $group->key)),
								'alerts'		=> false
						);

		// Set the e-mail title
		$title 		= JText::sprintf('COM_EASYSOCIAL_EMAILS_GROUP_CREATED_MODERATOR_EMAIL_TITLE', $group->getName());

		// Get a list of super admins on the site.
		$usersModel = FD::model('Users');
		$admins 	= $usersModel->getSiteAdmins();

		foreach ($admins as $admin) {
			// Ensure that the user is a site admin or the Receive System email is turned off
			if (!$admin->isSiteAdmin() || !$admin->sendEmail) {
				continue;
			}

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Set the admin's name.
			$params[ 'adminName' ]	= $admin->getName();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient($admin->getName(), $admin->email);

			// Set title
			$mailTemplate->setTitle($title);

			// Set the template
			$mailTemplate->setTemplate('site/group/moderate', $params);

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

			// Try to send out email to the admin now.
			$state 		= $mailer->create($mailTemplate);
		}

		return true;
	}


	public function deleteUserStreams($groupId, $userId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "delete a, b from `#__social_stream` as a";
		$query .= "		inner join `#__social_stream_item` as b on a.`id` = b.`uid`";
		$query .= " where a.`actor_id` = '$userId'";
		$query .= " and a.`cluster_id` = '$groupId'";
		$query .= " and a.`cluster_type` = '" . SOCIAL_TYPE_GROUP . "'";

		$sql->raw($query);
		$db->setQuery($sql);

		$db->query();

		return true;
	}



}
