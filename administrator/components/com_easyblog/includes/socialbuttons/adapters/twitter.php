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

require_once(dirname(__FILE__) . '/button.php');

class EasyBlogSocialButtonTwitter extends EasyBlogSocialButton
{
	public $type = 'twitter';

	/**
	 * Outputs the html code for Twitter button
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html()
	{
		// If this is a frontpage, ensure that show in frontpage is enabled
		if ($this->frontpage && !$this->config->get('main_twitter_button_frontpage', $this->config->get('social_show_frontpage'))) {
			return;
		}

		// Get the button size
		$size = $this->getButtonSize();

		// Get the via text
		$via = $this->config->get('main_twitter_button_via_screen_name', '');

		if ($via) {
			$via = JString::substr($via, 1);
		}

		// Get the absolute url to this blog post
		$url = $this->getUrl();

		// Ge the formatted title to this blog post
		$title = $this->getTitle();		

		// Twitter's sharing shouldn't have urlencoded values
		$title = urldecode($title);

		// Remove unwanted character inside url to avoid incorrect url sharing
		$title = str_replace('"', '', $title);

		// Determines if we should track with analytics
		$tracking = $this->config->get('main_twitter_analytics');
		$placeholder = $this->getPlaceholderId();

		$theme = EB::template();
		$theme->set('tracking', $tracking);
		$theme->set('size', $size);
		$theme->set('via', $via);
		$theme->set('placeholder', $placeholder);
		$theme->set('url', $url);
		$theme->set('title', $title);

		$output = $theme->output('site/socialbuttons/twitter');
		return $output;
	}


	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEnabled()
	{
		// If this is a frontpage, ensure that show in frontpage is enabled
		if ($this->frontpage && !$this->config->get('main_twitter_button_frontpage', $this->config->get('social_show_frontpage'))) {
			return false;
		}

		return $this->config->get('main_twitter_button');
	}
}
