<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/helpers/simplehtml.php');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class SocialCrawler
{
	/**
	 * Available hooks.
	 * @var	Array
	 */
	private $hooks	= array();

	/**
	 * Raw contents
	 * @var	string
	 */
	private $contents	= null;

	public static function factory()
	{
		$obj = new self();

		return $obj;
	}

	/**
	 * Normalize the url
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeUrl($url)
	{
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = 'http://' . $url;
		}

		return $url;
	}

	/**
	 * Normalizes the output of a page
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeContent($url, $content)
	{
		$info = parse_url($url);

		$content = str_ireplace('src="//', 'src="' . $info['scheme'] . '://' , $content);

		return $content;
	}

	/**
	 * Invoke the crawling.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function crawl($url)
	{
		// Ensure that urls always contains a protocol
		$url = $this->normalizeUrl($url);

		// Load up the connector first.
		$connector = FD::connector();
		$connector->addUrl($url);
		$connector->connect();

		// Get the result and parse them.
		$content = $connector->getResult($url);

		// Normalize the contents
		$this->contents = $this->normalizeContent($url, $content);

		// Get the final url, if there's any redirection.
		$originalUrl = $url;
		$url = $connector->getFinalUrl($url);

		$this->parse($originalUrl, $url);

		return $this;
	}

	/**
	 * Retrieves a list of hooks
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHooks()
	{
		$hooks = JFolder::files(__DIR__ . '/hooks');

		return $hooks;
	}

	/**
	 * Loads adapters into the current namespace allowing the processing part
	 * to call these adapters.
	 *
	 * @param	string		The URL
	 * @return	boolean		True on success, false if no adapters found.
	 */
	private function parse($originalUrl , $url)
	{
		// Get a list of available hooks
		$hooks = $this->getHooks();

		// Get the parser
		$parser	= SocialSimpleHTML::str_get_html($this->contents);

		if (!$parser) {
			return false;
		}

		$info = parse_url($url);
		$uri = $info['scheme'] . '://' . $info['host'];

		// Get the absolute url
		$absoluteUrl = $url;

		foreach ($hooks as $hook) {

			$file = __DIR__ . '/hooks/' . $hook;

			require_once($file);
			$name = str_ireplace('.php', '', $hook);

			$class = 'SocialCrawler' . ucfirst($name);

			// When item doesn't exist set it to false.
			if (!class_exists($class)) {
				continue;
			}

			$obj = new $class();
			$result = $obj->process($parser, $this->contents, $uri, $absoluteUrl, $originalUrl, $this->hooks);

			$this->hooks[$name] = $result;
		}

		// We should rely on the opengraph title if there is
		if (isset($this->hooks['opengraph']->title)) {
			$this->hooks['title'] = $this->hooks['opengraph']->title;
		}

		// We should rely on the opengraph title if there is
		if (isset($this->hooks['oembed']->title)) {
			$this->hooks['title'] = $this->hooks['oembed']->title;
		}

		return true;
	}

	/**
	 * Retrieves the hooks values.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getData()
	{
		return $this->hooks;
	}
}
