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

// Include main view file.
FD::import('site:/views/views');

class EasySocialViewProfile extends EasySocialSiteView
{
	/**
	 * Allows caller to take a picture
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveCamPicture()
	{
		// Ensure that the user is a valid user
		ES::requireLogin();

		$image = JRequest::getVar('image', '', 'default');
		$image = imagecreatefrompng($image);

		ob_start();
		imagepng($image, null, 9);
		$contents = ob_get_contents();
		ob_end_clean();

		// Store this in a temporary location
		$file = md5(FD::date()->toSql()) . '.png';
		$tmp = JPATH_ROOT . '/tmp/' . $file;
		$uri = JURI::root() . 'tmp/' . $file;

		JFile::write($tmp, $contents);

		$result = new stdClass();
		$result->file = $file;
		$result->url = $uri;

		return $this->ajax->resolve($result);
	}

	/**
	 * Allows caller to take a picture
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function takePicture()
	{
		// Ensure that the user is logged in
		ES::requireLogin();

		$theme = ES::themes();

		$user = FD::user();

		$theme->set('uid', $user->id);

		$output = $theme->output('site/profile/dialog.capture.picture');

		return $this->ajax->resolve($output);
	}

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
			return $ajax->reject( $this->getMessage());
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

	/**
	 * Retrieves the about tabs
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initInfo($steps = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		return $this->ajax->resolve($steps);
	}

	public function getInfo($fields = null)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$theme = FD::themes();

		$theme->set('fields', $fields);

		$contents = $theme->output('site/profile/default.info');

		return $ajax->resolve($contents);
	}

	/**
	 * Displays confirmation dialog to delete a user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDeleteUser()
	{
		// Only registered users can see this
		FD::requireLogin();

		// Only site admins can access
		if (!$this->my->isSiteAdmin()) {
			return;
		}

		$theme = FD::themes();

		$uid = $this->input->get('id', 0, 'int');

		// check if user exists or not.
		$user = JFactory::getUser($uid);

		if (! $user->id) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_INVALID_USER'));
		}

		if (! $this->my->canDeleteUser($user)) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_DELETE_USER'));
		}

		$theme = FD::themes();

		$content = $theme->output('site/profile/dialog.user.delete');

		return $this->ajax->resolve($content);
	}

	/**
	 * Confirmation to delete user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteUser()
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$message = $this->getMessage();

		$theme = FD::themes();

		$theme->set('msgObj', $message);
		$theme->set('userListingLink', FRoute::users());
		$theme->set('dashboardLink', FRoute::dashboard());

		$contents = $theme->output('site/profile/dialog.user.delete.success');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Post operation once an account is unblocked from the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unbanUser($user)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}
		$message = JText::_('COM_EASYSOCIAL_USER_UNBANNED_SUCCESS_MESSAGE');

		$theme = ES::themes();
		$theme->set('message', $message);
		$theme->set('user', $user);
		$contents = $theme->output('site/profile/dialog.user.unban.success');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Post operation after a user is banned on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function banUser($user)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$message = $this->getMessage();

		$theme = FD::themes();
		$theme->set('user', $user);
		$theme->set('msgObj', $message);
		$theme->set('userListingLink', FRoute::users());
		$theme->set('dashboardLink', FRoute::dashboard());

		$contents = $theme->output('site/profile/dialog.user.ban.success');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays confirmation dialog to ban a user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnban()
	{
		// Only registered users can see this
		ES::requireLogin();

		$theme = FD::themes();

		$uid = $this->input->get('id', 0, 'int');

		$user = ES::user($uid);

		if (! $this->my->canBanUser($user)) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'));
		}

		$contents = $theme->output('site/profile/dialog.user.unban');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays confirmation dialog to ban a user
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmBanUser()
	{
		// Only registered users can see this
		FD::requireLogin();

		$theme = FD::themes();

		$uid = $this->input->get('id', 0, 'int');

		$user = ES::user($uid);

		if (!$user->id) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_INVALID_USER'));
		}

		if (! $this->my->canBanUser($user)) {
			return $this->ajax->reject(JText::_('COM_EASYSOCIAL_PROFILE_NOT_ALLOWED_TO_BAN_USER'));
		}

		$contents = $theme->output('site/profile/dialog.user.ban');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation to remove an avatar
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveAvatar()
	{
		// Only registered users can do this
		ES::requireLogin();

		$theme = ES::themes();
		$contents = $theme->output('site/profile/dialogs/remove.avatar');

		return $this->ajax->resolve($contents);
	}
}
