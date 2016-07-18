<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewPrivacy extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function update()
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}


	public function browse( $users = array(), $options = array() )
	{
		$ajax = FD::ajax();

		// Get dialog
		$theme = FD::themes();
		$theme->set( 'options', $options );

		$friends = array();

		if( count( $users ) > 0 )
		{
			$arr = array();
			foreach( $users as $u )
			{
				$arr[] = $u->user_id;
			}

			// preload users.
			FD::user( $arr );

			foreach( $users as $u )
			{
				$friends[] = FD::user( $u->user_id );
			}
		}

		$theme->set( 'friends', $friends );

		$html = $theme->output( 'site/privacy/dialog.custom.form' );

		return $ajax->resolve( $html );

	}

	public function getfriends( $userid = '' )
	{
		// Check for valid tokens.
		FD::checkToken();

		// Only valid registered user has friends.
		FD::requireLogin();

		$query 		= JRequest::getVar( 'q' , '' );
		$uId 		= JRequest::getVar( 'userid', '' );
		$exclude 	= JRequest::getVar( 'exclude' );

		$ajax = FD::ajax();

		if( !$query )
		{
			$ajax->reject( JText::_( 'Empty query' ) );
			return $ajax->send();
		}


		if( empty( $userid ) )
		{
			$userid = $uId;
		}

		$my 	= FD::user( $userid );

		// Load friends model.
		$model 		= FD::model( 'Friends' );


		// Determine what type of string we should search for.
		$config 	= FD::config();
		$type 		= $config->get( 'users.displayName' );

		//check if we need to apply privacy or not.
		$options = array();

		if( $exclude )
		{
			$options[ 'exclude' ] = $exclude;
		}

		// Try to get the search result.
		$friends		= $model->search( $my->id , $query , $type, $options);

		$return 	= array();
		if( $friends )
		{
			foreach( $friends as $row )
			{
				$friend 		= new stdClass();
				$friend->id 	= $row->id;
				$friend->title 	= $row->getName();

				$return[] = $friend;
			}
		}

		return $ajax->resolve( $return );
	}

	public function getfriendsOld( $userid = '' )
	{

		$query 		= JRequest::getVar( 'q' , '' );
		$uId 		= JRequest::getVar( 'userid', '' );
		$exclude 	= JRequest::getVar( 'exclude', '' );

		if( empty( $userid ) )
		{
			$userid = $uId;
		}

		//$ajax 	= FD::getInstance( 'Ajax' );
		$ajax = FD::ajax();

		if( !$query )
		{
			$ajax->reject( JText::_( 'Empty query' ) );
			return $ajax->send();
		}

		$my 	= FD::user( $userid );

		$model 	 = FD::model( 'friends' );
		$friends = $model->getFriends( $my->id );

		$return 	= array();

		if( $friends )
		{
			foreach( $friends as $row )
			{
				$friend 		= new stdClass();
				$friend->id 	= $row->id;
				$friend->title 	= $row->getName();

				$return[] = $friend;
			}
		}

		// header('Content-type: text/x-json; UTF-8');
		// echo json_encode($return);
		// exit;

		return $ajax->resolve( $return );

	}

}
