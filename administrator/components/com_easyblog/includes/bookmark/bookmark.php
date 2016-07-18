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

class EasyBlogBookmark
{
	public function __construct()
	{
		$this->config = EB::config();
		$this->my = JFactory::getUser();
	}

	public function allow($params)
	{
		// Get the bookmark provider
		$provider = $params->get('post_bookmark_provider');

		// Determine if the social button should appear
		$enabled = $params->get('post_bookmark');

		if (!$enabled) {
			return false;
		}

		if ($enabled == 2 && $this->my->guest) {
			return false;
		}

		if (!method_exists($this, $provider)) {
			return false;
		}

		return true;
	}

	public function html($params)
	{
		// Get the bookmark provider
		$provider = $params->get('post_bookmark_provider');

		// Determine if the social button should appear
		$enabled = $params->get('post_bookmark');

		if (!$enabled) {
			return;
		}


		if ($enabled == 2 && $this->my->guest) {
			return;
		}

		if (!method_exists($this, $provider)) {
			return;
		}

		return $this->$provider();
	}




	/**
	 * Displays addthis social sharing widget
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addthis()
	{
		$code  = $this->config->get('social_addthis_customcode', 'xa-4be11e1875bf6363');

		if (empty($code)) {
			return;
		}

		$style = $this->config->get('social_addthis_style', 1);

		$text  = JText::_('COM_EASYBLOG_BOOKMARK');

		$theme = EB::template();
		$theme->set('code', $code);
		$theme->set('text', $text);

		return $theme->output('site/blogs/tools/addthis.style' . $style);
	}

	/**
	 * Renders the sharethis social sharing widget
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sharethis()
	{
		$code = $this->config->get('social_sharethis_publishers');

		if (empty($code)) {
			return;
		}


		$theme = EB::template();
		$theme->set('code', $code);

		return $theme->output('site/blogs/tools/sharethis');
	}
}
