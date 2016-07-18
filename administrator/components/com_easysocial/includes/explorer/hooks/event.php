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

require_once(__DIR__ . '/abstract.php');

class SocialExplorerHookEvent extends SocialExplorerHooks
{
	private $event  = null;
	private $access = null;

	public function __construct($uid, $type)
	{
		$this->event = FD::event($uid);
		$this->access = $this->event->getAccess();

		parent::__construct($uid, $type);
	}

	/**
	 * Determines if the event has ability to upload files here
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function allowUpload()
	{
		$model = FD::model('Files');
		$total = (int) $model->getTotalFiles($this->event->id, SOCIAL_TYPE_EVENT);

		if ($this->access->get('files.max') == 0 || $total < $this->access->get('files.max')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current person has access to the explorer of the event
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasReadAccess()
	{
		if ($this->my->isSiteAdmin()) {
			return true;
		}

		if ($this->event->getGuest()->isGuest()) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user has access to delete the files on the event
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
		
		if ($this->event->isAdmin() || $this->my->isSiteAdmin()) {
			return true;
		}

		return false;
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
		$max = $this->access->get( 'files.maxsize' ) . 'M';

		return $max;
	}

	/**
	 * Determines if the current person has access to the explorer of the event
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasWriteAccess()
	{
		// Since this is the same with the read access
		return $this->hasReadAccess();
	}

	/**
	 * Removes a folder from the event
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFolder($id = null)
	{
		// Check if the user has access to delete files from this group
		if (!$this->event->isMember() && !$this->my->isSiteAdmin()) {
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_DELETE_FOLDER' ) );
		}

		$id = is_null($id) ? $this->input->get('id', 0, 'int') : $id;

		if (!$id) {
			return FD::exception(JText::_('COM_EASYSOCIAL_EXPLORER_INVALID_FOLDER_ID_PROVIDED'));
		}

		// Load up the files collection
		$collection = FD::table('FileCollection');
		$collection->load($id);

		// Try to delete the folder
		if (!$collection->delete()) {
			return FD::exception($collection->getError());
		}

		return FD::exception(JText::_('COM_EASYSOCIAL_EXPLORER_FOLDER_DELETED_SUCCESS'), SOCIAL_MSG_SUCCESS);
	}

	/**
	 * Removes a file from an event.
	 *
	 * @since	1.3
	 * @access	public
	 * @return	mixed 	True if success, exception if false.
	 */
	public function removeFile()
	{
		// Check if the user has access to delete files from this group
		if (!$this->event->isMember() && !$this->my->isSiteAdmin()) {
			return FD::exception(JText::_('COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_DELETE'));
		}

		// Get the file id
		$ids 	= JRequest::getVar('id');
		$ids 	= FD::makeArray($ids);

		foreach ($ids as $id) {

			$file = FD::table('File');
			$file->load($id);

			if (!$id || !$file->id) {
				return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_INVALID_FILE_ID_PROVIDED' ) );
			}

			$state 	= $file->delete();

			if (!$state) {
				return FD::exception(JText::_($file->getError()));
			}
		}

		return $ids;
	}

	/**
	 * Override parent's implementation as we need to generate the stream
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

		$file = FD::table('File');
		$file->load($result->id);

		if ($createStream) {
			// Create a stream item for the groups now
			$stream = FD::stream();

			// Load the stream template
			$tpl = $stream->getTemplate();
			$stream		= FD::stream();

			// this is a cluster stream and it should be viewable in both cluster and user page.
			$tpl->setCluster($this->event->id, SOCIAL_TYPE_EVENT, 1);

			// Set the actor
			$tpl->setActor($this->my->id, SOCIAL_TYPE_USER);

			// Set the context
			$tpl->setContext($result->id, SOCIAL_TYPE_FILES);

			// Set the verb
			$tpl->setVerb('uploaded');


			// Set the params to cache the group data
			$registry	= FD::registry();
			$registry->set('event', $this->event);
			$registry->set('file', $file);

			// Set the params to cache the group data
			$tpl->setParams($registry);

			// since this is a cluster and user stream, we need to call setPublicStream
			// so that this stream will display in unity page as well
			// This stream should be visible to the public
			$tpl->setPublicStream('core.view');

			$streamItem	 = $stream->add($tpl);

			// Prepare the stream permalink
			$permalink 	= FRoute::stream(array('layout' => 'item', 'id' => $streamItem->uid));

			// Notify group members when a new file is uploaded
			$this->event->notifyMembers('file.uploaded', array('fileId' => $file->id, 'fileName' => $file->name, 'fileSize' => $file->getSize(), 'permalink' => $permalink, 'userId' => $file->user_id));
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
		return $this->event->canViewItem();
	}
}
