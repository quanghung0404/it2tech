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

class EasyBlogFollowers extends EasyBlog
{
	/**
	 * Determines if achievements should be displayed beneath the author box.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function hasIntegrations(EasyBlogTableProfile $profile)
	{
		// We shouldn't allow the current viewer to follow themselves.
		if ($profile->id == $this->my->id) {
			return false;
		}

		if (!EB::easysocial()->exists()) {
			return false;
		}

		if (!$this->config->get('integrations_easysocial_followers')) {
			return false;
		}

		$user = FD::user($profile->id);
		$followed = $user->isFollowed($this->my->id);

		if ($followed) {
			return false;
		}

		return true;
	}

	/**
	 * Renders the output of the followers link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html(EasyBlogTableProfile $profile)
	{
		// Initialize the scripts
		EB::easysocial()->init();

		$user = FD::user($profile->id);

		$theme = EB::template();
		$theme->set('user', $user);
		$html = $theme->output('site/easysocial/followers');

		return $html;
	}
}
