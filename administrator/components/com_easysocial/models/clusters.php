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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelClusters extends EasySocialModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( 'clusters' , $config );
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
		$filter 	= $this->getUserStateFromRequest( 'state' , 'all' );
		$ordering 	= $this->getUserStateFromRequest( 'ordering' , 'ordering' );
		$direction	= $this->getUserStateFromRequest( 'direction' , 'ASC' );

		$this->setState( 'state' , $filter );


		parent::initStates();

		// Override the ordering behavior
		$this->setState( 'ordering' , $ordering );
		$this->setState( 'direction' , $direction );
	}

	/**
	 * Saves the ordering of profiles
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveOrder( $ids , $ordering )
	{
		$table 	= FD::table( 'Profile' );
		$table->reorder();
	}

	/**
	 * Removes all owners from the nodes
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The cluster id
	 * @return
	 */
	public function removeOwners( $clusterId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->update( '#__social_clusters_nodes' );
		$sql->set('owner', 0);
		$sql->where( 'cluster_id' , $clusterId );

		$db->setQuery( $sql );

		return $db->Query();
	}

	/**
	 * Retrieves the total number of clusters created by a user given the cluster type and the user id
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @param	string	The cluster type
	 * @return
	 */
	public function getTotalCreated( $creatorId , $creatorType , $clusterType )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_clusters' );
		$sql->column( 'COUNT(1)' );
		$sql->where( 'creator_uid' , $creatorId );
		$sql->where( 'creator_type' , $creatorType );
		$sql->where( 'cluster_type' , $clusterType );

		$sql->where( 'state' , SOCIAL_CLUSTER_PUBLISHED );
		$db->setQuery( $sql );

		$total	= $db->loadResult();

		return $total;
	}

	/**
	 * Deletes all node associations between the cluster and the node item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteNodeAssociation( $clusterId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_clusters_nodes' );
		$sql->where( 'cluster_id' , $clusterId );

		$db->setQuery( $sql );

		$state 	= $db->Query();

		return $state;
	}

	/**
	 * Gets the total number of nodes in a cluster category
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The cluster's category id.
	 * @return
	 */
	public function getTotalNodes( $categoryId , $options = array() )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_clusters' , 'a' );
		$sql->column( 'COUNT(1)' );

		$excludeBlocked 	= isset( $options[ 'excludeblocked' ] ) ? $options[ 'excludeblocked' ] : 0;

		if (FD::config()->get('users.blocking.enabled') && $excludeBlocked && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.creator_uid' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		$sql->where( 'a.category_id' , $categoryId );
		$sql->where( 'a.state' , SOCIAL_STATE_PUBLISHED );

		// Determines if the type is provided
		$types 	= isset( $options[ 'types' ] ) ? $options[ 'types' ] : '';

		if( $types )
		{
			$types 	= FD::makeArray( $types );

			$sql->where( 'a.type' , $types , 'IN' );
		}

		$db->setQuery( $sql );

		$total 	= $db->loadResult();

		return $total;
	}

	/**
	 * Check if the cluster alias exist
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $alias   The alias to check
	 * @param  Int       $exclude The cluster id to exclude from checking
	 * @return Boolean            State of existance
	 */
	public function clusterAliasExists($alias, $exclude = null, $type = SOCIAL_TYPE_GROUP)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters');
		$sql->where('alias', $alias);
		$sql->where('cluster_type', $type);

		if (!empty($exclude)) {
			$sql->where('id', $exclude, '!=');
		}

		$db->setQuery($sql->getTotalSql());

		$result = $db->loadResult();

		return !empty($result);
	}

	/**
	 * Check if the cluster category alias exist
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  String    $alias   The alias to check
	 * @param  Int       $exclude The cluster category id to exclude from checking
	 * @return Boolean            State of existance
	 */
	public function clusterCategoryAliasExists($alias, $exclude = null)
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_categories');
		$sql->where('alias', $alias);

		if (!empty($exclude))
		{
			$sql->where('id', $exclude, '!=');
		}

		$db->setQuery($sql->getTotalSql());

		$result = $db->loadResult();

		return !empty($result);
	}

	/**
	 * delete stream from this cluster.
	 *
	 * @author Sam <sam@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  int    cluster id
	 * @param  string cluster type
	 * @return Boolean
	 */
	public function deleteClusterStream($clusterId, $clusterType)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'delete a, b from `#__social_stream_item` as a';
		$query .= '	inner join `#__social_stream` as b on a.`uid` = b.`id`';
		$query .= ' where b.`cluster_id` = ' . $db->Quote( $clusterId );
		$query .= ' and b.`cluster_type` = ' . $db->Quote( $clusterType );

		$sql->raw($query);
		$db->setQuery($sql);

		$state = $db->query();
		return $state;
	}

	/**
	 * delete notifications from this cluster.
	 *
	 * @author Sam <sam@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  int    cluster id
	 * @param  string cluster type
	 * @return Boolean
	 */
	public function deleteClusterNotifications($clusterId, $clusterType, $clusterContextType)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'delete from `#__social_notifications`';
		$query .= ' where (`uid` = ' . $db->Quote($clusterId) . ' and `type` = ' . $db->Quote($clusterType) .')';
		$query .= ' OR (`type` = ' . $db->Quote($clusterContextType) . ' and `context_ids` = ' . $db->Quote($clusterId) . ')';

		$sql->raw($query);
		$db->setQuery($sql);

		$state = $db->query();
		return $state;
	}


	/**
	 * Retrieves a list of news item from a particular group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getNews($id, $options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select('#__social_clusters_news', 'a');
		$sql->where('a.cluster_id', $id);

		// If we should exclude specific items
		$exclude 	= isset($options['exclude']) ? $options['exclude'] : '';

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
			$news 	= FD::table('ClusterNews');
			$news->bind($row);

			$items[]	= $news;
		}

		return $items;
	}


	public function preloadClusters($clusters)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select * from `#__social_clusters` where id in (" . implode(",", $clusters) . ")";

		$sql->raw($query);

		$db->setQuery($sql);

		$results = $db->loadObjectList();
		return $results;
	}
}
