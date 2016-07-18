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

FD::import('site:/controllers/controller');

class EasySocialControllerCrawler extends EasySocialController
{
	/**
	 * Validates to see if the remote url really exists
	 *
	 * @since	1.4.8
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function validate()
	{
		// Get the url
		$url = $this->input->get('url', '', 'default');

		// Get the crawler
		$connector = ES::connector();
		$connector->addUrl($url);
		$connector->connect();

		// Get the result and parse them.
		$content = $connector->getResult($url);
		$valid = true;

		if (!$content) {
			$valid = false;
		}

		return $this->view->call(__FUNCTION__, $valid);
	}

	/**
	 * Does a remote call to the server to fetch contents of a given url.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function fetch()
	{
		// Check for request forgeries!
		$urls = $this->input->get('urls', array(), 'array');

		// Result placeholder
		$result = array();

		if (!$urls) {
			$this->view->setMessage('COM_EASYSOCIAL_CRAWLER_INVALID_URL_PROVIDED', SOCIAL_MSG_ERROR);
			return $this->view->call(__FUNCTION__);
		}

		// Get the crawler
		$crawler = FD::get('crawler');

		foreach ($urls as $url) {

			// Generate a hash for the url
			$hash = md5($url);

			$link = FD::table('Link');
			$exists = $link->load(array('hash' => $hash));

			// If it doesn't exist, store it.
			if (!$exists) {

				$crawler->crawl($url);

				// Get the data from our crawler library
				$data = $crawler->getData();
				
				// Now we need to cache the link so that the next time, we don't crawl it again.
				$link->hash = $hash;
				$link->data = json_encode($data);
				$link->store();
			}

			$result[$url] = json_decode($link->data);
		}

		return $this->view->call(__FUNCTION__, $result);
	}
}
