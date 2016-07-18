<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class PlaylistsControllerPlaylist extends PlaylistsController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'playlist' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('playlist');

		if ($model->store($post)) {
			$msg = JText::_( 'PLAYLIST_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_PLAYLIST' );
		}

		$task = JRequest::getCmd( 'task' );
		$id = JRequest::getVar('id');
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_muscol&controller=playlist&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_muscol&controller=playlists';
				break;
		}
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('playlist');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_PLAYLISTS' );
		} else {
			$msg = JText::_( 'PLAYLISTS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=playlists', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=playlists', $msg );
	}
}