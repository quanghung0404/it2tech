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

require_once(__DIR__ . '/helpers/simplehtml.php');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogCrawler extends EasyBlog
{
	private $hooks = array();
	private $contents = null;

	/**
	 * Invoke the crawling.
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function crawl($url)
	{
		$tmp = str_ireplace(array('http://', 'https://'), '', $url);
		
		if (!EB::string()->isValidDomain($tmp)) {
			return false;
		}

		// Ensure that urls always contains a protocol
		if (stristr($url, 'http://') === false && stristr($url , 'https://') === false) {
			$url = 'http://' . $url;
		}

		// Load up the connector first.
		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->execute();

		// Get the result and parse them.
		$info = parse_url($url);
		$this->contents	= $connector->getResult($url);

		// Replace any href="// with href="scheme://"
		$this->contents = str_ireplace( 'src="//' , 'src="' . $info['scheme' ] . '://' , $this->contents );
		
		// Get the final url, if there's any redirection.
		$originalUrl = $url;
		$url = $connector->getFinalUrl($url);

		$this->parse($originalUrl, $url);

		return $this;
	}

	/**
	 * Loads adapters into the current namespace allowing the processing part
	 * to call these adapters.	 
	 *
	 * @param	string		The URL 
	 * @return	boolean		True on success, false if no adapters found.
	 */	 	 
	private function parse($originalUrl, $url)
	{
		// Load available hooks.
		$hooks = JFolder::files(__DIR__ . '/hooks');

		// Load up the html parser
		$parser	= SocialSimpleHTML::str_get_html($this->contents);

		if (!$parser) {
			return false;
		}
			
		$info = parse_url($url);
		$uri = $info['scheme'] . '://' . $info['host'];
		$absoluteUrl = $url;

		foreach ($hooks as $hook) {
			$file = __DIR__ . '/hooks/' . $hook;
			
			require_once($file);
			$name = str_ireplace('.php', '', $hook);

			$class = 'EasyBlogCrawler' . ucfirst($name);

			// When item doesn't exist set it to false.
			if (!class_exists($class)) {
				continue;
			}
			
			$obj = new $class();
			$this->hooks[$name]	= $obj->process($parser, $this->contents, $uri, $absoluteUrl, $originalUrl, $this->hooks);
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