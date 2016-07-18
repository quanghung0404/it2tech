<?php
/**
 * @package         Modals
 * @version         6.2.9PRO
 * 
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2016 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemModalsHelperData
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemModalsHelpers::getInstance();
		$this->params  = $this->helpers->getParams();

		$this->count_init = false;
	}

	public function setDataWidthHeight(&$data, $isexternal)
	{
		$this->setDataAxis($data, $isexternal, 'width');
		$this->setDataAxis($data, $isexternal, 'height');
	}

	public function setDataAxis(&$data, $isexternal, $axis = 'width')
	{
		if (!empty($data[$axis]))
		{
			return;
		}

		if ($isexternal)
		{
			$data[$axis] = $this->params->{'external' . $axis} ?: $this->params->{$axis} ?: '95%';

			return;
		}

		$data[$axis] = $this->params->{$axis} ?: $this->params->{'external' . $axis} ?: '95%';
	}

	public function setDataOpen(&$data, $attributes = null)
	{
		$data['open'] = !empty($data['open']) ? $data['open'] : '';
		$opentype     = !empty($data['opentype']) ? $data['opentype'] : '';

		switch (true)
		{
			// open once set via open=once or openOnce=1
			case (strtolower($data['open']) == 'once' || !empty($data['openonce'])):
				$count = $this->getOpenCount($opentype);
				$open  = $count <= 1;
				break;

			// min-max set via openMin and openMax parameter
			case (!empty($data['openmin']) || !empty($data['openmax'])):
				$min = !empty($data['openmin']) ? (int) $data['openmin'] : 0;
				$max = !empty($data['openmax']) ? (int) $data['openmax'] : 0;

				$count = $this->getOpenCount($opentype);
				$open  = (($max && $count <= $max) && $count >= $min);
				break;

			// min-max set via open parameter, like: open=2-10
			case (strpos($data['open'], '-') !== false):
				list($min, $max) = explode('-', $data['open'], 2);
				$min = (int) $min;
				$max = (int) $max;

				$count = $this->getOpenCount($opentype);
				$open  = (($max && $count <= $max) && $count >= $min);
				break;

			default:
				$open = (int) $data['open'];

				if ($open > 1)
				{
					$count = $this->getOpenCount($opentype);
					$open  = (bool) ($count == $open);
				}

				$open = (bool) $open;
		}

		unset($data['open']);
		unset($data['openonce']);
		unset($data['openmin']);
		unset($data['openmax']);

		if (!$open)
		{
			return;
		}

		$data['open'] = 'true';
	}

	public function flattenAttributeList($attributes)
	{
		$string = '';
		foreach ($attributes as $key => $val)
		{
			$key = trim($key);
			$val = trim($val);

			if ($key == '' || $val == '')
			{
				continue;
			}

			$string .= ' ' . $key . '="' . $val . '"';
		}

		return $string;
	}

	public function flattenDataAttributeList(&$dat)
	{
		if (isset($dat['width']))
		{
			unset($dat['externalWidth']);
		}

		if (isset($dat['height']))
		{
			unset($dat['externalHeight']);
		}

		$data = array();
		foreach ($dat as $key => $val)
		{
			if (!$str = $this->flattenDataAttribute($key, $val))
			{
				continue;
			}
			$data[] = $str;
		}

		return empty($data) ? '' : ' ' . implode(' ', $data);
	}

	public function flattenDataAttribute($key, $val)
	{
		if ($key == '' || $val == '')
		{
			return false;
		}

		if (strpos($key, 'title_') !== false || strpos($key, 'description_') !== false)
		{
			return false;
		}

		$key = $key == 'externalWidth' ? 'width' : $key;
		$key = $key == 'externalHeight' ? 'height' : $key;


		$val = str_replace('"', '&quot;', $val);

		if ($key == 'group')
		{
			// map group value to rel
			return 'data-modal-rel="' . $val . '"';
		}

		if (($key == 'width' || $key == 'height') && strpos($val, '%') === false)
		{
			// set param to innerWidth/innerHeight if value of width/height is a percentage
			return 'data-modal-inner-' . $key . '="' . $val . '"';
		}

		if (in_array(strtolower($key), $this->params->paramNamesLowercase))
		{
			// fix use of lowercase params that should contain uppercase letters
			$key = $this->params->paramNamesCamelcase[array_search(strtolower($key), $this->params->paramNamesLowercase)];
			$key = strtolower(preg_replace('#([A-Z])#', '-\1', $key));
		}

		return 'data-modal-' . $key . '="' . $val . '"';
	}

	private function getOpenCount($type = '')
	{
		$type = $type ?: $this->params->open_count_based_on;

		if ($type == 'session')
		{
			return JFactory::getSession()->get('session.counter', 0);
		}

		$cookie_name = 'nn_modals';
		$cookie_name .= ($type == 'page') ? '_' . md5(NNText::getURI()) : '';

		$count = (int) isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : 0;
		$count++;

		if ($this->count_init)
		{
			return $count;
		}

		$ttl = $this->params->open_count_ttl ? $this->params->open_count_ttl * 60 : (365 * 24 * 60 * 60); // default: 1 year
		setcookie($cookie_name, $count, time() + $ttl);

		$this->count_init = true;

		return $count;
	}
}
