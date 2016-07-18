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
defined('_JEXEC') or die('Unauthorized Access');

FD::import('site:/views/views');

class EasySocialViewUploader extends EasySocialSiteView
{
	/**
	 * Processes after a file is uploaded on the site
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function uploadTemporary($uploader = null)
	{
		$json = FD::json();

		// If there was an error uploading,
		// return error message.
		if ($this->hasErrors()) {
			return $json->send($this->getMessage());
		}

		$response = new stdClass();
		$response->id = $uploader->id;
		$response->preview = $uploader->getPermalink();

		return $json->send($response);
	}

	/**
	 * Responsible to output upload response.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function upload()
	{
		$json = FD::json();

		// If there was an error uploading,
		// return error message.
		if ($this->hasErrors()) {
			$json->send($this->getMessage());
		}

		// TODO: Huh is this used? This is an empty object.
		$response = new stdClass();

		$json->send($response);
	}
}
