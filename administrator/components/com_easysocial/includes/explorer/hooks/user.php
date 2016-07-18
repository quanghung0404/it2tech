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

require_once(dirname(__FILE__) . '/abstract.php');

class SocialExplorerHookUser extends SocialExplorerHooks
{
	private $group 	= null;

	public function __construct($uid, $type)
	{
		parent::__construct( $uid , $type );
	}

	/**
	 * Removes a folder from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFolder( $id = null )
	{
		// Check if the user has access to delete files from this group
		if( !$this->group->isMember() )
		{
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_DELETE_FOLDER' ) );
		}

		$id 	= is_null( $id ) ? JRequest::getInt( 'id' ) : $id;

		if( !$id )
		{
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_INVALID_FOLDER_ID_PROVIDED' ) );
		}

		$collection 		= FD::table( 'FileCollection' );
		$collection->load( $id );

		// Check if the current viewer can delete the item.
		$my 	= FD::user();

		if( ( $collection->user_id != $my->id ) && !$my->isSiteAdmin() )
		{
			return FD::exception( JText::_( 'Sorry, but you are not allowed to delete this folder.' ) );
		}

		if( !$collection->delete() )
		{
			return FD::exception( $collection->getError() );
		}


		return FD::exception( $id ,  JText::_( 'Folder removed successfully.' ) , SOCIAL_MSG_SUCCESS );
	}

	/**
	 * Removes a file from a group.
	 *
	 * @since	1.2
	 * @access	public
	 * @return	mixed 	True if success, exception if false.
	 */
	public function removeFile()
	{
		// Check if the user has access to delete files from this group
		if( !$this->group->isMember() )
		{
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_DELETE' ) );
		}

		// Get the file id
		$ids 	= JRequest::getInt( 'id' );
		$ids 	= FD::makeArray( $ids );

		foreach( $ids as $id )
		{
			$file 		= FD::table( 'File' );
			$file->load( $id );

			if( !$id || !$file->id )
			{
				return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_INVALID_FILE_ID_PROVIDED' ) );
			}

			$state 	= $file->delete();

			if( !$state )
			{
				return FD::exception( JText::_( $file->getError() ) );
			}
		}

		return true;
	}
	
	/**
	 * Override parent's behavior to insert a file
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addFile($title = null)
	{
		// Run the parent's logics first
		$result = parent::addFile($title);

		if ($result instanceof SocialException) {
			return $result;
		}

		$createStream = $this->input->get('createStream', false, 'bool');

		if ($createStream) {
			// Create a new stream for the user upload now
			$stream = FD::stream();
			$tpl = $stream->getTemplate();

			// Set the actor
			$tpl->setActor($this->my->id, SOCIAL_TYPE_USER);

			// Set the context
			$tpl->setContext($result->id, SOCIAL_TYPE_FILES);

			// Set the verb
			$tpl->setVerb('uploaded');

			// Set the access for this stream item
			$tpl->setAccess('core.view');

			// Insert the stream now
			$streamItem = $stream->add($tpl);
		}

		return $result;
	}

	/**
	 * Returns the maximum file size allowed
	 *
	 * @since	1.3
	 * @access	public
	 * @return	string
	 */
	public function getMaxSize()
	{
		// @TODO: Check for max size allowed
		return '100M';
	}

	/**
	 * Determines if the current person has access to upload files
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasWriteAccess()
	{
		// @TODO: Check for access
		return true;
	}

	/**
	 * The user should always have access to delete their own files.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function hasDeleteAccess(SocialTableFile $file)
	{
		return true;
	}

	/**
	 * Determines if the viewer can view the explorer
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canViewItem()
	{
		return $this->my->id == $this->uid;
	}
}
