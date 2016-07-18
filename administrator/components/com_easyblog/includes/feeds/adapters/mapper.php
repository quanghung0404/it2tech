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

class EasyBlogFeedAdapterMapper extends EasyBlog
{
	/**
	 * Maps the feed item with a post item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function map(EasyBlogPost &$post, &$item, EasyBlogTableFeed &$feed, &$params)
	{
		// Set the frontpage status
		$post->frontpage = $feed->item_frontpage;

		// Set the category
		$post->category_id = $feed->item_category;
		
		// Cheap fix
		$post->categories = array($post->category_id);

		// Set the author
		$post->created_by = $feed->item_creator;

		// Determines if comments is allowed
		$post->allowcomment = $this->config->get('main_comment', true);

		// Determines if subscription is allowed
		$post->subscription = $this->config->get('main_subscription', true);

		// The blog post should always be site wide
		$post->source_id = 0;
		$post->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;

		// If item_team is not empty, change the source_type
		if (!empty($feed->item_team)) {
			$post->source_id = $feed->item_team;
			$post->source_type = EASYBLOG_POST_SOURCE_TEAM;
		}

		// Set the blog post's language
		$post->language = $feed->language;

		// Set any copyright text
		$post->copyrights = $params->get('copyrights', '');

		// Get the offset
		$offset = $item->get_date('Z');
		$date = $item->get_date('U');

		// Get the gmt time
		$dateTime = $date - $offset;

		$dateTime = date('Y-m-d H:i:s', $dateTime);

		// Some of the feed does not show the created date hence it will return a fix 1st January 1970
		if (!$date) {
			// If null, just get the current date
			$dateTime = EB::date()->toMySQL();
		}

		// Set the creation date to the current date
		$post->created = $dateTime;
		$post->modified = $dateTime;
		$post->publish_up = $dateTime;

		// Determines if the blog should be new
		// since this is new item import, we always set this as new.
		$post->isnew = true;

		// Determines if the post published option is pending
		if ($feed->item_published == EASYBLOG_POST_PENDING) {
			$post->published = EASYBLOG_POST_PENDING;
		} else {
			// Set the publishing status
			$post->published = $feed->item_published != EASYBLOG_POST_UNPUBLISHED ? EASYBLOG_POST_PUBLISHED: EASYBLOG_POST_UNPUBLISHED;
		}

		// Bind the title
		$post->title = @$item->get_title();

		// If the title is empty, we need to intelligently get
		if (!$post->title) {
			$post->title = $this->getTitleFromLink();
		}

		// Ensure that there are no html entities
		$post->title = EB::string()->unhtmlentities($post->title);
	}

	/**
	 * Maps the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function mapContent(EasyBlogPost &$post, &$item, EasyBlogTableFeed &$feed, &$params)
	{
		// Initial content
		$contents = '';

		// Try to fetch the contents remotely
		if ($feed->item_get_fulltext) {
			$url = urldecode(@$item->get_link());
			$url = str_ireplace('&amp;', '&', $url);

			$connector = EB::connector();
			$connector->addUrl($url);
			$connector->execute();

			// Fetched contents from the site
			$tmp = $connector->getResult($url);

			// Clean up fetched contents by ensuring that there's no weird text before the html declaration
			$pattern = '/(.*?)<html/is';
			$replace = '<html';
			$tmp = preg_replace($pattern, $replace, $tmp, 1);

			if (!empty($tmp)) {

				// Enforce utf-8 encoding on the content since Joomla always uses utf-8
				$tmp = EB::string()->forceUTF8($tmp);

				// Load up the readability lib
				$readability = EB::readability($tmp);

				$readability->debug = false;
				$readability->convertLinksToFootnotes = false;

				$result = $readability->init();

				if ($result) {
					$output = $readability->getContent()->innerHTML;

					// Tidy up the contents
					$output = $this->tidyContent($output);

					$uri = JURI::getInstance();
					$scheme = $uri->toString(array('scheme'));
					$scheme = str_replace('://', ':', $scheme);

					// replace the image source to proper format so that feed reader can view the image correctly.
					$output = str_replace('src="//', 'src="' . $scheme . '//', $output);
					$output = str_replace('href="//', 'href="' . $scheme . '//', $output);					

					if (stristr(html_entity_decode($output), '<!DOCTYPE html') === false) {
					    $contents = $output;
					    $contents = $this->convertRelativeToAbsoluteLinks($contents, @$item->get_link());
					}
				}
			}
		}

		// Get the content of the item
		if (!$contents) {
			$contents = @$item->get_content();
		}

	    // Default allowed html codes
	    $allowed = '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>';

		// Remove disallowed tags
		$contents = strip_tags($contents, $params->get('allowed', $allowed));

		// Append original source link into article if necessary
		if ($params->get( 'sourceLinks')) {
			$contents .= '<div><a href="' . @$item->get_link() . '" target="_blank">' . JText::_( 'COM_EASYBLOG_FEEDS_ORIGINAL_LINK' ) . '</a></div>';
		}

		// Bind the author
		if ($feed->author) {
			$author = @$item->get_author();

			if ($author) {
				$name = @$author->get_name();
				$email = @$author->get_email();

				if ($name) {
					$contents .= '<div>' . JText::sprintf('COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR', $name) . '</div>';
				} else if ($email) {

					$segments = explode(' ', $email);

					if (isset($segments[1])) {
						$name = $segments[1];
						$name = str_replace(array('(', ')'), '', $name);
						$contents .= '<div>' . JText::sprintf('COM_EASYBLOG_FEEDS_ORIGINAL_AUTHOR', $name) . '</div>';
					}
				}
			}
		}

		// Try to get the media file if exist
		$enclosure = @$item->get_enclosure();
		$imageUrl = '';

		if ($enclosure) {
			$imageUrl = $enclosure->get_thumbnail();
		}

		// if the image URL exist, download the image
		if (!empty($imageUrl)) {
			$image = $this->downloadImage($imageUrl, $post);

			// If the image is exist, add it to content
			if (!empty($image)) {
				$contents .= $image;
			}
		}

		if ($feed->item_content == 'intro') {
			$post->intro = $contents;
		} else {
			$post->content = $contents;
		}

		// The doctype for imported post should be legacy because there are no blocks here.
		$post->doctype = 'legacy';
	}

	/**
     * Performs image download from the RSS
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return
     */
	private function downloadImage($url, $post)
	{
		$config = EB::getConfig();
		$main_image_path = $config->get('main_image_path');
		$main_image_path = rtrim($main_image_path, '/');

		$rel_upload_path = $main_image_path . '/' . $post->created_by;

		// Get the file name
		$fileName = basename($url);

		// Download the file to joomla tmp folder
		$img = JPATH_ROOT . '/tmp/' . $fileName;
		file_put_contents($img, file_get_contents($url));

		$file = getimagesize($img);
		$file['name'] = basename($img);
		$file['tmp_name'] = $img;
		$file['type'] = $file['mime'];

		$media = EB::mediamanager();
		$result = $media->upload($file, 'user:' . $post->created_by);

		$image = '';

		if (isset($result->type)) {
			$relativeImagePath = $rel_upload_path . '/' . $file['name'];
			$image = '<img src="'.$relativeImagePath.'">';
		}

		// Delete the tmp file
		JFile::delete($img);

		return $image;
	}

	/**
	 * Convert relative links to absolute links
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function convertRelativeToAbsoluteLinks($content, $absPath)
	{
		$dom = new DOMDocument();
		@$dom->loadHTML($content);

		// anchor links
		$links = $dom->getElementsByTagName('a');
		foreach($links as $link)
		{
			$oriUrlLink 	= $link->getAttribute('href');
			$urlLink    	= EB::helper('string')->encodeURL( $oriUrlLink );
			$urlLink    	= EB::helper('string')->rel2abs( $urlLink, $absPath );
            $link->setAttribute('href', $urlLink);

			$content    = str_replace( 'href="' . $oriUrlLink .'"', 'href="' . $urlLink .'"', $content );
		}


		// image src
		$imgs = $dom->getElementsByTagName('img');
		foreach($imgs as $img)
		{
			$oriImgLink = $img->getAttribute('src');
			$imgLink    = EB::helper('string')->encodeURL( $oriImgLink );
			$imgLink    = EB::helper('string')->rel2abs( $imgLink, $absPath );
			$content    = str_replace( 'src="' . $oriImgLink .'"', 'src="' . $imgLink .'"', $content );
		}

		return $content;
	}

	/**
	 * Tidy up the html contents
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function tidyContent($html)
	{
		return EB::string()->tidyHTMLContent($html);
	}

	/**
	 * Some feed items doesn't have a title. We need to convert the link to the title
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The linke to the item
	 * @return
	 */
	private function getTitleFromLink($link)
	{
		$segments = explode('/', $link);

		// Default title should we not be able to get the link
		$title = JText::sprintf('COM_EASYBLOG_FEEDS_GENERIC_TITLE', EB::date()->format(JText::_('DATE_FORMAT_LC3')));

		if (count($segments) > 1) {
			$title = $segments[count($segments) - 1];

			// Remove .html from the title
			$title = JString::str_ireplace('.html', '', $title);

			// Replace - with spaces
			$title = JString::str_ireplace('-', ' ', $title);

			$title = ucwords($title);
		}

		return $title;
	}
}
