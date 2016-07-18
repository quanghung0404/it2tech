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

class EasySocialViewCover extends EasySocialSiteView
{
	/**
	 * Displays the upload cover dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uploadDialog()
	{
		// Only logged in users allowed
		FD::requireLogin();

		$uid = $this->input->get('uid', 0, 'int');
		$type = $this->input->get('type', '', 'cmd');

		$theme	= FD::themes();
		$theme->set('uid', $uid);
		$theme->set('type', $type);

		$output = $theme->output('site/profile/cover.upload');

		return $this->ajax->resolve($output);
	}

	/**
	 * Post processing after a photo cover is uploaded on the site
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTablePhoto
	 */
	public function upload( $photo = null )
	{
		// Load up the ajax library
		$ajax = FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the photo in json format
		$data = $photo->export();

		return $ajax->resolve($data);
	}

	/**
	 * Post processing after creating a cover from a photo.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create( $cover = null )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Format the output
		$result 		= new stdClass();

		$result->url 		= $cover->getSource();
		$result->position 	= $cover->getPosition();
		$result->x 			= $cover->x;
		$result->y 			= $cover->y;

		return $ajax->resolve( $result );
	}


	/**
	 * Post process after a cover is deleted
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function remove()
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		$cover = FD::table('Cover');
		$cover->type = $this->input->getCmd('type');

		return $this->ajax->resolve($cover->getSource());
	}
}
