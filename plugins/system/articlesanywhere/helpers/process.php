<?php
/**
 * Plugin Helper File: Process
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

class PlgSystemArticlesAnywhereHelperProcess
{
	var $helpers = array();
	var $params = null;
	var $aid = null;
	var $articles = array();
	var $article_to_ids = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemArticlesAnywhereHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		// Tag character start and end
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		// Break/paragraph start and end tags
		$this->params->breaks_start = NNTags::getRegexSurroundingTagPre();
		$this->params->breaks_end   = NNTags::getRegexSurroundingTagPost();
		$breaks_start               = $this->params->breaks_start;
		$breaks_end                 = $this->params->breaks_end;
		$spaces                     = NNTags::getRegexSpaces();
		$inside_tag                 = NNTags::getRegexInsideTag();

		$this->params->tags = '(?P<tag>'
			. preg_quote($this->params->article_tag, '#')
			. '|' . preg_quote($this->params->articles_tag, '#')
			. ')';

		$this->params->regex = '#'
			. '(?P<start_pre>' . $breaks_start . ')'
			. $tag_start . $this->params->tags . $spaces . '(?P<id>' . $inside_tag . ')' . $tag_end
			. '(?P<start_post>' . $breaks_end . ')'

			. '(?P<start_div>(?:'
			. $breaks_start
			. $tag_start . 'div(?: ' . $inside_tag . ')?' . $tag_end
			. $breaks_end
			. '\s*)?)'

			. '(?P<pre>' . $breaks_start . ')'
			. '(?P<html>.*?)'
			. '(?P<post>' . $breaks_end . ')'

			. '(?P<end_div>(?:\s*'
			. $breaks_start
			. $tag_start . '/div' . $tag_end
			. $breaks_end
			. ')?)'

			. '(?P<end_pre>' . $breaks_start . ')'
			. $tag_start . '/\2' . $tag_end
			. '(?P<end_post>' . $breaks_end . ')'
			. '#s';

		$this->aid = JFactory::getUser()->getAuthorisedViewLevels();
	}

	public function removeAll(&$string, $area = 'articles')
	{
		$this->params->message = JText::_('AA_OUTPUT_REMOVED_NOT_ENABLED');
		$this->processArticles($string, $area);
	}

	public function processArticles(&$string, $area = 'articles', $context = '', $art = null)
	{
		list($tag_start, $tag_end) = $this->getTagCharacters();

		// Check if tags are in the text snippet used for the search component
		if (strpos($context, 'com_search.') === 0)
		{
			$limit = explode('.', $context, 2);
			$limit = (int) array_pop($limit);

			$string_check = substr($string, 0, $limit);

			if (
				strpos($string_check, $tag_start . $this->params->article_tag) === false
				&& strpos($string_check, $tag_start . $this->params->articles_tag) === false
			)
			{
				return;
			}
		}

		if (($area == 'articles' && !$this->params->articles_enable)
			|| ($area == 'components' && !$this->params->components_enable)
			|| ($area == 'other' && !$this->params->other_enable)
		)
		{
			$this->params->message = JText::_('AA_OUTPUT_REMOVED_NOT_ENABLED');
		}

		list($pre_string, $string, $post_string) = NNText::getContentContainingSearches(
			$string,
			array(
				$tag_start . $this->params->article_tag,
				$tag_start . $this->params->articles_tag
			),
			array(
				$tag_start . '/' . $this->params->article_tag . $tag_end,
				$tag_start . '/' . $this->params->articles_tag . $tag_end
			)
		);

		if ($string == '')
		{
			$string = $pre_string . $string . $post_string;

			return;
		}

		$regex = $this->params->regex;

		if (@preg_match($regex . 'u', $string))
		{
			$regex .= 'u';
		}

		if (!preg_match_all($regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			$string = $pre_string . $string . $post_string;

			return;
		}

		$this->getArticlesFromTags($matches);

		$matches = array();
		$break   = 0;

		while (
			$break++ < 10
			&& (
				strpos($string, $this->params->article_tag) !== false
				|| strpos($string, $this->params->articles_tag) !== false
			)
			&& preg_match_all($regex, $string, $matches, PREG_SET_ORDER) > 0)
		{
			foreach ($matches as $match)
			{
				if ($match['tag'] == $this->params->articles_tag)
				{
					$type = 'articles';
					if (strpos($match['id'], 'k2:') === 0)
					{
						$type .= 'k2';
						$match['id'] = substr($match['id'], 3);
					}

					$this->helpers->get($type)->replace($string, $match, $art);
					continue;
				}

				$this->helpers->get('article')->replace($string, $match, $art);
			}
			$matches = array();
		}

		$string = $pre_string . $string . $post_string;
	}

	public function getArticlesFromTags(&$matches)
	{
		$types = array();
		foreach ($matches as $match)
		{
			if ($match['tag'] != $this->params->article_tag)
			{
				continue;
			}

			if (strpos($match['id'], 'k2:') === 0)
			{
				$types['k2'][] = substr($match['id'], 3);
				continue;
			}

			$types['article'][] = $match['id'];
		}

		foreach ($types as $type => $ids)
		{
			$table = ($type == 'k2') ? 'k2_items' : 'content';

			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('CONCAT("' . $type . '_", a.id) AS type_id, CONCAT("' . $type . '_", a.id) AS orig_id, a.*')
				->from('#__' . $table . ' as a');

			$wheres = array();
			$ids    = array_unique($ids);
			foreach ($ids as $id)
			{
				$where = 'a.title = ' . $db->quote(NNText::html_entity_decoder($id));
				$where .= ' OR a.alias = ' . $db->quote(NNText::html_entity_decoder($id));

				if (is_numeric($id))
				{
					$where .= ' OR a.id = ' . $id;
				}

				$wheres[] = $where;
			}

			$query->where('((' . implode(') OR (', $wheres) . '))');

			if (!$this->params->ignore_language)
			{
				$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
			}

			if (!$this->params->ignore_state)
			{
				$jnow     = JFactory::getDate();
				$now      = $jnow->toSql();
				$nullDate = $db->getNullDate();

				$where = ($type == 'k2') ? 'a.published = 1 AND trash = 0' : 'a.state = 1';

				$query->where($where)
					->where('( a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ' )')
					->where('( a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ' )');
			}

			if (!$this->params->ignore_access)
			{
				$query->where('a.access IN(' . implode(', ', $this->aid) . ')');
			}

			$query->order('a.ordering');
			$db->setQuery($query);

			$articles       = $db->loadObjectList('type_id');
			$this->articles = array_merge($this->articles, $articles);

			foreach ($articles as $type_id => $article)
			{
				$this->article_to_ids[$type_id]                      = $article->id;
				$this->article_to_ids[$type . '_' . $article->alias] = $article->id;
				$this->article_to_ids[$type . '_' . $article->title] = $article->id;
			}
		}
	}

	public function processMatch(&$string, &$art, &$data, &$ignores, $type = 'article')
	{
		if ($this->params->message != '')
		{
			if ($this->params->place_comments)
			{
				return $this->params->comment_start . $this->params->message_start . $this->params->message . $this->params->message_end . $this->params->comment_end;
			}

			return '';
		}

		$id   = $data['id'];
		$html = trim($data['html']);

		$type = $type ?: 'article';
		if (strpos($id, ':') !== false)
		{
			$type = explode(':', $id, 2);
			if ($type['0'] == 'k2')
			{
				$id = trim($type['1']);
				$type = trim($type['0']);
			}
		}

		$html = $this->processArticle($id, $art, $html, $type, $ignores);

		// Don't add surrounding html if there is nothing to output (not only comments)
		if (!preg_match('#^(<\!--[^>]*-->)*$#', $html))
		{
			list($start_div, $end_div) = $this->getDivTags($data);

			$tags = NNTags::cleanSurroundingTags(array(
				'start_pre'      => $data['start_pre'],
				'start_post'     => $data['start_post'],
				'start_div_pre'  => $start_div['pre'],
				'start_div_post' => $start_div['post'],
				'pre'            => $data['pre'],
				'post'           => $data['post'],
				'end_div_pre'    => $end_div['pre'],
				'end_div_post'   => $end_div['post'],
				'end_pre'        => $data['end_pre'],
				'end_post'       => $data['end_post'],
			));

			$html = $tags['start_pre'] . $tags['start_post']
				. $tags['start_div_pre'] . $start_div['tag'] . $tags['start_div_post']
				. $tags['pre'] . $html . $tags['post']
				. $tags['end_div_pre'] . $end_div['tag'] . $tags['end_div_post']
				. $tags['end_pre'] . $tags['end_post'];
		}

		if ($this->params->place_comments)
		{
			$html = $this->params->comment_start . $html . $this->params->comment_end;
		}

		return $html;
	}

	private function processArticle($id, $art, $text = '', $type = 'article', $ignores = array(), $firstpass = 0)
	{
		list($tag_start, $tag_end) = $this->getTagCharacters(true, 'data');

		if ($firstpass)
		{
			// first pass: search for normal tags and tags around tags
			$regex = '#' . $tag_start . '(/?[a-z0-9][^' . $tag_end . ']*?|/?[a-z0-9](?:[^' . $tag_start . ']*?' . $tag_start . '[^' . $tag_end . ']*?' . $tag_end . ')*[^' . $tag_end . ']*?)' . $tag_end . '#si';
		}
		else
		{
			// do second pass
			$text = $this->processArticle($id, $art, $text, $type, $ignores, 1);

			$regex_close = '#' . $tag_start . '/' . $this->params->tags . $tag_end . '#si';
			if (preg_match($regex_close, $text))
			{
				return $text;
			}
			// second pass: only search for normal tags
			$regex = '#' . $tag_start . '(/?[a-z0-9][^' . $tag_end . ']*?)' . $tag_end . '#si';
		}

		if (!preg_match_all($regex, $text, $matches, PREG_SET_ORDER))
		{
			return $text;
		}

		$article = $this->getArticle($id, $art, $type, $ignores);

		if (!$article)
		{
			return '<!-- ' . JText::_('AA_ACCESS_TO_ARTICLE_DENIED') . ' -->';
		}

		self::addParams(
			$article,
			json_decode(
				isset($article->attribs)
					? $article->attribs
					: $article->params
			)
		);

		if (isset($article->images))
		{
			self::addParams($article, json_decode($article->images));
		}

		if (isset($article->urls))
		{
			self::addParams($article, json_decode($article->urls));
		}

		if ($type == 'k2')
		{
			$file = 'media/k2/items/cache/' . md5("Image" . $article->id) . '_L.jpg';
			if (JFile::exists(JPATH_SITE . '/' . $file))
			{
				$article->image_url = JUri::root() . $file;

				$article->image = $this->helpers->get('tags')->getImageHtml($article->image_url, $article->title, $article->image_caption, 'k2_image', false);
			}

			$file = 'media/k2/items/cache/' . md5("Image" . $article->id) . '_S.jpg';
			if (JFile::exists(JPATH_SITE . '/' . $file))
			{
				$article->thumb_url = JUri::root() . $file;

				$article->thumb = $this->helpers->get('tags')->getImageHtml($article->thumb_url, $article->title, $article->image_caption, 'k2_image k2_thumb', false);
			}
		}

		if (strpos($text, 'tag') !== false)
		{
			$method = ($type == 'k2') ? 'addTagsK2' : 'addTags';
			self::$method($article);
		}

		$helper = ($type == 'k2') ? 'tagsk2' : 'tags';

		$this->helpers->get($helper)->handleIfStatements($text, $article);

		if (!preg_match_all($regex, $text, $matches, PREG_SET_ORDER))
		{
			return $text;
		}

		$this->helpers->get($helper)->replaceTags($text, $matches, $article);

		return $text;
	}

	private function getArticle($id, $art, $type = 'article', $ignores = array())
	{
		list($tag_start, $tag_end) = $this->getTagCharacters(false, 'data');

		if (in_array($id, array('current', 'self', $tag_start . 'id' . $tag_end, $tag_start . 'title' . $tag_end, $tag_start . 'alias' . $tag_end), true))
		{
			if (isset($art->id))
			{
				$id = $art->id;
			}
			else if (isset($art->link) && preg_match('#&amp;id=([0-9]*)#', $art->link, $match))
			{
				$id = $match['1'];
			}
			else if ($type != 'k2' && $this->params->option == 'com_content' && JFactory::getApplication()->input->get('view') == 'article')
			{
				$id = JFactory::getApplication()->input->getInt('id', 0);
			}
			else if ($type == 'k2' && $this->params->option == 'com_k2' && JFactory::getApplication()->input->get('view') == 'item')
			{
				$id = JFactory::getApplication()->input->getInt('id', 0);
			}
		}

		$id = $this->getArticleId($id, $type, $ignores);

		if (isset($this->articles[$type . '_' . $id]))
		{
			return $this->articles[$type . '_' . $id];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');

		if ($type == 'article')
		{
			$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END AS slug')
				->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END AS catslug')
				->select('c.parent_id AS parent')
				->join('LEFT', '#__categories AS c ON c.id = a.catid');
		}

		$table = ($type == 'k2') ? 'k2_items' : 'content';
		$query->from('#__' . $table . ' as a')
			->where('a.id = ' . (int) $id);

		$db->setQuery($query);
		$this->articles[$type . '_' . $id] = $db->loadObject();

		return $this->articles[$type . '_' . $id];
	}

	private function getArticleId($id, $type = 'article', $ignores = array())
	{
		if (isset($this->articles[$type . '_' . $id]))
		{
			return $id;
		}

		if (isset($this->article_to_ids[$type . '_' . $id]))
		{
			return $this->article_to_ids[$type . '_' . $id];
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id');

		$table = ($type == 'k2') ? 'k2_items' : 'content';
		$query->from('#__' . $table . ' as a');

		$where = 'a.title = ' . $db->quote(NNText::html_entity_decoder($id));
		$where .= ' OR a.alias = ' . $db->quote(NNText::html_entity_decoder($id));
		if (is_numeric($id))
		{
			$where .= ' OR a.id = ' . $id;
		}
		$query->where('(' . $where . ')');

		$ignore_language = isset($ignores['ignore_language']) ? $ignores['ignore_language'] : $this->params->ignore_language;
		if (!$ignore_language)
		{
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}

		$ignore_state = isset($ignores['ignore_state']) ? $ignores['ignore_state'] : $this->params->ignore_state;
		if (!$ignore_state)
		{
			$jnow     = JFactory::getDate();
			$now      = $jnow->toSql();
			$nullDate = $db->getNullDate();

			$where = ($type == 'k2') ? 'a.published = 1 AND trash = 0' : 'a.state = 1';

			$query->where($where)
				->where('( a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ' )')
				->where('( a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ' )');
		}

		$ignore_access = isset($ignores['ignore_access']) ? $ignores['ignore_access'] : $this->params->ignore_access;
		if (!$ignore_access)
		{
			$query->where('a.access IN(' . implode(', ', $this->aid) . ')');
		}

		$query->order('a.ordering');
		$db->setQuery($query);

		$this->article_to_ids[$type . '_' . $id] = $db->loadResult();

		return $this->article_to_ids[$type . '_' . $id];
	}

	private function addParams(&$article, $params)
	{
		if (!$params
			|| (!is_object($params) && !is_array($params))
		)
		{
			return;
		}

		foreach ($params as $key => $val)
		{
			if (isset($article->$key))
			{
				continue;
			}

			$article->$key = $val;
		}
	}

	public function addTags(&$article)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('t.title')
			->from('#__tags AS t')
			->join('LEFT', '#__contentitem_tag_map AS m ON m.tag_id = t.id')
			->where('m.content_item_id = ' . (int) $article->id)
			->where('t.published = 1');
		$db->setQuery($query);

		$article->tags = $db->loadColumn();
	}

	public function addTagsK2(&$article)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('t.name')
			->from('#__k2_tags AS t')
			->join('LEFT', '#__k2_tags_xref AS m ON m.tagID = t.id')
			->where('m.itemID = ' . (int) $article->id)
			->where('t.published = 1');
		$db->setQuery($query);

		$article->tags = $db->loadColumn();
	}

	public function getIgnoreSetting(&$ignores, $group)
	{
		list($key, $val) = explode('=', $group, 2);

		if (!in_array(trim($key), array('ignore_language', 'ignore_access', 'ignore_state')))
		{
			return;
		}

		$val           = str_replace(array('\{', '\}'), array('{', '}'), trim($val));
		$ignores[$key] = $val;
	}

	public function getTagCharacters($quote = false, $type = 'tag')
	{
		switch ($type)
		{
			case 'data':
				if (!isset($this->params->tag_character_data_start))
				{
					list($this->params->tag_character_data_start, $this->params->tag_character_data_end) = explode('.', $this->params->tag_characters_data);
				}

				$start = $this->params->tag_character_data_start;
				$end   = $this->params->tag_character_data_end;
				break;

			case 'tag':
			default:
				if (!isset($this->params->tag_character_start))
				{
					list($this->params->tag_character_start, $this->params->tag_character_end) = explode('.', $this->params->tag_characters);
				}

				$start = $this->params->tag_character_start;
				$end   = $this->params->tag_character_end;
		}

		if ($quote)
		{
			$start = preg_quote($start, '#');
			$end   = preg_quote($end, '#');
		}

		return array($start, $end);
	}

	private function getDivTags($data)
	{
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		return NNTags::getDivTags($data['start_div'], $data['end_div'], $tag_start, $tag_end);
	}
}
