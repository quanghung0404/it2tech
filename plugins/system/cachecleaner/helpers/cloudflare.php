<?php
/**
 * Plugin Helper File: CloudFlare
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

class PlgSystemCacheCleanerHelperCloudFlare extends PlgSystemCacheCleanerHelperCache
{
	public function purge()
	{
		if (empty($this->params->cloudflare_username))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_USERNAME', JText::_('CC_CLOUDFLARE')));

			return;
		}

		if (empty($this->params->cloudflare_token))
		{
			$this->setError(JText::sprintf('CC_ERROR_CDN_NO_API_KEY', JText::_('CC_CLOUDFLARE')));

			return;
		}

		$api = $this->getAPI();
		if (!$api || is_string($api))
		{
			$error = JText::sprintf('CC_ERROR_CDN_COULD_NOT_INITIATE_API', JText::_('CC_CLOUDFLARE'));
			if (is_string($api))
			{
				$error .= '<br />' . $api;
			}

			$this->setError($error);

			return;
		}

		if (empty($this->params->cloudflare_domains))
		{
			$this->params->cloudflare_domains = JUri::getInstance()->toString(array('host'));
		}

		$domains = explode(',', $this->params->cloudflare_domains);
		foreach ($domains as $domain)
		{
			$api_call = json_decode($api->purge($domain));
			if (is_null($api_call))
			{
				$api_call = new stdClass;
			}

			if (!isset($api_call->result)
				|| $api_call->result != 'success'
			)
			{
				$this->setError(
					JText::sprintf('CC_ERROR_CDN_COULD_NOT_PURGE_ZONE', JText::_('CC_CLOUDFLARE'), $domain)
					. '<br />' . JText::_('CC_CLOUDFLARE') . ' Error: ' . $api_call->msg
				);

				return false;
			}
		}
	}

	public function getAPI()
	{
		require_once __DIR__ . '/../api/CloudFlare.php';

		return new CloudFlare($this->params->cloudflare_username, $this->params->cloudflare_token);
	}
}
