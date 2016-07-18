<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class FormatsControllerFormat extends FormatsController
{

	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'format' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('format');

		if ($model->store($post)) {
			$msg = JText::_( 'FORMAT_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_FORMAT' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_muscol&controller=formats';
		$this->setRedirect($link, $msg);
	}
	
	function saveorder(){
		
		$model = $this->getModel('format');
		if(!$model->saveorder()) {
			$msg = JText::_( 'ERROR_SAVING_ORDER' );
		} else {
			$msg = JText::_( 'ORDER_SAVED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=formats', $msg );
	}


	function remove()
	{
		$model = $this->getModel('format');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_FORMATS' );
		} else {
			$msg = JText::_( 'FORMATS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=formats', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=formats', $msg );
	}
}