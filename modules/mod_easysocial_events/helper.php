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

class EasySocialModEventsHelper
{
    public static function getEvents(&$params)
    {
        $model  = FD::model('Events');

        // Determine filter type
        $filter = $params->get('filter');

        // Determine the ordering of the events
        $ordering = $params->get('ordering', 'start');

        // Default options
        $options = array();

        // Limit the number of events based on the params
        $options['limit'] = $params->get('display_limit', 5);
        $options['ordering'] = $ordering;
        $options['state'] = SOCIAL_STATE_PUBLISHED;
        $options['type'] = array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE);
        $options['upcoming'] = true;
        $options['ongoing'] = true;
        
        $inclusion = trim($params->get('event_inclusion'));

        if ($inclusion) {
            $options['inclusion'] = explode(',', $inclusion);
        } 

        $events = array();

        // Category filtering
        $category = trim($params->get('category'));

        if (!empty($category)) {
            // Since category id's are stored as ID:alias, we only want the id portion
            $category = explode(':', $category);

            $options['category'] = $category[0];
        }

        // No filtering
        if ($filter == 0) {
            $events = $model->getEvents($options);
        }

        // Featured events only
        if ($filter == 2) {
            $options['featured'] = true;

            $events = $model->getEvents($options);
        }

        // Retrieve events participated by the current logged in user
        if ($filter == 3) {
            $my = FD::user();

            $options['type'] = 'user';
            $options['guestuid'] = $my->id;
            $events = $model->getEvents($options);
        }

        return $events;
    }
}
