<?php
/**
 * Plugin Helper File: JotCache Model
 *
 * @package         Cache Cleaner
 * @version         4.2.3PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;



class JotCacheMainModelMain extends MainModelMain
{
	function __construct()
	{
		JModelLegacy::__construct();
		$this->app = JFactory::getApplication();
		$pars      = $this->getPluginParams();
		if (!is_object($pars->storage))
		{
			$pars->storage       = new stdClass;
			$pars->storage->type = 'file';
		}
		switch ($pars->storage->type)
		{
			case 'memcache':
				JLoader::register('JotcacheMemcache', JPATH_ADMINISTRATOR . '/components/com_jotcache/helpers/memcache.php');
				$this->storage = new JotcacheMemcache($pars);
				break;
			case 'memcached':
				JLoader::register('JotcacheMemcached', JPATH_ADMINISTRATOR . '/components/com_jotcache/helpers/memcached.php');
				$this->storage = new JotcacheMemcached($pars);
				break;
			default:
				break;
		}
		$this->refresh = new JotcacheRefresh($this->_db, $this->storage);
		$this->store   = new JotcacheStore($this->_db, $this->storage);
	}
}
