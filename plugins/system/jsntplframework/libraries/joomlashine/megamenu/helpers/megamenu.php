<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2016 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

class JSNTplMMHelperMegamenu
{
	/**
	 * Get megamenu data
	 * 
	 */
	public static function getMegamenuItemsByStyleId($styleID, $language = '')
	{
		$checkMegamenuTableExits = JSNTplHelper::checkMegamenuTable();
		if (!$checkMegamenuTableExits)
		{
			return null;
		}
				
		$app = JFactory::getApplication();
		if (!$styleID)
		{
			return null;
		}
		
		if ($app->isSite())
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)');
			$query->from($db->quoteName('#__jsn_tplframework_megamenu'));
			$query->where('style_id=' . $db->quote((int) $styleID));
			$query->where('LOWER(language_code)=' . $db->quote((string) strtolower($language)));
			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$count = $db->loadResult();
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__jsn_tplframework_megamenu'));
		$query->where('style_id=' . $db->quote((int) $styleID));
		if ($app->isSite() && !$count)
		{
			//Get All for no language matched
			$query->where('LOWER(language_code)=' . $db->quote((string) '*'));
		}
		else if ($language)
		{
			$query->where('LOWER(language_code)=' . $db->quote((string) strtolower($language)));
		}
		$query->order('modified DESC');

		$db->setQuery($query);
		
		return $db->loadObject();
	}
	/**
	 * Migate all megamenu data from old version to new version
	 * 
	 */
	public static function migrate()
	{
		$checkMegamenuTableExits = JSNTplHelper::checkMegamenuTable();
		if (!$checkMegamenuTableExits)
		{
			return false;
		}
		
		$app   		= JFactory::getApplication();
		$input		= $app->input;
		$styleID	= $input->getInt('id', 0);
		
		$result = self::getTemplateStyle($styleID);
		$check = self::isMigrated($result);
		if ($check) 
		{
			self::executeMigration($styleID, $result);
		}
		
	}
	
	/**
	 * Check whether Megamenu data need to be migrated or not  
	 * @param object $results Template style data
	 * 
	 */
	public static function isMigrated($results)
	{
		if ($results->params)
		{
			$megamenu = json_decode($results->params, TRUE);
				
			if (isset($megamenu['megamenu']) && count($megamenu['megamenu']))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get Template style
	 * 
	 * @param int $styleID	Template Style ID
	 * @param string $template	Template name
	 * 
	 * @return (object)
	 */
	public static function getTemplateStyle($styleID, $template = '')
	{

		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		
		$query->from($db->quoteName('#__template_styles'));
		$query->where('client_id = 0 AND id = ' . (int) $styleID);
		if ($template)
		{
			$query->where('template = ' . $db->quote($template));
		}
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/**
	 * execute migration
	 * 
	 * @param int $styleID	Template Style ID
	 * @param object $results Template style data
	 * 
	 * @return (boolean) true/false;
	 */
	public static function executeMigration($styleID, $results)
	{
		$params = array();
		if (count($results))
		{
			$oldParams 	= json_decode($results->params, TRUE);
			$params 	= $oldParams['megamenu'];
		}
		self::cleanOldMegamenuItemData($styleID);
		$db 	= JFactory::getDbo();
		$date 	= JFactory::getDate()->toSql();
		
		$query = $db->getQuery(true);
		$columns = array('style_id', 'language_code', 'menu_type', 'params', 'created', 'modified');
		
		$lang = isset($params['megamenu']['language']) ? $params['megamenu']['language'] : '*';
		// Insert values.
		$values = array(
				$db->quote((int) $styleID),
				$db->quote((string) $lang),
				$db->quote((string) $params['menuType']),
				$db->quote(json_encode($params)),
				$db->quote($date),
				$db->quote($date)
		);
		$query
		->insert($db->quoteName('#__jsn_tplframework_megamenu'))
		->columns($db->quoteName($columns))
		->values(implode(',', $values));
			
		$db->setQuery($query);
		try 
		{
			$execute = $db->execute();
			if ($execute)
			{
				// Update old megamenu
				unset($oldParams['megamenu']);
			
				$query = $db->getQuery(true)
				->update('#__template_styles')
				->set('params=' . $db->quote(json_encode($oldParams)))
				->where('client_id=0')
				->where('id=' . $db->quote((int) $styleID));
					
				$db->setQuery($query);
				return $db->execute();
			}
		}
		catch (Exception $e)
		{
			return false;
		}
			
	}
	
	/**
	 * Clean clean Old Megamenu Item Data before Migration
	 * 
	 * @param int $styleID Template Style ID
	 * 
	 * @return boolean
	 */
	public static function cleanOldMegamenuItemData($styleID)
	{
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__jsn_tplframework_megamenu'));
		$query->where($db->quoteName('style_id') . ' = ' . $db->quote((int) $styleID));
		$db->setQuery($query);
		
		try
		{
			$megamenuItems = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			return false;
		}	
		
		if (!count($megamenuItems)) return false;
		
		$query = $db->getQuery(true);			
		$query->delete($db->quoteName('#__jsn_tplframework_megamenu'));
		$query->where($db->quoteName('style_id') . ' = ' . $db->quote((int) $styleID));
		$db->setQuery($query);
		
		try
		{
			method_exists($db, 'execute') ? $db->execute() : $db->query();
		}
		catch (Exception $e)
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Get Template Home style
	 *
	 * @param string $template	Template name
	 *
	 * @return (object)
	 */
	public static function getTemplateHomeStyle($template)
	{
	
		$db = JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
	
		$query->from($db->quoteName('#__template_styles'));
		$query->where('client_id = 0 AND home = 1');
		$query->where('template = ' . $db->quote($template));
		$db->setQuery($query);
		return $db->loadObject();
	}	
}