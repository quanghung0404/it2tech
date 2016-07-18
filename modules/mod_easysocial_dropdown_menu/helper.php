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

class ModEasySocialDropdownMenuHelper
{
	public static function getItems( &$params )
	{
		// Determine if we need to render menu items from specific menu.
		$menuType 		= $params->get( 'menu_type' , '' );

		if( empty( $menuType ) )
		{
			return false;
		}

		$menu			= JFactory::getApplication()->getMenu();
		$items 			= $menu->getItems( 'menutype' , $menuType );

		if( !$items )
		{
			return false;
		}

		foreach( $items as &$item )
		{
			self::buildRoute( $item );
		}

		return $items;

	}

	/**
	 * Returns the login url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getReturnURL(&$params)
	{
		// $params->get( 'return' , FD::getCallback() );
		$app = JFactory::getApplication();
		$router = $app->getRouter();
		$url = null;

		$itemid = $params->get('loginreturn');

		if ($itemid != '-1' && $itemid){

			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true)
				->select($db->quoteName('link'))
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('published') . '=1')
				->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);

			if ($link = $db->loadResult()) {
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$url = 'index.php?Itemid='.$itemid;
				} else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}

		if (!$url) {
			// Stay on the same page
			$url = JRequest::getURI();
		}

		return base64_encode($url);
	}

	public static function buildRoute( &$item )
	{
		$item->flink  = $item->link;

		// Reverted back for CMS version 2.5.6
		switch ($item->type)
		{
			case 'separator':
			case 'heading':
				// No further action needed.
				continue;

			case 'url':
				if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
				{
					// If this is an internal Joomla link, ensure the Itemid is set.
					$item->flink = $item->link . '&Itemid=' . $item->id;
				}
				break;

			case 'alias':
				// If this is an alias use the item id stored in the parameters to make the link.
				$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
				break;

			default:
				$router = JSite::getRouter();
				if ($router->getMode() == JROUTER_MODE_SEF)
				{
					$item->flink = 'index.php?Itemid=' . $item->id;
				}
				else
				{
					$item->flink .= '&Itemid=' . $item->id;
				}
				break;
		}

		if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false))
		{
			$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));
		}
		else
		{
			$item->flink = JRoute::_($item->flink);
		}
	}
}
