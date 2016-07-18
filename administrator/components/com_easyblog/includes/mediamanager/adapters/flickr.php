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

class EasyBlogMediaManagerFlickrSource extends EasyBlog
{
	private $oauth = null;

	public function __construct()
	{
		parent::__construct();

		// Test if the user is already associated with Flickr
		$this->oauth = EB::table('OAuth');
		$this->oauth->loadByUser($this->my->id, EBLOG_OAUTH_FLICKR);
	}

	/**
	 * Retrieves a list of images the user has on Flickr
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	/**
	 * Return a list of images that this user has.
	 */
	public function getItems($uri)
	{
		// If account is already associated, we just need to get the photos from their Flickr account.
		$client = EB::oauth()->getClient(EBLOG_OAUTH_FLICKR);

		$client->setAccess($this->oauth->access_token);
		$client->setParams($this->oauth->params);

		// Get list of photos from Flickr
		$result = $client->getPhotos();

		if (!$result) {
			return $result;
		}

		// Let's build the photos URL now.
		$items = EBMM::filegroup();

		foreach ($result as $row) {
			$items['image'][] = $this->decorate($row, '');
		}

		// Folder
		$folder = new stdClass();
		$folder->place = 'flickr';
		$folder->title = JText::_('COM_EASYBLOG_MM_FLICKR');
		$folder->url = 'flickr';
		$folder->uri = 'flickr';
		$folder->key = 'flickr';
		$folder->type = 'folder';
		$folder->icon = EBMM::$icons['place/flickr'];
		$folder->root = true;
		$folder->scantime = 0;
		$folder->contents = $items;
		$folder->total = count($items['image']);
		
		return $folder;
	}

	/**
	 * Returns the information of a photo object.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function getItem($uri)
	{
		// Get the photo id
		$photoId = EBMM::getFilename($uri);

		// If the account is already associated, we just need to get the photos from Flickr
		$client = EB::oauth()->getClient(EBLOG_OAUTH_FLICKR);
		$client->setAccess($this->oauth->access_token);
		$client->setParams($this->oauth->params);

		// Get the photo item from flickr
		$result = $client->getPhoto($photoId);

		// Decorate the photo object for MM
		$photo = $this->decorate($result, $uri);

		return $photo;
	}

	/**
	 * Given a raw format of a flickr object and convert it into a media manager object.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function decorate(&$item, $uri)
	{
		$obj = new stdClass();
		$obj->uri = $uri;
		$obj->place = 'flickr';
		$obj->title = $item->title;

		// Url should be the original source
		$obj->url = $item->sizes['Original']->source;

		$obj->key = EBMM::getKey('flickr/' . $item->id);
		$obj->type = 'image';
		$obj->icon = EBMM::getIcon('image');
		$obj->modified = $item->dateupload;
		$obj->size = 0;

		$obj->thumbnail = $item->sizes['Thumbnail']->source;
		$obj->preview = $item->sizes['Medium']->source;

		$obj->variations = array();

		foreach ($item->sizes as $size) {

			$key = 'system/' . strtolower($size->title);

			// Create variation
			$variation = new stdClass();
			$variation->key  = $key;
			$variation->name = $size->title;
			$variation->type = 'system';
			$variation->url  = $size->source;
			$variation->width  = $size->width;
			$variation->height = $size->height;
			$variation->size = 0;

			$obj->variations[$key] = $variation;
		}

		return $obj;
	}

}
