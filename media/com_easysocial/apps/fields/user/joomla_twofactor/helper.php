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

class SocialTwoFactorHelper
{
	/**
	 * Determines if the two factor authentication should be enabled
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isEnabled()
	{
		$config  = FD::config();
		$enabled = JPluginHelper::isEnabled('twofactorauth', 'totp');

		if (!$enabled || !$config->get('general.site.twofactor')) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the available methods on the site.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getMethods($userId = '')
	{
		// Import the two factor authentication plugin
		FOFPlatform::getInstance()->importPlugin('twofactorauth');
		$identities = FOFPlatform::getInstance()->runPlugins('onUserTwofactorIdentify', array());

		$forms   = self::getForm($userId);
		$methods = array();

		if (!empty($identities)) {

			foreach ($identities as $identity) {
				if (!is_object($identity)) {
					continue;
				}

				foreach ($forms as $form) {
					if ($form['method'] == $identity->method) {
						$identity->form = $form['form'];
					}
				}

				$methods[] = $identity;
			}
		}

		return $methods;
	}

	/**
	 * Retrieves the two factor plugin forms
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getForm($userId = '')
	{
		FOFPlatform::getInstance()->importPlugin('twofactorauth');

		if (!$userId) {
			$otpConfig = new stdClass();
			$otpConfig->method = 'none';
			$otpConfig->config = array();
			$otpConfig->otep   = array();
		} else {
			$user = FD::user($userId);
			$otpConfig = $user->getOtpConfig();
		}

		return FOFPlatform::getInstance()->runPlugins('onUserTwofactorShowConfiguration', array($otpConfig, $userId));
	}

	/**
	 * Generates a set of One Time Emergency Passwords (OTEPs) for a user. Technique taken from Joomla
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function generateOteps($otpConfig, $count = 10)
	{
		// Initialise
		$oteps = array();

		// If two factor authentication is not enabled, abort
		if (empty($otpConfig->method) || ($otpConfig->method == 'none')) {
			return $oteps;
		}

		$salt   = "0123456789";
		$base   = strlen($salt);
		$length = 16;

		for ($i = 0; $i < $count; $i++) {

			$makepass = '';
			$random   = JCrypt::genRandomBytes($length + 1);
			$shift    = ord($random[0]);

			for ($j = 1; $j <= $length; ++$j) {
				$makepass .= $salt[($shift + ord($random[$j])) % $base];
				$shift += ord($random[$j]);
			}

			$oteps[] = $makepass;
		}

		return $oteps;
	}

}
