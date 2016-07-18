<?php

/**
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TagsControllerTag extends TagsController
{

	function __construct()
	{
		parent::__construct();
		
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'apply',	'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'tag' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		$model = $this->getModel('tag');

		if ($model->store($post)) {
			$msg = JText::_( 'TAG_SAVED' );
		} else {
			$msg = JText::_( 'ERROR_SAVING_TAG' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_muscol&controller=tags';
		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		$model = $this->getModel('tag');
		if(!$model->delete()) {
			$msg = JText::_( 'ERROR_DELETE_TAGS' );
		} else {
			$msg = JText::_( 'TAGS_DELETED' );
		}

		$this->setRedirect( 'index.php?option=com_muscol&controller=tags', $msg );
	}

	function cancel()
	{
		$msg = JText::_( 'OPERATION_CANCELLED' );
		$this->setRedirect( 'index.php?option=com_muscol&controller=tags', $msg );
	}
}