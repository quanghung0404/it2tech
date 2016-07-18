<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JSNEasySliderSliders
{
	private $_db = null;
	
	/**
	 * Contructor
	 */
	public function __construct()
	{
		$this->_db = JFactory::getDbo();	
	}
	
	/**
	 * Count number of slider item
	 * @return (int)
	 */
	public function countSilderItems()
	{
		try
		{
			$query = $this->_db->getQuery(true);
			$query->clear();
			$query->select('COUNT(slider_id)');
			$query->from($this->_db->quoteName('#__jsn_easyslider_sliders'));
			$this->_db->setQuery($query);
			return $this->_db->loadResult();
		}
		catch (Exception $e) 
		{
			return false;
		}
		
	}
	
	
	/**
	 * Get list of slider
	 * 
	 * @param int $limit
	 * @return ArrayObject
	 */
	
	public function getSlidersWithoutState($limit = "")
	{
		$q = $this->_db->getQuery(true);
		$q->clear();
		$q->select('*');
		$q->from('#__jsn_easyslider_sliders');
		$q->order("slider_id DESC");

		if (empty($limit))
		{
			$this->_db->setQuery($q);
		}
		else
		{
			$this->_db->setQuery($q, 0, $limit);
		}

		try
		{
			$results = $this->_db->loadObjectList();
		} 
		catch (Exception $e)
		{
			$results = false;
		}

		return $results;
	}

	/**
	 * Get list of slider
	 *
	 * @param int $state
	 * @return ArrayObject
	 */
	public function getSliders($state = 1)
	{
		try
		{
			$q = $this->_db->getQuery(true);
			$q->clear();
			$q->select('*, slider_title AS text, slider_id AS id');
			$q->from($this->_db->quoteName('#__jsn_easyslider_sliders'));
			$q->where($this->_db->quoteName('published') . '=' . $this->_db->quote((int) $state));
			$q->order($this->_db->quoteName('slider_id') . " DESC");
			$this->_db->setQuery($q);
			return $this->_db->loadObjectList();
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function getSliderModule($moduleID)
	{
		try
		{
			$q = $this->_db->getQuery(true);
			$q->clear();
			$q->select('params as data');
			$q->from($this->_db->quoteName('#__modules'));
			$q->where($this->_db->quoteName('id') . '=' . $this->_db->quote((int) $moduleID));
			$this->_db->setQuery($q);
			return $this->_db->loadResult();
		}
		catch (Exception $e)
		{
			return false;
		}
	}
}