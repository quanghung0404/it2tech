<?php
/**
 * Plugin Helper File
 *
 * @package         Snippets
 * @version         4.1.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

// Load common functions
require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/tags.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/text.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_snippets');

/**
 * System Plugin that places a Snippets code block into the text
 */
class PlgSystemSnippetsHelper
{
	var $option = '';
	var $params = null;
	var $items = array();

	public function __construct(&$params)
	{
		$this->option = JFactory::getApplication()->input->get('option');

		$this->params                = $params;
		$this->params->comment_start = '<!-- START: Snippets -->';
		$this->params->comment_end   = '<!-- END: Snippets -->';
		$this->params->message_start = '<!--  Snippets Message: ';
		$this->params->message_end   = ' -->';

		$this->params->tag = trim($this->params->tag);

		// Tag character start and end
		list($tag_start, $tag_end) = $this->getTagCharacters(true);

		// Break/paragraph start and end tags
		$this->params->breaks_start = NNTags::getRegexSurroundingTagPre();
		$this->params->breaks_end   = NNTags::getRegexSurroundingTagPost();
		$breaks_start               = $this->params->breaks_start;
		$breaks_end                 = $this->params->breaks_end;
		$inside_tag                 = NNTags::getRegexInsideTag();
		$spaces                     = NNTags::getRegexSpaces();

		$this->params->tag_regex = preg_quote($this->params->tag, '#') . (($this->params->tag == 'snippet') ? 's?' : '');
		$this->params->regex     = '#'
			. '(?P<pre>' . $breaks_start . ')'
			. $tag_start . $this->params->tag_regex . $spaces . '(?P<id>' . $inside_tag . ')' . $tag_end
			. '(?P<post>' . $breaks_end . ')'
			. '#s';

		$this->params->protected_tags = array(
			$this->params->tag_character_start . $this->params->tag,
		);

		if ($this->params->tag == 'snippet')
		{
			$this->params->protected_tags[] = $this->params->tag_character_start . $this->params->tag . 's';
		}

		$disabled_components = is_array($this->params->disabled_components) ? $this->params->disabled_components : explode('|', $this->params->disabled_components);
		$this->params->disabled_components = array('com_acymailing');
		$this->params->disabled_components = array_merge($disabled_components, $this->params->disabled_components);

		require_once JPATH_ADMINISTRATOR . '/components/com_snippets/models/list.php';
		$list        = new SnippetsModelList;
		$this->items = $list->getItems(1);
	}

	public function onContentPrepare(&$article, &$context, &$params)
	{
		$area    = isset($article->created_by) ? 'articles' : 'other';
		$context = (($params instanceof JRegistry) && $params->get('nn_search')) ? 'com_search.' . $params->get('readmore_limit') : $context;

		NNFrameworkHelper::processArticle($article, $context, $this, 'replaceTags', array($area, $context));
	}

	public function onAfterDispatch()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && !NNFrameworkFunctions::isFeed())
		{
			return;
		}

		$html = JFactory::getDocument()->getBuffer('component');

		if (empty($html) || is_array($html))
		{
			return;
		}

		if (strpos($html, $this->params->tag_character_start . $this->params->tag) === false)
		{
			return;
		}

		$this->replaceTags($html, 'component');

		JFactory::getDocument()->setBuffer($html, 'component');
	}

	public function onAfterRender()
	{
		// only in html and feeds
		if (JFactory::getDocument()->getType() !== 'html' && !NNFrameworkFunctions::isFeed())
		{
			return;
		}

		$html = JResponse::getBody();

		if ($html == '')
		{
			return;
		}

		$this->replaceTags($html, 'body');

		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replaceTags(&$string, $area = 'article', $context = '')
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		// Check if tags are in the text snippet used for the search component
		if (strpos($context, 'com_search.') === 0)
		{
			$limit = explode('.', $context, 2);
			$limit = (int) array_pop($limit);

			$string_check = substr($string, 0, $limit);

			if (strpos($string_check, $this->params->tag_character_start . $this->params->tag) === false)
			{
				return;
			}
		}

		if (strpos($string, $this->params->tag_character_start . $this->params->tag) === false)
		{
			return;
		}

		// allow in component?
		if (
			$area == 'component'
			&& in_array(JFactory::getApplication()->input->get('option'), $this->params->disabled_components)
		)
		{
			if (!$this->params->disable_components_remove)
			{
				$this->protectTags($string);

				return;
			}

			$this->protect($string);

			$string = preg_replace($this->params->regex, '', $string);

			NNProtect::unprotect($string);

			return;
		}

		$this->protect($string);

		list($pre_string, $string, $post_string) = NNText::getContentContainingSearches(
			$string,
			array(
				$this->params->tag_character_start . $this->params->tag,
			),
			array(
				$this->params->tag_character_end,
			)
		);

		while (preg_match_all($this->params->regex, $string, $matches, PREG_SET_ORDER))
		{
			foreach ($matches as $match)
			{
				$string = str_replace($match['0'], $this->renderSnippet($match), $string);
			}
		}

		$string = $pre_string . $string . $post_string;

		NNProtect::unprotect($string);
	}

	function renderSnippet($data)
	{
		$id   = trim($data['id']);
		$vars = '';

		if (strpos($id, '|'))
		{
			list($id, $vars) = explode('|', $id, 2);
		}

		$snippet_html = $this->processSnippet(trim($id), trim($vars));

		$data = NNTags::fixSurroundingTags(array(
			'pre'     => $data['pre'],
			'content' => $snippet_html,
			'post'    => $data['post'],
		));

		if ($this->params->place_comments)
		{
			$data['content'] = $this->params->comment_start . $data['content'] . $this->params->comment_end;
		}

		return $data['pre'] . $data['content'] . $data['post'];
	}

	function processSnippet($id, $vars)
	{
		$item = isset($this->items[$id]) ? $this->items[$id] : isset($this->items[html_entity_decode($id, ENT_COMPAT, 'UTF-8')]) ? $this->items[html_entity_decode($id, ENT_COMPAT, 'UTF-8')] : '';

		if (!$item)
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('SNP_OUTPUT_REMOVED_NOT_FOUND') . $this->params->message_end;
			}

			return '';
		}

		if (!$item->published)
		{
			if ($this->params->place_comments)
			{
				return $this->params->message_start . JText::_('SNP_OUTPUT_REMOVED_NOT_ENABLED') . $this->params->message_end;
			}

			return '';
		}

		$html = $item->content;

		if ($vars != '')
		{
			$unprotected = array('\\|', '\\{', '\\}');
			$protected   = NNProtect::protectArray($unprotected);
			NNProtect::protectInString($vars, $unprotected, $protected);

			$vars = explode('|', $vars);

			foreach ($vars as $i => $var)
			{
				NNProtect::unprotectInString($var, array('|', '{', '}'), $protected);
				$html = preg_replace('#\\\\' . ($i + 1) . '(?![0-9])#', $var, $html);
			}
		}

		if (strpos($html, '[[escape]]') !== false
			&& preg_match_all('#\[\[escape\]\](.*?)\[\[/escape\]\]#s', $html, $matches, PREG_SET_ORDER)
		)
		{
			foreach ($matches as $match)
			{
				$replace = addslashes($match['1']);
				$html    = str_replace($match['0'], $replace, $html);
			}
		}

		return $html;
	}

	function protect(&$string)
	{
		NNProtect::protectFields($string);
		NNProtect::protectSourcerer($string);
	}

	function protectTags(&$string)
	{
		NNProtect::protectTags($string, $this->params->protected_tags);
	}

	function unprotectTags(&$string)
	{
		NNProtect::unprotectTags($string, $this->params->protected_tags);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$string)
	{
		$this->unprotectTags($string);

		$string = preg_replace('#<\!-- (START|END): SN_[^>]* -->#', '', $string);

		if ($this->params->place_comments)
		{
			return;
		}

		$string = str_replace(
			array(
				$this->params->comment_start, $this->params->comment_end,
				htmlentities($this->params->comment_start), htmlentities($this->params->comment_end),
				urlencode($this->params->comment_start), urlencode($this->params->comment_end),
			), '', $string
		);

		$string = preg_replace('#' . preg_quote($this->params->message_start, '#') . '.*?' . preg_quote($this->params->message_end, '#') . '#', '', $string);
	}

	public function getTagCharacters($quote = false)
	{
		if (!isset($this->params->tag_character_start))
		{
			list($this->params->tag_character_start, $this->params->tag_character_end) = explode('.', $this->params->tag_characters);
		}

		$start = $this->params->tag_character_start;
		$end   = $this->params->tag_character_end;

		if ($quote)
		{
			$start = preg_quote($start, '#');
			$end   = preg_quote($end, '#');
		}

		return array($start, $end);
	}
}
