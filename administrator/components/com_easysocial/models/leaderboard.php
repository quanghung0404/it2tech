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

// Include parent model
FD::import( 'admin:/includes/model' );

class EasySocialModelLeaderboard extends EasySocialModel
{
	private $data			= null;

	function __construct()
	{
		parent::__construct( 'leaderboard' );
	}

	/**
	 * Retrieves the ladder board
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getLadder( $options = array() , $loadUsers = true )
	{
		$config = FD::config();
		$db		= FD::db();

		$query		= array();
		$query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ', SUM( d.' . $db->nameQuote( 'points' ) . ') AS ' . $db->nameQuote( 'points' ) . ' FROM ' . $db->nameQuote( '#__users' ) . ' AS a';

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on a.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';

		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_users' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'user_id' );

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query[] = ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query[] = ' ON a.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query[] = ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		// If group is supplied, we only want to fetch users from a particular group
		if( isset( $options[ 'group'] ) && !empty( $options[ 'group' ] ) && $options[ 'group' ] != -1 )
		{
			$groupId 	= $options[ 'group' ];

			$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__user_usergroup_map' ) . ' AS c';
			$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = c.' . $db->nameQuote( 'user_id' );
			$query[]	= 'AND c.' . $db->nameQuote( 'group_id' ) . ' = ' . $db->Quote( $groupId );
		}

		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_points_history' ) . ' AS d';
		$query[]	= 'ON d.' . $db->nameQuote( 'user_id' ) . ' = a.' . $db->nameQuote( 'id' );

		// filter out user which is blocked.
		$query[]	= 'WHERE a.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote('0');

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query[] = ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}

		// If user id filters is provided, filter the users based on the id.
		if( isset( $options[ 'ids' ] ) && !empty( $options[ 'ids' ] ) )
		{
			$ids 	= $options[ 'ids' ];
			$ids	= FD::makeArray( $ids );

			$total		= count( $ids );
			$idQuery	= '';

			for( $i = 0; $i < $total; $i++ )
			{
				$idQuery	.= $db->Quote( $ids[ $i ] );

				if( next( $ids ) !== false )
				{
					$idQuery	.= ',';
				}
			}

			$query[]	= 'AND ( a.' . $db->nameQuote( 'id' ) . ' IN(' . $idQuery . ') )';
		}

		// Determine if the caller wants to excludeAdmin
		$excludeAdmin 	= isset( $options[ 'excludeAdmin' ] ) ? $options[ 'excludeAdmin' ] : '';

		// If we need to exclude admins
		if( $excludeAdmin )
		{
			// Get a list of site administrators from the site.
			$model 		= FD::model( 'Users' );
			$admins 	= $model->getSiteAdmins();

			if( $admins )
			{
				$ids	= array();

				foreach( $admins as $admin )
				{
					$ids[] 	= $db->Quote( $admin->id );
				}

				$query[]	= 'AND a.' . $db->nameQuote( 'id' ) . ' NOT IN (' . implode( ',' , $ids ) . ')';
			}
		}

		// If state is passed in, we need to determine the user's state.
		if( isset( $options[ 'state' ] ) && $options[ 'state' ] != -1 )
		{
			$state 		= $options[ 'state' ];
			$query[]	= 'AND b.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( $state );
		}

		// If login state is provided we need to filter the query.
		if( isset( $options[ 'login' ] ) && $options[ 'login' ] != -1 )
		{
			$loginState	= $options[ 'login' ];

			if( $loginState )
			{
				$query[]	= 'AND EXISTS(';
				$query[]	= ' SELECT ' . $db->nameQuote( 'userid' ) .  ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f';
				$query[]	= ' WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' );
				$query[]	= ')';
			}
			else
			{
				$query[]	= 'AND NOT EXISTS(';
				$query[]	= ' SELECT ' . $db->nameQuote( 'userid' ) .  ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f';
				$query[]	= ' WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' );
				$query[]	= ')';
			}
		}

		// If there's an exclusion list, we need to respect that too.
		if( isset( $options[ 'exclusion'] ) )
		{
			$exclusions	= $options[ 'exclusion' ];

			foreach( $exclusions as $column => $values )
			{
				if( !$values )
				{
					continue;
				}

				$query[]	= ' AND ' . $db->nameQuote( $column );

				if( is_array( $values ) )
				{
					$query[]	= ' NOT IN(';
					$total  = count( $values );

					for( $i = 0; $i < $total; $i++ )
					{
						$query[] = $db->Quote( $values[ $i ] );

						if( next( $values ) !== false )
						{
							$query[]  = ',';
						}
					}
					$query[]	= ')';
				}
				else
				{
					$query[]	= '!=' . $db->Quote( $values );
				}
			}
		}

		$query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );
		$query[]	= 'ORDER BY ' . $db->nameQuote( 'points' ) . ' DESC';

		// Merge the query array back.
		$query		= implode( ' ' , $query );

		// echo $query;exit;

		// @task: Process the count here.
		$count	= str_ireplace( 'SELECT a.* FROM' , 'SELECT COUNT(1) FROM' , $query );
		$this->setTotal( $count );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit )
		{
			$this->setLimit( $limit );
		}

		//now with the limit
		$result = $this->getDataColumn( $query );

		if( !$result )
		{
			return $result;
		}

		$users 	= array();
		foreach( $result as $id )
		{
			$users[]	= FD::user( $id );
		}

		return $users;
	}

	/**
	 * Retrieves a list of user data based on the given ids.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	$ids	An array of ids.
	 * @return
	 */
	public function getUsers( $options = array() , $loadUsers = true )
	{
		$config = FD::config();
		$db		= FD::db();

		$query		= array();
		$query[]	= 'SELECT DISTINCT( a.`id` ) FROM ' . $db->nameQuote( '#__users' ) . ' AS a';

		// exclude esad users
		$query[] = 'INNER JOIN `#__social_profiles_maps` as upm on a.`id` = upm.`user_id`';
		$query[] = 'INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1';


		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_users' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'user_id' );

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block
		    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
		    $query .= ' ON a.' . $db->nameQuote( 'id' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
		    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
		}

		$ordering 	= isset( $options[ 'ordering' ] ) ? $options[ 'ordering' ] : null;

		// If group is supplied, we only want to fetch users from a particular group
		if( isset( $options[ 'group'] ) && !empty( $options[ 'group' ] ) && $options[ 'group' ] != -1 )
		{
			$groupId 	= $options[ 'group' ];

			$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__user_usergroup_map' ) . ' AS c';
			$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = c.' . $db->nameQuote( 'user_id' );
			$query[]	= 'AND c.' . $db->nameQuote( 'group_id' ) . ' = ' . $db->Quote( $groupId );
		}

		if( !is_null( $ordering) )
		{
			$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_points_history' ) . ' AS d';
			$query[]	= 'ON d.' . $db->nameQuote( 'user_id' ) . ' = a.' . $db->nameQuote( 'id' );
		}

		// filter out user which is blocked.
		$query[]	= 'WHERE a.' . $db->nameQuote( 'block' ) . ' = ' . $db->Quote('0');

		if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    // user block continue here
		    $query[] = ' WHERE bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
		}


		// If user id filters is provided, filter the users based on the id.
		if( isset( $options[ 'ids' ] ) && !empty( $options[ 'ids' ] ) )
		{
			$ids 	= $options[ 'ids' ];
			$ids	= FD::makeArray( $ids );

			$total		= count( $ids );
			$idQuery	= '';

			for( $i = 0; $i < $total; $i++ )
			{
				$idQuery	.= $db->Quote( $ids[ $i ] );

				if( next( $ids ) !== false )
				{
					$idQuery	.= ',';
				}
			}

			$query[]	= 'AND ( a.' . $db->nameQuote( 'id' ) . ' IN(' . $idQuery . ') )';
		}


		// If state is passed in, we need to determine the user's state.
		if( isset( $options[ 'state' ] ) && $options[ 'state' ] != -1 )
		{
			$state 		= $options[ 'state' ];
			$query[]	= 'AND b.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( $state );
		}

		// If login state is provided we need to filter the query.
		if( isset( $options[ 'login' ] ) && $options[ 'login' ] != -1 )
		{
			$loginState	= $options[ 'login' ];

			if( $loginState )
			{
				$query[]	= 'AND EXISTS(';
				$query[]	= ' SELECT ' . $db->nameQuote( 'userid' ) .  ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f';
				$query[]	= ' WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' );
				$query[]	= ')';
			}
			else
			{
				$query[]	= 'AND NOT EXISTS(';
				$query[]	= ' SELECT ' . $db->nameQuote( 'userid' ) .  ' FROM ' . $db->nameQuote( '#__session' ) . ' AS f';
				$query[]	= ' WHERE ' . $db->nameQuote( 'userid' ) . ' = a.' . $db->nameQuote( 'id' );
				$query[]	= ')';
			}
		}

		// If there's an exclusion list, we need to respect that too.
		if( isset( $options[ 'exclusion'] ) )
		{
			$exclusions	= $options[ 'exclusion' ];

			foreach( $exclusions as $column => $values )
			{
				if( !$values )
				{
					continue;
				}

				$query[]	= ' AND ' . $db->nameQuote( $column );

				if( is_array( $values ) )
				{
					$query[]	= ' NOT IN(';
					$total  = count( $values );

					for( $i = 0; $i < $total; $i++ )
					{
						$query[] = $db->Quote( $values[ $i ] );

						if( next( $values ) !== false )
						{
							$query[]  = ',';
						}
					}
					$query[]	= ')';
				}
				else
				{
					$query[]	= '!=' . $db->Quote( $values );
				}
			}
		}


		$query[]	= 'ORDER BY d.' . $db->nameQuote( 'points' ) . ' DESC';

		// Merge the query array back.
		$query		= implode( ' ' , $query );
		// echo $query;exit;
		// @task: Process the count here.
		$count	= str_ireplace( 'SELECT a.* FROM' , 'SELECT COUNT(1) FROM' , $query );
		$this->setTotal( $count );

		$limit 	= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : '';

		if( $limit )
		{
			$this->setLimit( $limit );
		}

		//now with the limit
		$result = $this->getDataColumn( $query );

		// Pre-load the users.
		FD::user( $result );

		return $result;
	}
}
