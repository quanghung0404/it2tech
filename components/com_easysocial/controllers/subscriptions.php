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

class EasySocialControllerSubscriptions extends EasySocialController
{

	/**
	 * subscription toggle.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function toggle()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Ensure that the user needs to be logged in.
		FD::requireLogin();

		$uid  	= JRequest::getInt('uid');
		$type 	= JRequest::getVar('type');
		$group  = JRequest::getVar('group', SOCIAL_APPS_GROUP_USER);
		$notify = JRequest::getVar('notify', '0');

		$my 		= FD::user();
		$view 		= FD::view( 'Subscriptions' , false );
		$subscribe  = FD::get( 'Subscriptions');

		$isFollowed	= $subscribe->isFollowing( $uid, $type, $group, $my->id );

		$verb		= $isFollowed ? 'unfollow' : 'follow';
		$state		= '';

		if( $isFollowed )
		{
			// unsubscribe user.
			$state = $subscribe->unfollow( $uid, $type, $group, $my->id );
		}
		else
		{
			$state = $subscribe->follow( $uid, $type, $group, $my->id, $notify );
		}

		if (!$state) {
			// Set the view with error
			$view->setMessage( $subscribe->getError() , SOCIAL_MSG_ERROR );

			return $view->call( __FUNCTION__ , $verb );
		}

		return $view->call( __FUNCTION__ , $verb );

	}

	public function remove()
	{
		// Check for valid token
		FD::checkToken();

		// Ensure that the user is logged in
		FD::requireLogin();

		$sId		= JRequest::getVar( 'id' );

		if( empty($sId) )
		{
			FD::getInstance( 'View' , 'Subscriptions' , false )->setErrors( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) );
			return FD::getInstance( 'View' , 'Subscriptions' , false )->remove();
		}


		$state 		= FD::get('Subscriptions')->remove($sId);

		if( ! $state )
		{
			FD::getInstance( 'View' , 'Subscriptions' , false )->setErrors( JText::_( 'COM_EASYSOCIAL_SUBSCRIPTION_FAILED_TO_UNSUBSCRIBE' ) );

			return FD::getInstance( 'View' , 'Subscriptions' , false )->remove();
		}

		return FD::getInstance( 'View' , 'Subscriptions' , false )->remove();

	}

	private function formKeys( $element , $group )
	{
		return $element . '.' . $group;
	}

}
