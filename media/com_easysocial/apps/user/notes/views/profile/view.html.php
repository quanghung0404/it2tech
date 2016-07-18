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
class NotesViewProfile extends SocialAppsView
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
		// Get Notes model library
		$model 	= $this->getModel( 'Notes' );

		// Retrieve list of notes created by user
		$result 	= $model->getItems( $userId );
		$notes 		= array();

		if( $result )
		{
			foreach( $result as $row )
			{
				$note 	= $this->getTable( 'Note' );
				$note->bind( $row );

				$note->likes 	= FD::likes( $row->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER );
				$note->comments	= FD::comments( $row->id , 'notes' , 'create', SOCIAL_APPS_GROUP_USER );

				$notes[]	= $note;
			}
		}

		// Get the profile
		$user 	= FD::user( $userId );

		$this->set( 'user'	, $user );
		$this->set( 'notes' , $notes );

		echo parent::display( 'profile/default' );
	}
}
