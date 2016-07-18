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

// Include main controller here.
FD::import( 'admin:/controllers/controller' );

/**
 * Mailer controller.
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasySocialControllerMailer extends EasySocialController
{
	/**
	 * Class construct
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();

		// Register task aliases here.
		$this->registerTask( 'publish' 		, 'togglePublish' );
		$this->registerTask( 'unpublish'	, 'togglePublish' );

		// Task aliases for purging items
		$this->registerTask( 'purgeSent'	, 'purge' );
		$this->registerTask( 'purgePending'	, 'purge' );
		$this->registerTask( 'purgeAll'		, 'purge' );

		// Task aliases for saving new item.
		$this->registerTask( 'apply'	, 'store' );
		$this->registerTask( 'save'		, 'store' );
		$this->registerTask( 'save2new'	, 'store' );
	}

	/**
	 * Method to redirect to the appropriate form layout.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function form()
	{
		$this->setRedirect( 'index.php?option=com_easysocial&view=mailer&layout=form' );
	}

	/**
	 * Stores a new email queue.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function store()
	{
		JRequest::checkToken( 'get' ) or JRequest::checkToken() or die( 'Invalid token' );

		$task 	= $this->getTask();
		$method	= strtolower( $task );

		$id 	= JRequest::getInt( 'cid' );
		$view 	= FD::getInstance( 'View' , 'Mailer' );

		// Load the mailer object.
		$mailer	= FD::table( 'Mailer' );
		$mailer->load( $id );

		// Get the data from $_POST
		$post 	= JRequest::get( 'Post' );
		$mailer->bind( $post );

		// Try to store the mailer row.
		$state	= $mailer->store();

		// By default it redirects back to the mailer lists.
		$redirect 	= 'index.php?option=com_easysocial&view=mailer';

		if( !$state )
		{
			// Set the error.
			$view->setError( $mailer->getError() );

			return $view->call( $method , $mailer );
		}

		$info 	= FD::getInstance( 'Info' );
		$info->set( JText::_( 'Mail item added into the mail queue.' ) , SOCIAL_MSG_SUCCESS );

		return $view->call( $method , $mailer );
	}

	/**
	 * Purge mail items from the spool.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function purge()
	{
		JRequest::checkToken( 'get' ) or JRequest::checkToken() or die( 'Invalid token' );

		$task 	= $this->getTask();
		$method	= strtolower( $task );

		$view 	= FD::getInstance( 'View' , 'Mailer' );

		$model	= FD::model( 'Mailer' );
		$state	= $model->$method();

		if( !$state )
		{
			switch( $task )
			{
				case 'purgePending':
					$message 	= JText::_( 'COM_EASYSOCIAL_ERRORS_MAILER_PURGE_PENDING' );
				break;
				case 'purgeSent':
					$message	= JText::_( 'COM_EASYSOCIAL_ERRORS_MAILER_PURGE_SENT' );
				break;
				case 'purgeAll':
				default:
					$message	= JText::_( 'COM_EASYSOCIAL_ERRORS_MAILER_PURGE_ALL' );
				break;
			}

			$view->setError( $message );
			return $view->call( $task );
		}

		switch( $task )
		{
			case 'purgePending':
				$message 	= JText::_( 'COM_EASYSOCIAL_MAILER_PENDING_ITEMS_PURGED_SUCCESSFULLY' );
			break;
			case 'purgeSent':
				$message	= JText::_( 'COM_EASYSOCIAL_MAILER_SENT_ITEMS_PURGED_SUCCESSFULLY' );
			break;
			case 'purgeAll':
			default:
				$message	= JText::_( 'COM_EASYSOCIAL_MAILER_ALL_ITEMS_PURGED_SUCCESSFULLY' );
			break;
		}

		$info 	= FD::getInstance( 'Info' );
		$info->set( $message , SOCIAL_MSG_SUCCESS );
		return $view->call( $task );
	}

	/**
	 * Toggle publish button
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function togglePublish()
	{
		JRequest::checkToken( 'get' ) or JRequest::checkToken() or die( 'Invalid token' );

		$task 	= $this->getTask();
		$method = strtolower( $task );

		$ids	= JRequest::getVar( 'cid' );
		$ids 	= FD::makeArray( $ids );

		// Get the view object.
		$view 	= FD::getInstance( 'View' , 'Mailer' );

		// Test if there's any id's being passed in.
		if( empty( $ids ) )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_ERRORS_MAILER_NO_ID' ) );
			return $view->call( $task );
		}

		foreach( $ids as $id )
		{
			$mailer 	= FD::table( 'Mailer' );
			$mailer->load( $id );

			// When there's an error, just break out of the loop.
			if( !$mailer->$method() )
			{
				$view->setError( $mailer->getError() );
				return $view->call( $task );
			}
		}

		$info	 = FD::getInstance( 'Info' );
		$message = $task == 'publish' ? JText::_( 'COM_EASYSOCIAL_MAILER_ITEMS_MARKED_AS_SENT' ) : JText::_( 'COM_EASYSOCIAL_MAILER_ITEMS_MARKED_AS_PENDING' );

		$info->set( $message , SOCIAL_MSG_SUCCESS );

		return $view->call( $method );
	}
}
