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

class EasyBlogAdsense extends EasyBlog
{
	/**
	 * Generates the html codes for adsense
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html(EasyBlogPost $post)
	{
		$result = new stdClass();
		$result->header = '';
		$result->beforecomments = '';
		$result->footer = '';

		// Standard code
		$code = $this->config->get('integration_google_adsense_code');

		// Responsive code
		$responsiveCode = $this->config->get('integration_google_adsense_responsive_code');

		if (!$code || $responsiveCode && $this->config->get('integration_google_adsense_responsive')) {
			$code = $responsiveCode;
		}

		// Determines the location of the ads
		$location = $this->config->get('integration_google_adsense_display');

		// Ensure that adsense is enabled
		if (!$this->config->get('integration_google_adsense_enable')) {
			return $result;
		}

		// Determines who should we display the ads to
		$displayAccess = $this->config->get('integration_google_adsense_display_access');

		// If user is a guest and guest visibilty for ads are disabled, hide it.
		if ($displayAccess == 'members' && $this->my->guest) {
			return $result;
		}

		// If user is a guest, and settings is configured to be displayed to guests only, hide it.
		if ($this->config->get('integration_google_adsense_display_access') == 'guests' && !$this->my->guest) {
			return $result;
		}

		// Check if author enabled their own adsense
		$adsense = EB::table('Adsense');
		$adsense->load($post->getAuthor()->id);

		if ($adsense->code && $adsense->published) {
			$code = $adsense->code;
			$location = $adsense->display;
		}

		if ($location == 'userspecified') {
			return $result;
		}

		// If we can't find any adsense code, skip this
		if (!$code) {
			return $result;
		}

		$responsive = $this->config->get('integration_google_adsense_responsive');

		$theme = EB::template();
		$theme->set('code', $code);

		$namespace = 'site/adsense/responsive';

		if (!$responsive) {
			$namespace = 'site/adsense/code';
		}

		$html = $theme->output($namespace);

		if ($location == 'both') {
			$result->header = $html;
			$result->footer = $html;
		} else {
			$result->$location = $html;
		}

		return $result;
	}

	/**
	 * Process adsense codes
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function process($content, $bloggerId)
	{
		// If there's no content, we should skip this altogether
		if (!$content || !$bloggerId) {
			return $content;
		}

		// $pattern	= '/\{eblogads.*\}/si';
		$pattern = '/\{eblogads(\sleft|\sright)?\}/i';

		preg_match_all($pattern, $content, $matches);

		$adscode = $matches[0];

		if (count($adscode) > 0) {

			foreach ($adscode as $code) {
				$codes = explode(' ', $code);
				$alignment = (isset($codes[1])) ? $codes[1] : '';
				$alignment = str_ireplace('}', '', $alignment);

				$html = $this->_getAdsenseTemplate($bloggerId, $alignment);

				$content = str_ireplace($code, $html, $content);
			}
		}

		return $content;
	}

	/**
	 * Process adsense blocks
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function processsAdsenseCode( $content, $bloggerId )
	{
		$this->process($content, $bloggerId);
	}

	/**
	 * Strips all adsense codes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function strip($content)
	{
		$pattern = '/\{eblogads.*\}/i';
		$content = preg_replace($pattern, '', $content);

		return $content;
	}

	/**
	 * Strip adsense codes
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function stripAdsenseCode( $content )
	{
		return $this->strip($content);
	}

	private static function _getAdsenseTemplate( $bloggerId, $alignment = '')
	{
		$config = EB::getConfig();
		$my = JFactory::getUser();

		if ($config->get( 'integration_google_adsense_display_access' ) == 'members' && $my->id == 0) {
			return '';
		}

		if ($config->get( 'integration_google_adsense_display_access' ) == 'guests' && $my->id > 0) {
			return '';
		}

		if (! $config->get('integration_google_adsense_enable')) {
			return '';
		}

		if ($config->get('integration_google_adsense_centralized')) {
			$adminAdsenseCode = $config->get('integration_google_adsense_code');
			$adsenseResponsiveCode = $config->get('integration_google_adsense_responsive_code');
			$adminAdsenseDisplay = $config->get('integration_google_adsense_display');


			if (!empty($adminAdsenseCode) && !$config->get('integration_google_adsense_responsive')) {
				$defaultCode = $adminAdsenseCode;
				$defaultDisplay	= $adminAdsenseDisplay;
			} else {
				$defaultCode = $adsenseResponsiveCode;
				$defaultDisplay	= $adminAdsenseDisplay;
			}
		}

		//blogger adsense
		//now we check whether user enabled adsense or not.
		$bloggerAdsense = EB::table('Adsense');
		$bloggerAdsense->load($bloggerId);


		if (!empty($bloggerAdsense->code) && $bloggerAdsense->published) {
			$defaultCode = $bloggerAdsense->code;
			$defaultDisplay	= $bloggerAdsense->display;
		}

		// @task: If the user did not enter any adsense codes, fallback to the site admin's code
		if (empty($defaultCode)) {
			$adminAdsenseCode = $config->get('integration_google_adsense_code');
			$adminAdsenseDisplay = $config->get('integration_google_adsense_display');

			if (!empty($adminAdsenseCode)) {
				$defaultCode = $adminAdsenseCode;
				$defaultDisplay	= $adminAdsenseDisplay;
			}
		}

		if ($defaultDisplay != 'userspecified') {
			return '';
		}

		$responsive = $config->get('integration_google_adsense_responsive');

		$theme = EB::template();
		$theme->set('code', $defaultCode);


		$align = '';
		if (!empty($alignment)) {
		    $align = ($alignment == 'right') ? ' alignright' : ' alignleft';
		}

		$theme->set('alignment', $align);

		$namespace = 'site/adsense/responsive';

		if (!$responsive) {
			$namespace = 'site/adsense/code';
		}

		$adsenseHTML = $theme->output($namespace);
		return $adsenseHTML;
	}
}
