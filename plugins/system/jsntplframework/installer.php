<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  TPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Class to implement hook for installation process
 *
 * @package     TPLFramework
 * @subpackage  Plugin
 * @since       1.0.0
 */
class PlgSystemJSNTPLFrameworkInstallerScript
{
	/**
	 * Implement preflight hook.
	 *
	 * @param   string  $route  Route type: install, update or uninstall.
	 * @param   object  $parent  The installer object.
	 *
	 * @return  boolean
	 */
	
	public function preflight($route, $parent)
	{
		$this->_updateSchema();
	}
	
	/**
	 * Implement postflight hook.
	 *
	 * @param   string  $route  Route type: install, update or uninstall.
	 * @param   object  $_this  The installer object.
	 *
	 * @return  boolean
	 */
	public function postflight($route, $_this)
	{
		// Get a database connector object
		$db = JFactory::getDbo();

		try
		{
			// Enable plugin by default
			$q = $db->getQuery(true);

			$q->update('#__extensions');
			$q->set(array('enabled = 1', 'protected = 1', 'ordering = 9999'));
			$q->where("element = 'jsntplframework'");
			$q->where("type = 'plugin'", 'AND');
			$q->where("folder = 'system'", 'AND');

			$db->setQuery($q);

			method_exists($db, 'execute') ? $db->execute() : $db->query();
			
			jimport('joomla.filesystem.folder');
			
			$path = JPATH_PLUGINS . '/system/jsntplframework/html/com_contact';
			
			if (JFolder::exists($path))
			{
				JFolder::delete($path);
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	
	/**
	 * Update Extension Schema
	 *
	 * @param   int  Extension ID.
	 * @return boolean
	 */
	private function _updateSchema()
	{
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('element' => 'jsntplframework', 'type' => 'plugin'));
		if ($eid)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('version_id')
			->from('#__schemas')
			->where('extension_id = ' . $eid);
			$db->setQuery($query);
			$version = $db->loadResult();
	
			if (is_null($version))
			{
				$info = $this->_getInfo($eid);
				$info = json_decode($info->manifest_cache);
				$query = $db->getQuery(true);
				$query->delete()
				->from('#__schemas')
				->where('extension_id = ' . $eid);
				$db->setQuery($query);
				
				try
				{
					method_exists($db, 'execute') ? $db->execute() : $db->query();
				}
				catch (Exception $e)
				{
					return false;
				}
	
				$query->clear();
				$query->insert($db->quoteName('#__schemas'));
				$query->columns(array($db->quoteName('extension_id'), $db->quoteName('version_id')));
				$query->values($eid . ', ' . $db->quote($info->version));
				$db->setQuery($query);
	
				try
				{
					method_exists($db, 'execute') ? $db->execute() : $db->query();
				}
				catch (Exception $e)
				{
					return false;
				}
			}
		}
	
		return true;
	}
	
	/**
	 * Get extension info
	 *
	 * @param   int  Extension ID.
	 * @return  object
	 */
	private function _getInfo($id)
	{
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where($db->quoteName('element') . ' = ' . $db->quote('jsntplframework'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$query->where($db->quoteName('folder') . ' = ' . $db->quote('system'));
		$query->where($db->quoteName('extension_id') . ' = ' . $db->quote((int) $id));
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}	
}
