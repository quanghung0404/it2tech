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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewCrawler extends EasyBlogView
{
	/**
	 * Given a specific URL, try to crawl the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function crawl()
	{
		// Get a list of urls to crawl
		$urls = $this->input->get('url', array(), 'array');

		if (!is_array($urls)) {
			$urls = array($urls);
		}

		// Result placeholder
		$result 	= array();

		if (!$urls || empty($urls)) {
			return $this->ajax->reject();
		}

		// Get the crawler library
		$crawler = EB::crawler();
		
		foreach ($urls as $url) {

			// Ensures that the domain is valid
			if (!EB::string()->isValidDomain($url)) {
				return $this->ajax->reject(JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_EMPTY'));
			}

			// Crawl the url
			$state = $crawler->crawl($url);

			// Get the data from the crawled site
			$data = $crawler->getData();

			if (!$data['title']) {
				$data['title'] = JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_DEFAULT_TITLE');
			}

			if (!$data['description']) {
				$data['description'] = JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LINKS_DEFAULT_DESCRIPTION');
			}


			$result[$url] = $data;
		}

		return $this->ajax->resolve($result);
	}
}
