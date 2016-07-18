<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class SongsControllerSong extends SongsController{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'song' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('song');

		if ($album_id = $model->store($post)) {
			$msg = JText::_( 'SONG_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_SONG' );
		}
		
		$task = JRequest::getCmd( 'task' );
		$id = JRequest::getVar('id'); // song id
		
		$from = JRequest::getVar( 'from' );
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_muscol&controller=song&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				switch ($from)
				{
					case 'songs':
						$link = 'index.php?option=com_muscol&controller=songs';
						break;
		
					default:
						$link = 'index.php?option=com_muscol&controller=album&task=edit&tab=1&cid[]=' . $album_id;
						break;
				}
				
				break;
		}

		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		//this function is called only from the album form template
		$album_id = JRequest::getVar('id');
		
		$model = $this->getModel('song');
		
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_SONGS' );
		} else {
			$msg = JText::_( 'SONGS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=album&task=edit&tab=1&cid[]=' . $album_id, $msg );
	}

	function cancel()
	{
		$album_id = JRequest::getVar('album_id'); // song id
		
		$from = JRequest::getVar( 'from' );
		
		$msg = JText::_( 'OPERATION_CANCELLED' );
		switch ($from)
			{
				case 'songs':
					$link = 'index.php?option=com_muscol&controller=songs';
					break;
	
				default:
					$link = 'index.php?option=com_muscol&controller=album&task=edit&tab=1&cid[]=' . $album_id;
					break;
			}
		
		$this->setRedirect( $link, $msg );
	}
}