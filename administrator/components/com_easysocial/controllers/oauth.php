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

class EasySocialControllerOAuth extends EasySocialController
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
	}

	/**
	 * Revokes the access for the user that has already authenticated
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function revoke()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view.
		$view 		= $this->getCurrentView();

		$client 	= JRequest::getWord( 'client' );

		// Get the oauth library for the consumer type.
		$oauth 		= FD::oauth( ucfirst( $client ) );

		$uid 		= JRequest::getVar( 'uid' );
		$type 		= JRequest::getVar( 'type' );
		$callback 	= JRequest::getVar( 'callback' );

		// Grab the token.
		$table 		= FD::table( 'OAuth' );
		$table->load( array( 'uid' => $uid , 'type' => $type ) );

		// Set the access token.
		$oauth->setAccess( $table->token );

		$result 	= $oauth->revoke();

		if( !$result )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_OAUTH_THERE_WAS_ERROR_REVOKING_ACCESS' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $callback );
		}

		// Try to delete the record permanently
		$state 		= $table->delete();

		if( !$state )
		{
			$view->setError( JText::_( 'COM_EASYSOCIAL_OAUTH_THERE_WAS_DELETING_OAUTH_RECORD' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $callback );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_REVOKED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $callback );
	}

	/**
	 * Performs a request to social network sites to request for
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function grant()
	{
		// Get config
		$config		= FD::config();

		// Get the current view.
		$view 		= $this->getCurrentView();

		// Get the client.
		$client 	= JRequest::getWord( 'client' );

		// Determine what we should do next with the provided callback url.
		$callback 	= JRequest::getVar( 'callback' , '' );

		$callback 	= urldecode( $callback );

		// Check for oauth_callback as well
		if( !$client )
		{
			$view->setMessage( JText::_( 'Invalid client provided' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $callback );
		}

		// Load the respective oauth library
		$oauth 		= FD::oauth( ucfirst( $client ) );
		$access 	= $oauth->getAccess();

		// Get the necessary composite index
		$uid 		= JRequest::getInt( 'uid' );
		$type 		= JRequest::getWord( 'type' );

		if( empty( $uid ) || empty( $type ) )
		{
			$view->setMessage( JText::_( 'Please provide us with the proper keys' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $callback );
		}

		// Try to load the access object from the database first.
		$table 			= FD::table( 'OAuth' );
		$table->load( array( 'uid' => $uid , 'type' => $type ) );

		$table->uid 	= $uid;
		$table->type	= $type;
		$table->client 	= $client;
		$table->secret 	= $access->secret;
		$table->token 	= $access->token;
		$table->expires = $access->expires;
		$table->params 	= $access->params;


		// Try to store the access;
		$state 	= $table->store();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_OAUTH_GRANTED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ , $callback );
	}
}
