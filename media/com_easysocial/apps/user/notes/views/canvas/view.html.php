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

class NotesViewCanvas extends SocialAppsView
{
	/**
	 * Displays the single note item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display( $userId = null , $docType = null )
	{
		$id 	= JRequest::getInt( 'cid' );
		$appid 	= JRequest::getInt( 'id' );

		// Get the current owner of this app canvas
		$user 	= FD::user( $userId );

		$note 	= $this->getTable( 'Note' );
		$note->load( $id );

		if( !$id || !$note )
		{
			FD::info()->set( false , JText::_('APP_USER_NOTES_INVALID_NOTE_ID_PROVIDED') , SOCIAL_MSG_ERROR );

			return $this->redirect( FD::profile( array( 'id' => $user->getAlias() ) , false ) );
		}

		// Load up likes library for this note.
		$likes		= FD::likes( $note->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER );

		// Load up comments library for this note.
		$comments	= FD::comments( $note->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER , array( 'url' => '' ) );

		$this->set( 'likes'		, $likes );
		$this->set( 'comments'	, $comments );
		$this->set( 'note' , $note );
		$this->set( 'user' , $user );
		$this->set( 'appId' , $appid );

		echo parent::display( 'canvas/default' );
	}
}
