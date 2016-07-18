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

/**
 * Profile view for Notes app.
 *
 * @since	1.0
 * @access	public
 */
class NotesViewNote extends SocialAppsView
{
	/**
	 * Displays the application output in the canvas.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 */
	public function display( $userId = null , $docType = null )
	{
		$id 	= JRequest::getInt( 'cid' );

		// Try to load the note.
		$note 	= $this->getTable( 'Note' );
		$note->load( $id );

		if (!$id || !$note->id ){
			return;
		}

		// Perform content fixes here.
		$note->content 	= nl2br( $note->content );

		$this->set( 'note' , $note );

		echo parent::display( 'default.note' );
	}
}
