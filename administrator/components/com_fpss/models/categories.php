<?php
/**
 * @version		$Id: categories.php 2186 2012-11-15 19:22:34Z joomlaworks $
 * @package		Frontpage Slideshow
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class FPSSModelCategories extends FPSSModel
{

	function getData()
	{
		$db = $this->getDBO();
		$query = "SELECT category.*, COUNT(slide.id) AS numOfSlides FROM #__fpss_categories AS category
		LEFT JOIN #__fpss_slides AS slide ON category.id = slide.catid ";
		$conditions = array();
		if ($this->getState('published') != -1)
		{
			$conditions[] = "category.published = ".(int)$this->getState('published');
		}
		if ($this->getState('language'))
		{
			$conditions[] = "category.language = ".$db->Quote($this->getState('language'));
		}
		if ($this->getState('search'))
		{
			$conditions[] = "LOWER(category.name) LIKE ".$db->Quote('%'.$db->getEscaped($this->getState('search'), true).'%', false);
		}
		if (count($conditions))
		{
			$query .= " WHERE ".implode(' AND ', $conditions);
		}
		$query .= " GROUP BY category.id ORDER BY ".$this->getState('ordering')." ".$this->getState('orderingDir');
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$rows = $db->loadObjectList();
		return $rows;
	}

	function getTotal()
	{
		$db = $this->getDBO();
		$query = "SELECT COUNT(*) FROM #__fpss_categories";
		$conditions = array();
		if ($this->getState('published') != -1)
		{
			$conditions[] = "published = ".(int)$this->getState('published');
		}
		if ($this->getState('language'))
		{
			$conditions[] = "language = ".$db->Quote($this->getState('language'));
		}
		if ($this->getState('search'))
		{
			$conditions[] = "LOWER(name) LIKE ".$db->Quote('%'.$db->getEscaped($this->getState('search'), true).'%', false);
		}
		if (count($conditions))
		{
			$query .= " WHERE ".implode(' AND ', $conditions);
		}
		$db->setQuery($query);
		$total = $db->loadResult();
		return $total;
	}

	function publish()
	{
		$row = JTable::getInstance('category', 'FPSS');
		$row->publish($this->getState('id'), 1);
	}

	function unpublish()
	{
		$row = JTable::getInstance('category', 'FPSS');
		$row->publish($this->getState('id'), 0);
	}

	function remove()
	{
		$row = JTable::getInstance('category', 'FPSS');
		$row->truncate($this->getState('id'));
		$row->delete($this->getState('id'));
	}

	function saveorder()
	{
		$id = $this->getState('id');
		$order = $this->getState('order');
		$total = count($id);
		JArrayHelper::toInteger($order, array(0));
		$row = JTable::getInstance('category', 'FPSS');
		for ($i = 0; $i < $total; $i++)
		{
			$row->load((int)$id[$i]);
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				$row->store();
			}
		}
	}

}
