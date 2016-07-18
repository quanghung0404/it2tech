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


class AppNotesViewProcess extends SocialAppsView
{
	public function __construct( &$options )
	{
		parent::__construct( $options );
	}

	/**
	 * When a note is stored, this method would be invoked.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function save()
	{
		$my 	= FD::user();
		$post 	= JRequest::get( 'post' );

		$note 	= $this->table( 'Note' );
		$note->bind( $post );

		$note->user_id 	= $my->id;
		$note->created	= JFactory::getDate()->toMySQL();

		$note->store();

		FD::getInstance( 'Info' )->set( 'Your note is created successfully.' );
		$this->app->redirect( 'index.php?option=com_easysocial&view=dashboard#app-notes' );
	}

	/**
	 * Deletes a note.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function delete()
	{
		$my 	= FD::user();
		$id 	= JRequest::getInt( 'noteid' );

		$note 	= $this->table( 'Note' );
		$note->load( $id );

		if( !$note->delete() )
		{
			FD::getInstance( 'Info' )->set( $note->getError() , 'error' );

			return $this->redirect( 'index.php?option=com_easysocial&view=dashboard' );
		}

		FD::getInstance( 'Info' )->set( 'Note deleted successfully' , 'success' );
		$this->redirect( 'index.php?option=com_easysocial&view=dashboard#app-notes' );
	}
}
