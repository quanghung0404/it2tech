<?php
/**
 * @version    $Id: jsn_is_message.php 16139 2012-09-19 03:33:09Z giangnd $
 * @package    JSN.ImageShow
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 *
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Message Class
 *
 * @package  JSN.ImageShow
 * @since    2.5
 *
 */

class JSNISMessage
{

	/**
	 * Contructor
	 *
	 */

	public function JSNISMessage()
	{

	}

	/**
	 * Signleton pattern
	 *
	 * @return a instance
	 */

	public static function getInstance()
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (empty($instances['JSNISMessage']))
		{
			$instance	= new JSNISMessage;
			$instances['JSNISMessage'] = &$instance;
		}

		return $instances['JSNISMessage'];
	}

	/**
	 * Get message list.
	 *
	 * @param   string   $screen  Message screen.
	 * @param   boolean  $all     Query for both published and unpublished messages or published only.
	 *
	 * @return  array	Array of database query result objects.
	 */

	public function getList($screen = '', $all = false)
	{
		// Build query conditions
		$all           OR $where[] = "published = 1";
		empty($screen) OR $where[] = "`msg_screen` = '{$screen}'";

		// Get name of messages table
		$table = '#__jsn_' . preg_replace('/^com_/i', '', JFactory::getApplication()->input->getCmd('option')) . '_messages';

		// Get message list
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($table);
		$query->where(isset($where) ? implode(' AND ', $where) : '1');
		$query->order(array('msg_screen', 'ordering'));

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Set status for specified messages.
	 *
	 * @param   array   $cid     Message ID.
	 * @param   string  $screen  The specified screen .
	 *
	 * @return  true.
	 */

	public function setMessagesStatus($cid, $screen = '')
	{
		$db 		= JFactory::getDBO();
		$where 		= '';
		$whereAll 	= '';

		if ($screen != '')
		{
			$where 		= ' AND msg_screen = \'' . $screen . '\'';
			$whereAll 	= ' WHERE msg_screen = \'' . $screen . '\'';
		}

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids 	= implode(',', $cid);
			$query 	= 'UPDATE #__jsn_imageshow_messages'
			. ' SET published = 1'
			. ' WHERE msg_id IN (' . $cids . ')' . $where;
			$db->setQuery($query);
			$db->query();

			$query = 'UPDATE #__jsn_imageshow_messages'
			. ' SET published = 0'
			. ' WHERE msg_id NOT IN (' . $cids . ')' . $where;
			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = 'UPDATE #__jsn_imageshow_messages'
			. ' SET published = 0' . $whereAll;
			$db->setQuery($query);
			$db->query();
		}
		return true;
	}

	/**
	 * Refesh all messages
	 *
	 * @return  true.
	 */

	public function refreshMessage()
	{
		$db 					= JFactory::getDBO();
		$lang 					= JFactory::getLanguage();
		$currentlang   			= $lang->getTag();
		$objectReadxmlDetail 	= JSNISFactory::getObj('classes.jsn_is_readxmldetails');
		$objJNSUtils			= JSNISFactory::getObj('classes.jsn_is_utils');
		$infoXmlDetail 			= $objectReadxmlDetail->parserXMLDetails();
		$langSupport 			= $infoXmlDetail['langs'];
		$registry				= new JRegistry;
		$newStrings				= array();
		$path 					= null;
		$realLang				= null;
		$queries				= array();
		$pathEn					= JLanguage::getLanguagePath(JPATH_BASE, 'en-GB');

		if (array_key_exists($currentlang, $langSupport))
		{
			$path 		= JLanguage::getLanguagePath(JPATH_BASE, $currentlang);
			$realLang	= $currentlang;
		}
		else
		{
			if (!JFolder::exists($pathEn))
			{
				$filepath 		= JPATH_ROOT . DS . 'administrator' . DS . 'language';
				$foldersLang 	= $this->getFolder($filepath);

				foreach ($foldersLang as $value)
				{
					if (in_array($value, $langSupport))
					{
						$path 		= JLanguage::getLanguagePath(JPATH_BASE, $value);
						$realLang	= $value;
						break;
					}
				}
			}
		}

		if (JFolder::exists($pathEn))
		{
			$filename = $pathEn . DS . 'en-GB' . '.com_imageshow.ini';
		}
		else
		{
			$filename = $path . DS . $realLang . '.com_imageshow.ini';
		}

		$content = $objJNSUtils->readFileToString($filename);

		if ($content)
		{
			$registry->loadINI($content);
			$newStrings	= $registry->toArray();

			if (count($newStrings))
			{
				if (count($infoXmlDetail['menu']))
				{
					$queries [] = 'TRUNCATE TABLE #__jsn_imageshow_messages';

					foreach ($infoXmlDetail['menu'] as $value)
					{
						$index = 1;
						while (isset($newStrings['MESSAGE_' . $value . '_' . $index . '_PRIMARY']))
						{
							$queries [] = 'INSERT INTO #__jsn_imageshow_messages (msg_screen, published, ordering) VALUES (\'' . $value . '\', 1, ' . $index . ')';
							$index ++;
						}
					}
				}
			}

			if (count($queries))
			{
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '')
					{
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
		return true;
	}

	/**
	 * Disable a message
	 *
	 * @param int $msgID The messages ID
	 *
	 * @return bool
	 */
	public function disableMessage($msgID)
	{
		$db 	= JFactory::getDBO();
		$query 	= 'UPDATE #__jsn_imageshow_messages'
		. ' SET published = 0'
		. ' WHERE msg_id = ' . $msgID;
		$db->setQuery($query);
		$db->query();
		return true;
	}
}
