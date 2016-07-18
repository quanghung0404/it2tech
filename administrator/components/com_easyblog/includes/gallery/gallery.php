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

class EasyBlogGallery extends EasyBlog
{
	/**
	 * Search and removes the gallery tag from the content.
	 *
	 * @access	public
	 * @param	string	$content	The content to search on.
	 *
	 */
	public function strip( $content )
	{
		$pattern	= '/\[embed=gallery\].*?\[\/embed\]/';

		return preg_replace( $pattern , '' , $content );
	}

	public function removeGalleryCodes($text)
	{
		$pattern	= '#<div class="easyblog-placeholder-gallery"(.*)</div>#is';

		return preg_replace( $pattern , '' , $text );
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		$post->text = $this->strip($post->text);

		// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
		// @2.0: <input class="easyblog-gallery"
		// @3.5: {ebgallery:'name'}
		// $post->text = $this->removeGalleryCodes($post->text);
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format(EasyBlogPost &$blog)
	{
		$blog->intro = $this->process($blog->intro);
		$blog->content = $this->process($blog->content);
	}

	/**
	 * Retrieves a list of galleries associated with the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($content, $userId = '')
	{
		$pattern = '/\[embed=gallery\](.*)\[\/embed\]/i';
		$galleries = array();

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!$matches) {
			return $galleries;
		}

		// Get the image block so that we can construct it
		$block = EB::blocks()->getBlockByType('image');

		$totalColumns = 4;
		$columns = array();

		for ($i = 0; $i < $totalColumns; $i++) {
			$columns[$i] = array();
		}

		foreach ($matches as $match) {

			// The full text of the matched content
			list($text, $json) = $match;

			// Parse the raw json
			$gallery = json_decode($json);

			if ($gallery === false) {
				continue;
			}

			$images = $this->getImages($gallery);

			// We need to construct a block for each of these items
			for ($i = 0; $i < count($images); $i++) {
				$image = $block->getHtml($images[$i]);

				$column = $i - (floor($i / $totalColumns) * $totalColumns);


				$columns[$column][] = $image;
			}
		}

		$theme = EB::template();
		$theme->set('columns', $columns);

		$output = $theme->output('site/blogs/latest/blog.gallery');

		return $output;
	}

	/**
	 * Given a json string, locate all images in the respective folder
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImages($gallery)
	{
		// For posts created in 5.x with legacy editor
		if (isset($gallery->uri)) {
			$storage = EB::mediamanager()->getPath($gallery->uri);
			$url = EB::mediamanager()->getUrl($gallery->uri);
		}

		// For posts created in 3.x
		if (!isset($gallery->place) && !isset($gallery->uri)) {
			$storage = rtrim($this->config->get('main_image_path'), '/');
			$url = rtrim(JURI::root(), '/') . '/' . $storage . '/' . $userId . $gallery->path;
		}

		// For posts created in 3.x
		if (isset($gallery->place) && $gallery->place) {
			$folder = trim($gallery->file, '/\\');
			$storage = '';

			if ($gallery->place == 'shared') {
				$storage = JPATH_ROOT . '/' . $this->config->get('main_shared_path') . '/' . $folder;
				$url = rtrim(JURI::root(), '/') . '/' . $this->config->get('main_shared_path') . '/' . $folder;
			} else {

				// Get the user id
				$parts = explode(':', $gallery->place);

				$storage = JPATH_ROOT . '/' . rtrim($this->config->get('main_image_path'), '/') . '/' . $parts[1] . '/' . $folder;
				$url = rtrim(JURI::root(), '/') . '/' . rtrim($this->config->get('main_image_path'), '/') . '/' . $parts[1] . '/' . $folder;
			}
		}

		// Replace all / and \ from storage to the directory separator
		$storage = str_ireplace(array('\\', '/'), '/', $storage);

		// Let's test if the folder really exists.
		if (!JFolder::exists($storage)) {
			return false;
		}

		// Do not include image variations in the list.
		$exclusion = array(EBLOG_MEDIA_THUMBNAIL_PREFIX , EBLOG_BLOG_IMAGE_PREFIX . '_*', EBLOG_USER_VARIATION_PREFIX , EBLOG_SYSTEM_VARIATION_PREFIX );

		// Only allow specific file types here.
		$allowed = EBLOG_GALLERY_EXTENSION;

		// Get a list of images within this folder.
		$items = JFolder::files($storage, $allowed, false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX','index.html') , $exclusion);

		$images = array();

		if ($items) {
			foreach ($items as $item) {
				$block = new stdClass();

				$block->nested = true;
				$block->isolated = true;

				$block->data = new stdClass();
				$block->data->width = "100%";
				$block->data->element_width = "100%";

				// Get the meta about the image
				$imageMeta = $this->getImageMeta($storage, $url, $item);

				$block->data->url = $imageMeta->thumbnail;
				$block->data->popup_url = $imageMeta->original;


				$images[] = $block;
			}
		}

		return $images;
	}

	/**
	 * Retrieves the image data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImageMeta($storage, $storageUrl, $image)
	{
		// Original image file
		$original = $storage . '/' . $image;
		$originalUrl = $storageUrl . '/' . $image;

		// Get the thumbnail path
		$thumbnail = $storage . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_large_' . $image;
		$thumbnailUrl = $storageUrl . '/' . EBLOG_SYSTEM_VARIATION_PREFIX . '_large_' . $image;

		// The original must exists
		if (!JFile::exists($original)) {
			return false;
		}

		// If the thumbnail doesn't exist, for whatever reasons, just use the original one.
		if (!JFile::exists($thumbnail)) {
			$thumbnailUrl = $originalUrl;
		}

		$data = new stdClass();
		$data->original = $originalUrl;
		$data->thumbnail = $thumbnailUrl;

		return $data;
	}

	/**
	 * Retrieves a list of galleries associated with the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process($content, $userId = '')
	{
		$pattern = '/\[embed=gallery\](.*)\[\/embed\]/i';
		$galleries = array();

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!$matches) {
			return $content;
		}

		// Get the image block so that we can construct it
		$block = EB::blocks()->getBlockByType('image');

		foreach ($matches as $match) {

			$totalColumns = 4;
			$columns = array();

			for ($i = 0; $i < $totalColumns; $i++) {
				$columns[$i] = array();
			}

			// The full text of the matched content
			list($text, $json) = $match;

			// Ensure that the property is valid
			$json = str_ireplace('file:', '"file":', $json);
			$json = str_ireplace('place:', '"place":', $json);

			// Parse the raw json
			$gallery = json_decode($json);

			if ($gallery === null) {
				continue;
			}

			$images = $this->getImages($gallery);

			// We need to construct a block for each of these items
			for ($i = 0; $i < count($images); $i++) {
				
				$image = $block->getHtml($images[$i]);

				$column = $i - (floor($i / $totalColumns) * $totalColumns);

				$columns[$column][] = $image;
			}

			$theme = EB::template();
			$theme->set('columns', $columns);

			$output = $theme->output('site/blogs/latest/blog.gallery');

			$content = str_ireplace($match, $output, $content);
		}

		return $content;
	}

}
