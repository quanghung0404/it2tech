<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class ArtistsControllerArtist extends ArtistsController
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
		JRequest::setVar( 'view', 'artist' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('artist');

		if ($model->store($post)) {
			$msg = JText::_( 'ARTIST_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_ARTIST' );
		}

		$task = JRequest::getCmd( 'task' );
		$id = JRequest::getVar('id');
		
		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option=com_muscol&controller=artist&task=edit&cid[]='. $id ;
				break;

			case 'save':
			default:
				$link = 'index.php?option=com_muscol&controller=artists';
				break;
		}
		
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('artist');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_ARTISTS' );
		} else {
			$msg = JText::_( 'ARTISTS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=artists', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=artists', $msg );
	}
}