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

require_once( dirname( __FILE__ ) . '/abstract.php' );

class SocialExplorerHookGroup extends SocialExplorerHooks
{
	private $group 	= null;

	public function __construct( $uid , $type )
	{
		$this->group 	= FD::group( $uid );

		parent::__construct( $uid , $type );
	}

	/**
	 * Determines if the group has ability to upload files here
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowUpload()
	{
		$model 		= FD::model( 'Files' );
		$total 		= (int) $model->getTotalFiles( $this->group->id , SOCIAL_TYPE_GROUP );

		$access			= $this->group->getAccess();
		$allowUpload	= $access->get( 'files.max' ) == 0 || $total < $access->get( 'files.max' ) ? true : false;

		return $allowUpload;
	}

	/**
	 * Determines if the current person has access to the explorer of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasReadAccess()
	{
		if( $this->group->isMember() )
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns the maximum file size allowed
	 *
	 * @since	1.2
	 * @access	public
	 * @return	string
	 */
	public function getMaxSize()
	{
		$access 	= $this->group->getAccess();

		$max 	= $access->get( 'files.maxsize' ) . 'M';

		return $max;
	}

	/**
	 * Determines if the user has access to delete the files on the group
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function hasDeleteAccess(SocialTableFile $file)
	{
		// If the user owns the file, allow them to delete it
		if ($this->my->id == $file->user_id) {
			return true;
		}

		// If the user is the admin of the group allow them to delete the files
		if ($this->group->isAdmin() || $this->my->isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current person has access to the explorer of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasWriteAccess()
	{
		if ($this->group->isMember()) {
			return true;
		}

		return false;
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

		// Try to delete the folder
		if( !$collection->delete() )
		{
			return FD::exception( $collection->getError() );
		}

		return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_FOLDER_DELETED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
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
		$ids 	= JRequest::getVar( 'id' );
		$ids 	= FD::makeArray( $ids );

		foreach ($ids as $id) {
			$file 		= FD::table( 'File' );
			$file->load( $id );

			if (!$id || !$file->id) {
				return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_INVALID_FILE_ID_PROVIDED' ) );
			}

			$state 	= $file->delete();

			if (!$state) {
				return FD::exception( JText::_( $file->getError() ) );
			}
		}

		return $ids;
	}

	/**
	 * Override parent's implementation as we need to generate the stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFile( $title = null )
	{
		// Run the parent's logics first
		$result 	= parent::addFile( $title );

		if ($result instanceof SocialException) {
			return $result;
		}

		$createStream = $this->input->get('createStream', false, 'bool');

		if ($createStream) {
			// Create a stream item for the groups now
			$stream		= FD::stream();
			$tpl		= $stream->getTemplate();
			$actor 		= FD::user();

			// this is a cluster stream and it should be viewable in both cluster and user page.
			$tpl->setCluster( $this->group->id, SOCIAL_TYPE_GROUP, $this->group->type );

			// Set the actor
			$tpl->setActor( $actor->id , SOCIAL_TYPE_USER );

			// Set the context
			$tpl->setContext($result->id, SOCIAL_TYPE_FILES);

			// Set the verb
			$tpl->setVerb( 'uploaded' );

			$file 		= FD::table( 'File' );
			$file->load( $result->id );


			// Set the params to cache the group data
			$registry	= FD::registry();
			$registry->set( 'group' , $this->group );
			$registry->set( 'file'	, $file );

			// Set the params to cache the group data
			$tpl->setParams( $registry );

			// since this is a cluster and user stream, we need to call setPublicStream
			// so that this stream will display in unity page as well
			// This stream should be visible to the public
			$tpl->setAccess( 'core.view' );

			$streamItem	 = $stream->add( $tpl );

			// Prepare the stream permalink
			$permalink 	= FRoute::stream(array('layout' => 'item', 'id' => $streamItem->uid));

			// Notify group members when a new file is uploaded
			$this->group->notifyMembers('file.uploaded', array('fileId' => $file->id, 'fileName' => $file->name, 'fileSize' => $file->getSize(), 'permalink' => $permalink, 'userId' => $file->user_id));
		}
		
		return $result;
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
		return $this->group->canViewItem();
	}
}
