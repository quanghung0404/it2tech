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

// Include main view file.
FD::import('site:/views/views');

class EasySocialViewProfile extends EasySocialSiteView
{
	/**
	 * Displays the popbox of a user when hovering over the name or avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function popbox()
	{
		// Load front end's language file
		FD::language()->loadSite();

		$id 	= JRequest::getInt( 'id' );

		$ajax 	= FD::ajax();

		if( !$id )
		{
			// Throw some errors.
			return $ajax->reject( $this->getMessage() );
		}

		$user 	= FD::user( $id );

		$theme	= FD::themes();

		$theme->set( 'user' , $user );

		$contents 	= $theme->output( 'site/profile/popbox' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the popbox of a user when hovering over the name or avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function popboxFollow()
	{
		// Load front end's language file
		FD::language()->loadSite();

		$ajax 	= FD::ajax();
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw some errors.
			return $ajax->reject( $this->getMessage() );
		}

		$user 		= FD::user( $id );

		$theme 		= FD::themes();
		$theme->set( 'user' , $user );

		$contents 	= $theme->output( 'site/profile/popbox.follow' );

		return $ajax->resolve( $contents );
	}


	/**
	 * Displays the popbox of a user when hovering over the name or avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function popboxUnfollow()
	{
		// Load front end's language file
		FD::language()->loadSite();

		$ajax 	= FD::ajax();
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw some errors.
			return $ajax->reject( $this->getMessage() );
		}

		$user 		= FD::user( $id );

		$theme 		= FD::themes();
		$theme->set( 'user' , $user );

		$contents 	= $theme->output( 'site/profile/popbox.unfollow' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation dialog to delete a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Only registered users can see this
		FD::requireLogin();

		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$contents	= $theme->output( 'site/profile/dialog.profile.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays error message when user tries to save an invalid form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function showFormError()
	{
		// Only registered users can see this
		FD::requireLogin();

		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$contents	= $theme->output( 'site/profile/dialog.profile.error' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Allows a user to follow an object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function follow( $subscription )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme	= FD::themes();
		$button	= $theme->output( 'site/profile/button.followers.unfollow' );

		return $ajax->resolve( $button );
	}

	/**
	 * Allows a user to unfollow an object.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme	= FD::themes();
		$button	= $theme->output( 'site/profile/button.followers.follow' );

		return $ajax->resolve( $button );
	}

	/**
	 * Retrieves the user's timeline
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream( $stream , $story )
	{
		$ajax 	= FD::ajax();

		// // If there's an error throw it back to the caller.
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme 		= FD::themes();

		$theme->set( 'stream'	, $stream );
		$theme->set( 'story'	, $story );

		$contents 	= $theme->output( 'site/profile/default.stream' );

		// Return the contents
		return $ajax->resolve( $contents );
	}

	/**
	 * Retrieves the app contents
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getAppContents( $app , $userId )
	{
		$ajax 	= FD::ajax();

		// If there's an error throw it back to the caller.
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the current logged in user.
		$user 		= FD::user( $userId );

		// Load the library.
		$lib		= FD::apps();
		$contents 	= $lib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'profile' , $app , array( 'userId' => $user->id ) );

		// Return the contents
		return $ajax->resolve( $contents );
	}

	/**
	 * Retrieves a popbox button
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getButton()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );
		$button	= JRequest::getVar( 'button' );

		$user 	= FD::user( $id );

		$theme 	= FD::themes();


		$theme->set( 'user' , $user );

		$file 	= 'site/profile/popbox.' . $button;

		$output	= $theme->output( $file );

		return $ajax->resolve( $output );
	}

	/**
	 * Displays confirmation to cancel friend request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmCancelRequest()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );

		$theme 		= FD::themes();
		$theme->set( 'id' , $id );
		$contents 	= $theme->output( 'site/profile/dialog.friends.cancel' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays a confirmation before removing a friend
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveFriend()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );
		$user 	= FD::user( $id );

		$theme 	= FD::themes();

		$theme->set( 'user' , $user );
		$contents 	= $theme->output( 'site/profile/dialog.friends.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays a notice to the user that the friend request has been rejected
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function rejected()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );
		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents 	= $theme->output( 'site/profile/dialog.popbox.friends.rejected' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays a notice to the user that the friend has been deleted
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function friendRemoved()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );
		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents 	= $theme->output( 'site/profile/dialog.friends.deleted' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays a confirmation when both parties are friends
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmFriends()
	{
		$ajax 	= FD::ajax();

		$id 	= JRequest::getInt( 'id' );
		$user 	= FD::user( $id );

		$theme 	= FD::themes();

		$theme->set( 'user' , $user );
		$contents 	= $theme->output( 'site/profile/dialog.friends.confirmed' );

		return $ajax->resolve( $contents );
	}
}
