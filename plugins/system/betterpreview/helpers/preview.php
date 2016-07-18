<?php
/**
 * Plugin Preview Helper File
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

/**
 * Plugin that cleans cache
 */
class HelperBetterPreviewPreview extends PlgSystemBetterPreviewHelper
{
	public function __construct(&$params)
	{
		$this->params = $params;
		$this->states = array();
		$this->errors = array();
	}

	function renderPreview(&$article, $context)
	{
		JHtml::_('jquery.framework');
		require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';

		NNFrameworkFunctions::addScriptVersion(JUri::root(true) . '/media/betterpreview/js/preview.min.js');
		JHtml::stylesheet('betterpreview/preview.min.css', false, true);

		$has_changes = 0;
		$data        = JFactory::getApplication()->input->get('previewdata', array(), 'array');
		$this->urlDecode($data);

		$textpre = $this->getText($article);

		if (isset($data['attribs']) && !isset($data['params']))
		{
			$data['params'] = $data['attribs'];
		}

		foreach ($data as $key => $val)
		{
			// ignore unexsistent fields
			if (!isset($article->{$key}))
			{
				continue;
			}

			// ignore crappy tags
			// and dynamic/unsettable fields
			// and date fields (because offset checking is a hell)
			if (in_array($key, array('published', 'access', 'tags', 'hits', 'version', 'created', 'modified', 'publish_up', 'publish_down')))
			{
				continue;
			}

			$objects = array('params', 'attribs', 'metadata', 'urls', 'images');

			if (in_array($key, $objects))
			{
				if (is_object($article->{$key}))
				{
					foreach ($val as $k => $v)
					{
						if (!is_string($v) || $v != '')
						{
							if (!$has_changes && $this->diff($article->{$key}->get($k), $v))
							{
								$has_changes = 1;
							}
							$article->{$key}->set($k, $v);
						}
					}
					continue;
				}
				else if (is_string($article->{$key}))
				{
					$obj = (object) json_decode($article->{$key});
					if (is_object($obj))
					{
						// force fields to null if originals are.
						foreach ($val as $k => $v)
						{
							$this->prepareValueByKey($v, $k, $obj);
							if (!isset($v))
							{
								unset($val[$k]);
							}
							else
							{
								$val[$k] = $v;
							}
						}
						$val = urldecode(json_encode($val));
					}
				}
			}
			else
			{
				$this->prepareValueByKey($val, $key, $article);
			}

			if (!in_array($key, array('introtext', 'fulltext', 'text')))
			{
				if (!$has_changes && $this->diff($article->{$key}, $val))
				{
					$has_changes = 1;
				}
			}

			$article->{$key} = $val;
		}

		$article->text = $this->getText($article);

		// Fix weird issue with the video attribute being filled with the text on K2 items
		// @todo: find out why that happens
		if ($context = 'com_k2.item' && isset($article->video) && $article->video == $article->text)
		{
			// Set video value to data value or blank
			$article->video = isset($data['video']) ? $data['video'] : '';
		}

		if (!$has_changes && $this->diff($article->text, $textpre))
		{
			$has_changes = 1;
		}

		if ($has_changes)
		{
			$this->errors['BP_MESSAGE_HAS_CHANGES'] = JText::_('BP_MESSAGE_HAS_CHANGES');
		}
	}

	function purgeCache()
	{
		if (!$this->params->purge_component_cache
			|| !$option = JFactory::getApplication()->input->get('option')
		)
		{
			return;
		}

		try
		{
			JFactory::getCache($option)->clean();
		}
		catch (Exception $e)
		{
			// ignore
		}
	}

	function setLanguage()
	{
		$language          = JFactory::getLanguage();
		$previous_language = JFactory::getApplication()->input->get('lang');

		if ($language->get('lang') != $previous_language && $language->exists($previous_language))
		{
			$language->setLanguage($previous_language);
		}
	}

	function getText(&$article)
	{
		if ($this->getShowIntro($article) && isset($article->introtext) && isset($article->fulltext))
		{
			return $article->introtext . ' ' . $article->fulltext;
		}

		if (!empty($article->fulltext))
		{
			return $article->fulltext;
		}

		if (!empty($article->introtext))
		{
			return $article->introtext;
		}

		return $article->text;
	}

	function urlDecode(&$data)
	{
		if (is_array($data) || is_object($data))
		{
			foreach ($data as $k => $v)
			{
				$this->urlDecode($data[$k]);
			}
		}
		else if (is_string($data))
		{
			$data = urldecode($data);
		}
	}

	function prepareValueByKey(&$val, $key, &$obj)
	{
		if (is_array($val) && $val == array(0))
		{
			unset($val);

			return;
		}

		if (property_exists($obj, $key))
		{
			$v = $obj->{$key};
			switch ($v)
			{
				case null:
					if (empty($val))
					{
						$val = $v;
					}
					break;
				case '0000-00-00 00:00:00':
					if ($val == '')
					{
						$val = $v;
					}
					break;
			}
			if (is_bool($v))
			{
				$val = $val ? true : false;
			}
		}
	}

	function diff($string_1, $string_2)
	{
		if (is_string($string_1) && !is_string($string_2))
		{
			if (!is_array($string_2))
			{
				return 1;
			}
			$string_1 = explode(',', $string_1);
		}
		else if (is_array($string_1) && !is_array($string_2))
		{
			return 1;
		}
		else if (is_object($string_1) && !is_object($string_2))
		{
			return 1;
		}

		if (is_array($string_2) || is_object($string_2))
		{
			foreach ($string_2 as $k => $v)
			{
				if ($this->diff($string_1[$k], $v))
				{
					return 1;
				}
			}
		}
		else if (is_string($string_2))
		{
			return (trim(preg_replace('#\s#', '', $string_1)) != trim(preg_replace('#\s#', '', $string_2)));
		}

		return 0;
	}

	function addMessages()
	{
		$html = JResponse::getBody();

		if ($html == '')
		{
			return;
		}

		// Set the category description if original description is empty
		// Need to do this because the onContentPrepare is not triggered when the description is empty
		if (JFactory::getApplication()->input->get('view') == 'category')
		{
			$empty_cat = '#(<div class="category-desc[^"]*">)\s*(</div>)#';
			if (preg_match($empty_cat, $html))
			{
				$data = JFactory::getApplication()->input->get('previewdata', array(), 'array');
				$this->urlDecode($data);
				if (isset($data['description']))
				{
					$html = preg_replace($empty_cat, '\1' . $data['description'] . '\2', $html);
				}
			}
		}

		$html = str_replace('</body>', '<div class="betterpreview_message">' . JText::_('BP_MESSAGE_PAGE') . '</div></body>', $html);
		if (!empty($this->errors))
		{
			$html = str_replace('</body>', '<div class="betterpreview_error">' . implode('<br />', $this->errors) . '</div></body>', $html);
		}

		JResponse::setBody($html);
	}

	function initStates($item_name = 'content', $item_states = array(), $parent_name = 'categories', $parent_states = array())
	{
		$this->getState(
			JFactory::getApplication()->input->get('id'),
			$item_name,
			$item_states
		);
		$item = $this->states[count($this->states) - 1];
		while ($item->parent != 0)
		{
			$this->getState(
				$item->parent,
				$parent_name,
				$parent_states,
				1
			);
			$item = $this->states[count($this->states) - 1];
		}
		$this->setStates();
	}

	function getShowIntro(&$article)
	{
		if ($article && isset($article->params))
		{
			return $article->params->get('show_intro', '1');
		}

		return 1;
	}

	function getState($id, $table, $names = array(), $isparent = 0)
	{
		$names = (object) array_merge(
			array(
				'id'        => 'id',
				'published' => 'published',
				'access'    => 'access',
				'parent'    => 'parent',
			), $names
		);

		$data = JFactory::getApplication()->input->get('previewdata', array(), 'array');
		$this->urlDecode($data);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->from('#__' . $table . ' as a')
			->where('a.' . $names->id . ' = ' . (int) $id);
		foreach ($names as $k => $v)
		{
			if (!$k || !$v)
			{
				continue;
			}
			$query->select('a.' . $v . ' as ' . $k);
		}

		$db->setQuery($query);
		$item = $db->loadObject();

		if (!$item)
		{
			return;
		}

		$state = (object) array(
			'table'     => $table,
			'id'        => $id,
			'published' => $item->published,
			'access'    => $item->access,
			'parent'    => $item->parent,
			'names'     => $names,
		);

		if (isset($names->publish_up) && $item->publish_up)
		{
			$state->publish_up = $item->publish_up;
		}
		if (isset($names->publish_down) && $item->publish_down)
		{
			$state->publish_down = $item->publish_down;
		}
		if (isset($names->hits))
		{
			$state->hits = $item->hits;
		}

		$this->states[] = $state;

		if (empty($this->errors))
		{
			$now = strtotime(JFactory::getDate()->format('Y-m-d H:i:s'));
			if (
				$item->published != 1
				|| (isset($data['published']) && $data['published'] != 1)
				|| (isset($item->publish_up) && $item->publish_up > 1 && strtotime($item->publish_up) > $now)
				|| (isset($data['publish_up']) && $data['publish_up'] > 1 && strtotime($data['publish_up']) > $now)
				|| (isset($item->publish_down) && $item->publish_down > 1 && strtotime($item->publish_down) < $now)
				|| (isset($data['publish_down']) && $data['publish_down'] > 1 && strtotime($data['publish_down']) < $now)
			)
			{
				$this->errors['BP_MESSAGE'] = $isparent ? JText::_('BP_MESSAGE_PARENT_UNPUBLISHED') : JText::_('BP_MESSAGE_ITEM_UNPUBLISHED');
			}
			else if ($item->access != 1 || (isset($data['access']) && $data['access'] != 1))
			{
				$this->errors['BP_MESSAGE'] = $isparent ? JText::_('BP_MESSAGE_PARENT_ACCESS') : JText::_('BP_MESSAGE_ITEM_ACCESS');
			}
		}
	}

	function setStates()
	{
		foreach ($this->states as $state)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update('#__' . $state->table)
				->where($db->quoteName($state->names->id) . ' = ' . $state->id);

			if (isset($state->names->published))
			{
				$query->set($db->quoteName($state->names->published) . ' = 1');
			}

			if (isset($state->names->access))
			{
				$query->set($db->quoteName($state->names->access) . ' = 1');
			}

			if (isset($state->names->publish_up) && $state->publish_up > 0)
			{
				$query->set($db->quoteName($state->names->publish_up) . ' = CURDATE() - INTERVAL 1 DAY');
			}

			if (isset($state->names->publish_down) && $state->publish_down > 0)
			{
				$query->set($db->quoteName($state->names->publish_down) . ' = CURDATE() + INTERVAL 1 DAY');
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	function restoreStates()
	{
		foreach ($this->states as $state)
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->update('#__' . $state->table)
				->where($db->quoteName($state->names->id) . ' = ' . $state->id);

			if (isset($state->names->published))
			{
				$query->set($db->quoteName($state->names->published) . ' = ' . $state->published);
			}
			if (isset($state->names->access))
			{
				$query->set($db->quoteName($state->names->access) . ' = ' . $state->access);
			}
			if (isset($state->names->hits))
			{
				$query->set($db->quoteName($state->names->hits) . ' = ' . $state->hits);
			}
			if (isset($state->names->publish_up) && $state->publish_up > 0)
			{
				$query->set($db->quoteName($state->names->publish_up) . ' = ' . $db->quote($state->publish_up));
			}
			if (isset($state->names->publish_down) && $state->publish_down > 0)
			{
				$query->set($db->quoteName($state->names->publish_down) . ' = ' . $db->quote($state->publish_down));
			}

			$db->setQuery($query);
			$db->execute();
		}
	}

	function checkSession()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('session_id'))
			->from($db->quoteName('#__session'))
			->where($db->quoteName('userid') . ' = ' . $db->quote(JFactory::getApplication()->input->get('user')))
			->where($db->quoteName('client_id') . ' = 1')
			->order($db->quoteName('time') . ' DESC');
		$db->setQuery($query);
		$result = (string) $db->loadResult();

		return ($result && $result == JFactory::getApplication()->input->get('session_id'));
	}
}
