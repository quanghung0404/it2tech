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

require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

class PlgSystemReReplacerHelperProtect
{
	var $protect_start = '<!-- START: RR_PROTECT -->';
	var $protect_end = '<!-- END: RR_PROTECT -->';

	public function protect($string, $protect = 1)
	{
		return $protect
			? $this->protect_start . $string . $this->protect_end
			: $this->protect_end . $string . $this->protect_start;
	}

	public function stringToProtectedArray($string, &$item, $onlyform = 0)
	{
		$string_array = array($string);

		if (!($item->enable_in_edit_forms && !NNProtect::isAdmin())
			&& NNProtect::isEditPage()
		)
		{
			// Protect complete adminForm (to prevent ReReplacer messing stuff up when editing articles and such)
			$search = NNProtect::getFormRegex();
			$search = '(' . $search . '.*?</form>)';
			$this->protectArrayByRegex($string_array, $search, '', 1);
		}

		if ($onlyform)
		{
			return $string_array;
		}

		// Protect everything outside the between tags
		if (!$this->protectArrayOutsideBetweensStrings($string_array, $item->between_start, $item->between_end))
		{
			return array('', $string);
		}

		// Protect everything between the {noreplace} tags
		$search = '(\{noreplace\}.*?\{/noreplace\})';
		// Protect search result
		$this->protectArrayByRegex($string_array, $search, '', 1);

		// Protect all tags or everything but tags
		if ($item->enable_tags == 0 || $item->enable_tags == 2)
		{
			$search = '(</?[a-zA-Z][^>]*>)';
			if ($item->enable_tags == 0)
			{
				// no search permitted in tags, so all tags are protected
				// Protect search result
				$this->protectArrayByRegex($string_array, $search, '', 1);

				return $string_array;
			}

			// search only permitted in tags, so everything outside the tags is protected
			// Protect everything but search result
			$this->protectArrayByRegex($string_array, $search, '', 0);
		}

		// removes unwanted whitespace from tag selection
		$item->tagselect = preg_replace('#\s*(\[|\])\s*#', '\1', $item->tagselect);
		// removes unwanted params from tag selection
		// (if a asterisk is set, all other params for that tag name are redundant)
		$item->tagselect = preg_replace('#\[[^\]]*?\*[^\]]*\]#', '[*]', $item->tagselect);

		// tag selection is not used (or tags selection permits all tags)
		if (!$item->limit_tagselect || strpos($item->tagselect, '*[*]') !== false)
		{
			return $string_array;
		}

		// Convert tag selection to a nested array with trimmed tag names and params
		$tagselect = explode(']', $item->tagselect);

		$search_tags = array();
		foreach ($tagselect as $tag)
		{
			if (!strlen($tag))
			{
				continue;
			}

			$tag_parts  = explode('[', $tag);
			$tag_name   = trim($tag_parts['0']);
			$tag_params = array();

			if (count($tag_parts) < 2)
			{
				$search_tags[$tag_name] = $tag_params;
				continue;
			}

			$tag_params = $tag_parts['1'];
			// Trim and remove empty values
			$tag_params = array_diff(array_map('trim', explode(',', $tag_params)), array(''));

			if (in_array('*', $tag_params))
			{
				// Make array empty if asterisk is found
				// (the whole tag should be allowed)
				$search_tags[$tag_name] = array();
				continue;
			}

			$search_tags[$tag_name] = $tag_params;
		}

		// Tag selection is empty
		if (!count($search_tags))
		{
			return $string_array;
		}

		$this->protectArrayByTagList($string_array, $search_tags);

		return $string_array;
	}

	private function protectArrayByRegex(&$array, $search = '', $replace = '', $protect = 1, $convert = 1)
	{
		$search = '#' . $search . '#si';
		if (!$replace)
		{
			$replace = '\1';
		}

		$is_array = is_array($array);
		if (!$is_array)
		{
			$array = array($array);
		}

		foreach ($array as $key => &$string)
		{
			// only do something if string is not empty
			// or on uneven count = not yet protected
			if (trim($string) == '' || fmod($key, 2))
			{
				continue;
			}

			$this->protectStringByRegex($string, $search, $replace, $protect);
		}

		if (!$is_array)
		{
			$array = $array['0'];
		}

		if ($convert)
		{
			$array = $this->protectArray($array);
		}
	}

	private function protectStringByRegex(&$string, $search = '', $replace = '', $protect = 1)
	{
		if (@preg_match($search . 'u', $string))
		{
			$search .= 'u';
		}

		if (preg_match($search, $string))
		{
			$string = $protect
				? preg_replace($search, $this->protect($replace), $string)
				: $this->protect(preg_replace($search, $this->protect($replace, 0), $string));
		}

		$this->cleanProtected($string);
	}

	public function cleanProtect(&$string)
	{
		$string = str_replace(array($this->protect_start, $this->protect_end), '', $string);
	}

	private function cleanProtected(&$string)
	{
		while (strpos($string, $this->protect_start . $this->protect_start) !== false)
		{
			$string = str_replace($this->protect_start . $this->protect_start, $this->protect_start, $string);
		}
		while (strpos($string, $this->protect_end . $this->protect_end) !== false)
		{
			$string = str_replace($this->protect_end . $this->protect_end, $this->protect_end, $string);
		}
		while (strpos($string, $this->protect_end . $this->protect_start) !== false)
		{
			$string = str_replace($this->protect_end . $this->protect_start, '', $string);
		}
	}

	private function protectArray($array)
	{
		$new_array = array();

		foreach ($array as $key => $string)
		{
			// is string already protected?
			$protect    = fmod($key, 2);
			$item_array = $this->protectStringToArray($string, $protect);

			$new_array = array_merge($new_array, $item_array);
		}

		return $new_array;
	}

	private function protectStringToArray($string, $protected = 0)
	{
		if ($protected)
		{
			// If already protected, just clean string and place in an array
			$this->cleanProtect($string);

			return array($string);
		}

		// Return an array with 1 or 3 items.
		// 1) first part to protector start (if found) (= unprotected)
		// 2) part between the first protector start and its matching end (= protected)
		// 3) Rest of the string (= unprotected)

		$array = array();
		// Devide sting on protector start
		$string_array = explode($this->protect_start, $string);
		// Add first element to the string ( = even = unprotected)
		$this->cleanProtect($string_array['0']);
		$array[] = $string_array['0'];

		$count = count($string_array);
		if ($count < 2)
		{
			return $array;
		}

		for ($i = 1; $i < $count; $i++)
		{
			$substr        = $string_array[$i];
			$protect_count = 1;

			// Add the next string if not enough protector ends are found
			while (
				substr_count($substr, $this->protect_end) < $protect_count
				&& $i < ($count - 1)
			)
			{
				$protect_count++;
				$substr .= $string_array[++$i];
			}

			// Devide sting on protector end
			$substr_array = explode($this->protect_end, $substr);

			$protect_part = '';
			// Add as many parts to the string as there are protector starts
			for ($protect_i = 0; $protect_i < $protect_count; $protect_i++)
			{
				$protect_part .= array_shift($substr_array);
				if (!count($substr_array))
				{
					break;
				}
			}

			// This part is protected (uneven)
			$this->cleanProtect($protect_part);
			$array[] = $protect_part;

			// The rest of the string is unprotected (even)
			$unprotect_part = implode('', $substr_array);
			$this->cleanProtect($unprotect_part);
			$array[] = $unprotect_part;
		}

		return $array;
	}

	private function protectArrayOutsideBetweensStrings(&$string_array, $start, $end)
	{
		if ($start == '' && $end == '')
		{
			return true;
		}

		$has_betweens = false;
		foreach ($string_array as $key => $string)
		{
			// only do something if string is not empty
			// or on uneven count = not yet protected
			if (trim($string) == '' || fmod($key, 2))
			{
				continue;
			}

			if (
				($start == '' || strpos($string, $start) === false)
				&& ($end == '' || strpos($string, $start) === false)
			)
			{
				continue;
			}

			$has_betweens = true;
			break;
		}

		if (!$has_betweens)
		{
			// betweens not found, return false
			return false;
		}

		$search_start = $start == '' ? '^' : '(?<=' . preg_quote($start, '#') . ')';
		$search_end   = $end == '' ? '$' : '(?=' . preg_quote($end, '#') . ')';

		$this->protectArrayByRegex($string_array, $search_start . '(.*?)' . $search_end, '', 0);

		return true;
	}

	private function protectArrayByTagList(&$array, &$tags)
	{
		foreach ($array as $key => &$string)
		{
			// only do something if string is not empty
			// or on uneven count = not yet protected
			if (trim($string) == '' || fmod($key, 2))
			{
				continue;
			}

			$this->protectStringByTagList($string, $tags);
		}

		$array = $this->protectArray($array);
	}

	private function protectStringByTagList(&$string, &$tags)
	{
		// First: protect all tags
		$search = '(</?[a-zA-Z][^>]*>)';
		$this->protectArrayByRegex($string, $search, '', 1, 0);

		foreach ($tags as $tag_name => $tag_params)
		{
			$this->protectStringByTag($string, $tag_name, $tag_params);
		}
	}

	private function protectStringByTag(&$string, $tag_name, $tag_params)
	{
		if ($tag_name == '*')
		{
			$tag_name = '[a-zA-Z][^> ]*';
		}

		if (!count($tag_params))
		{
			// unprotect the whole tag
			$search = '(</?' . $tag_name . '( [^>]*)?>)';
			$this->protectArrayByRegex($string, $this->protect($search, 0), '', 1, 0);

			return;
		}

		// only unprotect the parameter values
		foreach ($tag_params as $tag_param)
		{
			$search = '#(<' . $tag_name . ' [^>]*' . $tag_param . '=")([^"]*)("[^>]*>)#si';

			if (@preg_match($search . 'u', $string))
			{
				$search .= 'u';
			}

			if (!preg_match($search, $string))
			{
				continue;
			}

			$replace = '\1' . $this->protect('\2', 0) . '\3';
			$string  = preg_replace($search, $replace, $string);
		}
	}
}
