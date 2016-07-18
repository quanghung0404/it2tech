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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewPrivacy extends EasySocialSiteView
{
	/**
	 * Responsible to output the single stream layout.
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	public function display()
	{
		// Unauthorized users should not be allowed to access this page.
		FD::requireLogin();

		// Check for user profile completeness
		FD::checkCompleteProfile();

		// Get the current logged in user.
		$user			= FD::user();

		// Get user's privacy
		$privacy	= FD::get( 'Privacy' )->getUserPrivacy( $user->id );

		$this->set( 'user'			, $user );
		$this->set( 'privacy'		, $privacy );


		echo parent::display( 'site/privacy/default' );
	}
}
