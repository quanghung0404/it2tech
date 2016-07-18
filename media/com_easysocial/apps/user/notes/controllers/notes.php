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

class NotesControllerNotes extends SocialAppsController
{
	/**
	 * Renders the notes form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Load up ajax lib
		$ajax 	= FD::ajax();

		$id 	= JRequest::getVar( 'id' );

		$note 	= $this->getTable( 'Note' );
		$state 	= $note->load( $id );

		$my 	= FD::user();

		// Check if the user is allowed to edit this note
		if( $id && $note->user_id != $my->id )
		{
			return $ajax->reject();
		}

		// Set the params
		$params 	= $this->getParams();

		// Load the contents
		$theme 		= FD::themes();
		$theme->set( 'note' 	, $note );
		$theme->set( 'params'	, $params );

		$contents	= $theme->output( 'apps/user/notes/dialog.form' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays a delete confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Check for request forgeries
		FD::checkToken();

		// User needs to be logged in
		FD::requireLogin();

		// Load up ajax library
		$ajax 	= FD::ajax();

		// Get the delete confirmation dialog
		$theme 		= FD::themes();

		$contents	= $theme->output( 'apps/user/notes/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Deletes a note from the server
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		// User needs to be logged in
		FD::requireLogin();

		$id 	= JRequest::getInt( 'id' );

		// Load up ajax library
		$ajax 	= FD::ajax();

		$note	= $this->getTable( 'Note' );
		$note->load( $id );

		// Throw error when the id not valid
		if( !$id || !$note->id )
		{
			return $ajax->reject();
		}

		// Get the current logged in user as we only want the current logged
		$my 	= FD::user();

		if( $note->user_id != $my->id )
		{
			return $ajax->reject();
		}

		$state	= $note->delete();

		if( !$state )
		{
			return $ajax->reject( JText::_( $note->getError() ) );
		}

		return $ajax->resolve();
	}

	/**
	 * Creates a new note.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function store()
	{
		// Check for request forgeriess
		FD::checkToken();

		// Ensure that the user is logged in.
		FD::requireLogin();

		// Get ajax lib
		$ajax 		= FD::ajax();

		// Get the current user.
		$my 		= FD::user();

		// Get the app id.
		$appId 		= JRequest::getInt( 'appId' );

		// Get the title from request
		$title 		= JRequest::getVar( 'title' );

		// Get the note content from request
		$content	= JRequest::getVar( 'content' );

		$stream 	= JRequest::getBool( 'stream' );

		// Check if this is an edited entry
		$id 		= JRequest::getInt( 'id' );

		// Create the note
		$note 		= $this->getTable( 'Note' );
		$state 		= $note->load( $id );

		if( $id && $state )
		{
			if( $note->user_id != $my->id )
			{
				return $ajax->reject();
			}
		}

		$note->title 	= $title;
		$note->content	= $content;
		$note->user_id 	= $my->id;

		$state 		= $note->store();

		$note->link 	= FRoute::_( 'index.php?option=com_easysocial&view=apps&layout=canvas&id=' . $appId . '&cid=' . $note->id . '&userId=' . $my->id );

		if( !$state )
		{
			return $ajax->reject( $note->getError() );
		}

		// Format the note comments
		// Get the comments count
		$comments			= FD::comments( $note->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::apps( array( 'layout' => 'canvas', 'userid' => $my->getAlias() , 'cid' => $note->id ) ) ) );
		$note->comments 	= $comments->getCount();

		// Get the likes count
		$likes 			= FD::likes( $note->id , 'notes', 'create', SOCIAL_APPS_GROUP_USER );

		$note->likes 		= $likes->getCount();

		// Create a stream record
		if( $stream )
		{
			$verb	 = $id ? 'update' : 'create';
			$note->createStream( $verb );
		}

		// Format the note.
		$comments	= FD::comments( $note->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER , array( 'url' => FRoute::apps( array( 'layout' => 'canvas', 'userid' => $my->getAlias() , 'cid' => $note->id ) ) ) );
		$likes 		= FD::likes( $note->id , 'notes', 'create', SOCIAL_APPS_GROUP_USER );
		$stream		= FD::stream();
		$options 	= array( 'comments' => $comments , 'likes' => $likes );

		$note->actions 	= $stream->getActions( $options );

		$app 	= $this->getApp();
		$theme 	= FD::themes();
		$theme->set( 'app'		, $app );
		$theme->set( 'user'		, $my );
		$theme->set( 'appId'	, $appId );
		$theme->set( 'note'		, $note );

		$content = $theme->output( 'apps/user/notes/dashboard/item' );

		return $ajax->resolve( $content );
	}
}
