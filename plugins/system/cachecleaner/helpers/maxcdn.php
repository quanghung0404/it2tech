<?php
/**
 * Plugin Helper File: MaxCDN
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

require_once __DIR__ . '/cache.php';

class PlgSystemCacheCleanerHelperMaxCDN extends PlgSystemCacheCleanerHelperCache
{
	public function purge()
	{
		if (empty($this->params->maxcdn_authorization_key))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_AUTHORIZATION_KEY', JText::_('CC_MAXCDN')));

			return;
		}

		if (empty($this->params->maxcdn_zones))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_ZONES', JText::_('CC_MAXCDN')));

			return;
		}

		$api = $this->getAPI();
		if (!$api || is_string($api))
		{
			$error = JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_MAXCDN'));
			if (is_string($api))
			{
				$error .= '<br />' . $api;
			}

			$this->setError($error);

			return;
		}
		if (!$api = $this->getAPI())
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_MAXCDN')));

			return;
		}

		$zones = explode(',', $this->params->maxcdn_zones);
		foreach ($zones as $zone)
		{
			$api_call = json_decode($api->delete('/zones/pull.json/' . $zone . '/cache'));
			if (is_null($api_call))
			{
				$api_call = new stdClass;
			}

			if (!isset($api_call->code)
				|| ($api_call->code != 200 && $api_call->code != 201)
			)
			{
				$this->setError(JText::sprintf('CC_ERROR_CDN_COULD_NOT_PURGE_ZONE', JText::_('CC_MAXCDN'), $zone));

				return false;
			}
		}
	}

	public function getAPI()
	{
		$keys = explode('+', $this->params->maxcdn_authorization_key, 3);

		if (count($keys) < 3)
		{
			return false;
		}

		list($alias, $consumer_key, $consumer_secret) = $keys;

		require_once __DIR__ . '/../api/NetDNA.php';

		return new NetDNA($alias, $consumer_key, $consumer_secret);
	}
}
