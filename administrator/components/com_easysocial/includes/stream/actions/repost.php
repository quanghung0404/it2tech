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

class SocialStreamActionRepost implements ISocialStreamAction
{
	private $item 	= null;

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

		if( !$config->get( 'stream.repost.enabled' ) )
		{
			return false;
		}

		if( $this->item->context == 'shares' )
		{
			return false;
		}

		$obj 	= $this->getElementSource();

		$share 	= FD::get( 'Repost', $obj->uid, $obj->element );

		return $share->getButton();
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

		if( !$config->get( 'stream.repost.enabled' ) )
		{
			return false;
		}

		if( $this->item->context == 'shares' )
		{
			return false;
		}

		// Load up necessary libraries.
		$obj 	= $this->getElementSource();
		$share 	= FD::get( 'Repost', $obj->uid, $obj->element );

		return $share->getHTML();
	}

	private function getElementSource()
	{
		$element = $this->item->context;
		$uid     = $this->item->contextId;

		// photos has the special threatment. if the item is a aggregated item, then the context is album and the uid is albumid.
		if( $element == 'shares' )
		{
			$share = FD::table( 'Share' );
			$share->load( $uid );

			$data 		= explode( '.', $share->element );
			$element	= $data[0];
			$uid 		= $share->uid;
		}
		else
		{
			$element	= 'stream';
			$uid 		= $this->item->uid;
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
		return 'repost';
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
		$obj 		= $this->getElementSource();

		if( $this->item->context == 'shares' )
		{
			return true;
		}

		$share 		= FD::get( 'Repost', $obj->uid, $obj->element );
		$shareCount	= $share->getCount();


		return $shareCount <= 0;
	}
}
