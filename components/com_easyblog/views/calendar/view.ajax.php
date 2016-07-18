<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewCalendar extends EasyBlogView
{
	/**
	 * Displays the calendar via ajax
	 *
	 * @since	4.0
	 * @access	public
	 */
    public function render()
    {
    	$timestamp = $this->input->get('timestamp', '', 'default');
        $category = $this->input->get('category', '', 'default');

    	// Get the archive model
    	$model = EB::model('Archive');

		// Get the date object based on the timestamp provided
		$date = EB::calendar()->getDateObject($timestamp);

		// Get the related data
		$calendar = EB::calendar()->prepare($date);

        // Get the posts data
        $posts = $model->getArchivePostByMonth($date->month, $date->year, false, $category);

        $template = EB::template();
        $template->set('calendar', $calendar);
        $template->set('date', $date);
        $template->set('posts', $posts);

        $output = $template->output('site/blogs/calendar/calendar');

        return $this->ajax->resolve($output);
    }

}
