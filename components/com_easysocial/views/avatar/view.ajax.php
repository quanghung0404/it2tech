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

class EasySocialViewAvatar extends EasySocialSiteView
{
	/**
	 * Displays a dialog to allow user to upload their profile picture.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload()
	{
		// Only allow logged in users
		FD::requireLogin();

		// Load up the ajax library
		$ajax 	= FD::ajax();

		// Get the unique item id
		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getCmd( 'type' );


		$theme	= FD::themes();
		$theme->set( 'uid' , $uid );
		$theme->set( 'type' , $type );

		$output	= $theme->output( 'site/avatar/upload' );

		return $ajax->resolve( $output );
	}

	/**
	 * Displays the dialog to allow user to crop avatar
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function crop()
	{
		// Require the user to be logged in
		FD::requireLogin();

		// Load up the ajax library
		$ajax = FD::ajax();

		// Get the unique object.
		$uid = JRequest::getInt('uid');
		$type = JRequest::getCmd('type');

		// Get photo id
		$id = JRequest::getInt('id');
		$table = FD::table('Photo');
		$table->load($id);

		// Get redirect url after avatar is created
		$redirectUrl = ESR::referer();

		// Load up the library
		$lib = FD::photo($table->uid , $table->type, $table);

		if(!$table->id) {
			return $this->deleted($lib);
		}

		// Check if the user is really allowed to upload avatar
		if(!$lib->canUseAvatar()) {
			return $ajax->reject();
		}

		$redirect = JRequest::getInt('redirect', 1);

		$theme = FD::themes();

		$theme->set('uid', $uid);
		$theme->set('type', $type);
		$theme->set('redirectUrl', $redirectUrl);
		$theme->set('photo', $lib->data);
		$theme->set('redirect', $redirect);

		$output = $theme->output('site/avatar/crop');

		return $ajax->resolve($output);
	}
}
