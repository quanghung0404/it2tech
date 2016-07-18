<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include the fields library
FD::import('admin:/includes/fields/dependencies');

// Include helper file.
FD::import('fields:/user/joomla_twofactor/helper');

/**
 * Field application for Joomla username
 *
 * @since	1.3
 */
class SocialFieldsUserJoomla_twofactor extends SocialFieldItem
{
	public function __construct()
	{
		parent::__construct();

		// This requires the FOF framework
		// Load the Joomla! RAD layer
		if (!defined('FOF_INCLUDED')) {
			include_once JPATH_LIBRARIES . '/fof/include.php';
		}
	}

	/**
	 * When a user saves their profile, we need to set the two factor data
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEditBeforeSave(&$data, SocialUser &$user)
	{
		// This feature is only available if the totp plugins are enabled
		if (!SocialTwoFactorHelper::isEnabled()) {
			return;
		}

		// Determines if the user wants to enable two factor authentication
		$enabled = isset($data[$this->inputName]) ? $data[$this->inputName] : false;

		// Ensure that the user selects a two factor authentication method
		$method = isset($data['twofactor_method']) ? $data['twofactor_method'] : false;

		// If the method is not totp, we don't wan't to do anything
		if ($method != 'totp' || !$enabled) {

			// We also want to make sure the user's OTP and OTEP is cleared
			$user->otpKey = '';
			$user->otep   = '';

			return;
		}

		$twofactor = isset($data['jform']) ? $data['jform'] : false;

		if (!$twofactor) {
			return;
		}

		$twofactor = json_decode($twofactor);

		// Get the user's otp configuration
		$otpConfig = $user->getOtpConfig();

		// If user has already configured.
		if ($otpConfig->method && $otpConfig->method != 'none') {
			return;
		}

		// Trigger Joomla's twofactorauth plugin to process the configuration since we do not want to handle those encryption stuffs.
		FOFPlatform::getInstance()->importPlugin('twofactorauth');
		$otpConfigReplies = FOFPlatform::getInstance()->runPlugins('onUserTwofactorApplyConfiguration', array($method));


		// Look for a valid reply
		foreach ($otpConfigReplies as $reply) {

			if (!is_object($reply) || empty($reply->method) || ($reply->method != $method)) {
				continue;
			}

			$otpConfig->method = $reply->method;
			$otpConfig->config = $reply->config;

			break;
		}

		// If the method is still none, we need to disable this
		if ($otpConfig->method == 'none') {
			$data[$this->inputName] = false;
		}


		// If the method is still false, we need to ensure that twofactor is disabled
		// Generate one time emergency passwords if required (depleted or not set)
		if (empty($otpConfig->otep)) {
			$otpConfig->otep = SocialTwoFactorHelper::generateOteps($otpConfig);
		}

		// Save OTP configuration.
		$user->setOtpConfig($otpConfig);

		return true;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		$config = FD::config();

		return $this->display();
	}

	/**
	 * Displays the field input for user when they edit their account.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	array	The posted data.
	 * @param	SocialTableRegistration
	 * @param	array	The errors
	 * @return	string	The html output.
	 *
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		// This feature is only available if the totp plugins are enabled
		if (!SocialTwoFactorHelper::isEnabled()) {
			return;
		}

		// Load com_users language file
		JFactory::getLanguage()->load('com_users', JPATH_ADMINISTRATOR);

		// Determines if there's any errors on the form
		$error = $this->getError($errors);

		// Display the two factor form
		$methods = SocialTwoFactorHelper::getMethods($user->id);

		$this->set('methods', $methods);
		$this->set('error', $error);
		$this->set('user', $user);

		return $this->display();
	}
}
