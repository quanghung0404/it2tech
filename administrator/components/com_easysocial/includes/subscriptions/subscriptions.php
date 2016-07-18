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

class SocialSubscriptions
{
	var $_error = null;


	public static function factory()
	{
		return new self();
	}

	public function button( $uid , $element, $group = SOCIAL_APPS_GROUP_USER )
	{
		$my = FD::user();

		$isFollow = $this->isFollowing( $uid , $element, $group, $my->id );

		$text = JText::_( 'COM_EASYSOCIAL_STREAM_FOLLOW' );
		if( $isFollow )
		{
		 	$text = JText::_( 'COM_EASYSOCIAL_STREAM_STOP_FOLLOW' );
		}

		$html = '';

		$themes 	= FD::get( 'Themes' );

		$themes->set( 'text', $text );
		$themes->set( 'uid'	, $uid );
		$themes->set( 'element', $element );
		$themes->set( 'group', $group );


 		$html = $themes->output( 'site/subscriptions/follow' );
 		return $html;
	}

	private function formKeys( $element , $group )
	{
		return $element . '.' . $group;
	}

	/**
	 * Follows a particular node item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function follow( $uid , $element, $group = SOCIAL_APPS_GROUP_USER, $userId = null, $notify = '0' )
	{
		if( is_null( $userId ) )
		{
			$userId 	= FD::user()->id;
		}

		// If user id is empty, we should not proceed.
		if( !$userId )
		{
			return false;
		}

		// Load the subscription item
		$table 		= FD::table( 'Subscription' );
		$isFollower	= $this->isFollowing( $uid , $element, $group, $userId );

		// Check if the user has already followed.
		if( $isFollower )
		{
			return false;
		}

		// Set the element of the subscription.
		$table->type 	= $this->formKeys( $element, $group );
		$table->uid 	= $uid;
		$table->user_id = $userId;
		$table->notify  = $notify;

		$state 	= $table->store();

		if( !$state )
		{
			$this->_error = $table-getError();
		}

		return $state;
	}

	/**
	 * Unfollow from a particular node item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow( $uid , $element, $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( empty( $uid ) )
		{
			//return JText::_('COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID');
			return false;
		}

		$table 		= FD::table( 'Subscription' );
		$options	= array( 'uid' => $uid , 'type' => $this->formKeys( $element, $group ) , 'user_id' => $userId );

		$table->load( $options );

		$state	= $table->delete();

		if( !$state )
		{
			$this->_error = $table-getError();
		}

		// Once unfollowed a user, delete the previously created streams
		$config = FD::config();
		if( $config->get( 'users.stream.following' ) )
		{
			$stream	= FD::stream();
			$stream->delete( $table->id , SOCIAL_TYPE_FOLLOWERS );
		}

		return $state;
	}

	/**
	 * Determines if the user is a follower.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target id.
	 * @param	element 	The target element.
	 * @param	int		The source user id.
	 *
	 * @return subscription id.
	 */
	public function isFollowing( $uid , $element , $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( is_null( $userId ) )
		{
			$userId 	= FD::user()->id;
		}

		$model 		= FD::model( 'Subscriptions' );
		$isFollower	= $model->isFollowing( $uid , $this->formKeys( $element, $group ) , $userId );

		return $isFollower;
	}

	public function getError()
	{
		return $this->_error;
	}
}
