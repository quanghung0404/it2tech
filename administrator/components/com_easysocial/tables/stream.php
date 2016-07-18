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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/tables/table');

class SocialTableStream extends SocialTable
{
	public $id				= null;
	public $actor_id		= null;
	public $actor_type		= null;
	public $alias			= null;
	public $created			= null;
	public $modified		= null;
	public $edited 			= null;
	public $title         	= null;
	public $content         = null;
	public $sitewide		= null;
	public $target_id 		= null;
	public $context_type 	= null;
	public $verb 			= null;
	public $stream_type 	= null;
	public $with 			= null;
	public $location_id 	= null;


	/**
	 * Determines if this stream is associated with a mood
	 * @var int
	 */
	public $mood_id			= null;

	public $ispublic 		= null;
	public $params 			= null;
	public $cluster_id 		= null;
	public $cluster_type 	= null;
	public $cluster_access 	= null;

	public $state = null;
	public $privacy_id = null;
	public $access = null;
	public $custom_access = null;

	public $last_action = null;
	public $last_userid = null;

	static $_streams 		= array();

	public function __construct( $db )
	{
		parent::__construct('#__social_stream', 'id', $db);
	}


	/**
	 * Overrides parent's load implementation
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function load( $keys = null, $reset = true )
	{
		if( is_array( $keys ) )
		{
			return parent::load( $keys, $reset );
		}

		if(! isset( self::$_streams[ $keys ] ) )
		{
			$state = parent::load( $keys );
			self::$_streams[ $keys ] = $this;
			return $state;
		}

		return parent::bind( self::$_streams[ $keys ] );
	}

	public function loadByBatch( $ids )
	{
		$db = FD::db();
		$sql = $db->sql();

		$streamIds = array();

		foreach( $ids as $pid )
		{
			if(! isset( self::$_streams[$pid] ) )
			{
				$streamIds[] = $pid;
			}
		}

		if( $streamIds )
		{
			foreach( $streamIds as $pid )
			{
				self::$_streams[$pid] = false;
			}

			$query = '';
			$idSegments = array_chunk( $streamIds, 5 );
			//$idSegments = array_chunk( $streamIds, count($streamIds) );

			for( $i = 0; $i < count( $idSegments ); $i++ )
			{
				$segment    = $idSegments[$i];

				$ids = implode( ',', $segment );
				$query .= 'select * from `#__social_stream` where `id` IN ( ' . $ids . ')';

				if( ($i + 1)  < count( $idSegments ) )
				{
					$query .= ' UNION ';
				}

			}


			$sql->raw( $query );
			$db->setQuery( $sql );

			$results = $db->loadObjectList();

			if( $results )
			{
				foreach( $results as $row )
				{
					self::$_streams[$row->id] = $row;
				}
			}
		}

	}

	/**
	 * Override the parent's store behavior
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		if (is_null($this->modified)) {
			$date = FD::date();
			$this->modified = $date->toSql();
		}

		return parent::store();
	}

	/**
	 * Retrieves a list of #__social_stream_items for the stream object
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_stream_item');
		$sql->where('uid', $this->id);

		$db->setQuery($sql);

		$items 	= $db->loadObjectList();

		return $items;
	}

	public function toJSON()
	{
		return array('id' => $this->id ,
					 'actor_id' => $this->actor_id ,
					 'actor_type' => $this->actor_type,
					 'alias' => $this->alias,
					 'created' => $this->created,
					 'modified' => $this->modified,
					 'title' => $this->title,
					 'content' => $this->content,
					 'sitewide' => $this->sitewide,
					 'target_id' => $this->target_id,
					 'location_id' => $htis->location_id,
					 'ispublic'	=> $this->ispublic,
					 'params'	=> $this->params,
					 'cluster_id' => $this->cluster_id,
					 'cluster_type' => $this->cluster_type,
					 'cluster_access' => $this->cluster_access,
					 'verb' => $this->verb,
					 'mood_id' => $this->mood_id,
					 'privacy_id' => $this->privacy_id,
					 'access' => $this->access,
					 'custom_access' => $this->custom_access
		 );
	}

	/**
	 * Get the uid association to this stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUID()
	{
		$db 	= FD::db();
		$sql 	= $db->sql();
		$sql->select( '#__social_stream_item' , 'a' );
		$sql->column( 'a.id' );
		$sql->where( 'a.uid' , $this->id );

		$db->setQuery( $sql );

		$id 	= $db->loadResult();

		return $id;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadByUID( $uid )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();
		$sql->select( '#__social_stream' , 'a' );
		$sql->column( 'a.*' );
		$sql->join( '#__social_stream_item' , 'b' );
		$sql->on( 'b.uid' , 'a.id' );
		$sql->where( 'b.id' , $uid );

		$db->setQuery( $sql );

		$obj	= $db->loadObject();

		return parent::bind( $obj );
	}

	/**
	 * Returns the stream's permalink
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determines if the output should be xhtml encoded.
	 * @return	string	The url
	 */
	public function getPermalink( $xhtml = true, $external = false, $sef = true )
	{
		return FRoute::stream( array( 'id' => $this->id , 'layout' => 'item', 'external' => $external, 'sef' => $sef ) , $xhtml );
	}

	/**
	 * Checks if the provided user is allowed to hide this item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hideable( $id = null )
	{
		return true;

		// $isOwner 	= $this->isOwner( $id );
		// $isAdmin	= $this->isAdmin( $id );

		// if( $isOwner || $isAdmin )
		// {
		// 	return true;
		// }

		// return false;
	}


	/**
	 * Checks if the provided user is the owner of this item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAdmin( $id = null )
	{
		$user 	= FD::user( $id );

		if( $user->isSiteAdmin() )
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if the provided user is the owner of this item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isOwner( $id = null )
	{
		$user 	= FD::user( $id );

		if( $this->actor_id == $user->id )
		{
			return true;
		}

		return false;
	}

	/**
	 * delete this stream and its associated stream_items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */

	public function delete( $pk = null )
	{
		$db = FD::db();
		$sql = $db->sql();

		$query = 'delete from `#__social_stream_item` where `uid` = ' . $db->Quote( $this->id );
		$sql->raw( $query );

		$db->setQuery( $sql );
		$db->query();

		return parent::delete();
	}

	/**
	 * Publishes a stream on the site.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$this->state = SOCIAL_STREAM_STATE_PUBLISHED;

		return $this->store();
	}

	/**
	 * Get a list of tags for the stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTags($types = array())
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select('#__social_stream_tags');
		$sql->where('stream_id', $this->id);

		$sql->where('utype', $types, 'IN');
		$db->setQuery($sql);

		$tags 	= $db->loadObjectList();

		return $tags;
	}

	/**
	 * Get assets related to this stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssets($type)
	{
        // Get the link object
        $model      = FD::model( 'Stream' );
        $assets		= $model->getAssets($this->id, $type);

        return $assets;
	}
}
