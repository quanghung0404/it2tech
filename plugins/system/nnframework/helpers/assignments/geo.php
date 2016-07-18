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

class NNFrameworkAssignmentsGeo extends NNFrameworkAssignment
{
	var $geo = null;

	/**
	 * passContinents
	 */
	function passContinents()
	{
		if (!$this->getGeo() || empty($this->geo->continentCode))
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->geo->continentCode);
	}

	/**
	 * passCountries
	 */
	function passCountries()
	{
		if (!$this->getGeo() || empty($this->geo->countryCode))
		{
			return $this->pass(false);
		}

		return $this->passSimple($this->geo->countryCode);
	}

	/**
	 * passRegions
	 */
	function passRegions()
	{
		if (!$this->getGeo() || empty($this->geo->countryCode) || empty($this->geo->regionCode))
		{
			return $this->pass(false);
		}

		$region = $this->geo->countryCode . '-' . $this->geo->regionCode;

		return $this->passSimple($region);
	}

	/**
	 * passPostalcodes
	 */
	function passPostalcodes()
	{
		if (!$this->getGeo() || empty($this->geo->postalCode))
		{
			return $this->pass(false);
		}

		// replace dashes with dots: 730-0011 => 730.0011
		$postalcode = str_replace('-', '.', $this->geo->postalCode);

		return $this->passInRange($postalcode);
	}

	public function getGeo($ip = '')
	{
		if ($this->geo !== null)
		{
			return $this->geo;
		}

		if (!file_exists(JPATH_LIBRARIES . '/geoip/geoip.php'))
		{
			return false;
		}

		require_once JPATH_LIBRARIES . '/geoip/geoip.php';

		$geo = new GeoIp($ip);

		$this->geo = $geo->get();

		return $this->geo;
	}
}
