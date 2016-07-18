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

/**
 * Plugin that replaces stuff
 */
class PlgSystemReReplacerHelperReplace
{
	var $helpers = array();
	var $item = null;
	var $article = null;
	var $counter = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemReReplacerHelpers::getInstance();
	}

	public function replaceInAreas(&$string)
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		$this->replaceInArea($string, 'component');
		$this->replaceInArea($string, 'head');
		$this->replaceInArea($string, 'body');

		$this->replaceEverywhere($string);
	}

	private function replaceInArea(&$string, $area_type = '')
	{
		if (!is_string($string) || $string == '' || !$area_type)
		{
			return;
		}

		$items = $this->helpers->get('items')->getItemList($area_type);

		if (empty($items))
		{
			return;
		}

		$areas = $this->helpers->get('tag')->getAreaByType($string, $area_type);

		foreach ($areas as $area_type)
		{
			$this->replaceItemList($area_type['1'], $items);
			$string = str_replace($area_type['0'], $area_type['1'], $string);
		}

		unset($areas);
	}

	private function replaceEverywhere(&$string)
	{
		if (!is_string($string) || $string == '')
		{
			return;
		}

		$items = $this->helpers->get('items')->getItemList('everywhere');

		$this->replaceItemList($string, $items);
	}

	public function replace(&$string, $item = null, &$article = null)
	{
		if (empty($string))
		{
			return;
		}

		if ($item)
		{
			$this->item = clone($item);
		}

		if ($article)
		{
			$this->article = $article;
		}

		if (is_array($string))
		{
			$this->replaceArray($string);

			return;
		}

		$this->helpers->get('variables')->protectVariables($string);

		switch ($this->item->regex)
		{
			case true:
				$this->replaceRegEx($string);
				break;

			default:
				$this->replaceString($string);
				break;
		}

		$this->helpers->get('variables')->replaceVariables($string);

		if ($this->item->treat_as_php)
		{
			$this->replacePhp($string);
		}
	}

	private function replaceArray(&$array)
	{
		if (!is_array($array))
		{
			return;
		}

		foreach ($array as &$string)
		{
			$this->replace($string);
		}
	}

	private function replaceItemList(&$string, $items)
	{
		if (empty($items))
		{
			return;
		}

		if (!is_array($items))
		{
			$items = array($items);
		}

		foreach ($items as $item)
		{
			$this->replace($string, $item);
		}
	}

	private function replaceRegEx(&$string)
	{
		$string       = str_replace(chr(194) . chr(160), ' ', $string);
		$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $this->item);

		$this->helpers->get('clean')->cleanString($this->item->search);

		// escape hashes
		$this->item->search = str_replace('#', '\#', $this->item->search);
		// unescape double escaped hashes
		$this->item->search = str_replace('\\\\#', '\#', $this->item->search);

		$this->prepareRegex($this->item->search, $this->item->s_modifier, $this->item->casesensitive);

		$this->replaceInArray($string_array);

		$string = implode('', $string_array);
	}

	private function replaceString(&$string)
	{
		$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $this->item);

		$search_array  = $this->item->treat_as_list ? explode(',', $this->item->search) : array($this->item->search);
		$replace_array = $this->item->treat_as_list ? explode(',', $this->item->replace) : array($this->item->replace);
		$replace_count = count($replace_array);

		foreach ($search_array as $key => $search)
		{
			if ($search == '')
			{
				continue;
			}

			// Prepare search string
			$this->helpers->get('clean')->cleanString($search);
			$this->item->search = preg_quote($search, "#");
			if ($this->item->word_search)
			{
				$this->item->search = '(?<!\p{L})(' . $this->item->search . ')(?!\p{L})';
			}
			$this->prepareRegex($this->item->search, 1, $this->item->casesensitive);

			// Prepare replace string
			$this->item->replace = ($replace_count > $key) ? $replace_array[$key] : $replace_array['0'];

			$this->replaceInArray($string_array);
		}

		$string = implode('', $string_array);
	}

	public function replaceInArray(&$array)
	{
		foreach ($array as $key => &$string)
		{
			// only do something if string is not empty
			// or on uneven count = not yet protected
			if (trim($string) == '' || fmod($key, 2))
			{
				continue;
			}

			$this->replacer($string);
		}
	}

	private function replacer(&$string)
	{
		if (substr($this->item->search, -1) !== 'u' && @preg_match($this->item->search . 'u', $string))
		{
			$this->item->search .= 'u';
		}

		if (!preg_match($this->item->search, $string))
		{
			return;
		}

		// Get the resulting html if treat as PHP is on
		if ($this->item->treat_as_php)
		{
			$this->item->replace = '<!-- >>> ReReplacer: START PHP >>> -->' . $this->item->replace . '<!-- <<< ReReplacer: END PHP <<< -->';
		}

		$this->helpers->get('clean')->cleanStringReplace($this->item->replace, $this->item->regex);

		// Do a simple replace if not thorough and counter is not found
		if (!$this->item->thorough && strpos($this->item->replace, '[[counter]]') === false && strpos($this->item->replace, '\#') === false)
		{
			$string = preg_replace($this->item->search, $this->item->replace, $string);

			return;
		}

		$counter_name = $this->getCounterName($this->item->search, $this->item->replace);

		$thorough_count = 1; // prevents the thorough search to repeat endlessly
		// preg_match_all: Needs the 3rd parameter in < php 5.4.0
		while ($count = preg_match_all($this->item->search, $string, $matches))
		{
			$this->replaceOccurrence($this->item->search, $this->item->replace, $string, $count, $counter_name);

			if (!$this->item->thorough || ++$thorough_count >= 100)
			{
				break;
			}
		}
	}

	private function getCounterName($search, $replace)
	{
		if (strpos($replace, '[[counter]]') === false && strpos($replace, '\#') === false)
		{
			return '';
		}

		// Counter is used to make it possible to use \# or [[counter]] in the replacement to refer to the incremental counter
		$counter_name = base64_encode($search . $replace);

		if (!isset($this->counter[$counter_name]))
		{
			$this->counter[$counter_name] = 0;
		}

		return $counter_name;
	}

	private function replaceOccurrence($search, $replace, &$string, $count = 0, $counter_name = '')
	{
		if (!$counter_name)
		{
			$string = preg_replace($search, $replace, $string);

			return;
		}

		for ($i = 0; $i < $count; $i++)
		{
			// Replace \# with the incremental counter
			$replace_c = str_replace(array('\#', '[[counter]]'), ++$this->counter[$counter_name], $replace);

			// Replace with offset
			preg_match($this->item->search, $string, $matches, PREG_OFFSET_CAPTURE);

			$substring          = substr($string, $matches['0']['1']);
			$substring_replaced = preg_replace($search, $replace_c, $substring, 1);

			$string = str_replace($substring, $substring_replaced, $string);
		}
	}

	private function replacePhp(&$string)
	{
		if (strpos($string, '<!-- >>> ReReplacer: START PHP >>> -->') === false)
		{
			return;
		}

		$regex = '#<\!-- >>> ReReplacer: START PHP >>> -->(.*?)<\!-- <<< ReReplacer: END PHP <<< -->#s';

		preg_match_all($regex, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$result = $this->getPhpResult($match['1']);
			$string = str_replace($match['0'], $result, $string);
		}

		return $string;
	}

	private function getPhpResult($string)
	{
		if (!isset($this->Itemid))
		{
			$this->Itemid = JFactory::getApplication()->input->getInt('Itemid');
		}
		if (!isset($this->mainframe) || !isset($this->app))
		{
			$this->mainframe = $this->app = JFactory::getApplication();
		}
		if (!isset($this->document) || !isset($this->doc))
		{
			$this->document = $this->doc = JFactory::getDocument();
		}
		if (!isset($this->database) || !isset($this->db))
		{
			$this->database = $this->db = JFactory::getDbo();
		}
		if (!isset($this->user))
		{
			$this->user = JFactory::getUser();
		}

		$this->preparePhp($string);
		$string = str_replace('?><?php', '', $string . '<?php ;');

		$temp_PHP_func = create_function('&$article, &$Itemid, &$mainframe, &$app, &$document, &$doc, &$database, &$db, &$user', $string);

		// evaluate the script
		// but without using the the evil eval
		ob_start();
		$temp_PHP_func($this->article, $this->Itemid, $this->mainframe, $this->app, $this->document, $this->doc, $this->database, $this->db, $this->user);
		unset($temp_PHP_func);
		$result = ob_get_contents();
		ob_end_clean();

		return $result;
	}

	private function preparePhp(&$string)
	{
		$string = trim($string);

		if (substr($string, 0, 5) !== '<?php')
		{
			$string = '?>' . $string;

			return;
		}

		$string = substr($string, 5);
	}

	private function prepareRegex(&$string, $dotall = 1, $casesensitive = 1)
	{
		$string = '#' . $string . '#';

		$string .= $dotall ? 's' : ''; // . (dot) also matches newlines
		$string .= $casesensitive ? '' : 'i'; // case-insensitive pattern matching

		// replace new lines with regex match
		$string = str_replace(array("\r", "\n"), array('', '(?:\r\n|\r|\n)'), $string);
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	public function cleanLeftoverJunk(&$string)
	{
		$string = preg_replace('#<\!-- (START|END): RR_[^>]* -->#', '', $string);

		// Remove any leftover protection strings (shouldn't be necessary, but just in case)
		$this->helpers->get('protect')->cleanProtect($string);

		// Remove any leftover protection tags
		if (strpos($string, '{noreplace}') !== false)
		{
			$item         = null;
			$string_array = $this->helpers->get('protect')->stringToProtectedArray($string, $item, 1);
			$this->replaceInArray($string_array, '#\{noreplace\}#', '');
			$this->replaceInArray($string_array, '#\{/noreplace\}#', '');
			$string = implode('', $string_array);
		}
	}
}
