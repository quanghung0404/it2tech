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

require_once dirname(__DIR__) . '/assignment.php';

class NNFrameworkAssignmentsURLs extends NNFrameworkAssignment
{
	function passURLs()
	{
		$regex = isset($this->params->regex) ? $this->params->regex : 0;

		if (!is_array($this->selection))
		{
			$this->selection = explode("\n", $this->selection);
		}

		if (count($this->selection) == 1)
		{
			$this->selection = explode("\n", $this->selection['0']);
		}

		$url = JUri::getInstance();
		$url = $url->toString();

		$urls = array(
			html_entity_decode(urldecode($url), ENT_COMPAT, 'UTF-8'),
			urldecode($url),
			html_entity_decode($url, ENT_COMPAT, 'UTF-8'),
			$url,
		);
		$urls = array_unique($urls);

		$pass = false;
		foreach ($urls as $url)
		{
			foreach ($this->selection as $s)
			{
				$s = trim($s);
				if ($s == '')
				{
					continue;
				}

				if ($regex)
				{
					$url_part = str_replace(array('#', '&amp;'), array('\#', '(&amp;|&)'), $s);
					$s        = '#' . $url_part . '#si';
					if (@preg_match($s . 'u', $url) || @preg_match($s, $url))
					{
						$pass = true;
						break;
					}

					continue;
				}

				if (strpos($url, $s) !== false)
				{
					$pass = true;
					break;
				}
			}

			if ($pass)
			{
				break;
			}
		}

		return $this->pass($pass);
	}
}
