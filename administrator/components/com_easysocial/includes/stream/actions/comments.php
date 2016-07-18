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

class SocialStreamActionComments implements ISocialStreamAction
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
	 * Displays the comments title in the stream.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTitle()
	{
		$config 	= FD::config();
		$access 	= FD::access();

		if( !$config->get( 'stream.comments.enabled' ) || !$access->allowed( 'comments.add' ) )
		{
			return false;
		}

		$my 		= FD::user();

		return JText::_( 'Comment' );
	}

	/**
	 * Returns the contents of this action.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	string	The html codes for the comments.
	 */
	public function getContents()
	{
		$config 	= FD::config();

		if( !$config->get( 'stream.comments.enabled' ) )
		{
			return false;
		}

		$obj		= $this->getElementSource();

		$output 	= FD::comments( $obj->uid, $obj->element, $this->item->verb, SOCIAL_APPS_GROUP_USER, array( 'url' => '' ) )->getHtml( array( 'hideEmpty' => true ) );

		return $output;
	}

	private function getElementSource()
	{
		$element	= $this->item->context;
		$uid		= $this->item->contextId;

		// photos has the special threatment. if the item is a aggregated item, then the context is album and the uid is albumid.
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
	 * Responsible to output the action link for comments
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
		return 'comments';
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
		// $obj = $this->getElementSource();

		// $model 		= FD::model( 'comments' );
		// $options 	= array( 'uid' => $obj->uid, 'element' => $obj->element );

		// $count 		= $model->getCommentCount( $options );

		// return $count > 0 ? false : true;

		return false;
	}
}
