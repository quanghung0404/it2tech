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

// Include the main views class
FD::import( 'admin:/views/views' );

class EasySocialViewProfiles extends EasySocialAdminView
{
	/**
	 * Processes the request to return a DefaultAvatar object in JSON format.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableDefaultAvatar	The avatar object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function uploadDefaultAvatars( $avatar )
	{
		// Get the ajax object.
		$ajax 	= FD::ajax();

		$avatars 	= array( $avatar );

		$theme 	= FD::themes();
		$theme->set( 'defaultAvatars' , $avatars );
		$output	= $theme->output( 'admin/profiles/avatar.item' );

		return $ajax->resolve( $output );
	}

	/**
	 * Confirmation to delete a profile avatar
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function confirmRemoveProfileAvatar()
	{
		$theme = ES::themes();
		$output = $theme->output('admin/profiles/dialog.delete.profile.avatar');
		
		return $this->ajax->resolve($output);
	}

	/**
	 * Displays a dialog confirmation before deleting a default avatar
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function confirmDeleteAvatar()
	{
		$ajax 	= FD::ajax();
		$theme 	= FD::themes();

		$contents	= $theme->output( 'admin/profiles/dialog.delete.avatar' );
		$ajax->resolve( $contents );
	}

	/**
	 * Allows caller to browse for a profile
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function browse()
	{
		// Load up the ajax library
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		// Determine if there's a jscallback
		$callback 	= JRequest::getCmd( 'jscallback' );

		$theme->set( 'callback' , $callback );

		$output	= $theme->output( 'admin/profiles/dialog.browse' );

		return $ajax->resolve( $output );
	}

	/**
	 * Retrieves the group template
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getGroupTemplate()
	{
		$ids = $this->input->get('groups', array(), 'array');

		if (!$ids) {
			return $this->ajax->reject();
		}

		$groups = array();

		foreach ($ids as $id) {
			$group = ES::group($id);

			$groups[] = $group;
		}

		$theme = ES::themes();
		$theme->set('groups', $groups);
	
		$html = $theme->output('admin/profiles/form.groups.item');

		return $this->ajax->resolve($html);
	}

	public function insertMember( $user )
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject();
		}

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );
		$output = $theme->output( 'admin/profiles/form.members.item' );

		return $ajax->resolve( $output );
	}

	public function confirmDelete()
	{
		$ajax 	= FD::ajax();

		$theme	= FD::themes();

		$contents = $theme->output( 'admin/profiles/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	public function getFieldValues( $values )
	{
		FD::ajax()->resolve( $values );
	}

	public function deleteField( $state )
	{
		FD::ajax()->resolve( $state );
	}

	public function deletePage( $state )
	{
		FD::ajax()->resolve( $state );
	}

	public function saveFields( $data )
	{
		if( $data === false )
		{
			return FD::ajax()->reject( $this->getError() );
		}

		FD::ajax()->resolve( $data );
	}

	public function getAclErrorDialog()
	{
		$key = $this->input->get('key', '', 'word');

		$message = 'COM_EASYSOCIAL_MAXUPLOADSIZE_ERROR_' . strtoupper($key);
		$message = JText::_($message);

		$theme = ES::themes();
		$theme->set('message', $message);
		$contents = $theme->output('admin/profiles/dialogs/acl.error');

		return $this->ajax->resolve($contents);
	}

	public function deleteProfileAvatar()
	{
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getError() );
		}

		return $ajax->resolve();
	}


	public function createBlankProfile( $data )
	{
		if( $data === false )
		{
			return FD::ajax()->reject( $this->getError() );
		}

		FD::ajax()->resolve( $data );
	}
}
