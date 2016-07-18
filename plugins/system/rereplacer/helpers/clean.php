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

class PlgSystemReReplacerHelperClean
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemReReplacerHelpers::getInstance();
	}

	public function cleanString(&$string)
	{
		$string = str_replace(array('[:space:]', '\[\:space\:\]', '[[space]]', '\[\[space\]\]'), ' ', $string);
		$string = str_replace(array('[:comma:]', '\[\:comma\:\]', '[[comma]]', '\[\[comma\]\]'), ',', $string);
		$string = str_replace(array('[:newline:]', '\[\:newline\:\]', '[[newline]]', '\[\[newline\]\]'), "\n", $string);
		$string = str_replace('[:REGEX_ENTER:]', '\\n', $string);
	}

	public function cleanStringReplace(&$string, $is_regex = 0)
	{
		if (!$is_regex)
		{
			$string = str_replace(array('\\', '\\\\#', '$'), array('\\\\', '\\#', '\\$'), $string);
		}

		$this->cleanString($string);
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
			$this->helpers->get('replace')->replaceInArray($string_array, '#\{noreplace\}#', '');
			$this->helpers->get('replace')->replaceInArray($string_array, '#\{/noreplace\}#', '');
			$string = implode('', $string_array);
		}
	}
}
