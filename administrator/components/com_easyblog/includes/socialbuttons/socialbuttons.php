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

class EasyBlogSocialButtons extends EasyBlog
{
	public $buttons = array('main_facebook_like',
								  'main_googleone',
								  'main_pinit_button',
								  'main_linkedin_button',
								  'main_pocket_button',
								  'main_reddit_button',
								  'main_stumbleupon_button',
								  'main_twitter_button',
								  'main_vk',
								  'main_xing_button');
	/**
	 * Determines if there are any social buttons that should be displayed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function enabled()
	{
		$enabled = false;
		$frontpage = true;

		// Get the current view of the page
		$view = $this->input->get('view', '');

		if ($view == 'entry') {
			$frontpage = false;
		}

		foreach ($this->buttons as $key) {

			$frontpageKey = $key . '_frontpage';

			// If any one of the option is enabled, we can safely say that we should be displaying the social buttons
			if (($frontpage && $this->config->get($frontpageKey)) || (!$frontpage && $this->config->get($key))) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Retrieve a specific button type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function get($button, EasyBlogPost &$blog)
	{
		$adapter = $this->getAdapter($button);

		if (!$adapter) {
			return false;
		}

		$className = 'EasyBlogSocialButton' . ucfirst($button);


		$isFrontpage = true;

		$view = $this->input->get('view', '');

		if ($view == 'entry') {
			$isFrontpage = false;
		}

		$options = array();
		$options['frontpage'] = $isFrontpage;

		$obj = new $className($blog, $options);

		return $obj;
	}

	/**
	 * Retrieves an adapter for a social button
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getAdapter($button)
	{
		$file = __DIR__ . '/adapters/' . strtolower($button) . '.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}
}
