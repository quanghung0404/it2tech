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

class EasySocialModRegisterHelper
{
	/**
	 * Returns the login url
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getReturnURL( &$params )
	{
		$app		= JFactory::getApplication();
		$router		= $app->getRouter();
		$url		= null;

		$itemid 	= $params->get( 'return' );

		if( $itemid )
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true)
				->select($db->quoteName('link'))
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('published') . '=1')
				->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);
			if ($link = $db->loadResult())
			{
				if ($router->getMode() == JROUTER_MODE_SEF)
				{
					$url = 'index.php?Itemid='.$itemid;
				}
				else {
					$url = $link.'&Itemid='.$itemid;
				}
			}
		}

		if (!$url)
		{
			// Stay on the same page
			$uri = clone JUri::getInstance();
			$vars = $router->parse($uri);
			unset($vars['lang']);
			if ($router->getMode() == JROUTER_MODE_SEF)
			{
				if (isset($vars['Itemid']))
				{
					$itemid = $vars['Itemid'];
					$menu = $app->getMenu();
					$item = $menu->getItem($itemid);
					unset($vars['Itemid']);
					if (isset($item) && $vars == $item->query)
					{
						$url = 'index.php?Itemid='.$itemid;
					}
					else {
						$url = 'index.php?'.JUri::buildQuery($vars).'&Itemid='.$itemid;
					}
				}
				else
				{
					$url = 'index.php?'.JUri::buildQuery($vars);
				}
			}
			else
			{
				$url = 'index.php?'.JUri::buildQuery($vars);
			}
		}

		return base64_encode($url);
	}
}
