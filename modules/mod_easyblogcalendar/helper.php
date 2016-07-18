<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modEasyBlogCalendarHelper
{
	public static function prepareData($date=array())
	{
		if (empty($date)) {
			$date = modEasyBlogCalendarHelper::getCurrentDate();
		}

		$calendar = new stdClass();

		//Here we generate the first day of the month
		$calendar->first_day = mktime(0,0,0,$date['month'], 1, $date['year']);

		//This gets us the month name
		$calendar->title = date('F', $first_day) ;

		//Here we find out what day of the week the first day of the month falls on
		$calendar->day_of_week = date('D', $first_day) ;

		//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
		switch($calendar->day_of_week)
		{
			case "Sun":
				$calendar->blank = 0;
				break;
			case "Mon":
				$calendar->blank = 1;
				break;
			case "Tue":
				$calendar->blank = 2;
				break;
			case "Wed":
				$calendar->blank = 3;
				break;
			case "Thu":
				$calendar->blank = 4;
				break;
			case "Fri":
				$calendar->blank = 5;
				break;
			case "Sat":
				$calendar->blank = 6;
				break;
		}

		//We then determine how many days are in the current month
		$calendar->days_in_month = cal_days_in_month(0, $date['month'], $date['year']);

		//This counts the days in the week, up to 7
		$day_count = 1;

		return $calendar;
	}

	public static function getCurrentDate()
	{
		//This gets today's date
		$jdate = EB::date();
		$now = $jdate->toUnix();

		//This puts the day, month, and year in seperate variables
		$date['day'] = date('d', $now);
		$date['month'] = date('m', $now);
		$date['year'] = date('Y', $now);

		return $date;
	}
}
