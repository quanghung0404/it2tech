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

class EasyBlogCrawlerOEmbed
{
	/**
	 * Ruleset to process document opengraph tags
	 *
	 * @params	string $contents	The html contents that needs to be parsed.
	 * @return	boolean				True on success false otherwise.	 	 	 
	 */	 	
	public function process($parser, &$contents, $uri, $absoluteUrl, $originalUrl)
	{
		$oembed = new stdClass();

		// Metacafe videos
		if (stristr($uri, 'metacafe.com') !== false) {
			return $this->metacafe($parser);
		}

		// Ted videos
		if (stristr($uri, 'ted.com') !== false) {
			return $this->ted($oembed, $contents);
		}

		if (stristr( $uri , 'pastebin.com' ) !== false) {
			return $this->pastebin($oembed, $absoluteUrl);
		}

		if (stristr($uri, 'twitter.com') !== false) {
			return $this->twitter($oembed, $absoluteUrl);
		}

		if ($uri == 'https://gist.github.com') {
			return $this->gist($oembed, $absoluteUrl);
		}

		if (stristr( $uri , 'soundcloud.com' ) !== false) {
			return $this->soundCloud($oembed, $absoluteUrl);
		}

		if( stristr( $uri , 'mixcloud.com' ) !== false )
		{
			return $this->mixCloud( $parser , $oembed , $absoluteUrl );
		}

		if( stristr( $uri , 'spotify.com' ) !== false )
		{
			return $this->spotify( $oembed , $originalUrl );
		}

		if (stristr($uri, 'codepen.io') !== false) {
			return $this->jsonParser($parser, $contents, $oembed, $originalUrl);
		}

		if (stristr($uri, 'behance.net') !== false) {
			return $this->jsonParser($parser, $contents, $oembed, $originalUrl);
		}

		if (stristr($uri, 'youtube.com') !== false) {
			return $this->youtube($oembed, $originalUrl);
		}

		if (stristr($uri, 'dailymotion.com') !== false) {
			return $this->dailymotion($oembed, $originalUrl);
		}

		if (stristr($uri, 'www.slideshare.net') !== false) {
			return $this->slideshare($oembed, $originalUrl);
		}				

		// Get a list of oembed nodes
		$nodes = $parser->find('link[type=application/json+oembed]');

		foreach ($nodes as $node) {
			
			// Get the oembed url
			if (!isset($node->attr['href'])) {
				continue;
			}

			// Get the oembed url from the doc
			$url = $node->attr['href'];

			// Load up the connector first.
			$connector = EB::connector();
			$connector->addUrl($url);
			$connector->execute();

			// Get the result and parse them.
			$contents = $connector->getResult($url);

			// We are retrieving json data
			$oembed = json_decode($contents);

			// Test if thumbnail_url is set so we can standardize this
			if (isset($oembed->thumbnail_url)) {
				$oembed->thumbnail = $oembed->thumbnail_url;
			}
		}

		return $oembed;
	}

	/**
	 * Processes videos from TED
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function ted(&$oembed, $contents)
	{
		$oembed = json_decode($contents);

		return $oembed;
	}

	public function jsonParser($parser, $contents, &$oembed, $absoluteUrl)
	{
		$obj = json_decode($contents);

		$oembed = $obj;

		return $oembed;
	}

	public function metacafe($parser)
	{
	}

	public function pastebin(&$oembed, $absoluteUrl)
	{
		$segment = str_ireplace('http://pastebin.com/', '', $absoluteUrl);

		$oembed->html = '<iframe src="http://pastebin.com/embed_iframe.php?i=' . $segment . '" style="border:none;width:100%"></iframe>';
			
		return $oembed;
	}

	public function twitter(&$oembed, $absoluteUrl)
	{
		$url = 'https://api.twitter.com/1/statuses/oembed.json?url=' . $absoluteUrl;

		// Load up the connector first.
		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		// Get the result and parse them.
		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		return $oembed;
	}

	public function gist(&$oembed, $absoluteUrl)
	{
		$oembed->html = '<script src="' . $absoluteUrl . '.js"></script>';

		return $oembed;
	}

	public function mixCloud( $parser , &$oembed , $absoluteUrl )
	{
		$url 	= 'http://www.mixcloud.com/oembed/?url=' . urlencode($absoluteUrl) . '&format=json';

		// Load up the connector first.
		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		// Get the result and parse them.
		$contents = $connector->getResult( $url );

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail 		= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function soundCloud( &$oembed , $absoluteUrl )
	{
		$url = 'http://soundcloud.com/oembed?format=json&url=' . urlencode( $absoluteUrl );

		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function spotify( &$oembed , $absoluteUrl )
	{
		$url = 'https://embed.spotify.com/oembed/?url=' . urlencode( $absoluteUrl );

		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail 	= $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function youtube(&$oembed, $absoluteUrl)
	{
		$url = 'https://www.youtube.com/oembed/?url=' . urlencode($absoluteUrl);

		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function dailymotion(&$oembed, $absoluteUrl)
	{
		$url = 'https://www.dailymotion.com/services/oembed/?url=' . urlencode($absoluteUrl);
		
		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}

	public function slideshare(&$oembed, $absoluteUrl)
	{
		$url = 'http://www.slideshare.net/api/oembed/2?url=' . urlencode($absoluteUrl);

		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		$contents = $connector->getResult($url);

		// We are retrieving json data
		$oembed = json_decode($contents);

		// Test if thumbnail_url is set so we can standardize this
		if (isset($oembed->thumbnail_url)) {
			$oembed->thumbnail = $oembed->thumbnail_url;
		}

		return $oembed;
	}	
}