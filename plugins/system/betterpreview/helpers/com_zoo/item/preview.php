<?php
/**
 * Helper class: com_zoo.item
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

class HelperBetterPreviewPreviewZooitem extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_zoo.element.textarea')
		{
			return;
		}
		parent::renderPreview($article, $context);
	}

	function states()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

		$zoo = App::getInstance('zoo');

		$id = JFactory::getApplication()->input->get('item_id');

		$item = $zoo->table->item->get($id);
		$cats = $item->getRelatedCategories();

		$this->states[] = (object) array(
			'table'     => 'zoo_item',
			'id'        => $item->id,
			'name'      => $item->name,
			'published' => $item->state,
			'access'    => $item->access,
			'hits'      => $item->hits,
			'url'       => $zoo->route->item($item, 0),
			'type'      => JText::_('ITEM'),
			'names'     => (object) array(
				'id'        => 'id',
				'published' => 'state',
				'access'    => 'access',
				'hits'      => 'hits',
			),
		);

		foreach ($cats as $cat)
		{
			$this->states[] = (object) array(
				'table'     => 'zoo_item',
				'id'        => $cat->id,
				'name'      => $cat->name,
				'published' => $cat->published,
				'url'       => $zoo->route->category($cat, 0),
				'type'      => JText::_('CATEGORY'),
				'names'     => (object) array(
					'id'        => 'id',
					'published' => 'published',
				),
			);
		}

		$this->setStates();
	}

	function getShowIntro(&$article)
	{
		return 1;
	}

	function setStates()
	{
		$app = App::getInstance('zoo');

		foreach ($this->states as $state)
		{
			$item = $app->table->item->get($state->id);

			if (empty($item))
			{
				continue;
			}

			if (isset($state->names->published))
			{
				$item->{$state->names->published} = 1;
			}

			if (isset($state->names->access))
			{
				$item->{$state->names->access} = 1;
			}

			if (isset($state->names->publish_up) && $state->publish_up > 0)
			{
				$now                               = $this->app->date->create()->toSql();
				$item->{$state->names->publish_up} = $app->date->create(time() - 86400)->toSql();
			}

			if (isset($state->names->publish_down) && $state->publish_down > 0)
			{
				$item->{$state->names->publish_down} = $app->date->create(time() + 86400)->toSql();
			}
		}
	}

	function restoreStates()
	{
		return;
	}
}
