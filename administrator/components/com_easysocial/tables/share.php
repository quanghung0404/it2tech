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

FD::import( 'admin:/tables/table' );
FD::import( 'admin:/includes/stream/dependencies' );

/**
 * Object mapping for share table.
 *
 * @author	Sam <sam@stackideas.com>
 * @since	1.0
 */
class SocialTableShare extends SocialTable
	implements ISocialStreamItemTable
{
	public $id          = null;
	public $uid     	= null;
	public $element 	= null;
	public $user_id 	= null;
	public $content		= null;
	public $created 	= null;

	static $_shares     = array();

	public function __construct(& $db )
	{
		parent::__construct( '#__social_shares' , 'id' , $db );
	}

	public function setSharesBatch( $data )
	{
		$model 	= FD::model( 'Stream' );
		$uids 	= array();

		foreach( $data as $item )
		{
			// Get related items
			$related 	= $model->getBatchRalatedItem( $item->id );
			if( !$related )
			{
				continue;
			}

			// Get the item's element.
			$element 	= $item->context_type;

			// Get the stream item
			$streamItem = $related[ 0 ];

			$key 		= $streamItem->context_id;

			// If it hasn't been set yet, we need to initialize the array
			if( !isset( self::$_shares[ $key ] ) )
			{
				// We skip this if context_id isn't set.
				if( !$streamItem->context_id )
				{
					continue;
				}

				$uids[] 	= $streamItem->context_id;

				self::$_shares[ $key ] = array();
			}
		}


		if( $uids )
		{
			$db 	= FD::db();
			$sql 	= $db->sql();

			$ids	= implode( ',' , $uids);

			$query = 'select * from `#__social_shares` where id IN (' . $ids . ')';
			$sql->raw( $query );

			$db->setQuery( $sql );
			$result = $db->loadObjectList();

			if( $result )
			{
				foreach( $result as $row )
				{
					$new = FD::table( 'Share' );
					$new->bind( $row );

					self::$_shares[ $row->id ] = $new;
				}
			}

		}

	}

	public function load( $id = null , $reset = true)
	{
		if( is_array( $id ) )
		{
			return parent::load( $id, $reset );
		}

		if(! isset( self::$_shares[ $id ] ) )
		{
			parent::load( $id );
			self::$_shares[ $id ] = $this;
		}
		else
		{
			$this->bind( self::$_shares[ $id ] );
		}

		return true;
	}

	public function store( $updateNulls = false )
	{
		$isNew = true;

		if( $this->id )
		{
			$isNew = false;
		}

		if( empty( $this->created ))
		{
			$this->created = FD::date()->toMySQL();
		}

		$state = parent::store( $updateNulls );

		if( $state )
		{
			// TODO: do any triggering here.
		}

		return $state;
	}

	public function delete( $pk = null )
	{
		$state = parent::delete( $pk );

		if( $state )
		{
			// TODO: do any triggering here.
		}

		return $state;
	}

	public function toJSON()
	{
		return array('id' 			=> $this->id ,
					 'uid' 			=> $this->uid ,
					 'element' 		=> $this->element,
					 'user_id'		=> $this->user_id,
					 'content' 		=> $this->content,
					 'created' 		=> $this->created
		 );
	}

	public function addStream( $verb )
	{
		// moved to controller.
	}

	public function removeStream()
	{
		$stream	= FD::stream();
		$stream->delete( $this->id, SOCIAL_TYPE_SHARE);
	}

}
