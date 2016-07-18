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

class NNCache
{
	static $cache = array();

	static public function has($hash)
	{
		return isset(self::$cache[$hash]);
	}

	static public function get($hash)
	{
		if (!isset(self::$cache[$hash]))
		{
			return false;
		}

		return is_object(self::$cache[$hash]) ? clone self::$cache[$hash] : self::$cache[$hash];
	}

	static public function set($hash, $data)
	{
		self::$cache[$hash] = $data;

		return $data;
	}

	static public function read($hash)
	{
		if (isset(self::$cache[$hash]))
		{
			return self::$cache[$hash];
		}

		$cache = JFactory::getCache('nonumber', 'output');

		return $cache->get($hash);
	}

	static public function write($hash, $data, $ttl = 0)
	{
		self::$cache[$hash] = $data;

		$cache = JFactory::getCache('nonumber', 'output');

		if ($ttl)
		{
			// convert ttl to minutes
			$cache->setLifeTime($ttl * 60);
		}

		$cache->setCaching(true);

		$cache->store($data, $hash);

		self::set($hash, $data);

		return $data;
	}
}
