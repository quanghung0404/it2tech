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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerPrivacy extends EasySocialController
{

	/**
	 * to hide the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */
	public function store()
	{
		FD::checkToken();

		FD::requireLogin();

		$post       = JRequest::get( 'POST' );
		$my         = FD::user();

		$state	= false;

		if( isset( $post[ 'privacy'] ) )
		{
			$model 	= FD::model( 'Privacy' );
			$state = $model->updatePrivacy( $my->id , $post[ 'privacy' ], 'user' );
		}


		if(! $state )
		{
			// FD::getInstance( 'View' , 'Privacy' , false )->setErrors( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATE_FAILED' ) );

			FD::getInstance( 'Info' )->set( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATE_FAILED' ) , 'error' );
			return FD::getInstance( 'View' , 'Privacy' , false )->display();
		}

		FD::getInstance( 'Info' )->set( JText::_( 'COM_EASYSOCIAL_PRIVACY_UPDATED' ) , 'success' );
		return FD::getInstance( 'View' , 'Privacy' , false )->display();

		exit;
	}



	/**
	 * to update privacy on an object by current logged in user
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */
	public function update()
	{
		FD::checkToken();

		FD::requireLogin();

		$my = FD::user();

		// get data from form post.
		$uid 		= JRequest::getInt( 'uid' );
		$utype 		= JRequest::getVar( 'utype' );
		$value		= JRequest::getVar( 'value' );
		$pid		= JRequest::getVar( 'pid' );
		$customIds 	= JRequest::getVar( 'custom', '' );
		$streamid 	= JRequest::getVar( 'streamid', '' );
		$userid 	= JRequest::getVar( 'userid', '' );
		$pitemid 	= JRequest::getVar( 'pitemid', '' );


		$view 	= FD::view( 'Privacy', false );

		// If id is invalid, throw an error.
		if (!$uid) {
			$view->setError( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) );
			return $view->call( __FUNCTION__ );
		}

		// we need to check if userid is belong to this privacy item or not.
		$oUserId = $my->id;

		if($userid && $userid != $my->id) {
			if ($streamid) {
				$streamTbl = FD::table('Stream');
				$streamTbl->load($streamid);

				$oUserId = $streamTbl->actor_id;
			} else if ($pitemid) {
				$pItemTbl = FD::table('PrivacyItems');
				$pItemTbl->load($pitemid);

				$oUserId = $pItemTbl->user_id;
			}
		}

		$model = FD::model( 'Privacy' );
		$state = $model->update( $oUserId, $pid, $uid, $utype, $value, $customIds );

		// If there's an error, log this down.
		if (!$state) {
			$view->setError( $model->getError() );

			return $view->call( __FUNCTION__ );
		}

		// lets check if there is stream id presented or not. if yes, means we need to update
		// privacy access in stream too.
		if ($streamid) {
			$access = FD::privacy()->toValue( $value );

			$stream = FD::stream();
			$stream->updateAccess( $streamid, $access, $customIds);
		}

		return $view->call( __FUNCTION__ );

	}

	public function browse()
	{
		FD::checkToken();
		FD::requireLogin();

		$pid 		= JRequest::getInt( 'pid', 1);
		$pItemId 	= JRequest::getInt( 'pItemId', 0);
		$userIds 	= JRequest::getString( 'userIds', '');

		$users = array();
		if( $pItemId )
		{
			$model = FD::model( 'Privacy' );
			$users = $model->getPrivacyCustom( $pItemId, 'item' );
		}
		else if( empty( $pItemId ) && !empty( $userIds ) )
		{
			$tmpData = explode( ',', $userIds );
			foreach( $tmpData as $data )
			{
				if( !empty( $data ) )
				{
					$user = new stdClass();
					$user->user_id = $data;

					$users[] = $user;
				}
			}
		}

		$view 	= FD::view( 'Privacy', false );
		return $view->call( __FUNCTION__, $users );
	}

}
