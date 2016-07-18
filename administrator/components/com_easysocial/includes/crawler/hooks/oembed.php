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

class SocialCrawlerOEmbed
{
	/**
	 * Ruleset to process document opengraph tags
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function process($parser, &$contents, $uri, $absoluteUrl, $originalUrl, $hooks)
	{
		$oembed = new stdClass();

		// Pastebin.com
		if (stristr($uri, 'pastebin.com') !== false) {
			return $this->pastebin($oembed, $absoluteUrl);
		}

		// Gist
		if ($uri == 'https://gist.github.com') {
			return $this->gist($oembed, $absoluteUrl);
		}

		// Soundcloud
		if (stristr($uri, 'soundcloud.com') !== false) {
			return $this->soundCloud($oembed, $absoluteUrl);
		}

		// Mixcloud
		if (stristr($uri, 'mixcloud.com') !== false) {
			return $this->mixCloud($parser, $oembed, $absoluteUrl);
		}

		// Spotify
		if (stristr($uri, 'spotify.com') !== false) {
			return $this->spotify($oembed, $originalUrl);
		}

		// Find oembed tags
		$oembeds = $parser->find('link[type=application/json+oembed]');

		if ($oembeds) {
			foreach ($oembeds as $node) {
				if (!isset($node->attr['href'])) {
					continue;
				}

				// Get the oembed url
				$url = $node->attr['href'];


				// Urls should not contain html entities
				$url = html_entity_decode($url);

				// Now we need to crawl the url again
				$connector = FD::connector();
				$connector->addUrl($url);
				$connector->connect();

				$contents = $connector->getResult($url);
				$oembed = json_decode($contents);

				if (isset($oembed->thumbnail_url)) {
					$oembed->thumbnail = $oembed->thumbnail_url;
				}

				$oembed->isArticle = true;
			}
		}



		if (stristr($uri, 'youtube.com')) {
			$this->youtube($oembed, $uri, $originalUrl, $parser);
			$oembed->isArticle = false;
		}

		if (stristr($uri, 'dailymotion.com')) {
			$this->dailymotion($oembed, $uri, $originalUrl, $parser);
			$oembed->isArticle = false;
		}

		if (isset($oembed) && is_float($oembed->duration)) {
			$oembed->duration = round($oembed->duration);
		}

		return $oembed;
	}

	/**
	 * Processes dailymotion video
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function dailymotion(&$oembed, $uri, $originalUrl, $parser)
	{
		// Get the video id
		$pattern = '/\/video\/(.*)/is';
		preg_match_all($pattern, $originalUrl, $matches);

		$parts = explode('_', $matches[1][0]);

		$videoId = $parts[0];

		$url = 'https://api.dailymotion.com/video/' . $videoId . '?fields=duration';

		$connector = ES::connector();
		$connector->addUrl($url);
		$connector->connect();

		$contents = $connector->getResult($url);

		$obj = json_decode($contents);

		$oembed->duration = $obj->duration;
	}

	/**
	 * Processes youtube links since it doesn't have duration
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function youtube(&$oembed, $uri, $originalUrl, $parser)
	{
		$node = $parser->find('[itemprop=duration]');
		$node = $node[0];

		$duration = $node->attr['content'];

		// For the title, we want to use the opengraph title instead
		// $oembed->title = $oembed->opengraph->title;

		// Match the duration
		$pattern = '/PT(\d+)H|(\d+)M(\d+)S/i';

		// $matches = preg_match($pattern, $duration);
		preg_match_all($pattern, $duration, $matches);

		$seconds = 0;

		// Get the hour
		if (isset($matches[1]) && $matches[1]) {
			$seconds = $matches[1][0] * 60 * 60;
		}

		// Minutes
		if (isset($matches[2]) && $matches[2]) {
			$seconds = $seconds + ($matches[2][0] * 60);
		}

		// Seconds
		if (isset($matches[3]) && $matches[3]) {
			$seconds = $seconds + $matches[3][0];
		}

		// Check if the oembed is exist or not.
		// If not exist, we need to hard coded it.
		if (!isset($oembed->html)) {

			// get the video id.
			parse_str(parse_url($originalUrl, PHP_URL_QUERY), $video_id);
			$video_id = $video_id['v'];

			$oembed = new stdClass();

			// Hard code the neccessary value.
			$oembed->height = 270;
			$oembed->width = 480;
			$oembed->html = '<iframe width="480" height="270" src="https://www.youtube.com/embed/'. $video_id .'?feature=oembed" frameborder="0" allowfullscreen></iframe>';
			$oembed->thumbnail = 'http://img.youtube.com/vi/'. $video_id .'/sddefault.jpg';
			$oembed->thumbnail_url = 'http://img.youtube.com/vi/'. $video_id .'/sddefault.jpg';				
		}

		$oembed->duration = (int) $seconds;

		// We want to get the HD version of the thumbnail
		$sd = str_ireplace('hqdefault.jpg', 'sddefault.jpg', $oembed->thumbnail);

		// Try to get the sd details
		$connector = ES::connector();
		$connector->addUrl($sd);
		$connector->useHeadersOnly();
		$connector->connect();

		$headers = $connector->getResult($sd, true);

		// If the image exists, we just use the sd version
		$notFound = stristr($headers, 'HTTP/1.1 404 Not Found');

		if ($notFound === false) {
			$oembed->thumbnail = $sd;
			return;
		}
	}

	public function pastebin(&$oembed, $absoluteUrl)
	{
		$segment 		= str_ireplace('http://pastebin.com/', '', $absoluteUrl);

		$oembed->html 	= '<iframe src="http://pastebin.com/embed_iframe.php?i=' . $segment . '" style="border:none;width:100%"></iframe>';

		return $oembed;
	}

	public function gist(&$oembed, $absoluteUrl)
	{
		$oembed->html 	= '<script src="' . $absoluteUrl . '.js"></script>';

		return $oembed;
	}

	public function mixCloud( $parser , &$oembed , $absoluteUrl )
	{
		$url 	= 'http://www.mixcloud.com/oembed/?url=' . urlencode($absoluteUrl) . '&format=json';

		// Load up the connector first.
		$connector 		= FD::get( 'Connector' );
		$connector->addUrl($url);
		$connector->connect();

		// Get the result and parse them.
		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail 		= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function soundCloud( &$oembed , $absoluteUrl )
	{
		$url 		= 'http://soundcloud.com/oembed?format=json&url=' . urlencode( $absoluteUrl );

		$connector 	= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if( isset($oembed->thumbnail_url) )
		{
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function spotify( &$oembed , $absoluteUrl )
	{
		$url 		= 'https://embed.spotify.com/oembed/?url=' . urlencode( $absoluteUrl );

		$connector 	= FD::get( 'Connector' );
		$connector->addUrl( $url );
		$connector->connect();

		$contents 	= $connector->getResult( $url );

		// We are retrieving json data
		$oembed 		= FD::json()->decode( $contents );

		// Test if thumbnail_url is set so we can standardize this
		if( isset($oembed->thumbnail_url) )
		{
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}
}
