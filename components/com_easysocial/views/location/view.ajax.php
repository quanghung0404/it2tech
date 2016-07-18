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

FD::import( 'site:/views/views' );

class EasySocialViewLocation extends EasySocialSiteView
{

	/**
	 * Responsible to output JSON encoded data.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableConversation			$conversation	The conversation table.
	 * @param	SocialTableConversationMessage	$messageTable	The message table.
	 * @param	string							$token			If form contains an uploder, a token is necessary (optional).
	 *
	 * @return	json
	 */
	public function delete()
	{
		// We know for the fact that guests can never access conversations.
		FD::requireLogin();

		$ajax 	= FD::getInstance( 'Ajax' );

		// Try to see if there's any error message.
		$errors	= $this->getErrors();

		if( !$errors )
		{
			return $ajax->success();
		}

		// @TODO: Error handling codes.
	}

	public function unarchive()
	{
		$errors	= $this->getErrors();

		// @TODO: Process errors here.
		if( $errors )
		{
		}

		FD::get( 'AJAX' )->success();
	}

	public function archive()
	{
		$errors	= $this->getErrors();

		// @TODO: Process errors here.
		if( $errors )
		{
		}

		FD::getInstance( 'AJAX' )->success();
	}
}
