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

class NNLicenses
{
	public static $instance = null;

	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new NoNumberLicenses;
		}

		return self::$instance;
	}
}

class NoNumberLicenses
{
	/*
	 *  Deprecated. Use render()
	*/
	public function getMessage($name, $check_pro = false)
	{
		return self::render($name, $check_pro);
	}

	public static function render($name, $check_pro = false)
	{
		if (!$name)
		{
			return '';
		}

		require_once __DIR__ . '/functions.php';

		$alias = NNFrameworkFunctions::getAliasByName($name);
		$name  = NNFrameworkFunctions::getNameByAlias($name);

		if ($check_pro && self::isPro($alias))
		{
			return '';
		}

		return
			'<div class="alert nn_licence">'
			. JText::sprintf('NN_IS_FREE_VERSION', $name)
			. '<br />'
			. JText::_('NN_FOR_MORE_GO_PRO')
			. '<br />'
			. '<a href="https://www.nonumber.nl/purchase?ext=' . $alias . '" target="_blank" class="btn btn-small btn-primary">'
			. ' <span class="icon-basket"></span>'
			. html_entity_decode(JText::_('NN_GO_PRO'), ENT_COMPAT, 'UTF-8')
			. '</a>'
			. '</div>';
	}

	private static function isPro($element)
	{
		require_once __DIR__ . '/functions.php';

		if (!$version = NNFrameworkFunctions::getXMLValue('version', $element))
		{
			return false;
		}

		return (stripos($version, 'PRO') !== false);
	}
}
