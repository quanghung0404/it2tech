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

class EasyBlogSocialButtonXing extends EasyBlogSocialButton
{
	public $type = 'xing';

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
		if (!$this->isEnabled()) {
			return;
		}

		$size = $this->getButtonSize();
		$placeholder = $this->getPlaceholderId();

		// Get the absolute url to this blog post
		$url = $this->getUrl();

		// Ge the formatted title to this blog post
		$title = $this->getTitle();

		$theme 	= EB::template();
		$theme->set('url', $url);
		$theme->set('title', $title);
		$theme->set('size', $size);
		$theme->set('placeholder', $placeholder);

		$output = $theme->output('site/socialbuttons/xing');
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
		if ($this->frontpage && !$this->config->get('main_xing_button_frontpage', $this->config->get('social_show_frontpage'))) {
			return false;
		}

		return $this->config->get('main_xing_button');
	}
}
