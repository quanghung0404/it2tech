<?php
/**
 * Helper class: com_k2.itemlist
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

class HelperBetterPreviewPreviewK2Itemlist extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_k2.category' || !isset($article->description))
		{
			return;
		}
		parent::renderPreview($article, $context);
	}

	function states()
	{
		parent::initStates(
			'k2_categories',
			array(),
			'k2_categories',
			array()
		);
	}

	function getShowIntro(&$article)
	{
		if (isset($article->params))
		{
			return 1;
		}

		if (!is_object($params))
		{
			$params = (object) json_decode($article->params);

			return $params->catItemIntroText;
		}

		return $article->params->get('catItemIntroText', '1');
	}
}
