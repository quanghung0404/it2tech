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

class EasyBlogVideos extends EasyBlog
{
	private $providers = array(
								'youtube.com'		=> 'youtube',
								'youtu.be'			=> 'youtube',
								'vimeo.com'			=> 'vimeo',
								'yahoo.com'			=> 'yahoo',
								'metacafe.com'		=> 'metacafe',
								'google.com'		=> 'google',
								'mtv.com'			=> 'mtv',
								'liveleak.com'		=> 'liveleak',
								'revver.com'		=> 'revver',
								'dailymotion.com'	=> 'dailymotion',
								'nicovideo.jp'		=> 'nicovideo',
								'blip.tv'			=> 'blip',
								'soundcloud.com'	=> 'soundcloud'
								);

	/**
	 * Removes any video codes from the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function strip($content)
	{
		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content = str_ireplace( '&quot;' , '"' , $content );

		$pattern = array('/\{video:.*?\}/',
						'/\{"video":.*?\}/',
						'/\[embed=.*?\].*?\[\/embed\]/'
						);

		$replace = array('','');


		return preg_replace($pattern, $replace, $content);
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
	}

	/**
	 * Used in conjunction with EB::formatter()
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format(EasyBlogPost &$post, $plain = false)
	{
		$post->intro = $this->formatContent($post->intro, $plain);
		$post->content = $this->formatContent($post->content, $plain);
	}

	/**
	 * Formats the content with the appropriate video codes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatContent($content, $plain = false)
	{
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if ($matches) {

			$allowed = array('video', 'videolink');

			foreach ($matches as $match) {

				list($search, $type, $result) = $match;

				if (!in_array($type, $allowed)) {
					continue;
				}

				if ($type == 'video') {
					$content = $this->processUploadedVideos($content, $plain, $search, $result);
				}

				if ($type == 'videolink') {
					$content = $this->processExternalVideos($content, $plain, $search, $result);
				}
			}
		}

		return $content;
	}

	/**
	 * Processes video codes and converts it accordingly.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string	The contents to search for
	 * @param 	bool	Determines if the caller only wants the video url
	 * @return
	 */
	public function processVideos($content, $isPlain = false)
	{
		return $this->formatContent($content, $isPlain);
	}

	/**
	 * Search and replace videos that are uploaded to the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processUploadedVideos($content, $isPlain = false, $findText = '', $result = '')
	{
		$cfg = EB::config();

		// Since 3.0 uses a different video format, we need to do some tests here.
		if ($result) {

			$data = json_decode($result);

			// New EasyBlog 5 legacy codes
			if (isset($data->uri)) {
				$mm = EB::mediamanager();
				$file = $mm->getFile($data->uri);

				$url = $file->url;
			} else {

				// This is the video codes used on EB3.9 or older
				$file = trim($data->file, '/\\');

				$place = $data->place;

				if ($place == 'shared') {
					$url = rtrim( JURI::root() , '/' ) . '/' . trim( str_ireplace( '\\' , '/' , $cfg->get( 'main_shared_path' ) ) , '/\\') . '/' . $file;
				} else {
					$place = explode( ':' , $place );
					$url = rtrim( JURI::root() , '/' ) . '/' . trim( $cfg->get( 'main_image_path' ) , '/\\') . '/' . $place[1] . '/' . $file;
				}
			}

			$options = array();
			$options['width'] = $data->width;
			$options['height'] = $data->height;
			$options['autostart'] = isset($data->autostart) ? $data->autostart : false;

			$player = EB::media()->renderVideoPlayer($url, $options);
			$content = str_ireplace($findText, $player, $content);

			return $content;
		}

		return $content;
	}

	/**
	 * Processes videos that are embedded on the post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processExternalVideos($content, $isPlain = false, $findText = '', $jsonString = '')
	{
		if (!$jsonString) {
			return $content;
		}

		$video = json_decode($jsonString);

		// The json string might contain html tag if the decode return null.
		if ($video == null) {
			$video = json_decode(strip_tags($jsonString));
		}

		$search = !empty($findText) ? $findText : $jsonString;

		if ($isPlain) {
			$html = ' ' . $video->video . ' ';
			$content = JString::str_ireplace($search, $html, $content);

			return $content;
		}


		$maxWidth = (int) $this->config->get('max_video_width');
		$maxHeight = (int) $this->config->get('max_video_height');

		// Ensure that the video dimensions doesn't exceed the maximum dimensions
		$video->width = $video->width > $maxWidth ? $maxWidth : $video->width;
		$video->height = $video->height > $maxHeight ? $maxHeight : $video->height;

		// Ensure that the video link is clean.
		$video->video = strip_tags($video->video);

		$output = $this->getProviderEmbedCodes($video->video, $video->width, $video->height);

		if ($output !== false) {
			$content = JString::str_ireplace($search, $output, $content);
		}

		return $content;
	}

	/**
	 * Retrieves a list of videos in an array
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getItems($content)
	{
		$videos = array();
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';

		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!$matches) {
			return $videos;
		}

		foreach ($matches as $match) {
			$type = $match[1];
			$search = $match[0];

			if ($type != 'videolink' && $type != 'video') {
				continue;
			}


			$video = $match[2];

			// Remove the video from the content since we have already extracted it.
			$content = JString::str_ireplace($search, '', $content);

			if ($type == 'videolink') {
				$videoObj = json_decode($video);
				if ($videoObj) {
					$videos[] = $this->getProviderEmbedCodes($videoObj->video, $videoObj->width, $videoObj->height);
				}

			}

			if ($type == 'video') {
				$videos[] = $this->processVideoLink($video);
			}
		}

		return $videos;
	}

	/**
	 * Given a set of content, try to match and return the list of videos that are found in the content.
	 * This is only applicable for videos that are supported by the library.
	 *
	 * @author	imarklee
	 * @access	public
	 * @param	string	$content	The html contents that we should look for.
	 * @return	Array				An array of videos that are found.
	 */
	public function getVideoObjects($content)
	{
		// This will eventually contain all the video objects
		$result = array();

		// Store temporary content for legacy fixes.
		$tmpContent	= $content;

		// @since 3.5
		// New pattern uses [embed=videolink] to process embedded videos from external URLs.
		//
		// videolink - External video URLs like Youtube, Google videos, MTV
		// video - Internal video URLs that are uploaded via media manager
		$pattern = '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

		if (!empty($matches)) {

			foreach ($matches as $match) {

				list($search, $type, $json) = $match;

				// Decode the json string
				$data = json_decode($json);

				// Let's remove it from the temporary content.
				$tmpContent	= str_ireplace($search, '', $tmpContent);

				if ($type == 'videolink') {
					$data->html = $this->getProviderEmbedCodes($data->video, $data->width, $data->height);
				}

				// Now, let's add the data object back to the result list.
				$result[]	= $data;
			}
		}

		return $result;
	}

	/**
	 * Detects the domain provider of the embedded video link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getDomain($link)
	{
		$link = strip_tags($link);
		$domain = '';

		// Ensure that the video link contains protocol
		if (stristr($link, 'http://') === false && stristr($link, 'https://') === false) {
			$link = 'http://' . $link;
		}

		// Break down the link information
		$link = parse_url($link);
		$link = explode('.', $link['host']);

		// The parts of the domains are always xxx.com regardless if it's a subdomain or not.
		// E.g: something.youtube.com, xxx.youtube.com and yyy.vimeo.com
		if (count($link) >= 2) {
			$domain = $link[count($link) - 2] . '.' . $link[count($link) - 1];
		}

		return $domain;
	}

	/**
	 * Retrieve the embed codes from specific video provider
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processVideoLink($jsonString)
	{
		// Since 3.0 uses a different video format, we need to do some tests here.
		if ($jsonString) {

			$data = json_decode($jsonString);

			$file = trim( $data->uri , '/\\' );
			$width = isset( $data->width ) ? $data->width : 0;
			$height = $data->height;
			$autostart = $data->autoplay;
			$place = $data->uri;


			if ($place == 'shared') {
				$sharedPath = $this->config->get('main_shared_path');
				$url = JURI::root() . $sharedPath . '/' . $file;
			} else {
				$imagePath = $this->config->get('main_image_path');
				$place = explode(':', $place);
				$url = JURI::root() . $imagePath . '/' . $place[1] . '/' . $file;
			}

			$theme		= EB::template();

			// Give a unique id for the video.
			$theme->set( 'uid'			, uniqid() );
			$theme->set( 'width' 		, $width );
			$theme->set( 'height'		, $height );
			$theme->set( 'autoplay'		, $autostart );
			$theme->set( 'url'			, $url );
			$output		= $theme->output( 'site/blogs/latest/blog.video' );

			return $output;
		}

	}

	/**
	 * Processes an embedded video hyperlink with the appropriate embed codes.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string	The link to the video item
	 * @param	int		The width of the video
	 * @param	int		The height of the video
	 * @return
	 */
	public function getProviderEmbedCodes($link, $width = null, $height = null)
	{
		$domain = $this->getDomain($link);

		// If we can't find the video, skip this altogether.
		if (!array_key_exists($domain, $this->providers)) {
			return false;
		}

		$provider = strtolower($this->providers[$domain]);
		$path = dirname(__FILE__) . '/adapters/' . $provider . '.php';

		// Ensure that the file really exists. Do not allow authors to break the flow
		if (!JFile::exists($path)) {
			return false;
		}

		require_once($path);

		$class = 'EasyBlogVideo' . ucfirst($provider);

		if (!class_exists($class)) {
			return false;
		}

		$provider = new $class();
		$output = $provider->getEmbedHTML($link, $width, $height);

		return $output;
	}
}
