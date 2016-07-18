<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('admin:/tables/table');

class SocialTableEventMeta extends SocialTable
{
    public $id = null;
    public $cluster_id = null;
    public $start = null;
    public $end = null;
    public $timezone = null;
    public $all_day = null;
    public $group_id = null;
    public $reminder = null;

    public function __construct(& $db)
    {
        parent::__construct('#__social_events_meta' , 'id' , $db);
    }

    /**
     * Returns the SocialDate object of the event start datetime.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  SocialDate The SocialDate object of the event start datetime.
     */
    public function getStart()
    {
        $datetime = FD::date($this->start, false);

        if (!empty($this->timezone)) {
            try {
                $datetime->setTimezone(new DateTimeZone($this->timezone));
            } catch(Exception $e) {}
        }

        return $datetime;
    }

    /**
     * Returns the SocialDate object of the event end datetime.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  SocialDate The SocialDate object of the event end datetime.
     */
    public function getEnd()
    {
        // If there's no end date, we assume that the end date is the same as the start date
        if (empty($this->end) || $this->end === '0000-00-00 00:00:00') {
            $datetime = FD::date($this->start, false);
        } else {
            $datetime = FD::date($this->end, false);
        }

        if (!empty($this->timezone)) {
            try {
                $datetime->setTimezone(new DateTimeZone($this->timezone));
            } catch(Exception $e) {}
        }

        return $datetime;
    }

    /**
     * Check if this event has an end date.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return boolean   True if event has an end date.
     */
    public function hasEnd()
    {
        return !empty($this->end) && $this->end !== '0000-00-00 00:00:00';
    }

    /**
     * Checks if this event is an all day event.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3.7
     * @access public
     * @return boolean   True if this event is an all day event.
     */
    public function isAllDay()
    {
        return (bool) $this->all_day;
    }

    /**
     * Checks if this event is a group event.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3.9
     * @access public
     * @return boolean   True if this is a group event.
     */
    public function isGroupEvent()
    {
        return !empty($this->group_id);
    }

    /**
     * Returns the SocialDate object of the event timezone.
     *
     * @author  Nik Faris <jasonrey@stackideas.com>
     * @since   1.4
     * @access  public
     * @return  SocialDate The SocialDate object of the event timezone.
     */
    public function getTimezone()
    {
        if (!empty($this->timezone)) {
            return $this->timezone;
        }

        return false;
    }

    public function getReminder()
    {
        return $this->reminder;
    }
}
