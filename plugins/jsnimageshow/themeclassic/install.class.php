<?php
/**
 * @version    $Id: install.class.php 16438 2012-09-26 04:34:43Z giangnd $
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

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

class plgjsnimageshowThemeClassicInstallerScript
{
	public function __construct()
	{
	}

	public function preflight($mode, $parent)
	{
		$this->_updateSchema();
	}

	private function _updateSchema()
	{
		$row = JTable::getInstance('extension');
		$eid = $row->find(array('element' => 'themeclassic', 'type' => 'plugin'));
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
				if ($db->Query())
				{
					$query->clear();
					$query->insert($db->quoteName('#__schemas'));
					$query->columns(array($db->quoteName('extension_id'), $db->quoteName('version_id')));
					$query->values($eid . ', ' . $db->quote($info->version));
					$db->setQuery($query);
					$db->Query();
				}
			}
		}
	}

	private function _getInfo($id)
	{
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('element=\'themeclassic\' AND type=\'plugin\' AND folder=\'jsnimageshow\' AND extension_id = ' . $id);
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}
}