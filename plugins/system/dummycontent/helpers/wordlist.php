<?php
/**
 * Plugin Helper File: Text
 *
 * @package         Dummy Content
 * @version         2.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemDummyContentHelperWordlist
{
	var $list = array();
	var $type = 'lorem';
	var $issentence = false;

	public function setType($type)
	{
		$this->issentence = false;
		$this->type       = trim(preg_replace('#[^a-z0-9]#', '', strtolower($type)));
		if (substr($this->type, -5) == 'ipsum')
		{
			$this->type = substr($this->type, 0, -5);
		}

		switch ($this->type)
		{
			case 'bowie':
				$this->issentence = true;
				break;

			case 'business':
				$this->type = 'corporate';
				break;

			case 'fish':
				$this->type = 'fishier';
				break;

			case 'gangster':
				$this->type = 'gangsta';
				break;

			case 'space':
				$this->issentence = true;
				break;

			case 'web2':
				$this->type = 'web20';
				break;

			case 'what':
				$this->type = 'whatnothing';
				break;

			case 'arab':
				$this->type = 'arabic';
				break;

			case 'leet':
			case 'l33t':
			case 'l33tspeak':
				$this->type = 'leetspeak';
				break;

			case 'luxembourg':
			case 'letzebuerg':
			case 'letzebuergesch':
				$this->type = 'luxembourgish';
				break;

			case 'volapuk':
				$this->type = 'volapuek';
				break;
		}

		$path = dirname(dirname(__FILE__)) . '/wordlists/';
		if (!JFile::exists($path . $this->type . '.txt'))
		{
			$this->type = 'lorem';
		}

	}

	public function getList()
	{
		if (isset($this->list[$this->type]))
		{
			return $this->list[$this->type];
		}

		$path  = dirname(dirname(__FILE__)) . '/wordlists/';
		$words = file_get_contents($path . $this->type . '.txt');
		$words = trim(preg_replace('#(^|\n)\/\/ [^\n]*#s', '', $words));

		$this->list[$this->type] = explode("\n", $words);

		return $this->list[$this->type];
	}

	public function isSentenceList()
	{
		return $this->issentence;
	}
}
