<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/controller.php');

class EasyBlogControllerQuickpost extends EasyBlogController
{
	public function __construct($options = array())
	{
		parent::__construct($options);
	}

	/**
	 * Processes an uploaded photo
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function uploadPhoto()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user user must be logged into the site
		EB::requireLogin();

		// Get the file from the request
		$file = $this->input->files->get('image');

		// Copy the temporary file to our own temporary folder
		$fileName = md5(EB::date()->toSql()) . '.png';
		$uri = JURI::root() . 'tmp/' . $fileName;
		$tmp = JPATH_ROOT . '/tmp/' . $fileName;

		JFile::copy($file['tmp_name'], $tmp);

		$result = new stdClass();
		$result->file = $fileName;
		$result->url = $uri;

		echo json_encode($result);exit;
	}

	/**
	 * Saves an uploaded webcam picture
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function saveWebcam()
	{
		// Check for request forgeries
		EB::checkToken();

		// Ensure that the user user must be logged into the site
		EB::requireLogin();

		$image = $this->input->get('image', '', 'default');
		$image = imagecreatefrompng($image);

		ob_start();
		imagepng($image, null, 9);
		$contents	= ob_get_contents();
		ob_end_clean();

		// Store this in a temporary location
		$file = md5(EB::date()->toSql()) . '.png';
		$tmp = JPATH_ROOT . '/tmp/' . $file;
		$uri = JURI::root() . 'tmp/' . $file;

		JFile::write($tmp, $contents);

		$result = new stdClass();
		$result->file = $file;
		$result->url = $uri;

		$this->ajax->resolve($result);
	}
}
