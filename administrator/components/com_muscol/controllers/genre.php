<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class GenresControllerGenre extends GenresController
{

	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'genre' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('genre');

		if ($model->store($post)) {
			$msg = JText::_( 'GENRE_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_GENRE' );
		}

		$link = 'index.php?option=com_muscol&controller=genres';
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('genre');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_GENRES' );
		} else {
			$msg = JText::_( 'GENRES_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=genres', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=genres', $msg );
	}
}