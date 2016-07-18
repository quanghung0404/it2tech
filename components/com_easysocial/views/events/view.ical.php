<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('site:/views/views');

class EasySocialViewEvents extends EasySocialSiteView
{
    public function display($tpl = null)
    {
        return $this->export();
    }
    /**
     * Allows caller to export event items into downloadable ics file
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function export()
    {
        // Get the event object
        $event = FD::event($this->input->get('id', 0, 'int'));

        $theme = FD::themes();
        $theme->set('event', $event);
        $output = $theme->output('site/events/ical');

        $ts = substr(md5(rand(0,100)), 0, 5);
        $fileName = 'calendar_' . $ts . '.ics';

        // Allow caller to download the file
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: inline; filename=' . $fileName);
        echo $output;

        // Debug
        // echo nl2br($output);

        exit;
    }
}
