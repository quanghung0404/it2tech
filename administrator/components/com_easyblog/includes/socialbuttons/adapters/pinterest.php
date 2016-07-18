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

require_once(dirname(__FILE__) . '/button.php');

class EasyBlogSocialButtonPinterest extends EasyBlogSocialButton
{
	public $type 	= 'pinterest';

	/**
	 * Appends the vk script on the head of the page
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function addScript()
	{
		static $loaded = false;

		if (!$loaded) {
			$this->doc->addScript('//assets.pinterest.com/js/pinit.js');

			$loaded = true;
		}

		return $loaded;
	}


	/**
	 * Outputs the html code for Twitter button
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html()
	{
		if (!$this->isEnabled()) {
			return;
		}

		$this->addScript();
		
		// Get the pinterest button style from the configuration
		$size = $this->getButtonSize();

		$url = EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $this->post->id, false, true);

		// Combine the introtext and the content
		$content = $this->post->intro . $this->post->content;

		// Get the media
		$media = $this->getMedia();

		$contentLength	= 350;

		$text = $this->post->intro . $this->post->content;
		$text = nl2br($text);
		$text = strip_tags( $text );
		$text = trim( preg_replace( '/\s+/', ' ', $text ) );
		$text = ( JString::strlen( $text ) > $contentLength ) ? JString::substr( $text, 0, $contentLength) . '...' : $text;

		$title = $this->post->title;

		// Urlencode all the necessary properties.
		$url = urlencode($url);
		$text = urlencode($text);
		$media = urlencode($media);

		$placeholder = $this->getPlaceholderId();

		$theme 	= EB::template();
		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('title', $title);
		$theme->set('media', $media);
		$theme->set('text', $text);

		$output = $theme->output('site/socialbuttons/pinterest');

		return $output;
	}

	public function getMedia()
	{
		// Retrieve image for Pinterest
		$image 	= $this->getImage();

		// Retrieve videos for Pinterest
		$video 	= $this->getVideo();

		// If there's no image or video, skip this altogether because
		// Pinterest requirements requires video or image.
		if (!$image && !$video) {

			return false;
		}

		$media = $image;

		if(!$image){
			$media 			= $video;
		}

		return $media;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEnabled()
	{
		if ($this->frontpage && !$this->config->get('main_pinit_button_frontpage', $this->config->get('social_show_frontpage'))) {
			return false;
		}

		if (!$this->getMedia()) {
			return false;
		}

		return $this->config->get('main_pinit_button');
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImage()
	{
		// Retrieve image that can be used for pinterest
		$image = $this->post->getImage('thumbnail', false, true);

		if (empty($image)) {

			// Combine the introtext and the content
			$content = $this->post->intro . $this->post->content;

			// Fetch the first image of the blog post
			$image = EB::getFirstImage($content);

			// Test if there's any blog image
			if (isset($this->post->images) && $this->post->images && is_array($this->post->images)) {
				$image 	= $this->post->images[0];
			}
		}

		return $image;
	}
	/**
	 * Retrieves video from blog post content
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getVideo()
	{
		// Retrieve video that can be used for pinterest
		$video		= '';
		$videoObj	= EB::videos()->getVideoObjects($this->post->content);

		if($videoObj && !empty($videoObj)){
			$video		= $videoObj[0]->video;
		}

		return $video;
	}
}

