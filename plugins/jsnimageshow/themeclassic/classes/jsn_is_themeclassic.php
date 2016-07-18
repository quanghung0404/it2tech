<?php
/**
 * @version    $Id: jsn_is_themeclassic.php 16394 2012-09-25 08:31:07Z giangnd $
 * @package    JSN.ImageShow
 * @subpackage JSN.ThemeClassic
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) @JOOMLASHINECOPYRIGHTYEAR@ JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
defined('_JEXEC') or die('Restricted access');

class JSNISThemeClassic
{
	function getSkin($themeID, $showcaseID)
	{
		$db    	= JFactory::getDbo();
		$query 	= 'SELECT * FROM #__imageshow_theme_profile WHERE showcase_id = ' . (int) $showcaseID . ' AND theme_id = ' . (int) $themeID;
		$db->setQuery($query);
		$data 	= $db->loadObject();

		if (!count($data))
		{
			return 'javascript';
		}
		else
		{
			if ($data->theme_style_name == '')
			{
				return 'flash';
			}
			else
			{
				return $data->theme_style_name;
			}
		}
	}

	function deleteRecordOfSpecifiedTable($themeID, $tableSuffix)
	{
		$db = JFactory::getDbo();
		$query = 'DELETE FROM #__imageshow_theme_classic_' . $tableSuffix . ' WHERE theme_id = ' . (int) $themeID;
		$db->setQuery($query);
		return ($db->query()) ? true : false;
	}

	function deleteThemeProfile($themeID, $showcaseID)
	{
		$db = JFactory::getDbo();
		$query = 'DELETE FROM #__imageshow_theme_profile WHERE theme_id = ' . (int) $themeID . ' AND theme_name="themeclassic" AND showcase_id = ' . (int) $showcaseID;
		$db->setQuery($query);
		return ($db->query()) ? true : false;
	}

	function recordIsExistedInSpecifiedTabled($themeID, $showcaseID, $themeStyleName, $tableSuffix)
	{
		$db = JFactory::getDbo();
		$query = 'SELECT COUNT(*) FROM #__imageshow_theme_classic_' . $tableSuffix . ' AS js INNER JOIN  #__imageshow_theme_profile AS p
		ON js.theme_id = p.theme_id WHERE p.showcase_id = ' . (int) $showcaseID . ' AND js.theme_id = ' . (int) $themeID . ' AND p.theme_name="themeclassic" AND p.theme_style_name = ' . $db->Quote($db->getEscaped($themeStyleName, true ), false);

		$db->setQuery($query);
		return (bool) $db->loadResult();
	}
}