<?php
/**
 * @package         NoNumber Framework
 * @version         16.1.9037
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class NNTags
{
	public static function getTagValues($string = '', $keys = array('title'), $separator = '|', $equal = '=', $limit = 0)
	{
		$temp_separator = '[[S]]';
		$temp_equal     = '[[E]]';
		$tag_start      = '[[T]]';
		$tag_end        = '[[/T]]';

		// replace separators and equal signs with special markup
		$string = str_replace(array($separator, $equal), array($temp_separator, $temp_equal), $string);
		// replace protected separators and equal signs back to original
		$string = str_replace(array('\\' . $temp_separator, '\\' . $temp_equal), array($separator, $equal), $string);

		// protect all html tags
		preg_match_all('#</?[a-z][^>]*>#si', $string, $tags, PREG_SET_ORDER);

		if (!empty($tags))
		{
			foreach ($tags as $tag)
			{
				$string = str_replace(
					$tag['0'],
					$tag_start . base64_encode(str_replace(array($temp_separator, $temp_equal), array($separator, $equal), $tag['0'])) . $tag_end,
					$string
				);
			}
		}

		// split string into array
		$vals = $limit
			? explode($temp_separator, $string, (int) $limit)
			: explode($temp_separator, $string);

		// initialize return vars
		$tag_values         = new stdClass;
		$tag_values->params = array();

		// loop through splits
		foreach ($vals as $i => $keyval)
		{
			// spit part into key and val by equal sign
			$keyval = explode($temp_equal, $keyval, 2);
			if (isset($keyval['1']))
			{
				$keyval['1'] = str_replace(array($temp_separator, $temp_equal), array($separator, $equal), $keyval['1']);
			}

			// unprotect tags in key and val
			foreach ($keyval as $key => $val)
			{
				preg_match_all('#' . preg_quote($tag_start, '#') . '(.*?)' . preg_quote($tag_end, '#') . '#si', $val, $tags, PREG_SET_ORDER);

				if (!empty($tags))
				{
					foreach ($tags as $tag)
					{
						$val = str_replace($tag['0'], base64_decode($tag['1']), $val);
					}

					$keyval[trim($key)] = $val;
				}
			}

			if (isset($keys[$i]))
			{
				$key = trim($keys[$i]);
				// if value is in the keys array add as defined in keys array
				// ignore equal sign
				$val = implode($equal, $keyval);
				if (substr($val, 0, strlen($key) + 1) == $key . '=')
				{
					$val = substr($val, strlen($key) + 1);
				}
				$tag_values->{$key} = $val;
				unset($keys[$i]);
			}
			else
			{
				// else add as defined in the string
				if (isset($keyval['1']))
				{
					$tag_values->{$keyval['0']} = $keyval['1'];
				}
				else
				{
					$tag_values->params[] = implode($equal, $keyval);
				}
			}
		}

		return $tag_values;
	}

	public static function getRegexSpaces($modifier = '+')
	{
		return '(?:\s|&nbsp;|&\#160;)' . $modifier;
	}

	public static function getRegexInsideTag()
	{
		return '(?:[^\{\}]*\{[^\}]*\})*.*?';
	}

	public static function getRegexSurroundingTagPre($elements = array('p', 'span'))
	{
		return '(?:<(?:' . implode('|', $elements) . ')(?: [^>]*)?>\s*(?:<br ?/?>\s*)*){0,3}';
	}

	public static function getRegexSurroundingTagPost($elements = array('p', 'span'))
	{
		return '(?:(?:\s*<br ?/?>)*\s*<\/(?:' . implode('|', $elements) . ')>){0,3}';
	}

	public static function getRegexTags($tags, $include_no_attributes = true, $include_ending = true, $required_attributes = array())
	{
		require_once __DIR__ . '/text.php';

		$tags = NNText::toArray($tags);
		$tags = count($tags) > 1 ? '(?:' . implode('|', $tags) . ')' : $tags['0'];

		$spaces = self::getRegexSpaces();

		$attribs = $spaces . '[^>"]*(?:"[^"]*"[^>"]*)+';

		$required_attributes = NNText::toArray($required_attributes);
		if (!empty($required_attributes))
		{
			$attribs = $spaces . '[^>"]*(?:"[^"]*"[^>"]*)*(?:' . implode('|', $required_attributes) . ')\s*=\s*(?:"[^"]*"[^>"]*)+';
		}

		if ($include_no_attributes)
		{
			$attribs = '(?:' . $attribs . ')?';
		}

		if (!$include_ending)
		{
			return '<' . $tags . $attribs . '>';
		}

		return '<(?:\/' . $tags . '|' . $tags . $attribs . ')(?:\/|' . $spaces . ')*>';
	}

	public static function cleanSurroundingTags($tags, $elements = array('p', 'span'))
	{
		require_once __DIR__ . '/text.php';

		$breaks = '(?:(?:<br ?/?>|:\|:)\s*)*';
		$keys   = array_keys($tags);

		$string = implode(':|:', $tags);
		// Remove empty tags
		while (preg_match('#<(' . implode('|', $elements) . ')(?: [^>]*)?>\s*(' . $breaks . ')<\/\1>\s*#s', $string, $match))
		{
			$string = str_replace($match['0'], $match['2'], $string);
		}

		// Remove paragraphs around block elements
		$block_elements = array(
			'p', 'div',
			'table', 'tr', 'td', 'thead', 'tfoot',
			'h[1-6]',
		);
		$block_elements = '(' . implode('|', $block_elements) . ')';
		while (preg_match('#(<p(?: [^>]*)?>)(\s*' . $breaks . ')(<' . $block_elements . '(?: [^>]*)?>)#s', $string, $match))
		{
			if ($match['4'] == 'p')
			{
				$match['3'] = $match['1'] . $match['3'];
				NNText::combinePTags($match['3']);
			}

			$string = str_replace($match['0'], $match['2'] . $match['3'], $string);
		}
		while (preg_match('#(</' . $block_elements . '>\s*' . $breaks . ')</p>#s', $string, $match))
		{
			$string = str_replace($match['0'], $match['1'], $string);
		}

		$tags = explode(':|:', $string);

		$new_tags = array();

		foreach ($tags as $key => $val)
		{
			$key            = isset($keys[$key]) ? $keys[$key] : $key;
			$new_tags[$key] = $val;
		}

		return $new_tags;
	}

	public static function fixSurroundingTags($tags)
	{
		$keys = array_keys($tags);

		$breaks = '(?:(?:<br ?/?>|:\|:)\s*)*';
		$string = implode(':|:', $tags);

		// Remove inline elements around block elements
		$string = preg_replace('#'
			. '<(?:' . implode('|', self::getInlineElements()) . ')(?: [^>]*)?>'
			. '(' . $breaks . '<(?:' . implode('|', self::getBlockElements()) . ')(?: [^>]*)?>)'
			. '#',
			'\1', $string);
		$string = preg_replace('#'
			. '(</(?:' . implode('|', self::getBlockElements()) . ')>' . $breaks . ')'
			. '</(?:' . implode('|', self::getInlineElements()) . ')>'
			. '#',
			'\1', $string);

		// Remove inner <p> tags if outer start/end <p> tags are found
		$string = preg_replace('#'
			. '(<(?:' . implode('|', self::getBlockElementsNoDiv()) . ')(?: [^>]*)?>' . $breaks . ')'
			. '<p(?: [^>]*)?>(.*)</p>'
			. '(' . $breaks . ')'
			. '#',
			'\1\2\3', $string);
		$string = preg_replace('#'
			. '(' . $breaks . ')'
			. '<p(?: [^>]*)?>(.*)</p>'
			. '(' . $breaks . '</(?:' . implode('|', self::getBlockElementsNoDiv()) . ')>)'
			. '#',
			'\1\2\3', $string);

		// Remove outer <p> tags around block elements
		$string = preg_replace('#'
			. '^\s*<p(?: [^>]*)?>'
			. '(' . $breaks . '</?(?:' . implode('|', self::getBlockElements()) . ')(?: [^>]*)?>)'
			. '#',
			'\1', $string);
		$string = preg_replace('#'
			. '(</?(?:' . implode('|', self::getBlockElements()) . ')>' . $breaks . ')'
			. '</p>\s*$'
			. '#',
			'\1', $string);

		$tags = explode(':|:', $string);

		$new_tags = array();

		foreach ($tags as $key => $val)
		{
			$key            = isset($keys[$key]) ? $keys[$key] : $key;
			$new_tags[$key] = $val;
		}

		return $new_tags;
	}

	private static function getBlockElements()
	{
		return array(
			'div', 'p', 'pre',
			'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
		);
	}

	private static function getBlockElementsNoDiv()
	{
		return array(
			'p', 'pre',
			'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
		);
	}

	private static function getInlineElements()
	{
		return array(
			'span', 'code', 'a',
			'strong', 'b', 'em', 'i', 'u', 'big', 'small', 'font',
		);
	}

	public static function setSurroundingTags($pre, $post, $tags = 0)
	{
		if ($tags == 0)
		{
			// tags that have a matching ending tag
			$tags = array(
				'div', 'p', 'span', 'pre', 'a',
				'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
				'strong', 'b', 'em', 'i', 'u', 'big', 'small', 'font',
				// html 5 stuff
				'header', 'nav', 'section', 'article', 'aside', 'footer',
				'figure', 'figcaption', 'details', 'summary', 'mark', 'time',
			);
		}
		$a = explode('<', $pre);
		$b = explode('</', $post);

		if (count($b) > 1 && count($a) > 1)
		{
			$a      = array_reverse($a);
			$a_pre  = array_pop($a);
			$b_pre  = array_shift($b);
			$a_tags = $a;
			foreach ($a_tags as $i => $a_tag)
			{
				$a[$i]      = '<' . trim($a_tag);
				$a_tags[$i] = preg_replace('#^([a-z0-9]+).*$#', '\1', trim($a_tag));
			}
			$b_tags = $b;
			foreach ($b_tags as $i => $b_tag)
			{
				$b[$i]      = '</' . trim($b_tag);
				$b_tags[$i] = preg_replace('#^([a-z0-9]+).*$#', '\1', trim($b_tag));
			}
			foreach ($b_tags as $i => $b_tag)
			{
				if ($b_tag && in_array($b_tag, $tags))
				{
					foreach ($a_tags as $j => $a_tag)
					{
						if ($b_tag == $a_tag)
						{
							$a_tags[$i] = '';
							$b[$i]      = trim(preg_replace('#^</' . $b_tag . '.*?>#', '', $b[$i]));
							$a[$j]      = trim(preg_replace('#^<' . $a_tag . '.*?>#', '', $a[$j]));
							break;
						}
					}
				}
			}
			foreach ($a_tags as $i => $tag)
			{
				if ($tag && in_array($tag, $tags))
				{
					array_unshift($b, trim($a[$i]));
					$a[$i] = '';
				}
			}
			$a = array_reverse($a);
			list($pre, $post) = array(implode('', $a), implode('', $b));
		}

		return array(trim($pre), trim($post));
	}

	public static function getDivTags($start_tag = '', $end_tag = '', $tag_start = '{', $tag_end = '}')
	{
		$start_div = array('pre' => '', 'tag' => '', 'post' => '');
		$end_div   = array('pre' => '', 'tag' => '', 'post' => '');

		if (!empty($start_tag)
			&& preg_match(
				'#^(?P<pre>.*?)(?P<tag>' . $tag_start . 'div(?: .*?)?' . $tag_end . ')(?P<post>.*)$#s',
				$start_tag,
				$match
			)
		)
		{
			$start_div = $match;
		}

		if (!empty($end_tag)
			&& preg_match(
				'#^(?P<pre>.*?)(?P<tag>' . $tag_start . '/div' . $tag_end . ')(?P<post>.*)$#s',
				$end_tag,
				$match
			)
		)
		{
			$end_div = $match;
		}

		if (empty($start_div['tag']) || empty($end_div['tag']))
		{
			return array($start_div, $end_div);
		}

		$extra = trim(preg_replace('#' . $tag_start . 'div(.*)' . $tag_end . '#si', '\1', $start_div['tag']));

		$start_div['tag'] = '<div>';
		$end_div['tag']   = '</div>';

		if (empty($extra))
		{
			return array($start_div, $end_div);
		}

		$extra  = explode('|', $extra);
		$extras = new stdClass;

		foreach ($extra as $e)
		{
			if (strpos($e, ':') === false)
			{
				continue;
			}

			list($key, $val) = explode(':', $e, 2);
			$extras->$key = $val;
		}

		$attribs = '';

		if (isset($extras->class))
		{
			$attribs .= 'class="' . $extras->class . '"';
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

		if (!isset($extras->align) && isset($extras->float))
		{
			$style[] = 'float:' . $extras->float;
		}

		if (!empty($style))
		{
			$attribs .= ' style="' . implode(';', $style) . ';"';
		}

		$start_div['tag'] = trim('<div ' . trim($attribs)) . '>';

		return array($start_div, $end_div);
	}
}
