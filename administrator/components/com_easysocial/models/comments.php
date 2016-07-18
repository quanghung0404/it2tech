<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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

class EasySocialModelComments extends EasySocialModel
{
	public $table 	= '#__social_comments';
	static $_counts = array();
	static $_data = array();

	function __construct()
	{
		parent::__construct( 'comments' );
	}

	public function getComments( $options = array() )
	{
		// Available options
		// element
		// uid
		// start
		// limit
		// order
		// direction

		// Define the default parameters
		$defaults = array(
			'start'		=> 0,
			'limit'		=> 5,
			'order'		=> 'created',
			'direction'	=> 'asc'
		);

		$options	= array_merge($defaults, $options);

		$db		= FD::db();

		$sql	= $db->sql();

		$useCache = true;

		// SELECT
		$sql->column( 'a.*');
		$sql->select( $this->table, 'a' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// WHERE
		if( isset( $options['stream_id'] ) )
		{
			$sql->where( 'a.stream_id', $options['stream_id'] );
		}

		if( isset( $options['element'] ) )
		{
			$sql->where( 'a.element', $options['element'] );
		}

		if( isset( $options['uid'] ) )
		{
			$sql->where( 'a.uid', $options['uid'] );
		}

		if( isset( $options['commentid'] ) )
		{
			$useCache = false;
			$sql->where( 'a.id', $options['commentid' ], '>=' );
		}

		if( isset( $options['parentid'] ) )
		{
			if ($options['parentid']) {
				$useCache = false;
			}

			$sql->where( 'a.parent', $options['parentid'] );
		}

		if ($options[ 'order' ] != 'created') {
			$useCache = false;
		}

		// ORDER
		$sql->order( $options[ 'order' ] , $options[ 'direction' ] );

		// LIMIT
		if(!empty($options['limit'])) {
			$sql->limit( $options[ 'start' ] , $options[ 'limit' ] );
		}

		// echo $sql;exit;

		$comments = false;
		$loadSQL  = true;

		$key = '';

		if ($useCache) {

			// $key = $options['uid'] . '.' . $options['element'] . '.' . $options['limit'];

			if (isset($options['stream_id'])) {
				// lets try to get the count from the static variable.
				$key = $options['stream_id'] . '.' . 'stream';
			} else {
				$key = $options['uid'] . '.' . $options['element'];
			}

			if (isset(self::$_data[$key])) {
				$loaded = self::$_data[$key];

				if( $loaded )
				{
					if ($options['direction'] == 'asc') {
						asort($loaded);
					} else {
						arsort( $loaded );
					}

					if (!empty($options['limit'])) {
						$loaded = array_slice($loaded, $options['start'], $options['limit']);
					}

					$comments = $loaded;
					$loadSQL = false;
				}
			}
		}


		if ($loadSQL){

			// var_dump('getComments::' . $key);
			// exit;
			//

			$db->setQuery( $sql );

			$comments	= $db->loadObjectList();
		}

		if ($comments === false) {
			return false;
		}

		$tables		= array();
		foreach( $comments as $comment )
		{
			$table = FD::table( 'comments' );
			$table->bind( $comment );
			$tables[] = $table;
		}

		return $tables;
	}

	/**
	 * Retrieves the comment statistics for a particular poster
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of dates to search for
	 * @param	int		The user id to look up for
	 * @return
	 */
	public function getCommentStats( $dates , $userId )
	{
		$db 		= FD::db();
		$comments	= array();

		foreach( $dates as $date )
		{
			// Registration date should be Y, n, j
			$date	= FD::date( $date )->format( 'Y-m-d' );

			$query 		= array();
			$query[] 	= 'SELECT `a`.`id`, COUNT( `a`.`id`) AS `cnt` FROM `#__social_comments` AS a';
			$query[]	= 'WHERE `a`.`created_by`=' . $db->Quote( $userId );
			$query[]	= 'AND DATE_FORMAT( `a`.`created`, GET_FORMAT( DATE , "ISO") ) = ' . $db->Quote( $date );
			$query[]    = 'group by a.`created_by`';

			$query 		= implode( ' ' , $query );
			$sql		= $db->sql();
			$sql->raw( $query );

			$db->setQuery( $sql );

			$items				= $db->loadObjectList();

			// There is nothing on this date.
			if( !$items )
			{
				$comments[]	= 0;
				continue;
			}

			foreach( $items as $item )
			{
				$comments[]	= $item->cnt;
			}
		}

		// Reset the index.
		$comments 	= array_values( $comments );

		return $comments;
	}

	public function getCommentCount( $options = array() )
	{
		$key = '';

		if (isset($options['stream_id']) && isset($options['element'])) {

			// lets try to get the count from the static variable.
			$key = $options['stream_id'] . '.' . 'stream';

			if (isset(self::$_counts[$key])) {
				return self::$_counts[ $key ];
			}
		}

		// We only use static variable if passed in options is element and uid
		// It is possible that other options are passed in and we will need to count separately
		if (isset($options['element']) && isset($options['uid'])) {

			// lets try to get the count from the static variable.
			$key = $options['uid'] . '.' . $options['element'];

			if (isset(self::$_counts[$key])) {
				return self::$_counts[ $key ];
			}
		}


		// var_dump('getCommentCount::' . $key);
		// exit;
		// var_dump( self::$_counts);
		// exit;


		$db		= FD::db();
		$sql	= $db->sql();

		// SELECT
		$sql->column('a.*');
		$sql->select( $this->table, 'a' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.user_id' );
		    $sql->on( 'bus.target_id', JFactory::getUser()->id );
		    $sql->isnull('bus.id');
		}

		// WHERE
		if( isset( $options['stream_id'] ) )
		{
			$sql->where( 'a.stream_id', $options['stream_id'] );
		}

		if( isset( $options['element'] ) )
		{
			$sql->where( 'a.element', $options['element'] );
		}

		if( isset( $options['uid'] ) )
		{
			$sql->where( 'a.uid', $options['uid'] );
		}

		if( isset( $options['parentid'] ) )
		{
			$sql->where( 'a.parent', $options['parentid'] );
		}

		$db->setQuery( $sql->getTotalSql() );

		$count = $db->loadResult();

		//lets save into static variable for later reference.
		if( $key )
		{
			self::$_counts[ $key ] = $count;
		}

		return $count;
	}

	public function deleteCommentBlock( $uid, $element )
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->delete( $this->table )
			->where( 'element', $element )
			->where( 'uid', $uid );

		$db->setQuery( $sql );
		return $db->query();
	}

	public function getParticipants( $uid, $element )
	{
		$db		= FD::db();

		$sql	= $db->sql();

		$sql->select( $this->table, 'a' );
		$sql->column( 'DISTINCT(a.`created_by`)' );

		if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
		    $sql->leftjoin( '#__social_block_users' , 'bus');
		    $sql->on( 'a.created_by' , 'bus.target_id' );
		    $sql->on( 'bus.user_id', JFactory::getUser()->id );

		    $sql->isnull('bus.id');
		}

		$sql->where( 'a.uid', $uid );
		$sql->where( 'a.element', $element );

		$db->setQuery( $sql );

		$result = $db->loadColumn();

		return $result;
	}

	public function getLastSibling( $parent )
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select( $this->table )
			->where( 'parent', $parent )
			->order( 'lft', 'desc' )
			->limit( 1 );

		$db->setQuery( $sql );

		$result = $db->loadObject();

		return $result;
	}

	public function updateBoundary( $node )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = "UPDATE `{$this->table}` SET `lft` = `lft` + 2 WHERE `lft` > {$node}";

		$sql->raw( $query );

		$db->setQuery( $sql );

		$db->query();

		$query = "UPDATE `{$this->table}` SET `rgt` = `rgt` + 2 WHERE `rgt` > {$node}";

		$sql->raw( $query );

		$db->setQuery( $sql );

		$db->query();

		return true;
	}

	public function setStreamCommentBatch( $data )
	{
		$config = FD::config();
		$db		= FD::db();
		$sql	= $db->sql();

		// Retrieve the stream model
		$model 		= FD::model( 'Stream' );
		$dataset	= array();

		// Go through each of the items
		foreach ($data as $item) {
			// Get related items
			$uid = $item->id;

			// If there's no context_id, skip this.
			if( !$uid )
			{
				continue;
			}

			// need to pre-fill the data 1st.
			$group	= ( $item->cluster_id ) ? $item->cluster_type : SOCIAL_APPS_GROUP_USER;

			// $key 	= $uid . '.' . $item->context_type . '.' . $group . '.' . $item->verb;
			$key = $uid . '.stream';
			self::$_data[ $key ] = array();

			$dataset[] = $uid;
		}

		// lets build the sql now.
		if( $dataset )
		{
			$query = "select x.* from `#__social_comments` as x";

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block
			    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
			    $query .= ' ON x.' . $db->nameQuote( 'created_by' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
			    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
			}

			$query .= ' where x.stream_id IN (' . implode(',', $dataset) . ')';

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
    			$query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
			}

			// echo $query;
			// exit;

			$sql->raw( $query );
			$db->setQuery( $sql );

			$result = $db->loadObjectList();

			if( $result )
			{
				$cids = array();

				foreach( $result as $rItem )
				{
					$cids[] = $rItem->id;
					//$key = $rItem->uid . '.' . $rItem->element;
					//
					$key = $rItem->stream_id . '.stream';

					self::$_data[ $key ][$rItem->created] = $rItem;
				}

				// based on the comments id, we need to pre fetch the likes for commetns
				$like = FD::model( 'Likes' );
				$like->setCommentLikesBatch( $result );


				// lets do the same for comment tagging.
				$tags = FD::model( 'Tags' );
				$tags->setTagBatch( $cids, 'comments' );
			}
		}
	}

	public function setStreamCommentCountBatch( $data )
	{
		$config = FD::config();
		$db		= FD::db();
		$sql	= $db->sql();

		// Retrieve the stream model
		$model 	= FD::model( 'Stream' );

		$dataset = array();

		// var_dump($data);exit;

		// Go through each of the items
		foreach( $data as $item )
		{
			// Get related items
			$uid = $item->id;

			// If there's no context_id, skip this.
			if( !$uid )
			{
				continue;
			}

			// need to pre-fill the data 1st.
			$group	= ($item->cluster_id ) ? $item->cluster_type : SOCIAL_APPS_GROUP_USER;

			// $key	= $uid . '.' . $item->context_type . '.' . $group . '.' . $item->verb;
			$key	= $uid . '.stream';

			self::$_counts[ $key ] = 0;

			$dataset[] = $uid;
		}

		// lets build the sql now.
		if( $dataset )
		{
			$query = "select count(1) as `cnt`, x.`uid`, x.`element`, x.`stream_id` from `#__social_comments` as x";

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
			    // user block
			    $query .= ' LEFT JOIN ' . $db->nameQuote( '#__social_block_users' ) . ' as bus';
			    $query .= ' ON x.' . $db->nameQuote( 'created_by' ) . ' = bus.' . $db->nameQuote( 'user_id' ) ;
			    $query .= ' AND bus.' . $db->nameQuote( 'target_id' ) . ' = ' . $db->Quote( JFactory::getUser()->id ) ;
			}


			$query .= ' where stream_id IN (' . implode(',', $dataset) . ')';

			if ($config->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
    			$query .= ' AND bus.' . $db->nameQuote( 'id' ) . ' IS NULL';
			}

			$query .= " group by x.`element`, x.`uid`, x.`stream_id`";

			// echo $query;
			// exit;

			$sql->raw( $query );
			$db->setQuery( $sql );

			$result = $db->loadObjectList();

			if ($result) {
				foreach ($result as $rItem) {
					// $key = $rItem->uid . '.' . $rItem->element;
					$key = $rItem->stream_id . '.stream';

					self::$_counts[$key] = $rItem->cnt;
				}
			}

		}
	}
}
