<?php
/**
 * Plugin Helper File
 *
 * @package         Better Preview
 * @version         4.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 ** Plugin that places the button
 */
class HelperBetterPreviewButton extends PlgSystemBetterPreviewHelper
{
	public function __construct(&$params)
	{
		parent::__construct($params);
	}

	function getExtraJavaScript($text)
	{
		return '';
	}
}
