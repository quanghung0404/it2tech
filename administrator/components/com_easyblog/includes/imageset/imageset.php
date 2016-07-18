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

// Let's test if the image size exists
jimport('joomla.filesystem.file');

class EasyBlogImageset extends EasyBlog
{
	/**
	 * Sizes available for each image set.
	 * @var array
	 */
	public $sizes = array('large', 'medium', 'thumbnail', 'small', 'icon');

	/**
	 * Allows initialization of a single variation
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function create($sizes, $imagePath)
	{
		// Ensure that the image really exists on the site.
		$exists = JFile::exists($imagePath);

		if (!$exists) {
			return EB::exception('Invalid file path provided to generate imagesets.', EASYBLOG_MSG_ERROR);
		}

		// Get the original image resource
		$original = EB::simpleimage();
		$original->load($imagePath);

		// Get the original width and height
		$originalWidth = $original->getWidth();
		$originalHeight = $original->getHeight();

		$fileName = basename($imagePath);
		$folder = dirname($imagePath);

		// Get the meta of the original image
		$meta = getimagesize($imagePath);

		// Clone the original image to avoid original image width and height being modified
		$image = clone($original);

		$data = new stdClass();
		$data->width = $this->config->get('main_image_' . $size . '_width');
		$data->height = $this->config->get('main_image_' . $size . '_height');
		$data->quality = $this->config->get('main_image_' . $size . '_quality');
		$data->path = $folder . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $size . '_' . $fileName;

		// Everything should be resized using "resize within" method
		$resizeMode = 'within';

		// Resize the image
		$image->$resizeMode($data->width, $data->height);

		// Save the image
		$image->write($data->path, $data->quality);

		unset($image);

		return $data;
	}

	/**
	 * Initialize dimensions available
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function initDimensions($imagePath, $size = null)
	{
		$images = array();

		// Ensure that the image really exists on the site.
		$exists = JFile::exists($imagePath);

		if (!$exists) {
			return EB::exception('Invalid file path provided to generate imagesets.', EASYBLOG_MSG_ERROR);
		}

		// Get the original image resource
		$original = EB::simpleimage();
		$original->load($imagePath);

		// Get the original image file name
		$fileName = basename($imagePath);

		// Get the original image containing folder
		$folder = dirname($imagePath);

		// Determines if we should generate a single size or multiple sizes
		$sizes = $this->sizes;

		if (!is_null($size)) {
			$sizes = array($size);
		}

		// Determines if there's a specific size to generate
		foreach ($sizes as $size) {

			// Clone the original image to avoid original image width and height being modified
			$image = clone($original);

			$data = new stdClass();
			$data->width = $this->config->get('main_image_' . $size . '_width');
			$data->height = $this->config->get('main_image_' . $size . '_height');
			$data->quality = $this->config->get('main_image_' . $size . '_quality');
			$data->path = $folder . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_' . $size . '_' . $fileName;

			// Resize the image
			$image->resizeWithin($data->width, $data->height);

			// Save the image
			$image->write($data->path, $data->quality);

			unset($image);

			$images[$size] = $data;
		}

		unset($original);

		return $images;
	}
}
