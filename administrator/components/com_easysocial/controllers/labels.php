<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerLabels extends EasySocialController
{
	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		$this->registerTask( 'apply' , 'save' );
		$this->registerTask( 'savenew' , 'save' );
	}

	/**
	 * Saves a label
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save()
	{
		// Check for request forgeries
		FD::checkToken();

		$task 		= $this->getTask();
		$view 		= $this->getCurrentView();

		$label 		= FD::table( 'Label' );
		$id 		= JRequest::getInt( 'id' );

		if( $id )
		{
			$label->load( $id );
		}

		$post 		= JRequest::get( 'post' );
		$label->bind( $post );

		// Validation checks.
		if( !$label->check() )
		{
			$view->setMessage( $label->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $task , $label );
		}

		$state 		= $label->store();

		if( !$state )
		{
			$view->setMessage( $label->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $task , $label );
		}

		$message 	= !$id ? 'COM_EASYSOCIAL_LABELS_LABEL_CREATED_SUCCESSFULLY' : 'COM_EASYSOCIAL_LABELS_LABEL_SAVED_SUCCESSFULLY';

		$view->setMessage( JText::_( $message ) , SOCIAL_MSG_SUCCESS );

		return $view->call( __FUNCTION__ , $task , $label );
	}

	/**
	 * Deletes a label
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		$ids 	= JRequest::getVar( 'cid' );
		$ids 	= FD::makeArray( $ids );

		$view 	= $this->getCurrentView();

		if( empty( $ids ) )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_LABELS_EMPTY_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		foreach( $ids as $id )
		{
			$label 	= FD::table( 'Label' );
			$label->load( $id );

			$label->delete();
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_LABELS_LABEL_DELETED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}
}
