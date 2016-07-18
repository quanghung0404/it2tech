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

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

class EasyBlogMedia extends EasyBlog
{
	/**
	 * Default options for videos
	 * @var Array
	 */
	public static $defaultVideoOptions = array(
										'width' => '400',
										'height' => '300',
										'ratio' => '',
										'muted' => false,
										'autoplay' => false,
										'loop' => false
									);

	/**
	 * Default options for audio player
	 * @var Array
	 */
	public static $defaultAudioOptions = array(
											'autoplay' => false,
											'loop' => false,
											'showArtist' => true,
											'showTrack' => true,
											'showDownload' => true,
											'artist' => '',
											'track' => ''
										);
	/**
	 * Renders video player
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renderVideoPlayer($uri, $options = array())
	{
		// Merge the options with the default options
		$options = array_replace_recursive(self::$defaultVideoOptions, $options);

		$responsive = (bool) strpos($options['width'], '%');
		$ratio = isset($options['ratio']) && $options['ratio'] ? EB::math()->ratioPadding($options['ratio']) : null;

		// Url to the video
		$url = $this->normalizeURI($uri);

		// Generate a random uid for this video now.
		$uid = 'video-' . EBMM::getHash($url);

        $template = EB::template();
        $template->set('url', $url);
        $template->set('width', $options['width']);
        $template->set('height', $options['height']);
        $template->set('autoplay', $options['autoplay']);
        $template->set('muted', $options['muted']);
        $template->set('loop', $options['loop']);
        $template->set('responsive', $responsive);
        $template->set('ratio', $ratio);
        $template->set('uid', $uid);

        $contents = $template->output('site/blogs/blocks/video');

        return $contents;
	}

	/**
	 * Renders audio player for the blog
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function renderAudioPlayer($uri, $options = array())
	{
		// Merge the options with the default options
		$options = array_replace_recursive(self::$defaultAudioOptions, $options);

		// Generate a random uid
		$uniqid = uniqid();
		$uid = 'audio-' . EBMM::getHash($uri . $uniqid);

		// Url to the audio
		$url = $this->normalizeURI($uri);

		// Get the track if there is no track provided
		if (!$options['track']) {
			$options['track'] = basename($url);
		}
			
		// Set a default artist if artist isn't set
		if (!$options['artist']) {
			$options['artist'] = JText::_('COM_EASYBLOG_BLOCKS_AUDIO_ARTIST');
		}

		$template = EB::template();

		$template->set('uid', $uid);
		$template->set('showTrack', $options['showTrack']);
		$template->set('showDownload', $options['showDownload']);
		$template->set('showArtist', $options['showArtist']);
		$template->set('autoplay', $options['autoplay']);
		$template->set('loop', $options['loop']);
		$template->set('artist', $options['artist']);
		$template->set('track', $options['track']);
		$template->set('url', $url);
		
		$output = $template->output('site/blogs/blocks/audio');

		return $output;
	}

	/**
	 * Normalizes an URI
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function normalizeURI($uri)
	{
		// If the url is already a hyperlink, just skip this
		$url = $uri;

		// If the url is not a hyperlink, MM uri format, we need to get the correct url
		if (!EB::string()->isHyperlink($uri)) {
			$url = EBMM::getUrl($uri);
		}

		return $url;		
	}
}
