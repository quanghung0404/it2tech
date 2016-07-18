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

class JSNEasySliderSlider
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
	 * Get Slider info by ID
	 * @param int $sliderID
	 * 
	 * @return object
	 */
	public function getSliderInfoByID($sliderID)
	{
		try
		{
			$query = $this->_db->getQuery(true);
			$query->clear();
			$query->select('*');
			$query->from($this->_db->quoteName('#__jsn_easyslider_sliders'));
			$query->where($this->_db->quoteName('slider_id') . ' =  ' . (int) $sliderID /*. ' AND ' . $this->_db->quoteName('published') . ' = 1'*/ );
			$this->_db->setQuery($query);
			return $this->_db->loadObject();
		}
		catch (Exception $e)
		{
			return false;
		}
	}


	public function randomString($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
	{
		$charsLength 	= (strlen($chars) - 1);
		$string 		= $chars{rand(0, $charsLength)};

		for ($i = 1; $i < $length; $i = strlen($string))
		{
			$r = $chars{rand(0, $charsLength)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}

		return $string;
	}
}