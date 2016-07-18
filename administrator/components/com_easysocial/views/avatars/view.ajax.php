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

// Include main view.
FD::import( 'admin:/views/views' );

/**
 * Default Avatar ajax view
 *
 * @since	1.0
 * @author	Mark Lee <mark@stackideas.com>
 */
class EasySocialViewAvatars extends EasySocialAdminView
{
	/**
	 * Invoked when a user tries to unpublish an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function unpublish()
	{
		$ajax 	= FD::getInstance( 'Ajax' );


		return $ajax->success();
	}

	/**
	 * Invoked when a user tries to publish an avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function publish()
	{
		$ajax 	= FD::getInstance( 'Ajax' );


		return $ajax->success();
	}

	/**
	 * Invoked when a user tries to set an avatar as the default avatar.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function setDefault()
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $this->getMessage() );
	}

	/**
	 * Invoked when a user tries to delete avatars
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function delete()
	{
		$ajax 	= FD::ajax();

		return $ajax->resolve( $this->getMessage() );
	}
}
