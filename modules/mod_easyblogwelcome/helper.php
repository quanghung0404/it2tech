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

class modEasyBlogWelcomeHelper
{
	/**
	 * Retrieves the return url
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function getReturnURL($params)
	{
		$my = JFactory::getUser();

		$type = $my->guest ? 'login' : 'logout';

		// Get the menu id to redirect to
		$itemid = $params->get($type);

		// Default to stay on the same page.
		$uri = JFactory::getURI();
		$return = $uri->toString(array('path', 'query', 'fragment'));

		if ($itemid) {
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getItem($itemid);

			// If there's a menu item
			if ($item) {
				//$return = JRoute::_($item->link . '&Itemid=' . $itemid, false);
				// if setup login redirection menu item, it should return back redirection url to SEF
				$return = rtrim(JURI::root(), '/') . JRoute::_($item->link . '&Itemid=' . $itemid, false);

			} else {
				$return = JUri::getInstance()->toString();
			}
		} else {
			$return = JUri::getInstance()->toString();
		}



		return base64_encode($return);
	}

	public static function getBloggerProfile($userid)
	{
		if(empty($userid)) {
			return false;
		}

		$blogger = EB::user($userid);

		$integrate	= new EasyBlogIntegrate();
		$profile	= $integrate->integrate($blogger);

		$profile->displayName   = $blogger->getName();

		return $profile;
	}
}
