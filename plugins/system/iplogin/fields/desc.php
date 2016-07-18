<?php
/**
 * Element: IP
 * Displays a description and example urls
 *
 * @package         IP Login
 * @version         2.1.1PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class JFormFieldIPLogin_Desc extends JFormField
{
	protected $type = 'Desc';
	private $params = null;

	protected function getLabel()
	{
		$this->params = $this->element->attributes();

		$label = $this->get('label');
		$class = $this->get('class');
		$key   = $this->generateKey();

		return '</div><div class="' . $class . '">'
		. JText::_($label) . '<br />'
		. JUri::root() . '...?<code>' . $key . '</code><br />'
		. JUri::root() . '...?...&<code>' . $key . '</code>';
	}

	protected function getInput()
	{
		return '';
	}

	protected function generateKey()
	{
		$chars = str_split('abcdefhjkmnpqrtuvwxy3478');
		$key   = '';

		for ($i = 0; $i < 8; $i++)
		{
			$c = $chars[array_rand($chars)];

			$key .= rand(0, 1) ? $c : strtoupper($c);
		}

		return $key;
	}

	private function get($val, $default = '')
	{
		return (isset($this->params[$val]) && (string) $this->params[$val] != '') ? (string) $this->params[$val] : $default;
	}
}
