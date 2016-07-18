<?php
/**
 * Plugin Helper File
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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

NNFrameworkFunctions::loadLanguage('plg_system_betterpreview');

/**
 * Plugin that cleans cache
 */
class PlgSystemBetterPreviewHelper
{
	var $params = null;
	var $db = null;
	var $q = null;

	public function __construct(&$params)
	{
		$this->params = $params;
		$this->db     = JFactory::getDbo();
		$this->q      = $this->db->getQuery(true);
	}

	public function getItemId($url)
	{
		$this->q->clear()
			->select('a.id')
			->from('#__menu as a')
			->where('a.link = ' . $this->db->quote($url))
			->where('a.client_id = 0')
			->where('a.published = 1');
		$this->db->setQuery($this->q);

		return $this->db->loadResult();
	}

	public function getItem($id = 0, $table, $selects = array(), $texts = array())
	{
		list($selects, $names) = $this->getSelects($selects);
		$texts = $this->getTexts($texts, $names);

		$this->q->clear()
			->from('#__' . $table . ' as a')
			->where('a.' . $names['id'] . ' = ' . (int) $id);
		foreach ($selects as $select)
		{
			$this->q->select($select);
		}
		$this->db->setQuery($this->q);
		$item      = $this->db->loadObject();
		$itemfound = 1;

		if (!$item)
		{
			$itemfound = 0;
			$item      = new stdClass;
			foreach ($selects as $k => $v)
			{
				$item->{$k} = '';
			}
		}

		foreach ($texts as $k => $v)
		{
			$item->{$k} = JText::_($v);
		}

		if ($itemfound && !$item->published)
		{
			$item->error = JText::_('BP_MESSAGE_ITEM_UNPUBLISHED');
		}
		$item->home = 0;

		return $item;
	}

	public function getParents(&$item, $table, $selects = array(), $texts = array(), $root = 0)
	{
		if (!isset($item->parent))
		{
			return array();
		}

		list($selects, $names) = $this->getSelects($selects);
		$texts = $this->getTexts($texts, $names);

		$id      = $item->parent;
		$parents = array();
		while ($id > $root)
		{
			$this->q->clear()
				->from('#__' . $table . ' as a')
				->where('a.' . $names['id'] . ' = ' . (int) $id);
			foreach ($selects as $select)
			{
				$this->q->select($select);
			}

			$this->db->setQuery($this->q);
			$parent = $this->db->loadObject();
			if ($parent)
			{
				$parents[] = $parent;
				$id        = $parent->parent;
			}
			else
			{
				break;
			}
		}

		$parents     = array_reverse($parents);
		$unpublished = 0;
		foreach ($parents as &$parent)
		{
			foreach ($texts as $k => $v)
			{
				$parent->{$k} = JText::_($v);
			}
			if (!$parent->published)
			{
				$unpublished   = 1;
				$parent->error = JText::_('BP_MESSAGE_ITEM_UNPUBLISHED');
			}
			else if ($unpublished)
			{
				$parent->published = 0;
				$parent->error     = JText::_('BP_MESSAGE_PARENT_UNPUBLISHED');
			}
		}
		$parents = array_reverse($parents);

		if ($unpublished)
		{
			$item->published = 0;
			$item->error     = JText::_('BP_MESSAGE_PARENT_UNPUBLISHED');
		}

		return $parents;
	}

	public function getSelects($selects)
	{
		$names   = array_merge(
			array(
				'id'        => 'id',
				'name'      => 'name',
				'published' => 'published',
				'parent'    => 'parent',
			), $selects
		);
		$selects = array();
		foreach ($names as $k => $v)
		{
			if (!$k || !$v)
			{
				continue;
			}
			$selects[$k] = 'a.' . $v . ' as ' . $k;
		}

		return array($selects, $names);
	}

	public function getType($item)
	{
		$key = MenusHelper::getLinkKey($item->url);
		if (isset($this->types[$key]))
		{
			$item->type = JText::_($this->types[$key]);
		}

		return $item->type;
	}

	public function getTexts($texts, $names)
	{
		$text_defaults = array(
			'url'  => '',
			'type' => '',
		);
		foreach ($text_defaults as $k => $v)
		{
			if (isset($names[$k]))
			{
				unset($text_defaults[$k]);
			}
		}

		return array_merge($text_defaults, $texts);
	}

	public function getMenuUrlById($id)
	{
		$this->q->clear()
			->select('a.link')
			->from('#__menu as a')
			->where('a.id = ' . (int) $id);
		$this->db->setQuery($this->q);

		return $this->db->loadResult();
	}

	public static function getHelperClass($type = 'link')
	{
		$option = JFactory::getApplication()->input->get('option');
		$view   = JFactory::getApplication()->input->get('view', JFactory::getApplication()->input->get('controller'));
		$task   = JFactory::getApplication()->input->get('task');

		$file = self::getHelperFileName($type, $option, $view, $task);

		if (!$file && $type != 'link')
		{
			return false;
		}

		require_once JPATH_PLUGINS . '/system/betterpreview/helpers/' . $type . '.php';

		if (!$file)
		{
			return 'HelperBetterPreview' . ucfirst($type);
		}

		require_once $file;

		$option = strlen($option) > 4 && substr($option, 0, 4) == 'com_' ? substr($option, 4) : $option;

		return 'HelperBetterPreview' . ucfirst($type) . ucfirst($option) . ucfirst($view) . ucfirst($task);
	}

	private static function getHelperFileName($type = 'link', &$option, &$view, &$task)
	{
		$file = JPATH_PLUGINS . '/system/betterpreview/helpers/' . $option . '/' . $view . '/' . $task . '/' . $type . '.php';

		if (JFile::exists($file))
		{
			return $file;
		}

		$task = '';
		$file = JPATH_PLUGINS . '/system/betterpreview/helpers/' . $option . '/' . $view . '/' . $type . '.php';

		if (JFile::exists($file))
		{
			return $file;
		}

		$view = '';
		$file = JPATH_PLUGINS . '/system/betterpreview/helpers/' . $option . '/' . $type . '.php';

		if (JFile::exists($file))
		{
			return $file;
		}

		return false;
	}
}
