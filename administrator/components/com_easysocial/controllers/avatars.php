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

// Import main controller
FD::import( 'admin:/controllers/controller' );

/**
 * Default Avatar controller
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasySocialControllerAvatars extends EasySocialController
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Sets an avatar as the default avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setDefault()
	{
		// Check for request forgeries.
		FD::checkToken();

		$id 	= JRequest::getInt( 'id' );
		$view 	= $this->getCurrentView();

		$avatar = FD::table( 'DefaultAvatar' );
		$avatar->load( $id );

		if( !$id || !$avatar->id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATAR_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$avatar->setDefault();

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATAR_SET_DEFAULT_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Delete's an avatar from the system.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function delete()
	{
		// Check for request forgeries
		FD::checkToken();

		$id 	= JRequest::getInt( 'id' );

		// Get the current view
		$view	= $this->getCurrentView();

		$avatar 	= FD::table( 'DefaultAvatar' );

		// If avatar doesn't exist, break and throw errors immediately.
		if( !$avatar->load( $id ) || !$id )
		{
			// Throw error here.
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATAR_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		if( !$avatar->delete() )
		{
			// Throw error here.
			$view->setMessage( $avatar->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_DEFAULT_AVATAR_DELETED_SUCCESSFULLY' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}
}
