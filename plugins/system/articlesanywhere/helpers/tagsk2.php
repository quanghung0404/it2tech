<?php
/**
 * Plugin Helper File: Tags
 *
 * @package         Articles Anywhere
 * @version         4.1.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once __DIR__ . '/tags.php';

class PlgSystemArticlesAnywhereHelperTagsK2 extends PlgSystemArticlesAnywhereHelperTags
{
	public function getArticleUrl()
	{
		if (isset($this->article->url))
		{
			return $this->article->url;
		}

		if (!isset($this->article->id))
		{
			return false;
		}

		if (!isset($this->article->category))
		{
			JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_k2/tables');
			$category = JTable::getInstance('K2Category', 'Table');
			$category->load($this->article->catid);
			$this->article->category = $category;
		}

		require_once JPATH_SITE . '/components/com_k2/helpers/route.php';
		$this->article->url = K2HelperRoute::getItemRoute($this->article->id . ':' . $this->article->alias, $this->article->catid . ':' . $this->article->category->alias);

		return $this->article->url;
	}

	public function processTagByType($tag, $extra)
	{
		switch (true)
		{
			// Extra fields
			case (preg_match('#^extra-[0-9]+$#', $tag, $image_tag)):
				return $this->processTagDatabase($tag, $extra, true);

			default:
				return parent::processTagByType($tag, $extra);
		}
	}

	public function processTagDatabase($tag, $extra, $return_empty = false)
	{
		// Get data from db columns
		$string = $this->getTagFromDatabase($tag);
		if ($string === false)
		{
			return $return_empty ? '' : false;
		}

		// Convert string if it is a date
		$string = $this->convertDateToString($string, $extra);

		return $string;
	}

	private function getTagFromDatabase($tag)
	{
		if (isset($this->article->$tag))
		{
			return $this->article->$tag;
		}

		return $this->getTagFromExtraField($tag);
	}

	private function getTagFromExtraField($tag)
	{
		$string = $this->getExtraFieldValue($this->article->extra_fields, $tag, $this->article->catid);

		if ($string === false)
		{
			return false;
		}

		return $string;
	}

	public function canEdit()
	{
		JLoader::register('K2HelperPermissions', JPATH_SITE . '/components/com_k2/helpers/permissions.php');

		if ($this->params->option != 'com_k2')
		{
			K2HelperPermissions::setPermissions();
		}

		return K2HelperPermissions::canEditItem($this->article->created_by, $this->article->catid);
	}

	public function getArticleEditUrl()
	{
		if (isset($this->article->editurl))
		{
			return $this->article->editurl;
		}

		if (!isset($this->article->id))
		{
			return false;
		}

		$this->article->editurl = '';

		if (!$this->canEdit())
		{
			return false;
		}

		$uri                    = JUri::getInstance();
		$this->article->editurl = JRoute::_('index.php?option=com_k2&view=item&task=edit&cid=' . $this->article->id . '&return=' . base64_encode($uri));

		return $this->article->editurl;
	}

	/*
	 * Retrieve data from k2 extra fields
	 */
	private function getExtraFieldValue(&$extra, $data, $catid)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear()
			->select('c.extraFieldsGroup')
			->from('#__k2_categories as c')
			->where('c.id = ' . (int) $catid);
		$db->setQuery($query);
		$extragroup = $db->loadResult();

		if (!$extragroup)
		{
			return false;
		}

		require_once JPATH_ADMINISTRATOR . '/components/com_k2/lib/JSON.php';
		$json = new Services_JSON;

		$query->clear()
			->select('e.*')
			->from('#__k2_extra_fields as e')
			->where('e.group = ' . (int) $extragroup)
			->where('e.published = 1');

		$where = 'e.name = ' . $db->quote($data);
		if (substr($data, 0, 6) == 'extra-' && is_numeric(substr($data, 6)))
		{
			$where = '(' . $where . ' OR e.id = ' . (int) substr($data, 6) . ')';
		}
		$query->where($where);

		$db->setQuery($query);
		$extrafield = $db->loadObject();

		if (!$extrafield)
		{
			return false;
		}

		$value = false;

		$fielddata = $json->decode($extra);
		foreach ($fielddata as $field)
		{
			if ($field->id != $extrafield->id)
			{
				continue;
			}

			if ($field->value == '')
			{
				continue;
			}

			$value = $field->value;

			if (in_array($extrafield->type, array('textfield', 'textarea', 'csv', 'date')))
			{
				return $value;
			}

			if ($extrafield->type == 'link' && is_array($field->value))
			{
				$link         = new stdClass;
				$link->name   = isset($field->value['0']) ? $field->value['0'] : '';
				$link->value  = isset($field->value['1']) ? $field->value['1'] : '';
				$link->target = isset($field->value['2']) ? $field->value['2'] : '';

				return $this->getFieldLink($link);
			}

			break;
		}

		$defaultdata = $json->decode($extrafield->value);

		if ($value == false && isset($defaultdata['0']))
		{
			switch ($extrafield->type)
			{
				case 'textfield':
				case 'textarea':
				case 'csv':
					$value = $defaultdata['0']->value;
					break;
				case 'link':
					$value = $this->getFieldLink($defaultdata['0']);
					break;
				case 'multipleSelect':
					$value = '';
					break;
				default:
					$value = $defaultdata['0']->name;
					break;
			}
		}

		$values = array();
		foreach ($defaultdata as $defaultvalue)
		{
			if (!is_array($value))
			{
				$value = array($value);
			}

			foreach ($value as $val)
			{
				if ($val != $defaultvalue->value)
				{
					continue;
				}

				$values[] = $defaultvalue->name;
			}
		}

		if (empty($values))
		{
			return false;
		}

		return implode(', ', $values);
	}

	private function getFieldLink(&$field)
	{
		if (!$field->value || $field->value == 'http://')
		{
			return $field->name;
		}
		$params = JComponentHelper::getParams('com_k2');

		switch ($field->target)
		{
			case 'same':
			default:
				$attributes = '';
				break;

			case 'new':
				$attributes = 'target="_blank"';
				break;

			case 'popup':
				$attributes = 'class="classicPopup" rel="{x:' . $params->get('linkPopupWidth') . ',y:' . $params->get('linkPopupHeight') . '}"';
				break;

			case 'lightbox':
				$filename      = @basename($field->value);
				$extension     = JFile::getExt($filename);
				$imgExtensions = array('jpg', 'jpeg', 'gif', 'png');
				if (!empty($extension) && in_array($extension, $imgExtensions))
				{
					$attributes = 'class="modal"';
				}
				else
				{
					$attributes = 'class="modal" rel="{handler:\'iframe\',size:{x:' . $params->get('linkPopupWidth') . ',y:' . $params->get('linkPopupHeight') . '}}"';
				}
				break;
		}

		return '<a href="' . $field->value . '" ' . $attributes . '>' . $field->name . '</a>';
	}

	public function processTagTags($extra)
	{
		require_once JPATH_SITE . '/components/com_k2/helpers/route.php';

		$tags = $this->getItemTags($this->article->id);
		foreach ($tags as &$tag)
		{
			$tag->link = JRoute::_(K2HelperRoute::getTagRoute($tag->name));
		}

		$extra = explode(':', $extra, 2);
		$clean = trim(array_shift($extra));

		$html = array();

		if ($clean != 'clean')
		{
			foreach ($tags as $tag)
			{
				if (!$tag->published)
				{
					continue;
				}

				$html[] = '<li><a href="' . $tag->link . '">' . htmlspecialchars($tag->name, ENT_COMPAT, 'UTF-8') . '</a></li>';
			}

			return
				'<div class="itemTagsBlock">'
				. '<span>' . JText::_('K2_TAGGED_UNDER') . '</span>'
				. '<ul class="itemTags">'
				. implode('', $html)
				. '</ul>'
				. '<div class="clr"></div>'
				. '</div>';
		}

		$separator = array_shift($extra);
		$separator = $separator != '' ? str_replace('separator=', '', $separator) : ' ';

		foreach ($tags as $tag)
		{
			if (!$tag->published)
			{
				continue;
			}

			$html[] = '<span class="tag-' . $tag->id . '" itemprop="keywords">'
				. '<a href = "' . $tag->link . '" class="tag_link">'
				. htmlspecialchars($tag->name, ENT_COMPAT, 'UTF-8')
				. '</a>'
				. '</span>';
		}

		return '<span class="tags">' . implode($separator, $html) . '</span>';
	}

	private function getItemTags($id)
	{
		$db = JFactory::getDbo();

		$query = "SELECT tag.*
		FROM #__k2_tags AS tag
		JOIN #__k2_tags_xref AS xref ON tag.id = xref.tagID
		WHERE tag.published=1
		AND xref.itemID = " . (int) $id . " ORDER BY xref.id ASC";

		$db->setQuery($query);

		return $db->loadObjectList();
		$K2ItemTagsInstances[$id] = $rows;
	}
}
