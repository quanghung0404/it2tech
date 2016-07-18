<?php
/**
 * @package    RSForm! Pro
 * @copyright  (c) 2007 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die;

/**
 * Response class for the VR API.
 */
class Response
{
	public $url;
	public $items;
	public $attributes;
	public $links;
	public $success;
	public $error;

	function __construct($response)
	{
		$this->items      = $this->extract("items", $response);
		$this->attributes = $this->extract("attributes", $response);
		$this->links      = $this->extract("links", $response);
		$this->success    = $this->extract("success", $response);
		$this->error      = $this->extract("error", $response);
		$this->url        = $this->extract("url", $response);
	}

	// Extracts the content of the response with the given key
	private function extract($key, $response)
	{
		$result = null;
		if (array_key_exists($key, $response))
		{
			$result = $response[$key];
		}

		return $result;
	}
}
