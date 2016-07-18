<?php
/**
 * Helper class: com_form2content.form
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

class HelperBetterPreviewPreviewContentArticle extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_form2content.form' || !isset($article->id) || $article->id != JFactory::getApplication()->input->get('id'))
		{
			return;
		}

		parent::renderPreview($article, $context);
	}

	function states()
	{
		parent::initStates(
			'content',
			array(
				'published'    => 'state',
				'publish_up'   => 'publish_up',
				'publish_down' => 'publish_down',
				'parent'       => 'catid',
				'hits'         => 'hits',
			),
			'categories',
			array(
				'parent' => 'parent_id',
			)
		);
	}
}
