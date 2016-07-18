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

class PlgSystemArticlesAnywhereHelperTags
{
	var $helpers = array();
	var $params = null;
	var $config = null;
	var $article = null;
	var $images = null;
	var $data = null;

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemArticlesAnywhereHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->config = JComponentHelper::getParams('com_content');

		$this->data = (object) array(
			'article' => null,
			'total'   => 1,
			'count'   => 1,
			'even'    => 0,
			'uneven'  => 1,
			'first'   => 1,
			'last'    => 1,
		);
	}

	public function handleIfStatements(&$string, &$article)
	{
		list($tag_start, $tag_end) = $this->helpers->get('process')->getTagCharacters('data', true);

		if (preg_match_all(
				'#' . $tag_start . 'if:.*?' . $tag_start . '/if' . $tag_end . '#si',
				$string,
				$matches,
				PREG_SET_ORDER
			) < 1
		)
		{
			return;
		}

		$this->data->article = $article;

		if (strpos($string, 'text') !== false)
		{
			$article->text = (isset($article->introtext) ? $article->introtext : '')
				. (isset($article->introtext) ? $article->fulltext : '');
		}

		foreach ($matches as $match)
		{
			if (preg_match_all(
					'#' . $tag_start . '(if|else *?if|else)(?:\:(.+?))?' . $tag_end . '(.*?)(?=' . $tag_start . '(?:else|\/if))#si',
					$match['0'],
					$ifs,
					PREG_SET_ORDER
				) < 1
			)
			{
				continue;
			}

			$replace = $this->getIfResult($ifs);

			// replace if block with the IF value
			$string = NNText::strReplaceOnce($match['0'], $replace, $string);
		}

		$article = $this->data->article;
	}

	private function getIfResult(&$matches)
	{
		foreach ($matches as $if)
		{
			if (!$this->passIfStatements($if))
			{
				continue;
			}

			return $if['3'];
		}

		return '';
	}

	private function passIfStatements($if)
	{
		$statement = trim($if['2']);

		if (trim($if['1']) == 'else' && $statement == '')
		{
			return true;
		}

		if ($statement == '')
		{
			return false;
		}

		$statement = html_entity_decode($statement);
		$statement = str_replace(
			array(' AND ', ' OR '),
			array(' && ', ' || '),
			$statement
		);

		$ands = explode(' && ', $statement);

		$pass = false;
		foreach ($ands as $statement)
		{
			$ors = explode(' || ', $statement);
			foreach ($ors as $statement)
			{
				if ($pass = $this->passIfStatement($statement))
				{
					break;
				}
			}

			if (!$pass)
			{
				break;
			}
		}

		return $pass;
	}

	private function passIfStatement($statement)
	{
		$statement = trim($statement);

		/*
		* In array syntax
		* 'bar' IN foo
		* 'bar' !IN foo
		* 'bar' NOT IN foo
		*/
		if (preg_match('#^[\'"]?(?P<val>.*?)[\'"]?\s+(?P<operator>(?:NOT\s+)?\!?IN)\s+(?P<key>[a-zA-Z0-9-_]+)$#s', $statement, $match))
		{
			$reverse = ($match['operator'] == 'NOT IN' || $match['operator'] == '!NOT');

			return $this->passIfStatementArray(
				$this->getValueFromData($match['key']),
				$this->getValueFromData($match['val'], $match['val']),
				$reverse
			);
		}

		/*
		* String comparison syntax:
		* foo = 'bar'
		* foo != 'bar'
		*/
		if (preg_match('#^(?P<key>[a-z0-9-_]+)\s*(?P<operator>\!?=)=*\s*[\'"]?(?P<val>.*?)[\'"]?$#si', $statement, $match))
		{
			$reverse = ($match['2'] == '!=');

			return $this->passIfStatementArray(
				$this->getValueFromData($match['key']),
				$this->getValueFromData($match['val'], $match['val']),
				$reverse
			);
		}

		/*
		* Variable check syntax:
		* foo (= not empty)
		* !foo (= empty)
		*/
		if (preg_match('#^(?P<operator>\!?)(?P<key>[a-z0-9-_]+)$#si', $statement, $match))
		{
			$reverse = ($match['operator'] == '!');

			return $this->passIfStatementSimple(
				$this->getValueFromData($match['key']),
				$reverse
			);
		}

		return $this->passIfStatementPHP($statement);
	}

	private function getValueFromData($key, $default = null)
	{
		if (!is_string($key))
		{
			return $default;
		}

		return isset($this->data->{$key}) ? $this->data->{$key} : (isset($this->data->article->{$key}) ? $this->data->article->{$key} : $default);
	}

	private function passIfStatementSimple($haystack, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		$pass = !empty($haystack);

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementString($haystack, $needle, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		if (is_array($haystack))
		{
			return $this->passIfStatementArray($haystack, $needle, $reverse);
		}

		$pass = $this->passString($haystack, $needle);

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementArray($haystack, $needle, $reverse = 0)
	{
		if (is_null($haystack))
		{
			return false;
		}

		if (!is_array($haystack))
		{
			$haystack = explode(',', str_replace(', ', ',', $haystack));
		}

		if (!is_array($haystack))
		{
			return false;
		}

		$pass = false;
		foreach ($haystack as $string)
		{
			if ($pass = $this->passString($string, $needle))
			{
				break;
			}
		}

		return $reverse ? !$pass : $pass;
	}

	private function passIfStatementPHP($statement)
	{
		$php = html_entity_decode($statement);
		$php = str_replace('=', '==', $php);

		// replace keys with $article->key
		$php = '$article->' . preg_replace('#\s*(&&|&&|\|\|)\s*#', ' \1 $article->', $php);

		// fix negative keys from $article->!key to !$article->key
		$php = str_replace('$article->!', '!$article->', $php);

		// replace back data variables
		foreach ($this->data as $key => $val)
		{
			if ($key == 'article')
			{
				continue;
			}

			$php = str_replace('$article->' . $key, (int) $val, $php);
		}
		$php = str_replace('$article->empty', (int) ($this->data->count > 0), $php);

		// Place statement in return check
		$php = 'return ( ' . $php . ' ) ? 1 : 0;';

		// Trim the text that needs to be checked and replace weird spaces
		$php = preg_replace(
			'#(\$article->[a-z0-9-_]*)#',
			'trim(str_replace(chr(194) . chr(160), " ", \1))',
			$php
		);

		// Fix extra-1 field syntax: $article->extra-1 to $article->{'extra-1'}
		$php = preg_replace(
			'#->(extra-[a-z0-9]+)#',
			'->{\'\1\'}',
			$php
		);

		$temp_PHP_func = create_function('&$article', $php);

		// evaluate the script
		// but without using the the evil eval
		ob_start();
		$pass = $temp_PHP_func($this->data->article);
		unset($temp_PHP_func);
		ob_end_clean();

		return $pass;
	}

	private function passString($haystack, $needle)
	{
		if (!is_string($haystack) && !is_string($needle)
			&& !is_numeric($haystack)
			&& !is_numeric($needle)
		)
		{
			return false;
		}

		// Simple string comparison
		if (strpos($needle, '*') === false && strpos($needle, '+') === false)
		{
			return strtolower($haystack) == strtolower($needle);
		}

		// Using wildcards
		$needle = preg_quote($needle, '#');
		$needle = str_replace(
			array('\\\\\\*', '\\*', '[:asterisk:]', '\\\\\\+', '\\+', '[:plus:]'),
			array('[:asterisk:]', '.*', '\\*', '[:plus:]', '.+', '\\+'),
			$needle
		);

		return preg_match('#' . $needle . '#si', $haystack);
	}

	public function replaceTags(&$text, &$matches, &$article)
	{
		$this->article = $article;
		foreach ($matches as $match)
		{
			$string = $this->processTag($match['1']);
			if ($string === false)
			{
				continue;
			}

			$text = str_replace($match['0'], $string, $text);
		}
	}

	public function processTag($data)
	{
		$data = explode(':', $data, 2);

		$tag   = trim($data['0']);
		$extra = isset($data['1']) ? $data['1'] : '';

		return $this->processTagByType($tag, $extra);
	}

	public function processTagByType($tag, $extra)
	{
		switch (true)
		{
			// Link closing tag
			case ($tag == '/link'):
				return '</a>';

			// Total count
			case ($tag == 'total' || $tag == 'totalcount'):
				return $this->data->total;

			// Counter
			case ($tag == 'count' || $tag == 'counter'):
				return $this->data->count;

			// Div closing tag
			case ($tag == '/div'):
				return '</div>';

			// Div
			case ($tag == 'div' || strpos($tag, 'div ') === 0):
				return $this->processTagDiv($tag, $extra);

			// URL
			case ($tag == 'url'):
				return $this->getArticleUrl();

			// Link tag
			case ($tag == 'link'):
				return $this->processTagLink();

			// Readmore link
			case (strpos($tag, 'readmore') === 0):
				return $this->processTagReadmore($extra);

			// Text
			case (
				(strpos($tag, 'text') === 0)
				|| (strpos($tag, 'intro') === 0)
				|| (strpos($tag, 'full') === 0)
			):
				return $this->processTagText($tag, $extra);

			// Intro image
			case ($tag == 'image-intro'):
				return $this->processTagImageIntro();

			// Fulltext image
			case ($tag == 'image-fulltext'):
				return $this->processTagImageFulltext();

			// Layout
			case ($tag == 'layout'):
				return $this->processTagLayout($tag, $extra);

			// Edit URL
			case ($tag == 'editurl'):
				return $this->processTagEditUrl();

			// Edit link tag
			case ($tag == 'edit'):
				return $this->processTagEditLink($extra);

			case (strpos($tag, 'tag') === 0):
				return $this->processTagTags($extra);

			// Image
			case (preg_match('#^image([-_])([0-9]+)#', $tag, $image_tag)):
				return $this->processTagImage($image_tag);

			// Database values
			case (NNText::is_alphanumeric(str_replace(array('-', '_'), '', $tag))):
				return $this->processTagDatabase($tag, $extra);

			default:
				return false;
		}
	}

	public function processTagDiv($tag, $extra)
	{
		if ($tag != 'div')
		{
			$extra = str_replace('div ', '', $tag)
				. ':'
				. $extra;
		}

		if (!$extra)
		{
			return '<div>';
		}

		$string = '';

		$extra  = explode('|', $extra);
		$extras = new stdClass;
		foreach ($extra as $e)
		{
			if (strpos($e, ':') !== false)
			{
				list($key, $val) = explode(':', $e, 2);
				$extras->$key = $val;
			}
		}

		if (isset($extras->class))
		{
			$string .= 'class="' . $extras->class . '"';
		}

		$style = array();

		if (isset($extras->width))
		{
			if (is_numeric($extras->width))
			{
				$extras->width .= 'px';
			}
			$style[] = 'width:' . $extras->width;
		}

		if (isset($extras->height))
		{
			if (is_numeric($extras->height))
			{
				$extras->height .= 'px';
			}
			$style[] = 'height:' . $extras->height;
		}

		if (isset($extras->align))
		{
			$style[] = 'float:' . $extras->align;
		}
		else if (isset($extras->float))
		{
			$style[] = 'float:' . $extras->float;
		}

		if (!empty($style))
		{
			$string .= ' style="' . implode(';', $style) . ';"';
		}

		return trim('<div ' . trim($string)) . '>';
	}

	public function processTagReadmore($extra)
	{
		if (!$link = $this->getArticleUrl())
		{
			return false;
		}

		// load the content language file
		NNFrameworkFunctions::loadLanguage('com_content', JPATH_SITE);

		$extra = explode('|', $extra);

		if (isset($extra['1']))
		{
			return '<a class="' . trim($extra['1']) . '" href="' . $link . '">' . $this->getReadMoreText($extra) . '</a>';
		}

		$params = JComponentHelper::getParams('com_content');
		$params->set('access-view', true);

		if ($text = $this->getCustomReadMoreText($extra))
		{
			$this->article->alternative_readmore = $text;
			$params->set('show_readmore_title', false);
		}

		return JLayoutHelper::render('joomla.content.readmore', array('item' => $this->article, 'params' => $params, 'link' => $link));
	}

	private function getCustomReadMoreText($extra)
	{
		if (!isset($extra['0']) || !trim($extra['0']))
		{
			return '';
		}

		$title = trim($extra['0']);
		$text  = JText::sprintf($title, $this->article->title);

		return $text ?: $title;
	}

	public function getReadMoreText($extra)
	{
		if ($text = $this->getCustomReadMoreText($extra))
		{
			return $text;
		}

		switch (true)
		{
			case (isset($this->article->alternative_readmore) && $this->article->alternative_readmore) :
				$text = $this->article->alternative_readmore;
				break;
			case (!$this->config->get('show_readmore_title', 0)) :
				$text = JText::_('COM_CONTENT_READ_MORE_TITLE');
				break;
			default:
				$text = JText::_('COM_CONTENT_READ_MORE');
				break;
		}

		if (!$this->config->get('show_readmore_title', 0))
		{
			return $text;
		}

		return $text . JHtml::_('string.truncate', ($this->article->title), $this->config->get('readmore_limit'));
	}

	public function processTagLink()
	{
		if (!$link = $this->getArticleUrl())
		{
			return false;
		}

		return '<a href="' . $link . '">';
	}

	public function processTagText($tag, $extra)
	{
		switch (true)
		{
			case (strpos($tag, 'intro') === 0):
				if (!isset($this->article->introtext))
				{
					return false;
				}
				$this->article->text = $this->article->introtext;
				break;

			case (strpos($tag, 'full') === 0):
				if (!isset($this->article->fulltext))
				{
					return false;
				}

				$this->article->text = $this->article->fulltext;
				break;

			case (strpos($tag, 'text') === 0):
				$this->article->text = (isset($this->article->introtext) ? $this->article->introtext : '')
					. (isset($this->article->introtext) ? $this->article->fulltext : '');
				break;
		}

		if ($this->article->text == '')
		{
			return '';
		}

		$string = $this->article->text;

		if (!$extra)
		{
			return $string;
		}

		$attribs = explode(':', $extra);

		$max      = 0;
		$strip    = 0;
		$noimages = 0;
		foreach ($attribs as $attrib)
		{
			$attrib = trim($attrib);
			switch ($attrib)
			{
				case 'strip':
					$strip = 1;
					break;
				case 'noimages':
					$noimages = 1;
					break;
				default:
					$max = $attrib;
					break;
			}
		}

		$word_limit = (strpos($max, 'word') !== false);
		if ($strip)
		{
			$string = $this->strip($string, $max);
		}
		else if ($noimages)
		{
			// remove images
			$string = preg_replace(
				'#(<p><' . 'img\s.*?></p>|<' . 'img\s.*?>)#si',
				' ',
				$string
			);
		}

		if (!$strip && $max && ($word_limit || (int) $max < strlen($string)))
		{
			$max = (int) $max;

			// store pagenavcounter & pagenav (exclude from count)
			preg_match('#<' . 'div class="pagenavcounter">.*?</div>#si', $string, $pagenavcounter);
			$pagenavcounter = isset($pagenavcounter['0']) ? $pagenavcounter['0'] : '';
			if ($pagenavcounter)
			{
				$string = str_replace($pagenavcounter, '<!-- ARTA_PAGENAVCOUNTER -->', $string);
			}
			preg_match('#<' . 'div class="pagenavbar">(<div>.*?</div>)*</div>#si', $string, $pagenav);
			$pagenav = isset($pagenav['0']) ? $pagenav['0'] : '';
			if ($pagenav)
			{
				$string = str_replace($pagenav, '<!-- ARTA_PAGENAV -->', $string);
			}

			// add explode helper strings around tags
			$explode_str = '<!-- ARTA_TAG -->';
			$string      = preg_replace(
				'#(<\/?[a-z][a-z0-9]?.*?>|<!--.*?-->)#si',
				$explode_str . '\1' . $explode_str,
				$string
			);

			$str_array = explode($explode_str, $string);

			$string    = array();
			$tags      = array();
			$count     = 0;
			$is_script = 0;
			foreach ($str_array as $i => $str_part)
			{
				if (fmod($i, 2))
				{
					// is tag
					$string[] = $str_part;
					preg_match(
						'#^<(\/?([a-z][a-z0-9]*))#si',
						$str_part,
						$tag
					);
					if (!empty($tag))
					{
						if ($tag['1'] == 'script')
						{
							$is_script = 1;
						}

						if (!$is_script
							// only if tag is not a single html tag
							&& (strpos($str_part, '/>') === false)
							// just in case single html tag has no closing character
							&& !in_array($tag['2'], array('area', 'br', 'hr', 'img', 'input', 'param'))
						)
						{
							$tags[] = $tag['1'];
						}

						if ($tag['1'] == '/script')
						{
							$is_script = 0;
						}
					}
				}
				else if ($is_script)
				{
					$string[] = $str_part;
				}
				else
				{
					if ($word_limit)
					{
						// word limit
						if ($str_part)
						{
							$words      = explode(' ', trim($str_part));
							$word_count = count($words);
							if ($max < ($count + $word_count))
							{
								$words_part = array();
								$word_count = 0;
								foreach ($words as $word)
								{
									if ($word)
									{
										$word_count++;
									}

									if ($max < ($count + $word_count))
									{
										break;
									}
									$words_part[] = $word;
								}
								$string_part = rtrim(implode(' ', $words_part));
								if (preg_match('#[^a-z0-9]$#si', $string_part))
								{
									$string_part .= ' ';
								}

								if ($this->params->use_ellipsis)
								{
									$string_part .= '...';
								}
								$string[] = $string_part;
								break;
							}
							$count += $word_count;
						}
						$string[] = $str_part;
					}
					else
					{
						// character limit
						if ($max < ($count + strlen($str_part)))
						{
							// strpart has to be cut off
							$maxlen = $max - $count;
							if ($maxlen < 3)
							{
								$string_part = '';
								if (preg_match('#[^a-z0-9]$#si', $str_part))
								{
									$string_part .= ' ';
								}

								if ($this->params->use_ellipsis)
								{
									$string_part .= '...';
								}
								$string[] = $string_part;
							}
							else
							{
								if (function_exists('mb_substr'))
								{
									$string_part = rtrim(mb_substr($str_part, 0, ($max - 3), 'utf-8'));
								}
								else
								{
									$string_part = rtrim(substr($str_part, 0, ($max - 3)));
								}

								if (preg_match('#[^a-z0-9]$#si', $string_part))
								{
									$string_part .= ' ';
								}

								if ($this->params->use_ellipsis)
								{
									$string_part .= '...';
								}

								$string[] = $string_part;
							}
							break;
						}
						$count += strlen($str_part);
						$string[] = $str_part;
					}
				}
			}

			// revers sort open tags
			krsort($tags);
			$tags  = array_values($tags);
			$count = count($tags);

			for ($i = 0; $i < 3; $i++)
			{
				foreach ($tags as $ti => $tag)
				{
					if ($tag['0'] == '/')
					{
						for ($oi = $ti + 1; $oi < $count; $oi++)
						{
							if (!isset($tags[$oi]))
							{
								unset($tags[$ti]);
								break;
							}
							$opentag = $tags[$oi];
							if ($opentag == $tag)
							{
								break;
							}

							if ('/' . $opentag == $tag)
							{
								unset($tags[$ti]);
								unset($tags[$oi]);
								break;
							}
						}
					}
				}
			}

			foreach ($tags as $tag)
			{
				// add closing tag to end of string
				if ($tag['0'] != '/')
				{
					$string[] = '</' . $tag . '>';
				}
			}
			$string = implode('', $string);

			$string = str_replace(array('<!-- ARTA_PAGENAVCOUNTER -->', '<!-- ARTA_PAGENAV -->'), array($pagenavcounter, $pagenav), $string);
		}

		// Fix links in pagination to point to the included article instead of the main article
		// This doesn't seem to work correctly and causes issues with other links in the article
		// So commented out untill I find a better solution
		/*if ($art && isset($art->id) && $art->id) {
			$string = str_replace('view=article&amp;id=' . $art->id, 'view=article&amp;id=' . $this->article->id, $string);
		}*/

		return $string;
	}

	private function strip($string, $max = '')
	{
		$word_limit = (strpos($max, 'word') !== false);

		// remove pagenavcounter
		$string = preg_replace('#(<' . 'div class="pagenavcounter">.*?</div>)#si', ' ', $string);
		// remove pagenavbar
		$string = preg_replace('#(<' . 'div class="pagenavbar">(<div>.*?</div>)*</div>)#si', ' ', $string);
		// remove inline scripts
		$string = preg_replace('#(<' . 'script[^a-z0-9].*?</script>)#si', ' ', $string);
		$string = preg_replace('#(<' . 'noscript[^a-z0-9].*?</noscript>)#si', ' ', $string);
		// remove inline styles
		$string = preg_replace('#(<' . 'style[^a-z0-9].*?</style>)#si', ' ', $string);
		// remove other tags
		$string = preg_replace('#(<' . '/?[a-z][a-z0-9]?.*?>)#si', ' ', $string);
		// remove double whitespace
		$string = trim(preg_replace('#(\s)[ ]+#s', '\1', $string));

		if (!$max)
		{
			return $string;
		}

		$orig_len = strlen($string);
		if ($word_limit)
		{
			// word limit
			$string = trim(
				preg_replace(
					'#^(([^\s]+\s*){' . (int) $max . '}).*$#s',
					'\1',
					$string
				)
			);
			if (strlen($string) < $orig_len)
			{
				if (preg_match('#[^a-z0-9]$#si', $string))
				{
					$string .= ' ';
				}

				if ($this->params->use_ellipsis)
				{
					$string .= '...';
				}
			}

			return $string;
		}

		// character limit
		$max = (int) $max;
		if ($max >= $orig_len)
		{
			return $string;
		}

		if (function_exists('mb_substr'))
		{
			$string = rtrim(mb_substr($string, 0, ($max - 3), 'utf-8'));
		}
		else
		{
			$string = rtrim(substr($string, 0, ($max - 3)));
		}

		if (preg_match('#[^a-z0-9]$#si', $string))
		{
			$string .= ' ';
		}

		if ($this->params->use_ellipsis)
		{
			$string .= '...';
		}

		return $string;
	}

	public function processTagImageIntro()
	{
		if (!isset($this->article->image_intro))
		{
			return '';
		}

		$class = 'img-intro-' . $this->article->float_intro;

		return $this->getImageHtml($this->article->image_intro, $this->article->image_intro_alt, $this->article->image_intro_caption, $class);
	}

	public function processTagImageFulltext()
	{
		if (!isset($this->article->image_fulltext))
		{
			return '';
		}

		$class = 'img-fulltext-' . $this->article->float_fulltext;

		return $this->getImageHtml($this->article->image_fulltext, $this->article->image_fulltext_alt, $this->article->image_fulltext_caption, $class);
	}

	public function getImageHtml($url, $alt = '', $caption = '', $class = '', $in_div = true)
	{
		$img_class = $caption ? 'caption' : '';
		$caption   = $caption ? ' title="' . htmlspecialchars($caption) . '"' : '';

		if ($in_div)
		{
			return '<div class="' . htmlspecialchars($class) . '"><img' . $caption . ' src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" class="' . $img_class . '" /></div>';
		}

		$img_class = trim($img_class . ' ' . htmlspecialchars($class));

		return '<img' . $caption . ' src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($alt) . '" class="' . $img_class . '" />';
	}

	public function processTagLayout($tag, $extra)
	{
		list($template, $layout) = $this->getTemplateAndLayout($extra);

		require_once __DIR__ . '/article_view.php';

		$view = new ArticlesAnywhereArticleView;

		$view->setParams($this->article->id, $template, $layout);

		return $view->display();
	}

	public function processTagEditUrl()
	{
		return $this->getArticleEditUrl();
	}

	public function processTagEditLink($extra)
	{
		if (!$url = $this->getArticleEditUrl())
		{
			return $url;
		}

		$text = JText::_($extra);

		if (!$text)
		{
			$state = isset($this->article->state) ? $this->article->state : (isset($this->article->published) ? $this->article->published : 0);
			$text  = '<span class="icon-' . ($state ? 'edit' : 'eye-close') . '"></span>&nbsp;' . JText::_('JGLOBAL_EDIT');
		}

		return '<a href="' . $url . '">' . $text . '</a>';
	}

	public function processTagTags($extra)
	{
		$tags = new JHelperTags;
		$tags->getItemTags('com_content.article', $this->article->id);

		$extra = explode(':', $extra, 2);
		$clean = trim(array_shift($extra));

		if ($clean != 'clean')
		{
			$layout = new JLayoutFile('joomla.content.tags');

			return $layout->render($tags->itemTags);
		}

		$separator = array_shift($extra);
		$separator = $separator != '' ? str_replace('separator=', '', $separator) : ' ';

		$html = array();

		foreach ($tags->itemTags as $tag)
		{
			if (!in_array($tag->access, JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'))))
			{
				continue;
			}

			$html[] = '<span class="tag-' . $tag->tag_id . '" itemprop="keywords">'
				. '<a href = "' . JRoute::_(TagsHelperRoute::getTagRoute($tag->tag_id . '-' . $tag->alias)) . '" class="tag_link">'
				. htmlspecialchars($tag->title, ENT_COMPAT, 'UTF-8')
				. '</a>'
				. '</span>';
		}

		return '<span class="tags">' . implode($separator, $html) . '</span>';
	}

	public function processTagImage($image_tag)
	{
		$images = $this->getArticleImages();
		if (empty($images))
		{
			return '';
		}

		if (!isset($this->images[$image_tag['2'] - 1]))
		{
			return '';
		}

		$type = $image_tag['1'] == '-' ? 'full' : 'url';

		if ($type == 'url')
		{
			return $this->images[$image_tag['2'] - 1]['2'];
		}

		return $this->images[$image_tag['2'] - 1]['0'];
	}

	public function processTagDatabase($tag, $extra, $return_empty = false)
	{
		// Get data from db columns
		if (!isset($this->article->$tag) || !is_string($this->article->$tag))
		{
			return $return_empty ? '' : false;
		}

		$string = $this->article->$tag;

		// Convert string if it is a date
		$string = $this->convertDateToString($string, $extra);

		return $string;
	}

	public function convertDateToString($string, $extra)
	{
		// Check if string could be a date
		if ((strpos($string, '-') == false)
			|| preg_match('#[a-z]#i', $string)
			|| !strtotime($string)
		)
		{
			return $string;
		}

		if (!$extra)
		{
			$extra = JText::_('DATE_FORMAT_LC2');
		}

		if (strpos($extra, '%') !== false)
		{
			$extra = NNText::dateToDateFormat($extra);
		}

		return JHtml::_('date', $string, $extra);
	}

	public function canEdit()
	{
		$user = JFactory::getUser();
		if ($user->get('guest'))
		{
			return false;
		}

		$userId = $user->get('id');
		$asset  = 'com_content.article.' . $this->article->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			return true;
		}

		// Now check if edit.own is available.
		if (empty($userId) || $user->authorise('core.edit.own', $asset))
		{
			return false;
		}

		// Check for a valid user and that they are the owner.
		if ($userId != $this->article->created_by)
		{
			return false;
		}

		return true;
	}

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

		require_once JPATH_SITE . '/components/com_content/helpers/route.php';
		$this->article->url = ContentHelperRoute::getArticleRoute($this->article->id, $this->article->catid, $this->article->language);

		return $this->article->url;
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
			return '';
		}

		$uri                    = JUri::getInstance();
		$this->article->editurl = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $this->article->id . '&return=' . base64_encode($uri));

		return $this->article->editurl;
	}

	public function getArticleImages()
	{
		if (!is_array($this->images))
		{
			$article_text = (isset($this->article->introtext) ? $this->article->introtext : '')
				. (isset($this->article->fulltext) ? $this->article->fulltext : '');

			preg_match_all(
				'#<img\s[^>]*src=([\'"])(.*?)\1[^>]*>#si',
				$article_text,
				$this->images,
				PREG_SET_ORDER
			);
		}

		return $this->images;
	}

	public function getLayoutFile($layout)
	{
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		list($template, $layout) = $this->getTemplateAndLayout($layout);

		// Load the language file for the template
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, false)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . '/' . $template, null, false, false)
		|| $lang->load('tpl_' . $template, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . '/' . $template, $lang->getDefault(), false, false);

		$paths = array(
			JPATH_THEMES . '/' . $template . '/html/com_content/article',
			JPATH_SITE . '/components/com_content/views/article/tmpl',
		);

		$file = JPath::find($paths, $layout . '.php');

		// Check if layout exists
		if (JFile::exists($file))
		{
			return $file;
		}

		// Return default layout
		return JPath::find($paths, 'default.php');
	}

	public function getTemplateAndLayout($layout)
	{
		$layout = $layout ?: (isset($this->article->article_layout) ? $this->article->article_layout : '');
		$layout = explode(':', $layout);

		$template = JFactory::getApplication()->getTemplate();

		// If first value is empty or '_', then use current template
		if (!$layout['0'] || $layout['0'] == '_')
		{
			$layout['0'] = $template;
		}

		// Two values found, so is a template:layout pair
		if (isset($layout['1']))
		{
			return $layout;
		}

		jimport('joomla.filesystem.folder');

		// Only one param given.
		// Check if it is a template or layout

		// Value is a template, so return default layout
		if ($layout['0'] == $template || JFolder::exists(JPATH_THEMES . '/' . $layout['0']))
		{
			return array($layout['0'], 'default');
		}

		// Value is not a template, so a layout
		return array($template, $layout['0']);
	}

}
