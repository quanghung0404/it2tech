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

class SocialCrawlerImages
{
	/**
	 * Tries to process the DOM to locate for images
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function process($parser, &$contents, $url, $absoluteUrl)
	{
		$result = array();

		if (!$parser) {
			return $result;
		}

		// Find all image tags on the page.
		$images = $parser->find('img');

		foreach ($images as $image) {

			// If the image src contains base64 encoded data, we should skip this for now.
			if (stristr($image->src, 'data:image/') !== false) {
				continue;
			}

			// If there's a /../ , we need to replace it.
			if (stristr($image->src , '/../') !== false) {
				$image->src = str_ireplace('/../', '/', $image->src);
			}

			if (stristr($image->src, '../') !== false) {
				$image->src = str_ireplace('../', '/', $image->src);
			}

			if (stristr($image->src, 'http://') === false && stristr($image->src, 'https://') === false) {
				$image->src = rtrim($url, '/') . '/' . ltrim($image->src, '/');
			}

			$result[] = $image->src;
		}

		// if not match at all
		if (empty($result)) {

			$patterns = '/(.*?)\.(gif|jpg|jpeg|png|bpm)$/i';
			
			// Check is it image URL
        	if (preg_match($patterns, $absoluteUrl, $matches)) {
				if (isset($matches[0])) {
					$result[] = $matches[0];
				}
	        }
		}

		// Ensure that there are no duplicate images.
		$result = array_values(array_unique($result, SORT_STRING));

		return $result;
	}
}
