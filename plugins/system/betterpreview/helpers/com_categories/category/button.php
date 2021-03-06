<?php
/**
 * Button Helper class: com_categories.category
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

include_once __DIR__ . '/helper.php';

class HelperBetterPreviewButtonCategoriesCategory extends HelperBetterPreviewButton
{
	function getURL($name)
	{
		$helper = new HelperBetterPreviewHelperCategoriesCategory($this->params);

		if (!$item = $helper->getCategory())
		{
			return;
		}

		return $item->url;
	}
}
