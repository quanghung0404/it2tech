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

FD::import('admin:/tables/table');

class SocialTableLinkImage extends SocialTable
{
	public $id = null;
	public $source_url = null;
	public $internal_url = null;
	public $storage = 'joomla';

	public function __construct($db)
	{
		parent::__construct('#__social_links_images', 'id' , $db);
	}

	/**
	 * Retrieves the absolute path to the cached image
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getAbsolutePath()
	{
		$config = FD::config();

		// Get the container location
		$container = FD::cleanPath($config->get('links.cache.location'));

		// Relative path to the item
		$path = JPATH_ROOT . '/' . $container . '/' . $this->internal_url;

		return $path;
	}


	/**
	 * Retrieves the relative path to the cached image
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getRelativePath()
	{
		$config = FD::config();

		// Get the container location
		$container = FD::cleanPath($config->get('links.cache.location'));

		// Relative path to the item
		$path = '/' . $container . '/' . $this->internal_url;

		return $path;
	}

	/**
	 * Retrieves the uri for this image
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getUrl()
	{
		$config = FD::config();

		// Get the container location
		$container = FD::cleanPath($config->get('links.cache.location'));

		// Relative path to the item
		$relativePath = $this->getRelativePath();

		// Default base url
		$url = rtrim(JURI::root(), '/') . $relativePath;

		// Get the storage type for cached images for links
		if ($this->storage != 'joomla') {
			$storage = FD::storage($this->storage);
			$url = $storage->getPermalink($relativePath);
		}

		return $url;
	}
}
