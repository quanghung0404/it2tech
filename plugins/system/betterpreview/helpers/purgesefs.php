<?php
/**
 * Purge SEF URLs page
 * Empty the SEF URL databas table
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

if (!JFactory::getApplication()->isAdmin())
{
	die('No Access!');
}

// need to set the user agent, to prevent breaking when debugging is switched on
$_SERVER['HTTP_USER_AGENT'] = '';

$db = JFactory::getDbo();

$query = $db->getQuery(true)
	->delete('#__betterpreview_sefs');
$db->setQuery($query);
$db->execute();
