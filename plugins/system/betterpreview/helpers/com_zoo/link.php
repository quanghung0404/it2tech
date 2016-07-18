<?php
/**
 * Link Helper class: com_zoo
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

class HelperBetterPreviewLinkZoo extends HelperBetterPreviewLink
{
	function getLinks()
	{
		// don't show any extra links by default
		return array();
	}
}
