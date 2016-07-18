<?php
/**
 * @package         ReReplacer
 * @version         6.2.0PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/parameters.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';

class PlgSystemReReplacerHelperItems
{
	var $helpers = array();
	var $items = array();
	var $sourcerer_tag = '';

	public function __construct()
	{
		$sourcerer_params = NNParameters::getInstance()->getPluginParams('sourcerer');
		if (!empty($sourcerer_params) && isset($sourcerer_params->syntax_word))
		{
			$this->sourcerer_tag = trim($sourcerer_params->syntax_word);
		}

		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemReReplacerHelpers::getInstance();
	}

	public function getItemList($area = 'articles')
	{
		if (isset($this->items[$area]))
		{
			return $this->items[$area];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('r.*')
			->from('#__rereplacer AS r')
			->where('r.published = 1');
		$where = 'r.area = ' . $db->quote($area);
		$where .= ' OR r.params LIKE ' . $db->quote('%"use_xml":"1"%');
		$query->where('(' . $where . ')')
			->order('r.ordering, r.id');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		$items = array();

		if (empty($rows))
		{
			$this->items[$area] = $items;

			return $this->items[$area];
		}

		foreach ($rows as $row)
		{
			if (!$item = $this->getItem($row, $area))
			{
				continue;
			}

			if (is_array($item))
			{
				$items = array_merge($items, $item);
				continue;
			}

			$items[] = $item;
		}

		if ($area != 'articles')
		{
			$this->filterItemList($items);
		}

		$this->items[$area] = $items;

		return $this->items[$area];
	}

	private function getItem($row, $area = 'articles')
	{
		if (!((substr($row->params, 0, 1) != '{') && (substr($row->params, -1, 1) != '}')))
		{
			$row->params = NNText::html_entity_decoder($row->params);
		}

		$item = NNParameters::getInstance()->getParams($row->params, JPATH_ADMINISTRATOR . '/components/com_rereplacer/item_params.xml');

		unset($row->params);
		foreach ($row as $key => $param)
		{
			$item->$key = $param;
		}

		if (
			!$item->use_xml
			&&
			strlen($item->search) < 3
		)
		{
			return false;
		}

		$this->prepareString($item->search);
		$this->prepareReplaceString($item->replace);

		if (!$item->use_xml)
		{
			return $item;
		}

		if ($item->xml == '')
		{
			return false;
		}

		jimport('joomla.filesystem.file');

		$file = str_replace('//', '/', JPATH_SITE . '/' . str_replace('\\', '/', $item->xml));
		if (!JFile::exists($file))
		{
			return false;
		}

		$xml_data = JFile::read($file);

		// prevent html tags in strings to mess up xml structure
		$xml_data = str_replace(
			array('<search>', '<replace>', '</search>', '</replace>'),
			array('<search><![CDATA[', '<replace><![CDATA[', ']]></search>', ']]></replace>'),
			$xml_data
		);

		if (strpos($xml_data, '<param name="other_replace">') !== false)
		{
			$xml_data = preg_replace('#(<param name="other_replace">)(.*?)(</param>)#si', '\1<![CDATA[\2]]>\3', $xml_data);
		}

		$xml_data = str_replace(
			array('<![CDATA[<![CDATA[', ']]>]]></'),
			array('<![CDATA[', ']]></'),
			$xml_data
		);

		$func = new NNFrameworkFunctions;

		$xml = $func->xmlToObject($xml_data, 'items');
		if (!isset($xml->item))
		{
			return false;
		}

		$items = array();

		if (!is_array($xml->item))
		{
			$xml->item = array($xml->item);
		}

		foreach ($xml->item as $xml_item)
		{
			if (!isset($xml_item->search))
			{
				continue;
			}

			$xml_item->replace = isset($xml_item->replace) ? $xml_item->replace : '';

			$subitem          = clone($item);
			$subitem->search  = isset($xml_item->search) ? $xml_item->search : $subitem->search;
			$subitem->replace = isset($xml_item->replace) ? $xml_item->replace : $subitem->replace;

			$this->prepareString($subitem->search);
			$this->prepareReplaceString($subitem->replace);

			if (isset($xml_item->params))
			{
				foreach ($xml_item->params as $key => $param)
				{
					$subitem->$key = $param;
				}
			}

			if ($subitem->area != $area)
			{
				continue;
			}

			if ((NNFrameworkFunctions::isFeed() && !$subitem->enable_in_feeds)
				|| (JFactory::getDocument()->getType() != 'feed' && $subitem->enable_in_feeds == 2)
			)
			{
				continue;
			}

			$items[] = $subitem;
		}

		return $items;
	}

	private function prepareString(&$string)
	{
		if (!is_string($string))
		{
			$string = '';

			return;
		}
	}

	private function prepareReplaceString(&$string)
	{
		$this->prepareString($string);

		if (!$this->sourcerer_tag || $string == '')
		{
			return;
		}

		// fix usage of non-protected {source} tags
		$string = str_replace('{' . $this->sourcerer_tag . '}', '{' . $this->sourcerer_tag . ' 0}', $string);
	}

	public function filterItemList(&$items, $article = 0)
	{
		foreach ($items as $key => &$item)
		{
			if (
				(JFactory::getApplication()->isAdmin() && $item->enable_in_admin == 0)
				|| (JFactory::getApplication()->isSite() && $item->enable_in_admin == 2)
			)
			{
				unset($items[$key]);
				continue;
			}

			$item = $this->helpers->get('assignments')->itemPass($item, $article);

			if (!$item)
			{
				unset($items[$key]);
			}
		}
	}
}
