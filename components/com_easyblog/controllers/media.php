<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerMedia extends EasyBlogController
{
	/**
	 * Handles uploads from media manager.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function upload()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		// Only allowed users who are allowed to upload images
		if (!$this->acl->get('upload_image')) {
			$this->output(EB::exception('COM_EASYBLOG_NOT_ALLOWED', EASYBLOG_MSG_ERROR));
		}

		// Load up media manager
		$mm = EB::mediamanager();

		// Get uri
		$key = $this->input->getRaw('key');

		// Get the target folder
		$placeId = EBMM::getUri($key);

		// Get the file input
		$file = $this->input->files->get('file');

		// Check if the file is really allowed to be uploaded to the site.
		$state = EB::image()->canUploadFile($file);

		if ($state instanceof Exception) {
			return $this->output($state);
		}

		// MM should check if the user really has access to upload to the target folder
		$allowed = EBMM::hasAccess($placeId);

		if ($allowed instanceof Exception) {
			return $this->output($allowed);
		}

		// Check the image name is it got contain space, if yes need to replace to '-'
		$fileName = $file['name'];
		$file['name'] = str_replace(' ', '-', $fileName);

		// Upload the file now
		$file = $mm->upload($file, $placeId);

		// Response object is intended to also include
		// other properties like status message and status code.
		// Right now it only inclues the media item.
		$response = new stdClass();
		$response->media = EBMM::getMedia($file->uri);

		return $this->output($response);
	}

	/**
	 * Generates the output for uploader
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function output($result)
	{
		// If this is an exception, port it to an array first.
		if ($result instanceof Exception) {

			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			// header('Content-type: text/x-json; UTF-8');
			echo json_encode($result->toArray());
			exit;
		}

		header('Content-type: text/x-json; UTF-8');
		echo json_encode($result, JSON_HEX_TAG);
		exit;
	}

}
