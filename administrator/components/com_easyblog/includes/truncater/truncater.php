<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogTruncater extends EasyBlog
{
	/**
	 * Truncates the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function truncate(EasyBlogPost &$post , $limit = null)
	{
		// Get the maximum allowed characters in the content
		$max = $this->config->get('layout_maxlengthasintrotext', 200);
		$max = $max <= 0 ? 200 : $max;

		// Default to truncate the post's content
		$truncate = true;

		// If introtext and content is already present, we don't need to truncate anything since it should respect the user's settings
		if ($post->intro && $post->content) {
			$truncate = false;

			$post->text = $post->intro;
		}

		// If limit exist we can ignore the following settings
		if (!$limit) {

			// If we do not need to run any truncation, just run a simple formatting
			if (!$truncate || !$this->config->get('layout_blogasintrotext')) {

				// Process videos
				EB::videos()->format($post);

				// Process audio files
				EB::audio()->format($post);

				// Format gallery items
				EB::gallery()->format($post);

				// Format albums
				EB::album()->format($post);

				// Remove known codes
				$this->stripCodes($post);

				// Determine the correct content to display
				$post->text = empty($post->intro) ? $post->content : $post->intro;

				return $post->text;
			}
		}

		// For normal posts, we will need to get a list of items included in the post
		if (isset($post->posttype) && $post->posttype == 'standard' || !$post->posttype) {
			$post->videos = EB::videos()->getItems($post->text);
			$post->galleries = EB::gallery()->getItems($post->text);
			$post->audios = EB::audio()->getItems($post->text);
			$post->albums = EB::album()->getItems($post->text);
			$post->images = $this->getImages($post->text);
		}

		// Strip out known codes
		$this->stripCodes($post);

		// Truncation by characters
		if ($this->config->get('main_truncate_type') == 'chars' || $limit) {
			$this->truncateByChars($post, $limit);
		}

		// Truncation by break tags
		if ($this->config->get('main_truncate_type') == 'break') {
			$this->truncateByParagraph($post);
		}

		// Truncation by words
		if ($this->config->get('main_truncate_type') == 'words') {
			$this->truncateByWords($post);
		}

		// Truncation by paragraph
		if ($this->config->get('main_truncate_type') == 'paragraph') {
			$this->truncateByParagraph($post);
		}

		// Append ellipses to the content if necessary
		if ($this->config->get('main_truncate_ellipses') && isset($post->readmore) && $post->readmore) {
			$post->text .= JText::_('COM_EASYBLOG_ELLIPSES');
		}

		// Only process standard posts
		if ($post->posttype == 'standard') {

			// Determine the position of media items that should be included in the content.
			$embedHTML = '';
			$embedVideoHTML = '';
			$imgHTML = '';
			$imgAudioHTML = '';
			$imgGalleryHTML = '';

			if (!empty($post->images) && !$post->hasImage() && $this->config->get('main_truncate_type') != 'paragraph') {

				$imgHTML .= $post->images;

				// Fix duplicate image issue when using truncation by break tags
				if ($this->config->get('main_truncate_type') == 'break') {
					preg_match('/(<img[^>]+>)/i', $post->text, $matches);

					if ($matches) {
						$imgHTML = '';
					}
				}
			}

			if (!empty($post->galleries)) {
				$imgGalleryHTML .= $post->galleries;
			}

			if (!empty($post->audios)) {
				$imgAudioHTML .= implode( '' , $post->audios );
			}

			if (!empty($post->videos)) {
				$embedVideoHTML = implode( '' , $post->videos );
			}

			if (!empty($post->albums)) {
				$embedHTML .= implode( '' , $post->albums );
			}

			// images
			if ($this->config->get( 'main_truncate_image_position') == 'top' && !empty($imgHTML)) {
				$post->text = $imgHTML . $post->text;
			} 

			if ($this->config->get( 'main_truncate_image_position') == 'bottom' && !empty($imgHTML)) {
				$post->text = $post->text . $imgHTML;
			}

			// Videos
			if ($this->config->get('main_truncate_video_position') == 'top' && !empty($embedVideoHTML)) {
				$post->text = $embedVideoHTML . '<br />' . $post->text;
			} 

			if ($this->config->get('main_truncate_video_position') == 'bottom' && !empty($embedVideoHTML)) {
				$post->text = $post->text . '<br />' . $embedVideoHTML;
			}

			// Galleries
			if ($this->config->get( 'main_truncate_gallery_position') == 'top' && !empty($imgGalleryHTML)) {
				$post->text = $imgGalleryHTML . $post->text;
			} 

			if($this->config->get('main_truncate_gallery_position') == 'bottom' && !empty($imgGalleryHTML)) {
				$post->text = $post->text . $imgGalleryHTML;
			}			

			// Audios
			if ($this->config->get('main_truncate_audio_position') == 'top' && !empty($imgAudioHTML)) {
				$post->text = $imgAudioHTML . $post->text;
			} 

			if($this->config->get('main_truncate_audio_position') == 'bottom' && !empty($imgAudioHTML)) {
				$post->text = $post->text . $imgAudioHTML;
			}
		}
	}

	/**
	 * Reverse of strip_tags
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function strip_only($str, $tags, $stripContent = false)
	{
		$content = '';

		if(!is_array($tags))
		{
			$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));

			if(end($tags) == '')
			{
				array_pop($tags);
			}
		}

		foreach($tags as $tag)
		{
			if ($stripContent)
			{
				$content = '(.+</'.$tag.'[^>]*>|)';
			}
			$str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
		}
		return $str;
	}

	/**
	 * Remove known dirty codes from the content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripCodes(EasyBlogPost &$post)
	{
		// Remove video codes
		EB::videos()->stripCodes($post);

		// Remove audio codes
		EB::audio()->stripCodes($post);

		// Remove gallery codes
		EB::gallery()->stripCodes($post);

		// Remove album codes
		EB::album()->stripCodes($post);
	}

	/**
	 * Performs truncation of the content by words
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function truncateByWords(EasyBlogPost &$post)
	{
		$tag = false;
		$count = 0;
		$output = '';

		// Get the number of maximum characters
		$maxCharacters = $this->config->get('layout_maxlengthasintrotext');

		// Remove uneccessary html tags to avoid unclosed html tags
		$post->text = strip_tags($post->text);

		// Get a list of space breaks
		$words = preg_split("/([\s]+)/", $post->text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		foreach ($words as $word) {

			if (!$tag || stripos($word, '>') !== false) {
				$tag = (bool) (strripos($word, '>') < strripos($word, '<'));
			}

			// If this is a space, we should skip this
			if (!$tag && trim($word) == '') {
				$count++;
			}

			if ($count > $maxCharacters && !$tag) {
				$post->readmore = true;
				break;
			}

			$output .= $word;
		}

		$post->text = $output;
	}

	/**
	 * Performs truncation by characters
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function truncateByChars(EasyBlogPost &$post, $maxCharacters)
	{
		if (!$maxCharacters) {
			$maxCharacters = $this->config->get('layout_maxlengthasintrotext', 150);
		}
		
		// Remove uneccessary html tags to avoid unclosed html tags
		$post->text = strip_tags($post->text);

		// Remove blank spaces since the word calculation should not include new lines or blanks.
		$post->text = trim($post->text);

		$post->readmore = false;

		// Determines if there's a read more
		if (JString::strlen($post->text) > $maxCharacters) {
			$post->readmore = true;
		}

		// Since this is truncation by characters, just slice the data out
		$post->text = JString::substr($post->text, 0, $maxCharacters);
	}

	/**
	 * Truncation by break tags
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function truncateByBreak(EasyBlogPost &$post)
	{
		$position = 0;
		$matches = array();
		$tag = '<br';

		$contents = $post->intro . $post->content;

		do {
			$position = @JString::strpos(JString::strtolower($contents), $tag, $position + 1);

			if ($position !== false) {
				$matches[] = $position;
			}
		} while ($position !== false);

		// Determines the total number of break tags to listen do
		$maxTag = (int) $this->config->get('main_truncate_maxtag');

		$post->readmore = false;

		if (count($matches) > $maxTag) {
			$post->readmore = true;
			$contents = JString::substr($contents, 0, $matches[$maxTag +1] + 6);
		}

		return $contents;
	}

	/**
	 * Truncates the content by paragraph
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function truncateByParagraph(EasyBlogPost &$post)
	{
		// Determines the maximum number of paragraphs allowed
		$maximumParagraphs = (int) $this->config->get('main_truncate_maxtag');

		$position = 0;
		$matches = array();
		$tag = '</p>';

		// If configured to not display any media items on frontpage, strip out these tags
		if ($this->config->get('main_truncate_image_position') == 'hidden') {
			$post->text = $this->strip_only($post->text, '<img>');
		}

		// Iterate through the contents
		do {
			$position = @JString::strpos(strtolower($post->text), $tag, $position +1);

			if ($position !== false) {
				$matches[] = $position;
			}
		} while ($position !== false);


		// If there's lesser number of paragraphs, just skip this altogether
		if (count($matches) <= $maximumParagraphs) {

			// There shouldn't be a read more
			$post->readmore = false;

			return;
		}

		$post->text = JString::substr($post->text, 0, $matches[$maximumParagraphs - 1] + 4);

		// Generate a list of known tags that might break the truncation
		$htmlTagPattern = array('/\<div/i', '/\<table/i');
		$htmlCloseTagPattern = array('/\<\/div\>/is', '/\<\/table\>/is');
		$htmlCloseTag = array('</div>', '</table>');

		for ($i = 0; $i < count($htmlTagPattern); $i++) {
			$htmlItem = $htmlTagPattern[$i];
			$htmlItemClosePattern = $htmlCloseTagPattern[$i];
			$htmlItemCloseTag = $htmlCloseTag[$i];

			preg_match_all($htmlItem, strtolower($post->text), $totalOpenItem);

			if (isset($totalOpenItem[0]) && !empty($totalOpenItem[0])) {
				$totalOpenItem = count($totalOpenItem[0]);

				preg_match_all($htmlItemClosePattern, strtolower($post->text), $totalClosedItem);
				
				$totalClosedItem = count($totalClosedItem[0]);
				$totalItemToAdd	= $totalOpenItem - $totalClosedItem;

				if ($totalItemToAdd > 0) {

					for ($y = 1; $y <= $totalItemToAdd; $y++) {
						$post->text .= $htmlItemCloseTag;
					}
				}
			}
		}

		// There needs to be a way to set a readmore on the post library
		$post->readmore = true;
	}

	/**
	 * Legacy method to retrieve images
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getImages($content, $debug = false)
	{
		// Match images that has preview.
		$pattern = '/<a class="easyblog-thumb-preview"(.*?)<\/a>/is';
		preg_match($pattern, $content, $matches);

		if (!$matches) {
			$pattern = '#<img[^>]*>#i';
			preg_match($pattern, $content, $matches);
		}

		if (!$matches) {
			return array();
		}

		return $matches[0];
	}
}
