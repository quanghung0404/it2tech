<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */


// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TypesControllerType extends TypesController
{

	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'type' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('type');

		if ($model->store($post)) {
			$msg = JText::_( 'TYPE_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_TYPE' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_muscol&controller=types';
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('type');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_TYPES' );
		} else {
			$msg = JText::_( 'TYPES_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=types', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=types', $msg );
	}
}