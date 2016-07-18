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

class SocialStreamActionLikes implements ISocialStreamAction
{
	private $item 		= null;
	private $isHidden 	= null;

	/**
	 * Class constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem
	 */
	public function __construct( SocialStreamItem &$item )
	{
		$this->item 	= $item;
		$this->isHidden = false;
	}

	/**
	 * Displays the like title in the stream.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTitle()
	{
		$config 	= FD::config();

		if( !$config->get( 'stream.likes.enabled' ) )
		{
			return false;
		}

		$obj 	= $this->getElementSource();

		$likes 	= FD::get( 'Likes' );
		$likes->get( $obj->uid , $obj->element, $this->item->verb );

		$count 	= 0;

		if( $likes->data !== false )
		{
			$count = count( $likes->data );
		}

		if( !$count )
		{
			$this->isHidden = true;
		}

		return $likes->button();
	}

	/**
	 * Returns the contents of this action.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html codes for the likes.
	 */
	public function getContents()
	{
		$config 	= FD::config();

		if( !$config->get( 'stream.likes.enabled' ) )
		{
			return false;
		}

		// Load up necessary libraries.
		$obj	= $this->getElementSource();

		$likes 	= FD::get( 'Likes' );
		$likes->get( $obj->uid , $obj->element, $this->item->verb );

		$count 	= 0;

		if( $likes->data !== false )
		{
			$count = count( $likes->data );
		}

		if( empty( $count ) )
		{
			$this->isHidden = true;
		}

		// Append comments into the stream actions as it is a core feature for stream.
		// Append likes to the stream object.
		return $likes->toHTML();
	}

	private function getElementSource()
	{
		$element = $this->item->context;
		$uid     = $this->item->contextId;

		// We need special treatment for photos because it could be aggregated items.
		// The context would then be album and the uid is the album's id
		if( $element == 'photos' )
		{
			if( count( $this->item->contextIds ) > 1 )
			{
				$photo = FD::table( 'Photo' );
				$photo->load( $uid );

				$element	= 'albums';
				$uid 		= $photo->album_id;
			}
		}

		if( $element == 'story' || $element == 'links' )
		{
			$uid = $this->item->uid;
		}

		if( $element == 'badges' )
		{
			$tbl = FD::table( 'StreamItem' );
			$tbl->load( array( 'uid' => $this->item->uid ) );

			$element	= SOCIAL_TYPE_ACTIVITY;
			$uid 		= $tbl->id;
		}


		$obj = new stdClass();
		$obj->element 	= $element;
		$obj->uid 		= $uid;

		return $obj;
	}

	/**
	 * Responsible to output the action link
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getLink()
	{
		return false;
	}

	/**
	 * Returns the unique key for this action.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The unique key for this action.
	 */
	public function getKey()
	{
		return 'likes';
	}

	/**
	 * Returns if the content should be hidden on load
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean The hide state of the content
	 */
	public function isHidden()
	{
		return $this->isHidden;
	}
}
