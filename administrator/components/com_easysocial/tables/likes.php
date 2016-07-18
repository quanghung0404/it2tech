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

// Include main table
FD::import( 'admin:/tables/table' );

/**
 * Database relation map for stream item.
 *
 * @since	1.0
 */
class SocialTableLikes extends SocialTable
{
	/**
	 * The auto incremented index for this table.
	 * @var int
	 */
	public $id			= null;

	/**
	 * The unique type of this liked item.
	 * @var string
	 */
	public $type		= null;

	/**
	 * The unique id of this liked item.
	 * @var int
	 */
	public $uid			= null;

	/**
	 * The stream id
	 * @var int
	 */
	public $stream_id	= null;

	/**
	 * The author of this like.
	 * @var int
	 */
	public $created_by	= null;

	/**
	 * The creation date for this liked item.
	 * @var int
	 */
	public $created		= null;

	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	DB
	 */
	public function __construct( $db )
	{
		parent::__construct('#__social_likes', 'id', $db);
	}

	/**
	 * Override parent's behavior
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function store( $updateNulls = false )
	{
		$isNew = false;

		if( empty( $this->id ) )
		{
			$isNew = true;
		}

		// Get dispatcher library
		$dispatcher 	= FD::dispatcher();
		$group 			= SOCIAL_APPS_GROUP_USER;

		$like = new stdClass();

        foreach (get_object_vars( $this ) as $key => $value)
        {
            $like->{$key} = $value;
        }

		$like->element 	= $this->type;
		$like->group 	= $group;
		$like->verb 	= '';

		if( strpos( $this->type, '.' ) !== false )
		{
			$tmp 	= explode( '.', $this->type );
			$group 	= $tmp['1'];

			$like->element 	= $tmp[0];
			$like->group 	= $tmp[1];
			$like->verb 	= isset( $tmp[2] ) ? $tmp[2] : '';
		}

		// if( $like->element == 'stream' )
		// {
		// 	$like->streamid 	= $like->uid;
		// 	$like->otype 		= $this->type;

		// 	$origin = $this->getItemOrigin( $like->uid );

		// 	if( $origin )
		// 	{
		// 		$like->element 	= $origin->context_type;
		// 		$like->uid 		= $origin->context_id;
		// 		$like->type 	= $origin->context_type . '.' . $group;

		// 		if( $like->verb )
		// 		{
		// 			$like->type 	= $like->type . '.' . $like->verb;
		// 		}
		// 	}
		// }

		$args 			= array( &$like );

		// @trigger: onBeforeLikeSave
		$dispatcher->trigger( $group , 'onBeforeLikeSave' , $args );

		$state = parent::store();

		if( !$state )
		{
			return $state;
		}

		// @trigger: onAfterLikeSave
		$dispatcher->trigger( $group , 'onAfterLikeSave' , $args );

		return $state;
	}

	private function getItemOrigin( $streamId )
	{
		$stream = FD::model( 'Stream' );
		$item 	= $stream->getContextItem( $streamId );
		return $item;
	}

	public function delete( $pk = null )
	{
		$state = parent::delete();

		if( $state )
		{
			// Get dispatcher library
			$dispatcher 	= FD::dispatcher();
			$args 			= array( &$this );
			$group 			= SOCIAL_APPS_GROUP_USER;

			$dispatcher->trigger( $group , 'onAfterLikeDelete' , $args );
		}

		return $state;
	}

}
