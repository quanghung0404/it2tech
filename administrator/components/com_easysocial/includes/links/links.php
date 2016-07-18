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

// Import the required file and folder classes.
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Links library
 *
 * @since	1.2.11
 * @author	Mark Lee <mark@stackideas.com>
 */
class SocialLinks extends EasySocial
{
	public function __construct()
	{
		$this->config = FD::config();
	}

	public static function factory()
	{
		return new self();
	}

	/**
	 * Fixes oembed links
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function fixOembedLinks($oembed)
	{
		// Fix video issues with youtube when site is on https
		if (isset($oembed->provider_url) && $oembed->provider_url == 'http://www.youtube.com/') {
			$oembed->html = JString::str_ireplace('http://', 'https://', $oembed->html);
			$oembed->thumbnail = str_ireplace('http://', 'https://', $oembed->thumbnail);
		}

		return $oembed;
	}

	/**
	 * Retrieves the correct image path
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getImageLink($assets, $params)
	{
		$uri = JURI::getInstance();

		// Get the image file
		$image = $assets->get('image');
		$cachePath = ltrim($this->config->get('links.cache.location'), '/');

		// @since 1.3.8
		// This block of code should be removed later.
		// FIX Older images where 'cached' state is not stored.
		// Check if the image string contains the cached storage path
		if (!$assets->get('cached') && (stristr($image, '/media/com_easysocial/cache') !== false)) {
			$assets->set('cached', true);
		}

		// Dirty way of checking 
		// If the image is cached, we need to get the correct path
		if ($assets->get('cached')) {

			// First we try to load the image from the image link table
			$linkImage = FD::table('LinkImage');
			$exists = $linkImage->load(array('internal_url' => $image));

			if ($exists) {
				$image = $linkImage->getUrl();
			} else {
				$fileName = basename($image);
				$image = rtrim(JURI::root(), '/') . '/' . $cachePath . '/' . $fileName;
			}
		}

		// If necessary, feed in our own proxy to avoid http over https issues.
		if ($params->get('stream_link_proxy', false) && ($oembed || $assets->get('image')) && $uri->getScheme() == 'https') {

			// Check if there are any http links
			if (isset($oembed->thumbnail) && $oembed->thumbnail && stristr($oembed->thumbnail, 'http://') !== false) {
				$oembed->thumbnail = FD::proxy($oembed->thumbnail);
			}

			if ($image && stristr($image, 'http://') !== false) {
				$image = FD::proxy($image);
			}
		}

		return $image;
	}

	/**
	 * Stores a given image link into the local cache
	 *
	 * @since	1.2.11
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cache($imageLink)
	{
		// Check if settings is enabled
		if (!$this->config->get('links.cache.images')) {
			return false;
		}

		// Try to load any existing cached image from the db
		$linkImage = FD::table('LinkImage');
		$exists = $linkImage->load(array('source_url' => $imageLink));

		// If this already exists, skip this altogether
		if ($exists) {
			return $linkImage->internal_url;
		}

		// Generate a unique name for this file
		$fileName = md5($imageLink) . '.png';

		// Get the storage path
		$container = FD::cleanPath($this->config->get('links.cache.location'));
		$storage = JPATH_ROOT . '/' . $container . '/' . $fileName;

		// Check if the file already exists
		$exists = JFile::exists($storage);

		// If the file is already cached, delete it
		if ($exists) {
			JFile::delete($storage);
		}

		// Crawl the image now.
		$connector = FD::get('Connector');
		$connector->addUrl($imageLink);
		$connector->connect();

		// Get the result and parse them.
		$contents = $connector->getResult($imageLink);

		// Store the file to a temporary directory first
		$tmpFile = SOCIAL_TMP . '/' . $fileName;
		JFile::write($tmpFile, $contents);

		// Load the image now
		$image = FD::image();
		$image->load($tmpFile);

		// Ensure that image is valid
		if (!$image->isValid()) {
			JFile::delete($tmpFile);
			return false;
		}

		// Delete the temporary file.
		JFile::delete($tmpFile);

		// Unset the image now since we don't want to use asido to resize
		unset($image);

		// Store the file now into our cache storage.
		JFile::write($storage, $contents);

		$linkImage->source_url = $imageLink;
		$linkImage->internal_url = $fileName;
		$linkImage->store();
		
		return $fileName;
	}
}
