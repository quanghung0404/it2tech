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

jimport('joomla.filesystem.file');

class SocialAcyMailingHelper
{
	/**
	 * Determines if Acymailing is enabled
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isEnabled()
	{
		$file 	= JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Retrieves a list of acymailing lists
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getLists()
	{
		if (!self::isEnabled()) {
			return false;
		}

		$lib = acymailing_get('class.list');

		$lists = $lib->getLists();

		return $lists;
	}

	/**
	 * Inserts a new user in acymailing list
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unsubscribe($lists, SocialUser &$user)
	{
		// Test if it is enabled first
		if (!self::isEnabled()) {
			return false;
		}

		$lib = acymailing_get('class.subscriber');

		$newSubscription = array();

		foreach ($lists as $id) {
			$newList = array();
			$newList['status'] = 0;

			$newSubscription[$id] = $newList;
		}

		// Get subscription id for this particular user
		$subscriberId = $lib->subid($user->id);

		if (!$subscriberId) {
			return false;
		}

		return $lib->saveSubscription($subscriberId, $newSubscription);
	}

	public static function isSubscribed($listId, SocialUser &$user)
	{
		$userClass = acymailing_get('class.subscriber');
		$subscribedLists = $userClass->subid($user->id);

		$lib = acymailing_get('class.listsub');
		$subscriptions = $lib->getSubscription($subscribedLists);


		foreach ($subscriptions as $subscription) {

			if ($subscription->listid == $listId) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Inserts a new user in acymailing list
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function subscribe($lists, SocialUser &$user)
	{
		// Test if it is enabled first
		if (!self::isEnabled()) {
			return false;
		}

		$lib = acymailing_get('class.subscriber');

		$newSubscription = array();

		foreach ($lists as $id) {
			$newList = array();
			$newList['status'] = 1;

			$newSubscription[$id] = $newList;
		}

		// Get subscription id for this particular user
		$subscriberId = $lib->subid($user->id);

		if (!$subscriberId) {
			return false;
		}

		return $lib->saveSubscription($subscriberId, $newSubscription);
	}
}
