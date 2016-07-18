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

// Include main model file.
FD::import( 'admin:/includes/model' );

class EasySocialModelStream extends EasySocialModel
{
	private $data = null;
	private $nextdate = null;
	private $enddate = null;
	private $paginationdate = null;
	private $uids = null; // stream ids
	protected $pagination = null;
	protected $total = null;

	//used in queries optmisation.
	static $_relateditems = array();
	static $_activitylogs = array();
	static $_tagging = array();


	public function __construct()
	{
		parent::__construct('stream');
	}

    public function initStates()
    {
		parent::initStates();

        $ordering = $this->getUserStateFromRequest('ordering', 'created');
        $this->setState('ordering', $ordering);

        $direction = $this->getUserStateFromRequest('direction', 'desc');
        $this->setState('direction', $direction);

        $state = $this->getUserStateFromRequest('state', 'all');

        $this->setState('state', $state);
    }

    public function getItemsWithState($options = array())
    {
    	$db = FD::db();
    	$sql = $db->sql();

		// Determines if user is filtering the items
		$state 	= $this->getState( 'state' );

		$sql->column( 'a.*' );
   		$sql->column( 'b.name', 'actorName');
   		$sql->column( 'c.title', 'clusterName');

   		if ($state == '3') {
   			$sql->select('#__social_stream_history', 'a');
   		} else {
   			$sql->select('#__social_stream', 'a');
   		}

		$sql->join('#__users', 'b', 'INNER');
		$sql->on('a.actor_id', 'b.id');

		$sql->join('#__social_clusters', 'c', 'LEFT');
		$sql->on('a.cluster_id', 'c.id');

		if(!is_null($state) && $state != '3') {
			if ($state == 'all') {
                $sql->where('(', '', '', 'AND');
                $sql->where('a.state', '1', '=', 'OR');
                $sql->where('a.state', '2', '=', 'OR');
                $sql->where(')');
			} else {
				$sql->where( 'a.state' , $state );
			}
		}

		// Ordering
		$ordering 	= $this->getState('ordering', 'created');

		if ($ordering) {
			$direction 	= $this->getState('direction') ? $this->getState('direction') : 'DESC';

			$sql->order( $ordering , $direction );
		}

		// Set the total
		$this->setTotal($sql->getTotalSql());

		$result 	= parent::getData($sql->getSql());

		$this->pagination = parent::getPagination();

		return $result;
    }


	public function exists( $uid, $context, $verb, $actorId, $options = array() )
	{
		$db = FD::db();
		$sql = $db->sql();

		// options
		$actorType = isset($options['actortype']) ? $options['actortype'] : SOCIAL_TYPE_USER;


		$query = "select `id` from `#__social_stream_item`";
		$query .= " where `actor_id` = '$actorId'";
		$query .= " and `actor_type`='$actorType'";
		$query .= " and `context_type`='$context'";
		$query .= " and `context_id`='$uid'";
		$query .= " and `verb`='$verb'";

		$sql->raw($query);
		$db->setQuery($sql);

		$result = $db->loadResult();

		return $result ? true : false;
	}

	public function hide($ids, $userId)
	{
		if( empty($ids) )
			return false;

		if(! is_array($ids) )
		{
			$ids = array( $ids );
		}


		$db = FD::db();

		foreach($ids as $cid)
		{
			$tbl = FD::table('StreamHide');
			$tbl->user_id 	= $userId;
			$tbl->uid 		= $cid;
			$tbl->type 		= SOCIAL_STREAM_HIDE_TYPE_STREAM;


			if( ! $tbl->store() )
			{
				return false;
			}
			else
			{
				//since this stream might consist of several activity logs, then we will need to 'hide' them all as well.
				$query = 'select ' . $db->nameQuote( 'id' ) . ' from ' . $db->nameQuote( '#__social_stream_item' ) . ' where ' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $cid );
				$db->setQuery( $query );

				$items = $db->loadObjectList();

				if( count( $items ) > 0 )
				{
					foreach( $items as $item )
					{
						$tbl = FD::table( 'StreamHide' );
						$tbl->uid 		= $item->id;
						$tbl->user_id 	= $userId;
						$tbl->type 		= SOCIAL_STREAM_HIDE_TYPE_ACTIVITY;
						$tbl->store();
					}

				}
			}
		}

		return true;
	}

	public function hideapp( $context, $userId)
	{
		if( empty($context) )
			return false;


		$db = FD::db();


		$tbl = FD::table('StreamHide');
		$tbl->user_id 	= $userId;
		$tbl->uid 		= '0';
		$tbl->type 		= '';
		$tbl->context 	= $context;

		if( ! $tbl->store() )
		{
			return false;
		}

		return true;
	}


	public function hideactor( $actor_id, $userId )
	{
		if( empty( $actor_id ) )
			return false;


		$db = FD::db();

		$tbl = FD::table('StreamHide');
		$tbl->user_id 	= $userId;
		$tbl->uid 		= '0';
		$tbl->type 		= '';
		$tbl->actor_id 	= $actor_id;

		if( ! $tbl->store() )
		{
			return false;
		}

		return true;
	}

	public function unhideactor( $actor_id, $userId )
	{
		if( empty( $actor_id ) )
			return false;


		$db = FD::db();

		$delQuery = 'delete from ' . $db->nameQuote( '#__social_stream_hide' ) . ' where ' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $actor_id );
		$delQuery .= ' and ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );

		$db->setQuery( $delQuery );
		$db->query();

		return true;
	}



	/**
	 * Retrieves the past 7 days statistics for all postings by specific user
	 *
	 * @since	1.0
	 * @access	public
	 * @return	Array
	 */
	public function getPostStats( $dates , $userId )
	{
		$db 	= FD::db();
		$posts	= array();

		foreach( $dates as $date )
		{
			// Registration date should be Y, n, j
			$date	= FD::date( $date )->format( 'Y-m-d' );

			$query 		= array();
			$query[] 	= 'SELECT `a`.`id`, COUNT( `a`.`id`) AS `cnt` FROM `#__social_stream` AS a';
			$query[]	= 'WHERE `a`.`actor_id`=' . $db->Quote( $userId );
			$query[]	= 'AND `a`.`actor_type`=' . $db->Quote( SOCIAL_TYPE_USER );
			$query[]	= 'AND DATE_FORMAT( `a`.`created`, GET_FORMAT( DATE , "ISO") ) = ' . $db->Quote( $date );
			$query[]    = 'group by a.`actor_id`';

			$query 		= implode( ' ' , $query );

			$sql		= $db->sql();
			$sql->raw( $query );

			$db->setQuery( $sql );

			$items				= $db->loadObjectList();

			// There is nothing on this date.
			if( !$items )
			{
				$posts[]	= 0;
				continue;
			}

			foreach( $items as $item )
			{
				$posts[]	= $item->cnt;
			}
		}

		// Reset the index.
		$posts 	= array_values( $posts );

		return $posts;
	}

	public function unhideapp( $context, $userId )
	{
		if( empty( $context ) )
			return false;


		$db = FD::db();

		$delQuery = 'delete from ' . $db->nameQuote( '#__social_stream_hide' ) . ' where ' . $db->nameQuote( 'context' ) . ' = ' . $db->Quote( $context );
		$delQuery .= ' and ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );

		$db->setQuery( $delQuery );
		$db->query();

		return true;
	}

	/**
	 * Deletes stream items given the context type and context id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The unique context id.
	 * @param	string		The unique context type.
	 * @return	boolean
	 */
	public function delete($contextId, $contextType, $actorId = '', $verb = '')
	{
		$db = FD::db();

		// Get a list of items from the item table first.
		$sql	= $db->sql();

		$sql->select('#__social_stream_item');
		$sql->where('context_id', $contextId);
		$sql->where('context_type', $contextType);

		if ($actorId) {
			$sql->where('actor_id', $actorId);
		}

		if ($verb) {
			$sql->where('verb', $verb);
		}

		$db->setQuery($sql);

		$items = $db->loadObjectList();

		if (!$items) {
			$this->setError(JText::sprintf('There is no items matching the context type of %1s and context id of %2s.' , $contextType , $contextId ) );
			return false;
		}

		// Delete from #__social_stream_item
		$sql->clear();

		$sql->delete('#__social_stream_item');
		$sql->where('context_id', $contextId);
		$sql->where('context_type', $contextType);

		if ($actorId) {
			$sql->where( 'actor_id' , $actorId );
		}

		if ($verb) {
			$sql->where('verb', $verb);
		}

		$db->setQuery($sql);
		$db->Query();

		// lets check if the UID has more than one item or not. If yes, then we shouldn't
		// delete the master record.

		foreach ($items as $item) {

			// Delete from #__social_stream
			$sql->clear();

			$sql->select('#__social_stream_item');
			$sql->column('count(1)', 'cnt');
			$sql->where('uid', $item->uid);

			$db->setQuery($sql);

			$count = $db->loadResult();

			// Delete the parent stream item if necessary (If it is aggregated, we should check if there's any child left)
			if ($count <= 0) {
				$sql->clear();
				$sql->delete('#__social_stream');
				$sql->where('id', $item->uid);

				$db->setQuery($sql);
				$db->Query();
			}
		}

		return true;
	}

	public function unhide($ids, $userId)
	{
		if( empty($ids) )
			return false;

		if(! is_array($ids) )
		{
			$ids = array( $ids );
		}


		$db = FD::db();

		foreach($ids as $cid)
		{
			$delQuery = 'delete from ' . $db->nameQuote( '#__social_stream_hide' ) . ' where ' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $cid );
			$delQuery .= ' and ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
			$delQuery .= ' and ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( SOCIAL_STREAM_HIDE_TYPE_STREAM );

			$db->setQuery( $delQuery );
			$db->query();


			//since this stream might consist of several activity logs, then we will need to 'hide' them all as well.
			$query = 'select ' . $db->nameQuote( 'id' ) . ' from ' . $db->nameQuote( '#__social_stream_item' ) . ' where ' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $cid );
			$db->setQuery( $query );

			$items = $db->loadObjectList();

			if( count( $items ) > 0 )
			{
				$itemIds = array();
				foreach( $items as $item )
				{
					$itemIds[] = $item->id;
				}

				$strIds = implode( ',', $itemIds);

				$delQuery = 'delete from ' . $db->nameQuote( '#__social_stream_hide' ) . ' where ' . $db->nameQuote( 'uid' ) . ' IN (' . $strIds . ')';
				$delQuery .= ' and ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
				$delQuery .= ' and ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( SOCIAL_STREAM_HIDE_TYPE_ACTIVITY );

				$db->setQuery( $delQuery );
				$db->query();

			}
		}

		return true;
	}

	public function getItems()
	{
		$search 		= FD::get( 'Themes' )->getUserStateFromRequest( 'com_easysocal.stream.search', 'search', '', 'string' );
		$actor_type 	= FD::get( 'Themes' )->getUserStateFromRequest( 'com_easysocal.stream.actor_type', 'actor_type', '', 'string' );
		$context_type 	= FD::get( 'Themes' )->getUserStateFromRequest( 'com_easysocal.stream.context_type', 'context_type', '', 'string' );

		$db = FD::db();
		$where  = array();
		//if( !empty( $search ) )
		//	$where[]    = ' a.`actor_id` = ' . $this->_db->Quote( $search );

		if( !empty($actor_type) )
			$where[]    = 'a.' . $db->nameQuote( 'actor_type' ) . ' = ' . $db->Quote( $actor_type );

		if( !empty($context_type) )
			$where[]    = 'a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote( $context_type );

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		$CountHeader  = 'select count(1)';

	    $header  = 'select a.*, b.' . $db->nameQuote( 'name' ) . ' as ' . $db->nameQuote( 'actor_name' ) . ',';
	    $header  .= ' FLOOR( TIME_TO_SEC( TIMEDIFF( NOW(), a.' . $db->nameQuote( 'created' ) . ' ) ) / 60) AS ' . $db->nameQuote( 'mindiff' ) . ',';
	    $header  .= ' FLOOR( TIME_TO_SEC( TIMEDIFF( NOW(), a.' . $db->nameQuote( 'created' ) . ' ) ) / 60 / 60) AS ' . $db->nameQuote( 'hourdiff' ) . ',';
		$header  .= ' FLOOR( TIME_TO_SEC( TIMEDIFF( NOW(), a.' . $db->nameQuote( 'created' ) . ' ) ) / 60 / 60 / 24) AS ' . $db->nameQuote( 'daydiff' );

	    $query  = ' from ' . $db->nameQuote( '#__social_stream' ) . ' as a';
	    $query  .= '   left join ' . $db->nameQuote( '#__users' ) . ' as b on a.' . $db->nameQuote( 'source_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' and a.' . $db->nameQuote( 'actor_type' ) . ' = ' . $db->Quote( 'people' );
	    $query  .= $extra;
	    $query  .= ' order by a.' . $db->nameQuote( 'created desc' );

	    $mainSQL    = $header   . $query;

	    $countSQL   = $CountHeader . $query;
		$this->setTotal( $countSQL );

		// echo $mainSQL;

		return $this->getStreamData( $mainSQL );
	}

	public function getPagination()
	{
		return $this->pagination;
	}

	public function getType( $type )
	{
		$db = FD::db();
	    $targetType = '';

		switch( $type )
		{
		    case 'source':
		        $targetType = 'actor_type';
				break;
			case 'context':
		        $targetType = 'context_type';
				break;
			default:
			    break;
		}

	    if( empty( $targetType ) )
	        return;

		$query  = 'SELECT DISTINCT ' . $db->nameQuote( $targetType );
		$query  .= ' FROM  ' . $db->nameQuote( '#__social_stream' );

		return $this->getStreamData( $query, false );
	}

	public function getTotalCount()
	{
		return $this->total;
	}

	public function getNextEndDate()
	{
		return $this->enddate;
	}

	public function getNextStartDate()
	{
		return $this->nextdate;
	}


	public function getCurrentStartDate()
	{
		//use the current datetime
		return FD::date()->toMySQL();
	}

	public function getUids()
	{
		//stream ids
		return $this->uids;
	}



	/**
	 * Retrive the start date and end date used in query limit.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string - start date ( in mysql date format )
	 * @return	array - startdate and enddate
	 */

	private function getLimitDates( $startdate, $enddate, $tables, $conds = array(), $direction = 'older' )
	{
		$config = FD::config();
		$db 	= FD::db();

		$fetchLimit = $config->get( 'stream.pagination.limit' );
		$sortDate   = $config->get( 'stream.pagination.sort', 'modified' );

		// $fetchLimit = 10080 + 10080;
		// $fetchLimit = 5;

		$dates      = array();
		$countConds = $conds;

		//use last modified date from stream.
		// $query = 'SELECT MAX( a.' . $db->nameQuote( 'modified' ) . ') AS ' . $db->nameQuote('startdate') . ',';
		// $query .= ' DATE_ADD( MAX( a.' . $db->nameQuote( 'modified' ) . ' )  , INTERVAL -' . $fetchLimit . ' MINUTE) AS ' . $db->nameQuote( 'enddate' ) . ' ';

		$query = 'SELECT a.' . $db->nameQuote( 'modified' ) . ' AS ' . $db->nameQuote('startdate') . ',';
		$query .= ' DATE_ADD( a.' . $db->nameQuote( 'modified' ) . ' , INTERVAL -' . $fetchLimit . ' MINUTE) AS ' . $db->nameQuote( 'enddate' ) . ' ';

		if( $startdate )
		{
			if( $direction == 'later' )
			{
				$conds[] = ' and a.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $startdate );
			}
			else
			{
				$conds[] = ' and a.' . $db->nameQuote( $sortDate ) . ' < ' . $db->Quote( $startdate );
			}
		}

		//unset the index 1, 2 and 3 to remove the join on location, mood and users table.
		unset( $tables[1] );
		unset( $tables[2] );

		if ( strpos( $tables[3] , '#__users' ) !== false ) {
			unset( $tables[3] );
		}


		$tables 	= implode( ' ', $tables );
		$conds 		= implode( ' ' , $conds );

		$ordering = ' order by a.`modified` DESC LIMIT 1';

		$query .= $tables . ' ' . $conds . $ordering;

		// echo $query . '<br /><br />';
		// exit;

		$db->setQuery( $query );
		$data = $db->loadObject();


		// var_dump( $data);

		if( isset( $data->startdate ) )
		{
			$dates['startdate'] 		= $data->startdate;
			$dates['paginationdate'] 	= $data->startdate;
		}

		if( $enddate )
		{
			$dates['enddate'] = $enddate;
		}
		else
		{
			if( isset( $data->enddate ) )
			{
				$dates['enddate'] = $data->enddate;
			}
		}


		// now lets test whether the next set of dates has data or not.
		if( isset( $data->enddate ) && $direction != 'later' )
		{
			$limiting = ' LIMIT 1';

			$query = 'select a.`id`';
			$countConds[] = ' and a.' . $db->nameQuote( $sortDate ) . ' < ' . $db->Quote( $data->enddate );

			// joining the condition
			$countConds = implode( ' ' , $countConds );

			$query .= $tables . ' ' . $countConds . $limiting;

			$db->setQuery( $query );
			$data = $db->loadResult();
			if( empty( $data ) )
			{
				$dates['paginationdate'] = false;
			}

			// always return true to skid the count check to improve the page load speed.
			// $dates['paginationdate'] = true;
		}

		return $dates;
	}


	// called in backend dashboard.
	public function getRecentFeeds( $maxCnt = 10 )
	{
		$db = FD::db();

		$config  	= FD::config();
		$sortDate   = $config->get( 'stream.pagination.sort', 'modified' );

		if( empty( $maxCnt ) )
			$maxCnt = 10;

		$query	= 'SELECT a.*';
		$query	.= ', FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 ) AS ' . $db->nameQuote( 'min' );
		$query	.= ', FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 / 60 ) AS ' . $db->nameQuote( 'hour' );
		$query	.= ', FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 / 60 / 24 ) AS ' . $db->nameQuote( 'day' );
		$query	.= ' FROM ' . $db->nameQuote( '#__social_stream' ) . ' AS a';
		$query	.= ' ORDER BY a.' . $db->nameQuote( $sortDate ) . ' DESC';
		$query  .= ' LIMIT ' . $maxCnt;

		$db->setQuery( $query );

		$result 	= $db->loadObjectList();
		return $result;
	}


	private function getStreamTableAlias( $userId, $type, $userStickyOnly = false, $useDate = false, $direction = '', $startdate = null, $enddate = null, $customView = false )
	{

		// apps that other user can post on my profile timeline
		$profileApps = array('friends' => 'add',
							'story' => 'create',
							'photos' => 'share',
							'links' => 'create',
							'files' => 'create',
							'videos' => 'create');


		$db 	= FD::db();
		$view 	= ($customView) ? $customView : JRequest::getVar( 'view', '');

		$config     = FD::config();
		$sortDate   = $config->get( 'stream.pagination.sort', 'modified' );

		$streamTableAlias = '(';
		$streamTableAlias .= 'select a1.* from ' . $db->nameQuote( '#__social_stream' ) . ' as a1 where ' . $db->nameQuote( 'actor_type' ) . ' = ' . $db->Quote( $type ) . ' and ' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( $userId );

		if (! $userStickyOnly) {
			$streamTableAlias .= ' UNION ';

			// tagged item
			$streamTableAlias .= 'select a2.* from ' . $db->nameQuote( '#__social_stream' ) . ' as a2 ';
			$streamTableAlias .= ' inner join ' . $db->nameQuote( '#__social_stream_item' ) . ' as ai2 on a2.' . $db->nameQuote( 'id' ) . ' = ai2.' . $db->nameQuote( 'uid' );
			$streamTableAlias .= ' where a2.' . $db->nameQuote( 'actor_type' ) . ' = ' . $db->Quote( $type );
			// $streamTableAlias .= ' and a2.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $userId ) . ' and a2.' . $db->nameQuote( 'context_type' ) . ' IN (' . $db->Quote( 'friends' ) . ',' . $db->Quote( 'story' ) . ', ' . $db->Quote( 'photos' ) . ',' . $db->Quote( 'links' ) . ')';
			// $streamTableAlias .= ' and (';
			// $streamTableAlias .= '	  ( ai2.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote( 'friends' ) . ' and ai2.' . $db->nameQuote( 'verb' ) . ' = ' . $db->Quote( 'add' ) . ' ) or ';
			// $streamTableAlias .= ' 	  ( ai2.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote( 'story' ) .' and ai2.' . $db->nameQuote( 'verb' ) . ' = ' . $db->Quote( 'create' ) . ' ) or ';
			// $streamTableAlias .= '	  ( ai2.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote( 'photos' ) .' and ai2.' . $db->nameQuote( 'verb' ) . ' = ' . $db->Quote( 'share' ) . ' ) or ';
			// $streamTableAlias .= '	  ( ai2.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote( 'links' ) .' and ai2.' . $db->nameQuote( 'verb' ) . ' = ' . $db->Quote( 'create' ) . ' ) ';
			// $streamTableAlias .= '	)';

			$inQuery = '';
			$orQuery = '';
			foreach($profileApps as $context => $verb) {
				$join = ($inQuery) ? ',' : '';
				$inQuery .= $join . $db->Quote($context);

				$orJoin = ($orQuery) ? ' or ' : '';
				$orQuery .= $orJoin . '(ai2.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($context) . ' and ai2.' . $db->nameQuote( 'verb' ) . ' = ' . $db->Quote($verb) . ' )';
			}

			$streamTableAlias .= ' and a2.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( $userId ) . ' and a2.' . $db->nameQuote( 'context_type' ) . ' IN (' . $inQuery . ')';
			$streamTableAlias .= ' and (';
			$streamTableAlias .= $orQuery;
			$streamTableAlias .= '	)';

		}

		if( $useDate )
		{
			if( $direction == 'later' )
			{
				$streamTableAlias .=	' AND a2.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $startdate );
			}
			else
			{
				$streamTableAlias .=	' AND ( a2.' . $db->nameQuote( $sortDate ) . ' <= ' . $db->Quote( $startdate ) . ' AND a2.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $enddate ) . ')';
			}
		}

		if($view == 'dashboard' && !$userStickyOnly) {

			$streamTableAlias .= ' UNION ';
			// friends item
			$streamTableAlias .= 'select a4.* from ' . $db->nameQuote( '#__social_stream' ) . ' as a4 INNER JOIN ' . $db->nameQuote( '#__social_friends' ) . ' AS f1 ON a4.' . $db->nameQuote( 'actor_id' ) . ' = f1.' . $db->nameQuote( 'target_id' ) . ' and f1.' . $db->nameQuote( 'actor_id' ) . ' =  ' . $db->Quote( $userId ) . ' and f1.' . $db->nameQuote( 'state') . ' = ' . $db->Quote('1');

			$streamTableAlias .= ' UNION ';
			$streamTableAlias .= 'select a5.* from ' . $db->nameQuote( '#__social_stream' ) . ' as a5 INNER JOIN ' . $db->nameQuote( '#__social_friends' ) . ' AS f2 ON a5.' . $db->nameQuote( 'actor_id' ) . ' = f2.' . $db->nameQuote( 'actor_id' ) . ' and f2.' . $db->nameQuote( 'target_id' ) . ' =  ' . $db->Quote( $userId ) . ' and f2.' . $db->nameQuote( 'state') . ' = ' . $db->Quote('1');
		}

		$streamTableAlias .= ' UNION ';
		$streamTableAlias .= 'select a6.* from ' . $db->nameQuote( '#__social_stream' ) . ' as a6 inner join ' . $db->nameQuote( '#__social_stream_tags' ) . ' as st on a6.' . $db->nameQuote( 'id' ) . ' = st.' . $db->nameQuote( 'stream_id' ) . ' where st.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( $userId ) . ' and st.' . $db->nameQuote( 'utype' ) . ' = ' . $db->Quote( SOCIAL_STREAM_TAGGING_TYPE_USER );

		$streamTableAlias .= ') as a';

		// echo $streamTableAlias;

		return $streamTableAlias;
	}

	/**
	 * Retrieves a list of stream items from cluster types
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getClusterStreamData($options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$config = FD::config();

		// Get the view
		$view = JRequest::getVar('view', '');

		// Enforce a hard limit
		$hardLimit = SOCIAL_STREAM_HARD_LIMIT;

		// Get the sorting behavior
		$sortDate = $config->get('stream.pagination.sort', 'modified');

		// Determines if the user wants to filter items by cluster
		$clusterId = isset( $options[ 'clusterId' ] ) ? $options[ 'clusterId' ] : false;
		$clusterType = isset( $options[ 'clusterType' ] ) ? $options[ 'clusterType' ] : false;
		$clusterCategory = isset( $options[ 'clusterCategory' ] ) ? $options[ 'clusterCategory' ] : false;

		$context 	= isset( $options[ 'context' ] ) ? $options[ 'context' ] : false;
		$userid 	= isset( $options[ 'userid' ] ) ? $options[ 'userid' ] : false;

		$uid 		= isset( $options[ 'uid' ] ) ? $options[ 'uid' ] : false;
		$type 		= isset( $options[ 'type' ] ) ? $options[ 'type' ] : SOCIAL_TYPE_USER;
		$viewer 	= isset( $options[ 'viewer' ] ) ? $options[ 'viewer' ] : false;

		if ($viewer !== false) {
			$viwer = (int) $viewer;
		}

		$limitstart = isset( $options[ 'limitstart' ] ) ? $options[ 'limitstart' ] : false;
		$limitend 	= isset( $options[ 'limitend' ] ) ? $options[ 'limitend' ] : false;

		$streamId	= isset( $options[ 'streamId' ] ) ? $options[ 'streamId' ] : false;
		$direction	= isset( $options[ 'direction' ] ) ? $options[ 'direction' ] : 'older';

		$isSticky 	= isset( $options[ 'issticky' ] ) ? $options[ 'issticky' ] : false;
		$noSticky = isset( $options[ 'nosticky' ] ) ? $options[ 'nosticky' ] : false;

		$ignoreUser = isset( $options[ 'ignoreUser' ] ) ? $options[ 'ignoreUser' ] : false ;
		$tag 		= isset( $options[ 'tag' ] ) ? $options[ 'tag' ] : false;

		$guest 		= isset( $options[ 'guest' ] ) ? $options[ 'guest' ] : false ;
		$limit 		= isset( $options[ 'limit' ] ) ? $options[ 'limit' ] : false ;
		$startlimit = isset( $options[ 'startlimit' ] ) ? $options[ 'startlimit' ] : '0' ;

		// If tag is provided, we need to ensure that it's an array
		$tag = FD::makeArray($tag);

		// Ensure that the cluster type is an array
		$clusterType = FD::makeArray($clusterType);

		// Ensure that the cluster id is an array
		$clusterId = FD::makeArray($clusterId);

		// Ensure that the cluster category is an array
		$clusterCategory = FD::makeArray($clusterCategory);


		$query = array();
		$table = array();
		$cond = array();
		$order = array();

		$distinctRow = $tag ? ' DISTINCT' : '';

		$query[] = 'SELECT ' . $distinctRow. ' a.*';
		$query[] = ', l.id as loc_id, l.uid as loc_uid, l.type as loc_type, l.user_id as loc_user_id, l.created as loc_created, l.short_address as loc_short_address';
		$query[] = ',l.address as loc_address, l.longitude as loc_longitude, l.latitude as loc_latitude, l.params as loc_params';
		$query[] = ',md.id as md_id, md.namespace as md_namespace,md.namespace_uid as md_namespace_uid, md.icon as md_icon, md.verb as md_verb, md.subject as md_subject, md.custom as md_custom';
		$query[] = ',md.text as md_text, md.user_id as md_user_id, md.created as md_created, ssk.id as sticky';

		if ($viewer) {
			$query[]	= ',sbm.id as bookmarked';
		} else {
			$query[]	= ',0 as bookmarked';
		}


		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.`modified` ) ) / 60 ) AS `min`';
		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.`modified` ) ) / 60 / 60 ) AS `hour`';
		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.`modified` ) ) / 60 / 60 / 24 ) AS `day`';


		$table[] = ' FROM `#__social_stream` AS a INNER JOIN `#__social_clusters` AS sc ON a.`cluster_id` = sc.`id`';

		// joining events meta table
		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_events_meta' ) . ' AS em ON sc.' . $db->nameQuote( 'id' ) . ' = em.' . $db->nameQuote( 'cluster_id' );

		// joining location table
		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_locations' ) . ' AS l ON a.' . $db->nameQuote( 'location_id' ) . ' = l.' . $db->nameQuote( 'id' );

		// joining mood table
		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_moods' ) . ' AS md ON a.' . $db->nameQuote( 'mood_id' ) . ' = md.' . $db->nameQuote( 'id' );

		// joining bookmark table
		if ($viewer) {
			$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_bookmarks' ) . ' AS sbm ON a.' . $db->nameQuote( 'id' ) . ' = sbm.' . $db->nameQuote( 'uid' ) . ' and sbm.' . $db->nameQuote('type') . ' = ' . $db->Quote('stream');
			$table[] = 'and sbm.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($viewer);
		}

		if ($isSticky) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_stream_sticky' ) . ' AS ssk';
			$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = ssk.' . $db->nameQuote( 'stream_id' );
		} else {
			$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_stream_sticky' ) . ' AS ssk';
			$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = ssk.' . $db->nameQuote( 'stream_id' );
		}

		if (!$ignoreUser) {
			$table[] = 'INNER JOIN `#__users` AS uu ON a.`actor_id` = uu.`id` AND uu.`block` = 0' ;
		}

		if ($tag) {
			$table[] = 'INNER JOIN `#__social_stream_tags` AS tags';
			$table[] = 'ON a.`id` = tags.`stream_id`';
		}

		$isSingleItem = false;

		if (isset($options['moderated']) && $options['moderated']) {
			$cond[] = 'WHERE (a.`state` = 1 OR a.`state` = 5)';
		} else {

			if (isset($options['onlyModerated']) && $options['onlyModerated']) {
				$cond[] = 'WHERE a.`state` = ' . $db->Quote(SOCIAL_STREAM_STATE_MODERATE);
			} else {
				$cond[] = 'WHERE a.`state` = ' . $db->Quote(SOCIAL_STREAM_STATE_PUBLISHED);
			}
		}

		if ($streamId) {
			$cond[] = 'AND a.`id` = ' . $db->Quote($streamId);
			$isSingleItem = true;
		} else {

			// Support for multiple cluster type
			// Also support for clusterType = false
			if ($clusterType) {
				if (count($clusterType) > 1) {
					$cond[] = 'AND a.`cluster_type` IN (' . implode(',', $db->quote($clusterType)) . ')';
				} else {
					$cond[] = 'AND a.`cluster_type` = ' . $db->quote($clusterType[0]);
				}
			}

			if ($clusterId) {
				if (count($clusterId) > 1) {
					$cond[] = 'AND a.`cluster_id` IN (' . implode(',', $db->quote($clusterId)) . ')';
				} else {
					$cond[] = 'AND a.`cluster_id` = ' . $clusterId[0] ;
				}
			}

			if( $clusterCategory )
			{
				if( count( $clusterCategory ) > 1 )
				{
					$tmp = '(';
					for( $i = 0; $i < count( $clusterCategory ); $i++ )
					{
						$tmp .= $db->Quote( $clusterCategory[ $i ] );
						$tmp .= ( $i < count( $clusterCategory ) - 1 ) ? ',' : '';
					}
					$tmp .= ')';
					$cond[] = 'AND sc.`category_id` IN ' . $tmp ;
				}
				else
				{
					$cond[] = 'AND sc.`category_id` = ' . $clusterCategory[0] ;
				}
			}


			$cond[]	= 'AND a.`actor_type` = ' . $db->Quote( $type );
			if( $context !== 'all' )
			{

				// context used to filter the apps.
				if( is_array( $context) )
				{
					if( count($context) == 1 )
					{
						$cond[]	= 'AND a.`context_type` = ' . $db->Quote( $context );
					}
					else
					{

						$tmpString = '';
						foreach( $context as $citem )
						{
							$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $citem ) : $db->Quote( $citem );
						}

						$cond[]	= 'AND a.`context_type` IN (' . $tmpString . ')';
					}
				}
				else
				{
					// $cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . '=' . $db->Quote( $context );
					$cond[]	= 'AND a.`context_type` =' . $db->Quote( $context );
				}

				// context used to filter the apps.
				//$cond[]	= 'AND a.`context_type` =' . $db->Quote( $context );
			}
		}

		// exclude these context items
		$excludeApps = array();

		if ($clusterType) {
			foreach ($clusterType as $c) {
				$excludeApps[$c] = $this->getUnAccessilbleUserApps($viewer, $c);
			}
		}

		// maybe for clsuter type, we do not need to filter out the streams.
		if( $excludeApps )
		{
			$appOnly = array();
			$appWithVerb = array();


			foreach ($excludeApps as $ctype => $capps ) {
				foreach ($capps as $app => $verbs ) {
					if ($verbs === true) {
						$appOnly[$ctype][] = $app;
					} else {
						$appWithVerb[$app] = $verbs;
					}
				}
			}

			// var_dump($appWithVerb);

			if (!empty($appOnly)) {
				$tmpCond = array();
				foreach($appOnly as $ctype => $capps) {
					$tmpString = '';
					foreach( $capps as $eApp )
					{
						$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $eApp ) : $db->Quote( $eApp );
					}
					$tmpCond[]	= '( a.' . $db->nameQuote( 'context_type' ) . ' NOT IN (' . $tmpString . ') and a.`cluster_type` = ' . $db->Quote($ctype) . ')';
				}

				if ($tmpCond > 1) {
					$cond[] = ' AND (' . implode(' OR ', $tmpCond) . ')';
				} else {
					$cond[] = ' AND ' . $tmpCond;
				}
			}


			if(!empty($appWithVerb)) {
				foreach ($appWithVerb as $app => $verbs) {
					if (count($verbs) == 1) {
						$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
								. $db->nameQuote( 'verb' ) . ' != ' . $db->Quote($verbs[0]) .') OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					} else {
						$tmpString = '';
						foreach( $verbs as $verb )
						{
							$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $verb ) : $db->Quote( $verb );
						}

						$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
								. $db->nameQuote( 'verb' ) . ' NOT IN (' . $tmpString .') ) OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					}
				}
			}

			// if (!empty($appOnly)) {
			// 	$tmpString = '';
			// 	foreach( $appOnly as $eApp )
			// 	{
			// 		$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $eApp ) : $db->Quote( $eApp );
			// 	}
			// 	$cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . ' NOT IN (' . $tmpString . ')';
			// }

			// if(!empty($appWithVerb)) {
			// 	foreach ($appWithVerb as $app => $verbs) {
			// 		if (count($verbs) == 1) {
			// 			$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
			// 					. $db->nameQuote( 'verb' ) . ' != ' . $db->Quote($verbs[0]) .') OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
			// 		} else {
			// 			$tmpString = '';
			// 			foreach( $verbs as $verb )
			// 			{
			// 				$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $verb ) : $db->Quote( $verb );
			// 			}

			// 			$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
			// 					. $db->nameQuote( 'verb' ) . ' NOT IN (' . $tmpString .') ) OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
			// 		}
			// 	}
			// }



		}

		if ($viewer) {

			// If user is site admin, do not block them
			if (!FD::user()->isSiteAdmin()) {
				// Group privacy
				$cond[]	= 'AND (';
				$cond[] = ' (sc.`type` = 1) OR';
				$cond[]	= ' (sc.`type` > 1) AND ' . $db->Quote($viewer) . ' IN ( select scn.`uid` from `#__social_clusters_nodes` as scn where (scn.`cluster_id` = a.`cluster_id` OR scn.`cluster_id` = em.`group_id`) and `type` = ' . $db->Quote(SOCIAL_TYPE_USER) . ' and `state` = 1)';
				$cond[] = ')';
			}

			// based on stream item.
			$cond[]	= 'AND NOT EXISTS (';
			$cond[]	= 'SELECT h.' . $db->nameQuote( 'uid' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h';
			$cond[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . '= h.' . $db->nameQuote( 'uid' );
			$cond[]	= 'AND h.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer );
			$cond[]	= 'AND h.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'stream' );
			$cond[]	= ')';

			//based on context
			$cond[]	= 'AND NOT EXISTS (';
			$cond[] = 'SELECT h1.' . $db->nameQuote( 'context' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h1';
			$cond[] = 'WHERE a.' . $db->nameQuote( 'context_type' ) . ' = h1.' . $db->nameQuote( 'context' );
			$cond[] = 'AND h1.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer ) ;
			$cond[] = 'AND h1.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
			$cond[] = 'AND h1.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( '0' );
			$cond[] = ')';

			//based on user who blocked the viewer
			if ($config->get('users.blocking.enabled')) {
				$cond[]	= 'AND NOT EXISTS (';
				$cond[]	= 'select bs.' . $db->nameQuote('user_id') . ' from ' . $db->nameQuote( '#__social_block_users' ) . ' as bs';
				$cond[] = 'where a.' . $db->nameQuote('actor_id') . ' = bs.' . $db->nameQuote('user_id');
				$cond[] = 'and bs.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($viewer);
				$cond[] = ')';
			}

		} else {
			// group privacy
			$cond[]	= 'AND sc.`type` = 1';
		}

		if( $tag )
		{
			if( count( $tag ) == 1 )
			{
				$cond[]	= 'AND tags.`title` = ' . $db->Quote( $tag[0] );
			}
			else
			{
				$totalTags = count( $tag );
				$tagQuery  = '';

				for( $t = 0; $t < $totalTags ; $t++ )
				{
					$tagQuery .= ( $t < $totalTags - 1 ) ? ' ( tags.`title` = ' . $db->Quote( $tag[ $t ] ) . ') OR ' : ' ( tags.`title` = ' . $db->Quote( $tag[ $t ] ) . ')';
				}

				$cond[]	= 'AND ( ' . $tagQuery . ' )';
			}
		}

		if ($noSticky) {
			// exclude sticky posts.
			$cond[] = ' AND ssk.`id` is null';
		}

		// lets get the limit dates here instead
		$limitDates = array();

		// startdate holding the larger date
		// enddte holding the smaller date.
		if(! $isSingleItem && !$limit)
		{
			if( $direction == 'later' )
			{
				$cond[]	= 'AND a.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $limitstart );
			}
		}


		// ordering. DO NOT change the ordering.
		$order[]	= 'ORDER BY a.' . $db->nameQuote( $sortDate ) . ' DESC';

		// concate all the queries segments.
		$query 		= implode( ' ' , $query );
		$table 		= implode( ' ' , $table );

		$cond 		= implode( ' ' , $cond );
		$order 		= implode( ' ' , $order );

		$totalSQL 	= 'select count(1) from ( ' . $query . ' ' . $table . ' ' . $cond . ' ) as x';

		$query 		= $query . ' ' . $table . ' ' . $cond . ' ' . $order;

		if ($limit) {
			$query 		.= ' LIMIT ' . $startlimit . ',' . ( $limit + 1 );
		}

		// echo $query;
		// echo '<br/><br/>';
		// exit;

	 	$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadObjectList();
		$counts = count($result);


		// now we need to remove the last index from the result.
		if(! $isSingleItem && $limit && $counts > $limit)
		{
			array_pop($result);
		}

		if ($counts > $limit) {
			$total = 1;
		} else {
			$total = 0;
		}

		$this->total = $total;

		// query to get the pagination total;
		if ($config->get('stream.pagination.style') == 'page') {

			$page = array();

			$page['previous'] = null;
			$page['next'] = null;

			if ($startlimit) {
				$p = $startlimit - $limit;
				$page['previous'] = ($p > 0) ? $p : 0;
			}

			if($total) {
				$page['next'] = $startlimit + $limit;
			}

			$this->pagination 	=  $page;
		}

		$lastItemDate 	= '';
		$total = count($result);

		if (!$total) {
			return $result;
		}

		$streamIds = array();
		$streamContexts = array();

		foreach ($result as $row) {
			$streamIds[] = $row->id;
			$streamContexts[] = $row->context_type;

			$lastItemDate = $row->modified;
		}

		// -------------------------------------------------------------
		// This is the starting points of optimizing queries for stream.
		// -------------------------------------------------------------

		$this->setBatchRelatedItems( $streamIds, $streamContexts, $viewer );

		// set stream actors.
		$this->setActorsBatch( $result );

		// set stream photos.
		$this->setMediaBatch( $result );

		// set stream tagging
		$this->setTaggingBatch( $streamIds );

		//set stream likes
		if ($config->get('stream.likes.enabled')) {
			$like = FD::model('Likes');
			$like->setStreamLikesBatch($result);
		}

		//set stream repost
		if ($config->get('stream.repost.enabled')) {
			$repost = FD::model('Repost');
			$repost->setStreamRepostBatch($result);

			$share = FD::table('Share');
			$share->setSharesBatch($result);
		}

		// comment count
		if ($config->get('stream.comments.enabled')) {
			$commentModel = FD::model('Comments');
			$commentModel->setStreamCommentCountBatch($result);
		}

		return $result;
	}

	public function generateMutualFriendSQL( $source, $target )
	{
		$query = '';

		$query = "select count(1) from (";
		$query .= "	select af1.`actor_id` as `fid` from `#__social_friends` as af1 where af1.`target_id` = $source and af1.`state` = 1";
		$query .= "		union ";
		$query .= "	select af2.`target_id` as `fid`  from `#__social_friends` as af2 where af2.`actor_id` = $source and af2.`state` = 1";
		$query .= " ) as x";
		$query .= " where exists (";
		$query .= "	select bf1.`actor_id` from `#__social_friends` as bf1 where bf1.`target_id` = $target and bf1.`actor_id` = x.`fid` and bf1.`state` = 1";
		$query .= " 	union ";
		$query .= "	select bf2.`target_id` from #__social_friends as bf2 where bf2.`actor_id` = $target and bf2.`target_id` = x.`fid`  and bf2.`state` = 1";
		$query .= " )";

		return $query;
	}

	public function generateIsFriendSQL( $source, $target )
	{
		$query = "select count(1) from `#__social_friends` where ( `actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";

		return $query;
	}


	/**
	 * Retrieves the stream data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStreamData( $config = array() )
	{

		$db 		= FD::db();
		$sysconfig 	= FD::config();

		$sortDate   = $sysconfig->get( 'stream.pagination.sort', 'modified' );


		// If a context is given.
		$actorid 		= isset( $config[ 'actorid' ] ) ? $config[ 'actorid' ] : false;
		$context 		= isset( $config[ 'context' ] ) ? $config[ 'context' ] : false;
		$userid 	= isset( $config[ 'userid' ] ) ? $config[ 'userid' ] : false;
		$listid 	= isset( $config[ 'list' ] ) ? $config[ 'list' ] : false;
		$profileId  = isset($config['profileId']) ? $config['profileId'] : false;
		$uid 		= isset( $config[ 'uid' ] ) ? $config[ 'uid' ] : false;

		$type 		= isset( $config[ 'type' ] ) ? $config[ 'type' ] : SOCIAL_TYPE_USER;
		$viewer 	= isset( $config[ 'viewer' ] ) ? $config[ 'viewer' ] : false;
		if ($viewer !== false) {
			$viwer = (int) $viewer;
		}

		$limitstart = isset( $config[ 'limitstart' ] ) ? $config[ 'limitstart' ] : false;
		$limitend 	= isset( $config[ 'limitend' ] ) ? $config[ 'limitend' ] : false;

		$isFollow 	= isset( $config[ 'isfollow' ] ) ? $config[ 'isfollow' ] : false;

		$isBookmark 	= isset( $config[ 'isbookmark' ] ) ? $config[ 'isbookmark' ] : false;

		$isSticky 	= isset( $config[ 'issticky' ] ) ? $config[ 'issticky' ] : false;

		$userStickyOnly 	= isset( $config[ 'userstickyonly' ] ) ? $config[ 'userstickyonly' ] : false;


		$noSticky = isset( $config[ 'nosticky' ] ) ? $config[ 'nosticky' ] : false;

		$streamId	= isset( $config[ 'streamId' ] ) ? $config[ 'streamId' ] : false;

		$direction	= isset( $config[ 'direction' ] ) ? $config[ 'direction' ] : 'older';

		$ignoreUser = isset( $config[ 'ignoreUser' ] ) ? $config[ 'ignoreUser' ] : false ;


		// pagination for public stream
		$limit 		= isset( $config[ 'limit' ] ) ? $config[ 'limit' ] : false ;
		$startlimit = isset( $config[ 'startlimit' ] ) ? $config[ 'startlimit' ] : '0' ;

		$guest 		= isset( $config[ 'guest' ] ) ? $config[ 'guest' ] : false ;
		$tag 		= isset( $config[ 'tag' ] ) ? $config[ 'tag' ] : false;

		$customView = isset( $config[ 'view' ] ) ? $config[ 'view' ] : false ;

		if( $tag && !is_array( $tag ) )
		{
			$tag = array( $tag );
		}

		$query 		= array();
		$table 		= array();
		$cond 		= array();
		$order 		= array();

		//for total count
		$cntTable = array();
		$cntCond = array();

		$view = JRequest::getVar( 'view', '');

		$my = FD::user();
		$isAdmin = $my->isSiteAdmin();


		if (empty( $listid )
			&& empty($isBookmark)
			&& empty($isFollow)
			&& empty($profileId)
			&& !$streamId
			&& !$guest
			&& !$tag
			&& $context === 'all'
			&& $isAdmin) {

			//if this is true, most likely the user is an admin and admin is clicking on 'me/my friend' filter.
			// if that is the case, then we need to get only admin and friends stream. so we canot set isAdmin to true.

			$isAdmin = false;
		}


		$streamTableAlias = $db->nameQuote( '#__social_stream' ) . ' AS a';

		// if( empty( $listid ) && empty( $isBookmark ) && empty( $isSticky ) && empty( $isFollow ) && empty( $profileId ) && !$streamId && !$guest && !$tag && !$isAdmin)
		if( empty( $listid ) && empty( $isBookmark ) && empty( $isFollow ) && empty( $profileId ) && !$streamId && !$guest && !$tag && !$isAdmin)
		{
			$streamTableAlias = $this->getStreamTableAlias( $userid[ 0 ], $type, $userStickyOnly, false, '', null, null, $customView );
		}

		// since we are joining social_items table, we will need to distinct the results.
		$distinctRow = ( $tag ) ? ' distinct' : '';

		$query[]	= 'SELECT ' . $distinctRow. ' a.*';
		$query[]    = ', l.id as loc_id, l.uid as loc_uid, l.type as loc_type, l.user_id as loc_user_id, l.created as loc_created, l.short_address as loc_short_address';
		$query[]	= ',l.address as loc_address, l.longitude as loc_longitude, l.latitude as loc_latitude, l.params as loc_params';
		$query[]	= ',md.id as md_id, md.namespace as md_namespace,md.namespace_uid as md_namespace_uid, md.icon as md_icon, md.verb as md_verb, md.subject as md_subject, md.custom as md_custom';
		$query[]	= ',md.text as md_text, md.user_id as md_user_id, md.created as md_created, ssk.id as sticky';

		if ($viewer) {
			$query[]	= ',sbm.id as bookmarked';
		} else {
			$query[]	= ',0 as bookmarked';
		}

		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 ) AS ' . $db->nameQuote( 'min' );
		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 / 60 ) AS ' . $db->nameQuote( 'hour' );
		$query[]	= ',FLOOR( ( UNIX_TIMESTAMP( now() ) - UNIX_TIMESTAMP( a.' . $db->nameQuote( 'modified' ) . ') ) / 60 / 60 / 24 ) AS ' . $db->nameQuote( 'day' );

		$table[] = ' FROM ' . $streamTableAlias;

		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_events_meta' ) . ' AS em ON a.' . $db->nameQuote( 'cluster_id' ) . ' = em.' . $db->nameQuote( 'cluster_id' );

		// joining location table - do not change the position of this code
		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_locations' ) . ' AS l ON a.' . $db->nameQuote( 'location_id' ) . ' = l.' . $db->nameQuote( 'id' );

		// joining mood table - do not change the position of this code
		$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_moods' ) . ' AS md ON a.' . $db->nameQuote( 'mood_id' ) . ' = md.' . $db->nameQuote( 'id' );

		// joining users table - do not change the position of this code
		if (!$ignoreUser) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS uu ON a.' . $db->nameQuote( 'actor_id' ) . ' = uu.' . $db->nameQuote( 'id' ) . ' AND uu.' . $db->nameQuote( 'block' ) . ' = 0' ;
		}

		if ($isFollow) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_subscriptions' ) . ' AS s';
			$table[] = 'ON a.' . $db->nameQuote( 'actor_id' ) . ' = s.' . $db->nameQuote( 'uid' );
		}

		if ($profileId) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_profiles_maps' ) . ' AS spm';
			$table[] = 'ON a.' . $db->nameQuote( 'actor_id' ) . ' = spm.' . $db->nameQuote( 'user_id' ) . ' and spm.`profile_id` = ' . $db->Quote($profileId) ;
		}


		if ($viewer) {
			if ($isBookmark) {
				$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_bookmarks' ) . ' AS sbm';
				$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = sbm.' . $db->nameQuote( 'uid' ) . ' and sbm.' . $db->nameQuote('type') . ' = ' . $db->Quote('stream') . ' and sbm.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($viewer);
			} else {
				$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_bookmarks' ) . ' AS sbm';
				$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = sbm.' . $db->nameQuote( 'uid' ) . ' and sbm.' . $db->nameQuote('type') . ' = ' . $db->Quote('stream') . ' and sbm.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($viewer);
			}
		}

		if ($isSticky) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_stream_sticky' ) . ' AS ssk';
			$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = ssk.' . $db->nameQuote( 'stream_id' );
		} else {
			$table[] = 'LEFT JOIN ' . $db->nameQuote( '#__social_stream_sticky' ) . ' AS ssk';
			$table[] = 'ON a.' . $db->nameQuote( 'id' ) . ' = ssk.' . $db->nameQuote( 'stream_id' );
		}

		if (!empty($listid)) {
			$table[] = 'INNER JOIN ' . $db->nameQuote( '#__social_lists_maps' ) . ' AS lm';
			$table[] = 'ON a.' . $db->nameQuote( 'actor_id' ) . ' = lm.' . $db->nameQuote( 'target_id' ) . ' AND lm.' . $db->nameQuote( 'list_id' ) . ' = ' . $db->Quote( $listid ) . ' and lm.' . $db->nameQuote( 'target_type' ) . ' = ' .$db->Quote( 'user' );

		}

		if ($tag) {
			$table[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_stream_tags' ) . ' AS tags';
			$table[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = tags.' . $db->nameQuote( 'stream_id' );
		}

		$isSingleItem = false;

		// we do not want any cluster stream here.

		// * cntCond should not inlcude this cluster criteria.
		$cond[] = 'WHERE a.`state` = 1';

		if ($actorid) {
			$cond[] = 'AND a.`actor_id` = ' . $actorid;
		}

		$cond[] = 'AND (';
		$cond[]	= '(a.`cluster_id`= 0) OR';
		$cond[]	= '(a.`cluster_id` > 0 and a.`cluster_access` = 1)';
		if ($viewer) {
			$cond[]	= 'OR (a.`cluster_id` > 0 and a.`cluster_access` > 1 and ' . $viewer . ' IN (select scn.`uid` from `#__social_clusters_nodes` as scn where (scn.`cluster_id` = a.`cluster_id` OR scn.`cluster_id` = em.`group_id`) and scn.`type` = ' . $db->Quote( SOCIAL_TYPE_USER ) . ' and scn.`state` = 1) )';
		}
		$cond[]	= ')';

		if ($streamId) {
			$cond[]		= 'AND a.' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $streamId );

			if ($viewer && $sysconfig->get('users.blocking.enabled')) {
				//based on user who blocked the viewer
				$cond[]	= 'AND NOT EXISTS (';
				$cond[]	= 'select bs.' . $db->nameQuote('user_id') . ' from ' . $db->nameQuote( '#__social_block_users' ) . ' as bs';
				$cond[] = 'where a.' . $db->nameQuote('actor_id') . ' = bs.' . $db->nameQuote('user_id');
				$cond[] = 'and bs.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($viewer);
				$cond[] = ')';
			}

			$isSingleItem 	= true;
		}
		else
		{
			if( ! $guest )
			{
				if( $uid )
				{
					// filtering based on a particular stream.
					$cond[]	= 'AND a.' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $uid );
					$isSingleItem = true;
				}
				else
				{
					$cond[]	= 'AND a.' . $db->nameQuote( 'actor_type' ) . '=' . $db->Quote( $type );


					if( $context !== 'all' )
					{
						// context used to filter the apps.
						if( is_array( $context) )
						{
							if( count($context) == 1 )
							{
								$cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . '=' . $db->Quote( $context[0] );

							}
							else
							{

								$tmpString = '';
								foreach( $context as $citem )
								{
									$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $citem ) : $db->Quote( $citem );
								}

								$cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . 'IN (' . $tmpString . ')';

							}
						}
						else
						{
							$cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . '=' . $db->Quote( $context );
						}
					}
				}

			}

			// exclude these context items
			$excludeApps = $this->getUnAccessilbleUserApps( $viewer );

			if( $excludeApps )
			{
				$appOnly = array();
				$appWithVerb = array();

				foreach ($excludeApps as $app => $verbs ) {
					if ($verbs === true) {
						$appOnly[] = $app;
					} else {
						$appWithVerb[$app] = $verbs;
					}
				}

				if (!empty($appOnly)) {
					$tmpString = '';
					foreach( $appOnly as $eApp )
					{
						$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $eApp ) : $db->Quote( $eApp );
					}
					$cond[]	= 'AND a.' . $db->nameQuote( 'context_type' ) . ' NOT IN (' . $tmpString . ')';
				}

				if(!empty($appWithVerb)) {
					foreach ($appWithVerb as $app => $verbs) {
						if (count($verbs) == 1) {
							$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' != ' . $db->Quote($verbs[0]) .') OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
						} else {
							$tmpString = '';
							foreach( $verbs as $verb )
							{
								$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $verb ) : $db->Quote( $verb );
							}

							$cond[] = 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' NOT IN (' . $tmpString .') ) OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
						}
					}
				}
			}

			if( $isFollow )
			{
				$cond[]	= 'AND s.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( SOCIAL_TYPE_USER . '.' . SOCIAL_SUBSCRIPTION_TYPE_USER );
				$cond[]	= 'AND s.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userid[ 0 ] );
			}

			if( $viewer )
			{
				if (!$isAdmin) {
					// privacy here.
					$cond[] = 'AND (';

					//public
					$cond[] = '(a.`access` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR';

					//member
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $viewer . ' > 0 ) ) OR ';

					//friends of friends
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateMutualFriendSQL( $viewer, 'a.`actor_id`' ) . ') > 0 ) ) OR ';

					//friends
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'a.`actor_id`', $viewer ) . ') > 0 ) ) OR ';

					//friends
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'a.`actor_id`', $viewer ) . ') > 0 ) ) OR ';

					//only me
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( a.`actor_id` = ' . $viewer . ' ) ) OR ';

					// custom
					$cond[] = '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ') AND ( a.`custom_access` LIKE ' . $db->Quote( '%,' . $viewer . ',%' ) . '    ) ) OR ';

					// my own items.
					$cond[] = '(a.`actor_id` = ' . $viewer . ')';

					// privacy checking end here.
					$cond[] = ')';
				}

				// based on stream item.
				$cond[]	= 'AND NOT EXISTS (';
				$cond[]	= 'SELECT h.' . $db->nameQuote( 'uid' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h';
				$cond[]	= 'WHERE a.' . $db->nameQuote( 'id' ) . '= h.' . $db->nameQuote( 'uid' );
				$cond[]	= 'AND h.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer );
				$cond[]	= 'AND h.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'stream' );
				$cond[]	= ')';

				//based on context
				$cond[]	= 'AND NOT EXISTS (';
				$cond[] = 'SELECT h1.' . $db->nameQuote( 'context' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h1';
				$cond[] = 'WHERE a.' . $db->nameQuote( 'context_type' ) . ' = h1.' . $db->nameQuote( 'context' );
				$cond[] = 'AND h1.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer ) ;
				$cond[] = 'AND h1.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
				$cond[] = 'AND h1.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( '0' );
				$cond[] = ')';

				//based on actor
				$cond[]	= 'AND NOT EXISTS (';
				$cond[] = 'SELECT h2.' . $db->nameQuote( 'actor_id' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h2';
				$cond[] = 'WHERE a.' . $db->nameQuote( 'actor_id' ) . '= h2.' . $db->nameQuote( 'actor_id' ) ;
				$cond[] = 'AND h2.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer ) ;
				$cond[] = 'AND h2.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
				$cond[] = 'AND h2.' . $db->nameQuote( 'context' ) . ' is null';
				$cond[] = ')';

				if ($sysconfig->get('users.blocking.enabled')) {
					//based on user who blocked the viewer
					$cond[]	= 'AND NOT EXISTS (';
					$cond[]	= 'select bs.' . $db->nameQuote('user_id') . ' from ' . $db->nameQuote( '#__social_block_users' ) . ' as bs';
					$cond[] = 'where a.' . $db->nameQuote('actor_id') . ' = bs.' . $db->nameQuote('user_id');
					$cond[] = 'and bs.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($viewer);
					$cond[] = ')';
				}

			} else {
				// guest privacy
				$cond[] = 'AND a.`access` = ' . $db->Quote('0');
			}

			if( $tag )
			{
				if( count( $tag ) == 1 )
				{
					$cond[]	= 'AND tags.' . $db->nameQuote( 'title' ) . '=' . $db->Quote( $tag[0] );
				}
				else
				{
					$totalTags = count( $tag );
					$tagQuery  = '';

					for( $t = 0; $t < $totalTags ; $t++ )
					{
						$tagQuery .= ( $t < $totalTags - 1 ) ? ' ( tags.title = ' . $db->Quote( $tag[ $t ] ) . ') OR ' : ' ( tags.title = ' . $db->Quote( $tag[ $t ] ) . ')';
					}

					$cond[]	= 'AND ( ' . $tagQuery . ' )';
				}
			}

			if ($noSticky) {
				// exclude sticky posts.
				$cond[] = ' AND ssk.`id` is null';
			}
		}

		// lets get the limit dates here instead
		$limitDates = array();


		// ordering. DO NOT change the ordering.
		$order[]	= 'ORDER BY a.' . $db->nameQuote( $sortDate ) . ' DESC';

		// startdate holding the larger date
		// enddte holding the smaller date.
		if(! $isSingleItem && !$limit)
		{
			if( $direction == 'later' )
			{
				$cond[]	= 'AND a.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $limitstart );
				$cntCond[] = 'AND a.' . $db->nameQuote( $sortDate ) . ' >= ' . $db->Quote( $limitstart );
			}
		}

		// concate all the queries segments.
		$query 		= implode( ' ' , $query );
		$table 		= implode( ' ' , $table );

		$cond 		= implode( ' ' , $cond );
		$order 		= implode( ' ' , $order );


		$query 		= $query . ' ' . $table . ' ' . $cond . ' ' . $order;

		if(! $isSingleItem && $limit )
		{
	 		$query 		.= ' LIMIT ' . $startlimit . ',' . ( $limit + 1 );
	 	}

	 	// echo $query;
	 	// echo '<br /><br />';
	 	// exit;

		$db->setQuery( $query );
		$result 	= $db->loadObjectList();

		$counts = count($result);

		// now we need to remove the last index from the result.
		if(! $isSingleItem && $limit && $counts > $limit)
		{
			array_pop($result);
		}

		if ($counts > $limit) {
			$total = 1;
			} else {
			$total = 0;
			}

		$this->total = $total;

		// query to get the pagination total;
		if ($sysconfig->get('stream.pagination.style') == 'page') {

			$page = array();

			$page['previous'] = null;
			$page['next'] = null;

			if ($startlimit) {
				$p = $startlimit - $limit;
				$page['previous'] = ($p > 0) ? $p : 0;
			}

			if($total) {
				$page['next'] = $startlimit + $limit;
			}

			$this->pagination 	=  $page;
		}

		$lastItemDate 	= '';
		$itemCnt 		= count( $result );

		if( $itemCnt )
		{
			$streamIds		= array();
			$streamContexts	= array();
			$clusterIds = array();

			foreach( $result as $row )
			{
				$streamIds[] 	= $row->id;
				$streamContexts[] = $row->context_type;
				$lastItemDate 	= $row->modified;

				if ($row->cluster_type == SOCIAL_TYPE_GROUP || $row->cluster_type == SOCIAL_TYPE_EVENT) {
					$clusterIds[] = $row->cluster_id;
				}
			}

			$this->uids = $streamIds;

			// -------------------------------------------------------------
			// This is the starting points of optimizing queries for stream.
			// -------------------------------------------------------------

			// @sam: it seems like adding these two slowing down the page load. lets comment it for now.
			// $streamTbl = FD::table( 'Stream' );
			// $streamTbl->loadByBatch( $streamIds );

			// $streamItemTbl = FD::table( 'StreamItem' );
			// $streamItemTbl->loadByUIDBatch( $streamIds );

			$this->setBatchRelatedItems( $streamIds, $streamContexts, $viewer );

			if ($clusterIds) {
				$clusterIds = array_unique($clusterIds);
				FD::cache()->cacheClusters($clusterIds);
			}

			// set stream actors.
			$this->setActorsBatch( $result );

			// set stream photos.
			$this->setMediaBatch( $result );

			// set stream tagging
			$this->setTaggingBatch( $streamIds );

			if ($sysconfig->get('stream.likes.enabled')) {

				//set stream likes
				$like = FD::model('Likes');
				$like->setStreamLikesBatch($result);
			}

			if ($sysconfig->get('stream.repost.enabled')) {
				//set stream repost
				$repost = FD::model('Repost');
				$repost->setStreamRepostBatch($result);

				$share = FD::table('Share');
				$share->setSharesBatch($result);
			}

			if ($sysconfig->get('stream.comments.enabled')) {
				// comment count
				$commentModel = FD::model('Comments');
				$commentModel->setStreamCommentCountBatch($result);

				/// comments
				$commentModel->setStreamCommentBatch($result);
			}

			// privacy
			$privacyModel = FD::model('Privacy');
			$privacyModel->setStreamPrivacyItemBatch($result);
		}

		return $result;
	}

	private function getUnAccessilbleUserApps( $userId = null, $group =  SOCIAL_APPS_GROUP_USER )
	{
		static $_cache = array();



		if (! isset($_cache[$group])) {

			$db = FD::db();
			$sql = $db->sql();
			$apps = array();

			// if ($userId) {
				$query = "select `element` from `#__social_apps`";
				$query .= " where `type` = 'apps'";
				$query .= " and `state` = 0";
				$query .= ' and `group` = ' . $db->Quote( $group );
				// $query .= " union ";
				// $query .= "select a.`element` from `#__social_apps` as a";
				// $query .= " left join `#__social_apps_map` as b on a.`id` = b.`app_id`";
				// $query .= "	and b.`type` = 'user'";
				// $query .= "	and b.`uid` = '$userId'";
				// $query .= " where a.`type` = 'apps'";
				// $query .= " and a.`group` = 'user'";
				// $query .= " and a.`default` = 0";
				// $query .= " and a.`system` = 0";
				// $query .= " and a.`state` = 1";
				// $query .= " and b.`app_id` is null";

				$sql->raw($query);
				$db->setQuery($sql);

				$results = $db->loadColumn();

				if ($results) {
					foreach ($results as $app) {
						$apps[$app] = true;
					}
				}
			// }

			//now we need to triggers all the apps to see if there is any setting to exclude certain verb's item or not.
			$appLib 	= FD::getInstance( 'Apps' );
			$appLib->load( $group ); // load user apps

			// Pass arguments by reference.
			$args 			= array( &$apps );

			// @trigger: onStreamVerbExclude
			$dispatcher		= FD::dispatcher();
			$result 		= $dispatcher->trigger( $group , 'onStreamVerbExclude' , $args );

			$_cache[$group] = $apps;
		}

		return $_cache[$group];
	}

	public function setMediaBatch( $result )
	{
		$photoIds 	= array();
		$albumIds 	= array();

		$streamModel = FD::model( 'Stream' );

		foreach( $result as $item )
		{
			if( $item->context_type == 'photos' )
			{
				$relatedData = $streamModel->getBatchRalatedItem( $item->id );

				if( $relatedData )
				{
					foreach( $relatedData as $rdata )
					{
						$photoIds[] = $rdata->context_id;
					}
				}
			}
		}

		if( $photoIds )
		{
			// photos
			$photo = FD::table( 'Photo' );
			$photo->setCacheable( true );
			$albumIds = $photo->loadByBatch( $photoIds );

			// photos meta
			$photoModel = FD::model( 'Photos' );
			$photoModel->setCacheable( true );
			$photoModel->setMetasBatch( $photoIds );
		}

		if( $albumIds )
		{
			$albumIds = array_unique( $albumIds );
			$album = FD::table( 'Album' );
			$album->loadByBatch( $albumIds );
		}

	}

	public function setActorsBatch( $result )
	{
		$actorIds  = array();

		$streamModel = FD::model( 'Stream' );

		foreach( $result as $item )
		{
			$relatedData = $streamModel->getBatchRalatedItem( $item->id );

			if( $relatedData )
			{
				foreach( $relatedData as $rdata )
				{
					$actorIds[] = $rdata->actor_id;

					if( $rdata->target_id )
					{
						if( !( $rdata->context_type == 'photos' && $rdata->verb == 'add' )
							&& !( $rdata->context_type == 'shares' && $rdata->verb == 'add.stream' ) )
						{
							$actorIds[] = $rdata->target_id;
						}
					}
				}
			}
		}

		$actorIds[] = FD::user()->id;
		$actors		= array_unique($actorIds);

		if ($actors) {
			// Preload users
			FD::user( $actors );
			FD::cache()->cacheUsersPrivacy($actors);
		}
	}


	public function setBatchRelatedItems( $uids , $contexts, $viewer = null )
	{
		// _relateditems

		// make sure the keys is not already added.
		foreach( $uids as $id )
		{
			if( array_key_exists( $id, self::$_relateditems ) )
			{
				return ;
			}
		}

		// Get related activities for aggregation.
		$query 		= array();
		$db 		= FD::db();

		$contextSegments = array();

		for( $i = 0; $i < count($uids); $i++ )
		{
			$contextSegments[ $contexts[$i] ][] = $uids[$i];
		}

		$ci = 0;
		$cCount = count($contextSegments);
		foreach( $contextSegments as $context => $cSegmentIds )
		{
			$idSegments = array_chunk( $cSegmentIds, 5 );

			for( $i = 0; $i < count( $idSegments ); $i++ )
			{
				$segment    = $idSegments[$i];
				$ids  		= implode( ',', $segment );

				$query[]	= 'SELECT * FROM ' . $db->nameQuote( '#__social_stream_item' ) . ' as a';
				$query[]	= 'WHERE ' . $db->nameQuote( 'uid' ) . ' IN ( ' . $ids . ')';

				if( $context != 'all' && $context != SOCIAL_TYPE_STREAM )
				{
					$query[]	= 'AND ' . $db->nameQuote( 'context_type' ) . '=' . $db->Quote( $context );
				}

				if( $viewer )
				{
					$query[]	= 'AND a.' . $db->nameQuote( 'id' ) . ' NOT IN (';
					$query[]	= 'SELECT h.' . $db->nameQuote( 'uid' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h';
					$query[]	= 'WHERE h.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer );
					$query[]	= 'AND h.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'activity' );
					$query[]	= ')';
				}

				if( ($i + 1)  < count( $idSegments ) )
				{
					$query[] = ' UNION ';
				}
			}

			if( ++$ci < $cCount )
			{
				$query[] = ' UNION ';
			}
		}

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$result = $db->loadObjectList();

		foreach( $result as $row )
		{
			self::$_relateditems[ $row->uid ][] = $row;
		}

		// var_dump(count(self::$_relateditems));

	}


	public function getBatchRalatedItem( $uid )
	{
		if( isset( self::$_relateditems[ $uid ] ) )
		{
			return self::$_relateditems[ $uid ];
		}
	}


	/**
	 * Get related activities from a single stream so that we can perform aggregation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The context.
	 */
	public function getRelatedActivities( $uid, $context, $viewer = null )
	{

		$keys = $uid;

		if( isset( self::$_relateditems[$keys] ) )
		{
			return self::$_relateditems[$keys];
		}

		// items not found from static variable. lets fall back to manual sql method.
		$db 		= FD::db();

		// Get related activities for aggregation.
		$query 		= array();
		$query[]	= 'SELECT * FROM ' . $db->nameQuote( '#__social_stream_item' ) . ' as a';
		$query[]	= 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid );

		if( $context != 'all' && $context != SOCIAL_TYPE_STREAM )
		{
			$query[]	= 'AND ' . $db->nameQuote( 'context_type' ) . '=' . $db->Quote( $context );
		}

		if( $viewer )
		{
			$query[]	= 'AND a.' . $db->nameQuote( 'id' ) . ' NOT IN (';
			$query[]	= 'SELECT h.' . $db->nameQuote( 'uid' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h';
			$query[]	= 'WHERE h.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $viewer );
			$query[]	= 'AND h.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'activity' );
			$query[]	= ')';
		}

		$query 		= implode( ' ' , $query );

		$db->setQuery( $query );

		$result 	= $db->loadObjectList();

		// log into static variable.
		self::$_relateditems[$keys] = $result;

		return $result;
	}

	public function setBatchActivityItems( $data )
	{
		foreach( $data as $row )
		{
			self::$_activitylogs[ $row->id ][] = $row;
 		}

 		// var_dump( self::$_activitylogs );
 		// exit;
	}

	/**
	 * Get related activities from a single stream so that we can perform aggregation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		activity item id.
	 */
	public function getActivityItem( $uid, $column = 'id' )
	{

		if( $column == 'uid' )
		{
			if( isset( self::$_relateditems[ $uid ] ) )
			{
				return self::$_relateditems[ $uid ];
			}
		}
		else
		{
			if( isset( self::$_activitylogs[ $uid ] ) )
			{
				return self::$_activitylogs[ $uid ];
			}
		}


		$db 		= FD::db();

		// Get related activities for aggregation.
		$sql 		= $db->sql();
		$sql->select( '#__social_stream_item' );
		$sql->where( $column , $uid );

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		return $result;
	}

	/**
	 * used in stream api
	 */
	public function updateStream($data)
	{
		$db = FD::db();
		$sql = $db->sql();

		$date = FD::date( $data->created );
		$duration   = 30;
		$isClusterType = ( $data->cluster_id && $data->cluster_type ) ? true : false;

		// Get the config obj.
		$config 	= FD::config();

		$allowAggregation = $config->get( 'stream.aggregation.enabled' );

		// The duration between activities.
		$duration 	= $config->get( 'stream.aggregation.duration' );

		if( $data->isAggregate )
		{

			// retrive the last item
			$query  = 'select a.`id`, a.`uid` from `#__social_stream_item` as a';
			if( $isClusterType )
			{
				$query .= ' inner join `#__social_stream` as b on a.`uid` = b.`id` and b.`cluster_id` = ' . $db->Quote( $data->cluster_id );
			}

			$query  .= ' where a.`actor_id` = ' . $db->Quote( $data->actor_id );
			$query  .= ' and a.`actor_type` = ' . $db->Quote( $data->actor_type );
			$query  .= ' and a.`context_type` = ' . $db->Quote( $data->context_type );
			$query  .= ' and a.`verb` = ' . $db->Quote( $data->verb );
			$query  .= ' and a.`sitewide` = ' . $db->Quote( $data->sitewide );
			$query  .= ' and date_add( a.`created` , INTERVAL ' . $duration . ' MINUTE ) >= ' . $db->Quote( $date->toMySQL() );
			if ($data->aggregateWithTarget) {
				$query  .= ' and a.`target_id` = ' . $db->Quote( $data->target_id );
			}
			$query	.= ' order by a.`created` DESC limit 1';

			$sql->raw( $query );
			$db->setQuery( $sql );

			$result = $db->loadObject();


			if( isset( $result->uid ) )
			{

				$streamTbl	= FD::table('Stream');
				$streamTbl->load( $result->uid );
				$streamTbl->modified    = $date->toMySQL();
				$streamTbl->store();

				return $result->uid;
			}

			// if not found.
			$query  = 'select a.* from `#__social_stream_item` as a';
			if( $isClusterType )
			{
				// $query .= ' inner join `#__social_clusters` as b on a.`cluster_id` = b.`id` and a.`cluster_id` = ' . $db->Quote( $data->cluster_id );
				$query .= ' inner join `#__social_stream` as b on a.`uid` = b.`id` and b.`cluster_id` = ' . $db->Quote( $data->cluster_id );
			}
			$query	.= ' order by a.`created` DESC limit 1';

			$db->setQuery( $query );

			$result = $db->loadObject();

			if( isset( $result->id ) )
			{
				$doAggregation = false;

				if( !$data->aggregateWithTarget &&
					($result->actor_id 		== $data->actor_id &&
					$result->actor_type 	== $data->actor_type &&
					$result->context_type 	== $data->context_type &&
					$result->verb 			== $data->verb &&
					$result->sitewide 		== $data->sitewide) ) {
					// here
					$doAggregation = true;
				} else if ( $data->aggregateWithTarget &&
					($result->actor_id 		== $data->actor_id &&
					$result->actor_type 	== $data->actor_type &&
					$result->context_type 	== $data->context_type &&
					$result->verb 			== $data->verb &&
					$result->sitewide 		== $data->sitewide &&
					$result->target_id 		== $data->target_id) ) {
					// here
					$doAggregation = true;
				}

				if ($doAggregation) {
					$streamTbl	= FD::table('Stream');
					$streamTbl->load( $result->uid );
					$streamTbl->modified    = $date->toMySQL();
					$streamTbl->store();

					return $result->uid;
				}


			}

		}

		// Create a new stream record
		$table = FD::table('Stream');
		$table->bind($data);

		$table->actor_type = isset($data->actor_type) && !empty($data->actor_type) ? $data->actor_type : 'user';
		$table->alias = '';
		$table->created = $date->toSql();
		$table->params = $data->params;
		$table->modified = $date->toSql();
		$table->cluster_id = $data->cluster_id;
		$table->cluster_type = $data->cluster_type;
		$table->verb = $data->verb;

		// Set the state of the stream
		$table->state = $data->state;

		// Privacy access
		$table->privacy_id = $data->privacy_id;
		$table->access = $data->access;
		$table->custom_access = $data->custom_access;

		$table->store();

		return $table->id;
	}

	/**
	 * Remove mentions associated with a stream
	 *
	 * @since	1.2.8
	 * @access	public
	 * @param	int 	The stream's id
	 * @return	bool
	 */
	public function removeMentions($streamId)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete('#__social_stream_tags');
		$sql->where('stream_id', $streamId);

		$db->setQuery($sql);

		return $db->Query();
	}

	/**
	 * Creates the mentions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of mention values
	 * @return
	 */
	public function addMentions($streamId , $mentions)
	{
		foreach ($mentions as $mention) {

			$tag 			= FD::table('StreamTags');
			$tag->stream_id	= $streamId;

			// Since this is not a with property.
			$tag->with 		= 0;

			// Set the offset and length
			$tag->offset	= $mention->start;
			$tag->length 	= $mention->length;

			if ($mention->type == 'entity') {

                $parts = explode(':', $mention->value);

                if (count($parts) != 2) {
                    continue;
                }

                $type = $parts[0];
                $id = $parts[1];

				// Set the type and uid
				$tag->utype 	= $type;
				$tag->uid 		= $id;
			}

			if ($mention->type == 'hashtag') {

				$tag->utype = 'hashtag';
				$tag->uid = 0;
				$tag->title = trim($mention->value);
			}

			$tag->store();
		}
	}

	public function setWith( $streamId, $ids )
	{
		if(! is_array( $ids ) )
		{
			$ids = array( $ids );
		}

		foreach( $ids as $id )
		{
			if( ! $id )
			{
				continue;
			}

			$tbl = FD::table( 'StreamTags' );
			$tbl->stream_id = $streamId;
			$tbl->uid 		= $id;
			$tbl->utype 	= SOCIAL_STREAM_TAGGING_TYPE_USER;
			$tbl->with 		= 1;

			$tbl->store();
		}
	}

	public function setTaggingBatch( $streamIds )
	{
		// _tagging

		$db 	= FD::db();
		$sql 	= $db->sql();

		//$uids = implode( ',', $streamIds );


		$ids = array();
		foreach( $streamIds as $sid )
		{
			if( ! isset( self::$_tagging[ $sid ] ) )
			{
				$ids[] = $db->Quote( $sid );
				self::$_tagging[ $sid ] = array();
			}
		}

		if( $ids )
		{
			$uids = implode( ',', $ids );

			$query 	= 'SELECT * FROM `#__social_stream_tags` WHERE `stream_id` IN(' . $uids . ') ORDER BY `stream_id`, `offset` DESC';
			$sql->raw( $query );

			$db->setQuery( $sql );

			$result = $db->loadObjectList();

			if( $result )
			{
				foreach( $result as $row )
				{

					if( $row->with )
					{
						self::$_tagging[ $row->stream_id ][ 'with' ][] = FD::user( $row->uid );
					}
					else
					{
						//this is a mention
						$obj = new stdClass();
						$obj->type		= $row->utype;
						$obj->offset	= $row->offset;
						$obj->length	= $row->length;
						$obj->id 		= $row->id;

						if( $row->utype == SOCIAL_TYPE_USER )
						{
							$obj->user 	= FD::user( $row->uid );

							self::$_tagging[ $row->stream_id ][ 'tags' ][] = $obj;
						}

						if( $row->utype == 'hashtag' )
						{
							$obj->title 	= $row->title;
							self::$_tagging[ $row->stream_id ][ 'tags' ][]	= $obj;
						}
					}

				}
			}
		}

	}


	/**
	 * int - stream id
	 * string - request type. with / mention
	 */
	public function getTagging( $streamId , $reqType = 'with' )
	{
		if( !isset( self::$_tagging[ $streamId ] ) )
		{

			$db 		= FD::db();
			$sql 		= $db->sql();

			$sql->select( '#__social_stream_tags', 'a' );
			$sql->column( 'a.*' );
			$sql->where( 'a.stream_id' , $streamId );
			$sql->order( 'a.offset' , 'DESC' );

			$db->setQuery( $sql );
			$result		= $db->loadObjectList();

			$withs 		= array();
			$tags		= array();

			if( $result )
			{
				$users	= array();
				foreach( $result as $row )
				{
					if( $row->uid && $row->utype == SOCIAL_TYPE_USER )
					{
						$users[] = $row->uid;
					}
				}

				// Preload the user's list
				FD::user( $users );

				foreach( $result as $row )
				{
					if( $row->with )
					{
						$withs[] = FD::user( $row->uid );
					}
					else
					{
						//this is a mention
						$obj 			= new stdClass();
						$obj->type 		= $row->utype;
						$obj->offset	= $row->offset;
						$obj->length	= $row->length;
						$obj->id 		= $row->id;

						if( $row->utype == SOCIAL_TYPE_USER )
						{
							$obj->user		= FD::user( $row->uid );
							$tags[]		= $obj;
						}

						if( $row->utype == 'hashtag' )
						{
							$obj->title 	= $row->title;
							$tags[]		= $obj;
						}
					}
				}
			}

			self::$_tagging[ $streamId ][ 'with' ]		= $withs;
			self::$_tagging[ $streamId ][ 'tags' ]		= $tags;
		}

		if( isset( self::$_tagging[ $streamId ][ $reqType ] ) )
		{
			return self::$_tagging[ $streamId ][ $reqType ];
		}

		return array();
	}

	public function getStreamActor( $streamId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select `actor_id` from `#__social_stream` where `id` = ' . $db->Quote( $streamId );
		$sql->raw( $query );

		$db->setQuery( $sql );

		$result = $db->loadResult();

		if( $result )
		{
			$user = FD::user( $result );
			return $user;
		}

		return false;
	}

	/**
	 * Retrieves new stream item counts for cluster
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function getClusterUpdateCount( $source, $currentdate, $clusterType, $clusterId, $exclude = null )
	{
		$db 	= FD::db();
		$sql  	= $db->sql();

		$config 	= FD::config();
		$sortDate   = $config->get( 'stream.pagination.sort', 'modified' );

		$query = 'select count(1) as `cnt`, a.`cluster_type` as `type` from `#__social_stream` as a';
		$query .= ' WHERE a.`state` = 1';
		$query .= ' and a.`cluster_id` = ' . $db->Quote( $clusterId );
		$query .= ' and a.`cluster_type` = ' . $db->Quote( $clusterType );
		$query .= ' and a.`modified` > ' . $db->Quote( $currentdate );
		if( $exclude )
		{
			$query .= ' and a.`id` NOT IN (' . $exclude . ')';
		}

		// exclude these context items
		$excludeApps = $this->getUnAccessilbleUserApps( '',  SOCIAL_APPS_GROUP_GROUP );

		// var_dump( $excludeApps);
		// exit;

		if( $excludeApps )
		{
			$appOnly = array();
			$appWithVerb = array();

			foreach ($excludeApps as $app => $verbs ) {
				if ($verbs === true) {
					$appOnly[] = $app;
				} else {
					$appWithVerb[$app] = $verbs;
				}
			}

			if (!empty($appOnly)) {
				$tmpString = '';
				foreach( $appOnly as $eApp )
				{
					$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $eApp ) : $db->Quote( $eApp );
				}
				$query	.= ' AND a.' . $db->nameQuote( 'context_type' ) . ' NOT IN (' . $tmpString . ')';
			}

			if(!empty($appWithVerb)) {
				foreach ($appWithVerb as $app => $verbs) {
					if (count($verbs) == 1) {
						$query .= ' AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' != ' . $db->Quote($verbs[0]) .') OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					} else {
						$tmpString = '';
						foreach( $verbs as $verb )
						{
							$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $verb ) : $db->Quote( $verb );
						}

						$query .= 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' NOT IN (' . $tmpString .') ) OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					}
				}
			}
		}

		// do not get the context / actor that user choosed to hide.
		$user = FD::user();

		if(! $user->guest )
		{
			$userId = $user->id;

			//based on context
			$query	.= ' AND NOT EXISTS (';
			$query .= ' SELECT h1.' . $db->nameQuote( 'context' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h1';
			$query .= ' WHERE a.' . $db->nameQuote( 'context_type' ) . ' = h1.' . $db->nameQuote( 'context' );
			$query .= ' AND h1.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) ;
			$query .= ' AND h1.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
			$query .= ' AND h1.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( '0' );
			$query .= ')';

			//based on actor
			$query .= ' AND NOT EXISTS (';
			$query .= ' SELECT h2.' . $db->nameQuote( 'actor_id' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h2';
			$query .= ' WHERE a.' . $db->nameQuote( 'actor_id' ) . '= h2.' . $db->nameQuote( 'actor_id' ) ;
			$query .= ' AND h2.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) ;
			$query .= ' AND h2.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
			$query .= ' AND h2.' . $db->nameQuote( 'context' ) . ' is null';
			$query .= ')';

			if ($config->get('users.blocking.enabled')) {
				$query .= ' AND NOT EXISTS (';
				$query .= ' select bs.' . $db->nameQuote('user_id') . ' from ' . $db->nameQuote( '#__social_block_users' ) . ' as bs';
				$query .= ' where a.' . $db->nameQuote('actor_id') . ' = bs.' . $db->nameQuote('user_id');
				$query .= ' and bs.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($userId);
				$query .= ')';
			}
		}



		$sql->raw( $query );
		$db->setQuery( $sql );

		$result = $db->loadAssocList();
		return $result;
	}

	/**
	 * Retrieves new stream item counts for users
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function getUpdateCount( $source, $currentdate, $type, $id = '', $exclude = null )
	{
		$db 	= FD::db();
		$sql  	= $db->sql();
		$user 	= FD::user();
		$isAdmin = $user->isSiteAdmin();


		$userId = $user->id;
		if( $type == 'me' || $type == 'following')
		{
			if( $id )
			{
				$userId = (int) $id;
			}
		}

		$config 	= FD::config();
		$sortDate   = $config->get( 'stream.pagination.sort', 'modified' );


		$commonCond = '';
		//$commonCond .= ' where a.`modified` >= ' . $db->Quote( $currentdate );

		// we use the > to unsure the same stream will not get counted.
		// this is to prevent when someone post a story at the same time as the current date being returned by the
		// checkupdate, which lead to duplicate stream.
		$commonCond .= ' WHERE a.`state` = 1';
		$commonCond .= ' and a.`actor_type` = ' . $db->Quote( SOCIAL_TYPE_USER );
		$commonCond .= ' and a.`' . $sortDate . '` > ' . $db->Quote( $currentdate );
		if( $exclude )
		{
			$commonCond .= ' and a.`id` NOT IN (' . $exclude . ')';
		}

		// we do not want stream item from clusters.
		// $commonCond .= ' and ( a.`cluster_id` = ' . $db->Quote( '0' ) . ' OR a.`cluster_access` = ' . $db->Quote( '1' ) . ' )';

		$commonCond .= ' and (';
		$commonCond .= ' (a.`cluster_id`= 0) OR';
		$commonCond .= ' (a.`cluster_id` > 0 and a.`cluster_access` = 1)';
		if ($userId) {
			$commonCond .= 'OR (a.`cluster_id` > 0 and a.`cluster_access` > 1 and ' . $userId . ' IN (select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = a.`cluster_id` and scn.`type` = ' . $db->Quote( SOCIAL_TYPE_USER ) . ' and scn.`state` = 1) )';
		}
		$commonCond .= ' )';


		// exclude these context items
		$excludeApps = $this->getUnAccessilbleUserApps( $userId );

		// var_dump( $excludeApps);
		// exit;

		if( $excludeApps )
		{
			$appOnly = array();
			$appWithVerb = array();

			foreach ($excludeApps as $app => $verbs ) {
				if ($verbs === true) {
					$appOnly[] = $app;
				} else {
					$appWithVerb[$app] = $verbs;
				}
			}

			if (!empty($appOnly)) {
				$tmpString = '';
				foreach( $appOnly as $eApp )
				{
					$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $eApp ) : $db->Quote( $eApp );
				}
				$commonCond	.= ' AND a.' . $db->nameQuote( 'context_type' ) . ' NOT IN (' . $tmpString . ')';
			}

			if(!empty($appWithVerb)) {
				foreach ($appWithVerb as $app => $verbs) {
					if (count($verbs) == 1) {
						$commonCond .= ' AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' != ' . $db->Quote($verbs[0]) .') OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					} else {
						$tmpString = '';
						foreach( $verbs as $verb )
						{
							$tmpString .= ( $tmpString ) ? ',' . $db->Quote( $verb ) : $db->Quote( $verb );
						}

						$commonCond .= 'AND ( (a.' . $db->nameQuote( 'context_type' ) . ' = ' . $db->Quote($app) . ' and a.'
									. $db->nameQuote( 'verb' ) . ' NOT IN (' . $tmpString .') ) OR ( a.' . $db->nameQuote('context_type') .' != ' . $db->Quote( $app ) . ') )';
					}
				}
			}
		}

		// do not get the context / actor that user choosed to hide.
		if( $userId )
		{
			if (! $isAdmin) {
				// privacy here.
				$commonCond	.= ' AND (';

				//public
				$commonCond	.= ' (a.`access` = ' . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ') OR';

				//member
				$commonCond	.= ' ( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ') AND (' . $userId . ' > 0 ) ) OR ';

				//friends of friends
				// $commonCond .= '( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateMutualFriendSQL( $userId, 'a.`actor_id`' ) . ') > 0 ) ) OR ';

				//friends
				// $commonCond	.= ' ( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIENDS_OF_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'a.`actor_id`', $userId ) . ') > 0 ) ) OR ';

				//friends
				$commonCond	.= ' ( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ') AND ( (' . $this->generateIsFriendSQL( 'a.`actor_id`', $userId ) . ') > 0 ) ) OR ';

				//only me
				$commonCond	.= ' ( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ') AND ( a.`actor_id` = ' . $userId . ' ) ) OR ';

				// custom
				$commonCond	.= ' ( (a.`access` = ' . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ') AND ( a.`custom_access` LIKE ' . $db->Quote( '%,' . $userId . ',%' ) . '    ) ) OR ';

				// my own items.
				$commonCond	.= ' (a.`actor_id` = ' . $userId . ')';

				// privacy checking end here.
				$commonCond .= ')';
			}


			//based on context
			$commonCond	.= ' AND NOT EXISTS (';
			$commonCond .= ' SELECT h1.' . $db->nameQuote( 'context' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h1';
			$commonCond .= ' WHERE a.' . $db->nameQuote( 'context_type' ) . ' = h1.' . $db->nameQuote( 'context' );
			$commonCond .= ' AND h1.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) ;
			$commonCond .= ' AND h1.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
			$commonCond .= ' AND h1.' . $db->nameQuote( 'actor_id' ) . ' = ' . $db->Quote( '0' );
			$commonCond .= ')';

			//based on actor
			$commonCond .= ' AND NOT EXISTS (';
			$commonCond .= ' SELECT h2.' . $db->nameQuote( 'actor_id' ) . ' FROM ' . $db->nameQuote( '#__social_stream_hide' ) . ' AS h2';
			$commonCond .= ' WHERE a.' . $db->nameQuote( 'actor_id' ) . '= h2.' . $db->nameQuote( 'actor_id' ) ;
			$commonCond .= ' AND h2.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) ;
			$commonCond .= ' AND h2.' . $db->nameQuote( 'uid' ) . ' = ' . $db->Quote( '0' );
			$commonCond .= ' AND h2.' . $db->nameQuote( 'context' ) . ' is null';
			$commonCond .= ')';

			if ($config->get('users.blocking.enabled')) {
				//based on user who blocked the viewer
				$commonCond	.= ' AND NOT EXISTS (';
				$commonCond	.= ' select bs.' . $db->nameQuote('user_id') . ' from ' . $db->nameQuote( '#__social_block_users' ) . ' as bs';
				$commonCond .= ' where a.' . $db->nameQuote('actor_id') . ' = bs.' . $db->nameQuote('user_id');
				$commonCond .= ' and bs.' . $db->nameQuote('target_id') . ' = ' . $db->Quote($userId);
				$commonCond .= ')';
			}

		} else {
			// public stream
			$commonCond .= ' AND a.`access` = ' . $db->Quote('0');

		}

		$query = '';

		// following
		$query .= 'select a.id, ' . $db->Quote( 'following' ) . ' as `type` from `#__social_stream` as a';
		$query .= ' inner join `#__social_subscriptions` as s';
		$query .= ' on a.`actor_id` = s.`uid`';
		$query .= '		AND s.`type` = ' . $db->Quote( SOCIAL_TYPE_USER . '.' . SOCIAL_SUBSCRIPTION_TYPE_USER );
		$query .= '		AND s.`user_id` = ' . $db->Quote( $userId );
		$query .= $commonCond;


		$query .= ' union ';

		// me
		$queryMe = '';
		$queryMe .= 'select a.id, ' . $db->Quote( 'me' ) . ' as `type` from `#__social_stream` as a';
		$view = JRequest::getVar( 'view', '');

		if( $source == 'dashboard' )
		{
			$queryMe	.= ' LEFT JOIN `#__social_friends` AS f1 ON a.`actor_id` = f1.`target_id` and f1.`actor_id` = ' . $db->Quote( $userId ) . ' and f1.`state` = 1';
			$queryMe	.= ' LEFT JOIN `#__social_friends` AS f2 ON a.`actor_id` = f2.`actor_id` and f2.`target_id` = ' . $db->Quote( $userId ) . ' and f2.`state` = 1';
		}
		$queryMe .= $commonCond;

		// start bracket
		$tmp	= 'AND (';

		// my items.
		$tmp	.= ' a.`actor_id` = ' . $db->Quote( $userId );
		$tmp 	.= ' OR ( a.`target_id` = ' . $db->Quote( $userId ) . ' and a.`context_type` = ' . $db->Quote( 'story' ) . ')' ;

		if( $source == 'dashboard' )
		{
			// my friends items.
			$tmp 	.= ' OR f1.`actor_id` = ' . $db->Quote( $userId );
			$tmp 	.= ' OR f2.`target_id` = ' . $db->Quote( $userId );
		}

		// my tagged items.
		$tmp .= ' OR exists ( select st.`stream_id` from `#__social_stream_tags` as st' ;
		$tmp .= '                 where st.`stream_id` = a.`id`';
		$tmp .= ' 					and st.`uid` = ' . $db->Quote( $userId );
		$tmp .= ' 					and st.`utype` = ' . $db->Quote( SOCIAL_STREAM_TAGGING_TYPE_USER ) . ')';

		// end bracket
		$tmp 	.= ')';
		$queryMe .= $tmp;

		$query .= $queryMe;

		if( $source == 'dashboard' )
		{
			$query .= ' union ';

			$queryE = '';
			$queryE = 'select xE.* from (';

			//public stream
			$queryE .= 'select a.id, ' . $db->Quote( 'everyone' ) . ' as `type` from `#__social_stream` as a';
			$queryE .= $commonCond;

			$queryE .= ') as xE';

			$query .= $queryE;
		}


		$query .= ' union ';

		//list
		$query .= '';
		$query .= 'select a.id, CONCAT(' . $db->Quote( 'list-' ) . ', c.`id` ) as `type`';
		$query .= ' from `#__social_stream` as a';
		$query .= ' 	inner join `#__social_lists_maps` as b on a.`actor_id` = b.`target_id` and b.`target_type` = ' . $db->Quote( SOCIAL_TYPE_USER );
		$query .= ' 	inner join `#__social_lists` as c on b.`list_id` = c.`id`';
		$query .= $commonCond;
		$query .= '	and c.`user_id` = ' . $db->Quote( $userId );

		$sql->raw( $query );


		$db->setQuery( $sql );


		$results = $db->loadObjectList();

		// echo '<pre>';print_r( $result );echo '</pre>';

		$data = array();
		if( $results )
		{
			foreach( $results as $item )
			{
				$data[ $item->type ] = ( isset( $data[ $item->type ] ) ) ? $data[ $item->type ] + 1 : 1;
			}
		}

		$counts = array();
		if( $data )
		{
			foreach( $data as $key => $val )
			{
				$counts[] = array( 'type' => $key, 'cnt' => $val );
			}
		}

		return $counts;
	}


	/**
	 * Retrieves assets based on the stream id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The id of the stream item
	 * @param	string	The context type
	 * @return
	 */
	public function getAssets( $id , $type )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_stream_assets' );
		$sql->where( 'stream_id' , $id );
		$sql->where( 'type' , $type );

		$db->setQuery( $sql );

		$items 	= $db->loadObjectList();

		return $items;
	}

	/**
	 * Retrieves a list of unique stream filters
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAppFilters($group = SOCIAL_TYPE_USER)
	{
		$db = ES::db();
		$sql = $db->sql();

		$sql->select('#__social_stream', 'a');
		$sql->column('DISTINCT(a.context_type)');

		$db->setQuery($sql);

		$rows = $db->loadColumn();

		if (!$rows) {
			return;
		}

		$filters = array();

		foreach ($rows as $row) {

			$app = ES::table('App');
			$options = array('type' => SOCIAL_APPS_TYPE_APPS, 'element' => $row, 'group' => $group);
			$exists = $app->load($options);

			if ($app->group != $group) {
				continue;
			}

			if (!$app->state) {
				continue;
			}


			// Get the app object
			$obj = $app->getAppClass();

			if (!$obj) {
				continue;
			}

			if (!$obj->hasStreamFilter()) {
				continue;
			}

			$filter = new stdClass();

			$filter->id = $app->id;

			// We should use pre-defined or user-defined app title instead of a default language string since we allow user to change the title in the backend
			// JText it as well for users who want to use language strings for multilang sites
			$filter->title = JText::_( 'COM_EASYSOCIAL_STREAM_APP_FILTER_' . strtoupper( $row ) );
			$filter->image = '';
			$filter->icon = '';
			$filter->favicon = '';
			$filter->alias = strtolower($row);

			// Since 1.4, we do not want to display the icons any longer.
			$filter->icon = 'icon-es-app';

			// if ($exists) {
			// 	// Try to get a favicon
			// 	$filter->favicon = $app->getFavIcon();

			// 	if (!$filter->favicon) {
			// 		$filter->image = $app->getIcon();
			// 	}
			// }
			// else
			// {

			// }

			$filters[] = $filter;
		}

		return $filters;
	}

	/**
	 * Retrieves a list of stream filters from the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @param 	int
	 * @param 	string
	 * @return	Array 	An array of SocialTableStreamFilter object.
	 */
	public function getFilters( $uId, $uType = 'user' )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_stream_filter' );
		$sql->where( 'uid' , $uId );
		$sql->where( 'utype' , $uType );

		$db->setQuery( $sql );
		$items 	= $db->loadObjectList();

		if( !$items )
		{
			return $items;
		}

		$filters 	= array();

		foreach( $items as $item )
		{
			$filter	= FD::table( 'StreamFilter' );
			$filter->bind( $item );

			$filters[]	= $filter;
		}

		return $filters;
	}

	/**
	 * Update stream.modified date.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @param 	int
	 * @return	boolean
	 *
	 * the context can be 'stream' and when the context is stream, the uid is the stream.id
	 * the context can be 'activity' and when the context is activity, the uid is the stream_item.id
	 * we need to work accordingly based on the context passed in.
	 */

	public function updateModified( $streamId, $user_id = '', $user_action = '' )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$now   = FD::date()->toMySQL();

		$updateQuery = '';

		$updateQuery = 'update `#__social_stream` set `modified` = ' . $db->Quote( $now );

		if ($user_id && $user_action) {
			$updateQuery .= ', last_userid = ' . $db->Quote($user_id);
			$updateQuery .= ', last_action = ' . $db->Quote($user_action);
		}

		$updateQuery .= ' where `id` = ' . $db->Quote( $streamId );

		$sql->raw( $updateQuery );
		$db->setQuery( $sql );

		$db->query();

		return true;
	}

	public function revertLastAction($streamId, $user_id = '', $user_action = '')
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		if ($user_id && $user_action) {
			// let check first if the last action of this stream is belong to the same person and same action or not.
			$query = 'select last_userid, last_action from `#__social_stream` where id = ' . $db->Quote($streamId);
			$sql->raw($query);

			$db->setQuery($sql);
			$item = $db->loadObject();

			if ($item->last_userid == $user_id && $item->last_action == $user_action) {
				// okay, we are reverting the same user action. now we need to get the 2nd last action and user id.
				$prev_last_action = '';
				$prev_last_userid = '';

				$query = "select * from (";
				$query .= "	(select 'like' as `action`, `created_by`, `created` from `#__social_likes` where `stream_id` = " . $db->Quote('172321') . " order by `id` desc limit 1)";
				$query .= "	union all";
				$query .= "	(select 'comment' as `action`, `created_by`, `created` from `#__social_comments` where `stream_id` = " . $db->Quote('172321') . " order by `id` desc limit 1)";
				$query .= ") as x order by x.`created` desc limit 1";

				$sql->clear();
				$sql->raw($query);

				$db->setQuery($sql);
				$result = $db->loadObject();

				if ($result) {
					$prev_last_action = $result->action;
					$prev_last_userid = $result->created_by;
				}

				// now we have the data and we are ready to update the stream last action.
				$updateQuery = 'update `#__social_stream` set';
				$updateQuery .= ' last_userid = ' . $db->Quote($prev_last_userid);
				$updateQuery .= ', last_action = ' . $db->Quote($prev_last_action);
				$updateQuery .= ' where `id` = ' . $db->Quote( $streamId );

				$sql->clear();
				$sql->raw($updateQuery);
				$db->setQuery($sql);

				$db->query();
			}
		}

		return true;
	}

	/**
	 * This determines if the provided context_id is aggregated with other items
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The context_id
	 * @return	bool	True if it is aggregated with other items
	 */
	public function isAggregated($contextId, $contextType)
	{
		static $_cache = array();

		$key = $contextId . $contextType;

		if (! isset($_cache[$key])) {
			$db 	= FD::db();
			$sql 	= $db->sql();

			$sql->select('#__social_stream_item');
			$sql->column('uid');
			$sql->where('context_id', $contextId);
			$sql->where('context_type', $contextType);

			$db->setQuery($sql);

			$uid 	= $db->loadResult();

			if (!$uid){
				$_cache[$key] = false;
				return false;
			}

			$sql->clear();
			$sql->select('#__social_stream_item');
			$sql->column('COUNT(1)');
			$sql->where('uid', $uid);
			$db->setQuery($sql);

			$total 	= $db->loadResult();

			// If there's more than 1 item, we know it is being aggregated
			if ($total > 1) {
				$_cache[$key] = true;
			} else {
				$_cache[$key] = false;
			}
		}

		return $_cache[$key];
	}

	/**
	 * Retrieves the stream id given the appropriate item contexts
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int 	The context id
	 * @param	string	The context type
	 * @return	int 	The stream id
	 */
	public function getStreamId($contextId, $contextType)
	{
		static $_cache = array();

		$key = $contextId . $contextType;

		if (! isset($_cache[$key])) {

			$db 	= FD::db();
			$sql 	= $db->sql();

			$sql->select('#__social_stream_item');
			$sql->column('uid');
			$sql->where('context_id', $contextId);
			$sql->where('context_type', $contextType);

			$uid 	= $db->loadResult();

			$_cache[$key] = $uid;
		}

		return $_cache[$key];
	}

	public function getContextItem( $streamId )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'select a.`context_type`, a.`context_id`';
		$query .= ' from `#__social_stream_item` as a';
		$query .= ' where a.`uid` = ' . $db->Quote( $streamId );
		$query .= ' order by a.`id` limit 1';

		$sql->raw( $query );
		$db->setQuery( $sql );

		$result = $db->loadObject();

		return $result;
	}

	/**
	 * update stream privacy access
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string		privacy rule. e.g. core.view
	 *          int			privacy value in integer
	 *          string 		userids
	 */
	public function updateAccess( $streamId, $privacy, $custom = null)
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$query = 'update `#__social_stream` set `access` = ' . $db->Quote( $privacy );
		$query .= ', `custom_access` = ' . $db->Quote( $custom );
		$query .= ' where `id` = ' . $db->Quote( $streamId );

		$sql->raw($query);
		$db->setQuery($sql);

		$state = $db->query();

		return $state;
	}

	/**
	 * delete stream and stream items based on stream id
	 *
	 * @since	1.3
	 * @access	public
	 * @param	int - stream id
	 * @return  boolean
	 */
	public function deleteStreamItem($streamId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = array();
		$query[] = 'DELETE a, b FROM ' . $db->qn('#__social_stream') . ' AS a';
		$query[] = 'INNER JOIN ' . $db->qn('#__social_stream_item') . ' AS b';
		$query[] = 'ON a.' . $db->qn('id') . '=b.' . $db->qn('uid');
		$query[] = 'WHERE a.' . $db->qn('id') . '=' . $db->Quote($streamId);

		$query = implode(' ', $query);
		$sql->raw($query);

		$db->setQuery($sql);

		$state = $db->query();
		return $state;
	}

	/**
	 * trash stream and stream items based on stream id
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array - stream ids
	 * @return  boolean
	 */
	public function trashStreamItem($streamIds)
	{
		$db = FD::db();
		$sql = $db->sql();

		if (empty($streamIds)) {
			return false;
		}

		$ids = implode(',', $streamIds);

		$query = "update `#__social_stream` as a inner join `#__social_stream_item` as b on a.`id` = b.`uid`";
		$query .= " set a.`state` = " . SOCIAL_STREAM_STATE_TRASHED . ",";
		$query .= "		b.`state` = " . SOCIAL_STREAM_STATE_TRASHED;
		$query .= " where a.`id` IN ($ids)";

		$sql->raw($query);
		$db->setQuery($sql);
		$state = $db->query();

		return $state;
	}


	public function restoreStreamItem($streamIds, $type = 'trash')
	{
		$db = FD::db();
		$sql = $db->sql();

		if (empty($streamIds)) {
			return false;
		}

		$ids = implode(',', $streamIds);

		if ($type == 'trash') {
			$query = "update `#__social_stream` as a inner join `#__social_stream_item` as b on a.`id` = b.`uid`";
			$query .= " set a.`state` = " . SOCIAL_STREAM_STATE_PUBLISHED . ",";
			$query .= "		b.`state` = " . SOCIAL_STREAM_STATE_PUBLISHED;
			$query .= " where a.`id` IN ($ids)";

			$sql->raw($query);
			$db->setQuery($sql);

			$state = $db->query();
		} else {
			// need to 'migrate' from history table

			$state = true;
		}

		return $state;
	}


	/**
	 * archive stream items into history table.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array - stream ids
	 * @return  boolean
	 */
	public function archive($streamIds)
	{
		$db = FD::db();
		$sql = $db->sql();

		if (empty($streamIds)) {
			return false;
		}

		$ids = implode(',', $streamIds);

		// lets archive stream_item first.
		$query = "insert into `#__social_stream_item_history` select a.* from `#__social_stream_item` as a where a.`uid` IN ($ids) ON DUPLICATE KEY UPDATE id = a.`id`";

		$sql->raw($query);
		$db->setQuery($sql);
		$state = $db->query();

		if ($state) {
			// now we archive stream table.

			$query = "insert into `#__social_stream_history` select a.* from `#__social_stream` as a where a.`id` IN ($ids) ON DUPLICATE KEY UPDATE id = a.`id`";

			$sql->clear();
			$sql->raw($query);
			$db->setQuery($sql);
			$state = $db->query();

			if ($state) {
				// okay everything is now archived. lets remove the data.

				$query = "delete a, b from `#__social_stream` as a";
				$query .= " inner join `#__social_stream_item` as b on a.`id` = b.`uid`";
				$query .= "where a.`id` IN ($ids)";

				$sql->clear();
				$sql->raw($query);
				$db->setQuery($sql);

				$state = $db->query();
			}

		}

		return $state;
	}


	/**
	 * restore archived items into stream table.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array - stream ids
	 * @return  boolean
	 */
	public function restoreArchivedItem($streamIds)
	{
		$db = FD::db();
		$sql = $db->sql();

		if (empty($streamIds)) {
			return false;
		}

		$ids = implode(',', $streamIds);

		// lets archive stream_item first.
		$query = "insert into `#__social_stream_item` select a.* from `#__social_stream_item_history` as a where a.`uid` IN ($ids) ON DUPLICATE KEY UPDATE id = a.`id`";

		$sql->raw($query);
		$db->setQuery($sql);
		$state = $db->query();

		if ($state) {
			// now we archive stream table.

			$query = "insert into `#__social_stream` select a.* from `#__social_stream_history` as a where a.`id` IN ($ids) ON DUPLICATE KEY UPDATE id = a.`id`";

			$sql->clear();
			$sql->raw($query);
			$db->setQuery($sql);
			$state = $db->query();

			if ($state) {

				//now we update the stream.status to 'restored from archive'
				$query = "update `#__social_stream` as a set a.`state` = " . SOCIAL_STREAM_STATE_RESTORED;
				$query .= " where a.`id` IN ($ids)";

				$sql->clear();
				$sql->raw($query);
				$db->setQuery($sql);
				$state = $db->query();

				if ($state) {
					// okay everything is now restored. lets remove the data from history table.
					$query = "delete a, b from `#__social_stream_history` as a";
					$query .= " inner join `#__social_stream_item_history` as b on a.`id` = b.`uid`";
					$query .= "where a.`id` IN ($ids)";

					$sql->clear();
					$sql->raw($query);
					$db->setQuery($sql);
					$state = $db->query();
				}

			}

		}

		return $state;
	}

	/**
	 * restore archived items into stream table.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array - stream ids
	 * @return  boolean
	 */
	public function getItemsToArchive( $months )
	{
		$config = FD::config();
		$db = FD::db();
		$sql = $db->sql();

		$column = $config->get('stream.pagination.sort', 'modified');

		$now    = FD::date();

		// clean the registration temp data for records that exceeded 1 hour.
		$query = "select `id` from `#__social_stream`";
		$query .= " where `state` = 1";
		$query .= " and date_add( `$column` , INTERVAL $months MONTH) <= " . $db->Quote( $now->toMySQL() );
		$query .= " order by `modified` asc limit 5";

		$sql->raw($query);

		$db->setQuery($sql);

		$results = $db->loadColumn();

		return $results;
	}


	public function getStreamItemsCount($streamId)
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "select count(1) from `#__social_stream_item` where `uid` = '$streamId'";
		$sql->raw($query);

		$db->setQuery($sql);
		$result = $db->loadResult();

		return $result;
	}


}
