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

FD::import('admin:/includes/model');

class EasySocialModelEvents extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('events', $config);
    }

    public function initStates()
    {
        // Direction, search, limit, limitstart is handled by parent::initStates();
        parent::initStates();

        // Override ordering default value
        $ordering = $this->getUserStateFromRequest('ordering', 'a.id');
        $this->setState('ordering', $ordering);

        // Init other parameters
        $type = $this->getUserStateFromRequest('type', 'all');
        $state = $this->getUserStateFromRequest('state', 'all');

        $this->setState('type', $type);
        $this->setState('state', $state);
    }

    /**
     * Returns array of SocialEvent object for backend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return array    Array of SocialEvent object.
     */
    public function getItems()
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters', 'a');
        $sql->column('a.id');

        $search = $this->getState('search');

        if (!empty($search)) {
            $sql->where('a.title', '%' . $search . '%', 'LIKE');
        }

        $state = $this->getState('state');
        if ($state !== 'all') {
            $sql->where('a.state', $state);
        }

        $type = $this->getState('type');
        if ($type !== 'all') {
            $sql->where('a.type', $type);
        }

        $sql->order($this->getState('ordering'), $this->getState('direction'));

        $sql->leftjoin('#__social_clusters_categories', 'b');
        $sql->on('a.category_id', 'b.id');

        $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

        $this->setTotal($sql->getTotalSql());

        $result = $this->getDataColumn($sql->getSql());

        if (empty($result)) {
            return array();
        }

        // Result is an array of ids, we directly use this instead of looping through the result to bind to SocialEvent object since FD::event() is array-ids-ready
        $events = FD::event($result);

        return $events;
    }

    /**
     * Returns array of SocialEvent object for frontend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array    $options    Array of options.
     * @return array                Array of SocialEvent object.
     */
    public function getEvents($options = array())
    {
        $db = FD::db();

        $q = array();

        if (!empty($options['location'])) {
            // If this is a location based search, then we want to include distance column
            $searchUnit = strtoupper(FD::config()->get('general.location.proximity.unit','mile'));

            $unit = constant('SOCIAL_LOCATION_UNIT_' . $searchUnit);
            $radius = constant('SOCIAL_LOCATION_RADIUS_' . $searchUnit);

            $lat = $options['latitude'];
            $lng = $options['longitude'];

            // ($radius * acos(cos(radians($lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians($lng)) + sin(radians($lat)) * sin(radians(latitude)))) as distance

            // If there is a distance provided, then we need to put the distance column into a subquery in order to filter condition on it
            if (!empty($options['distance'])) {
                $distance = $options['distance'];

                $lat1 = $lat - ($distance / $unit);
                $lat2 = $lat + ($distance / $unit);

                $lng1 = $lng - ($distance / abs(cos(deg2rad($lat)) * $unit));
                $lng2 = $lng + ($distance / abs(cos(deg2rad($lat)) * $unit));

                $q[] = "SELECT `a`.`id`, `a`.`distance` FROM (
                    SELECT `x`.*, ($radius * acos(cos(radians($lat)) * cos(radians(`x`.`latitude`)) * cos(radians(`x`.`longitude`) - radians($lng)) + sin(radians($lat)) * sin(radians(`x`.`latitude`)))) AS `distance` FROM `#__social_clusters` AS `x` WHERE `x`.`cluster_type` = " . $db->q(SOCIAL_TYPE_EVENT) . " AND (cast(`x`.`latitude` AS DECIMAL(10, 6)) BETWEEN $lat1 AND $lat2) AND (cast(`x`.`longitude` AS DECIMAL(10, 6)) BETWEEN $lng1 AND $lng2)
                ) AS `a`";
            } else {
                $q[] = "SELECT DISTINCT `a`.`id`, ($radius * acos(cos(radians($lat)) * cos(radians(`a`.`latitude`)) * cos(radians(`a`.`longitude`) - radians($lng)) + sin(radians($lat)) * sin(radians(`a`.`latitude`)))) AS `distance` FROM `#__social_clusters` AS `a`";
            }
        } else {
            $q[] = "SELECT DISTINCT `a`.`id` AS `id` FROM `#__social_clusters` AS `a`";
        }

        $q[] = "LEFT JOIN `#__social_events_meta` AS `b` ON `a`.`id` = `b`.`cluster_id`";

        if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
            $q[] = "LEFT JOIN `#__social_block_users` AS `bus`";
            $q[] = "ON `a`.`creator_uid` = `bus`.`user_id`";
            $q[] = "AND `bus`.`target_id` = '" . JFactory::getUser()->id . "'";
            $q[] = "AND `bus`.`id` IS NULL";
        }

        if (isset($options['type']) && $options['type'] === 'user') {
            $q[] = "LEFT JOIN `#__social_clusters_nodes` AS `nodes`";
            $q[] = "ON `a`.`id` = `nodes`.`cluster_id`";
        }

        if (isset($options['guestuid'])) {
            $q[] = "LEFT JOIN `#__social_clusters_nodes` AS `c`";
            $q[] = "ON `a`.`id` = `c`.`cluster_id`";
        }

        $q[] = "WHERE `a`.`cluster_type` = " . $db->q(SOCIAL_TYPE_EVENT);

        // Filter by event type
        if (isset($options['type']) && $options['type'] !== 'all') {

            // We do this is because other than getting Open and Closed, we also need to get Invite but only if it is participated by the user
            if ($options['type'] === 'user') {
                $userid = isset($options['userid']) ? $options['userid'] : FD::user()->id;

                $q[] = "AND (`a`.`type` IN (" . implode(',', $db->q(array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE))) . ")";
                $q[] = "OR (`a`.`type` = " . $db->q(SOCIAL_EVENT_TYPE_INVITE);
                $q[] = "AND `nodes`.`uid` = " . $db->q($userid) . "))";
            } else {

                if (is_array($options['type'])) {
                    if (count($options['type']) === 1) {
                        $q[] = "AND `a`.`type` = " . $db->q($options['type'][0]);
                    } else {
                        $q[] = "AND `a`.`type` IN (" . implode(',', $db->q($options['type'])) . ")";
                    }
                } else {
                    $q[] = "AND `a`.`type` = " . $db->q($options['type']);
                }
            }
        }

        // Filter by category id
        if (isset($options['category']) && $options['category'] !== 'all') {
            $q[] = "AND `a`.`category_id` = " . $db->q($options['category']);
        }

        // Filter by featured
        if (isset($options['featured']) && $options['featured'] !== 'all') {
            $q[] = "AND `a`.`featured` = " . $db->q((int) $options['featured']);
        }

        // Inclusion
        if (isset($options['inclusion']) && !empty($options['inclusion'])) {
            $includeEvent = array();
            $inclusions = $options['inclusion'];

            foreach ($inclusions as $inclusion) {
                if ($inclusion !== '') {
                    $includeEvent[] = $inclusion;
                }
            }

            if (!empty($includeEvent)) {
                $q[] = "AND `a`.`id` IN (" . implode(',', $db->q($includeEvent)) . ")";
            }
        }

        // Filter by creator
        if (isset($options['creator_uid'])) {
            $q[] = "AND `a`.`creator_uid` = " . $db->q($options['creator_uid']);
            $q[] = "AND `a`.`creator_type` = " . $db->q(isset($options['creator_type']) ? $options['creator_type'] : SOCIAL_TYPE_USER);
        }

        // Filter by state
        if (isset($options['state'])) {
            $q[] = "AND `a`.`state` = " . $db->q($options['state']);
        }

        // Filter by guest state
        if (isset($options['guestuid'])) {
            $q[] = "AND `c`.`uid` = " . $db->q($options['guestuid']);

            if (isset($options['gueststate']) && $options['gueststate'] !== 'all') {
                $q[] = "AND `c`.`state` = " . $db->q($options['gueststate']);
            }
        }

        // Time filter
        // Filter by past, ongoing, or upcoming
        $now = FD::date()->toSql(true);
        if (!empty($options['past'])) {
            $q[] = "AND (";

            $q[] = "(`b`.`end` != '0000-00-00 00:00:00' AND `b`.`end` < " . $db->q($now) . ")";

            $q[] = "OR (`b`.`end` = '0000-00-00 00:00:00' AND `b`.`start` < " . $db->q($now) . ")";

            $q[] = ")";
        }
        if (!empty($options['ongoing']) && empty($options['upcoming'])) {
            // No need to check for end != 0000-00-00 00:00:00 because $now is ALWAYS > 0000-00-00 00:00:00, and b.end >= now will never get 0000-00-00 00:00:00

            $q[] = "AND `b`.`start` <= " . $db->q($now);
            $q[] = "AND `b`.`end` >= " . $db->q($now);
        }
        if (!empty($options['upcoming']) && empty($options['ongoing'])) {
            $q[] = "AND `b`.`start` >= " . $db->q($now);
        }
        if (!empty($options['ongoing']) && !empty($options['upcoming'])) {
            // Upcoming
            $q[] = "AND (`b`.`start` >= " . $db->q($now);

            // Ongoing
            $q[] = "OR (`b`.`start` <= " . $db->q($now);
            $q[] = "AND `b`.`end` >= " . $db->q($now) . "))";
        }

        // Manual filter by start and end range
        if (!empty($options['start-before'])) {
            $q[] = "AND `b`.`start` <= " . $db->q($options['start-before']);
        }
        if (!empty($options['start-after'])) {
            $q[] = "AND `b`.`start` >= " . $db->q($options['start-after']);
        }
        if (!empty($options['end-before'])) {
            $q[] = "AND `b`.`end` <= " . $db->q($options['end-before']);
        }
        if (!empty($options['end-after'])) {
            $q[] = "AND `b`.`end` >= " . $db->q($options['end-after']);
        }

        // Nearby filter
        if (!empty($options['location']) && !empty($options['distance'])) {
            $range = isset($options['range']) ? $options['range'] : '<=';
            $q[] = "AND `a`.`distance` $range " . (float) $options['distance'];
        }

        // Group event filter
        if (isset($options['group_id']) && $options['group_id'] !== 'all') {
            $q[] = "AND `b`.`group_id` = " . $db->q($options['group_id']);
        }

        // If no group_id set, then we check against the settings
        // By default we do not want group event in listing
        // If settings state to NOT include group events, then we have to filter by group_id = 0
        if (!isset($options['group_id']) && !FD::config()->get('events.listing.includegroup', false)) {
            $q[] = "AND `b`.`group_id` = " . $db->q(0);
        }

        // Recurring event filter
        if (isset($options['parent_id'])) {
            $q[] = "AND `a`.`parent_id` = " . $db->q($options['parent_id']);
        }

        // Conditions ends here
        // We set the total here first before going into order and limit block
        $sql = $db->sql();
        $sql->raw(implode(' ', $q));
        $this->setTotal($sql->getSql(), true);

        // Ordering
        if (isset($options['ordering'])) {
            $direction = isset($options['direction']) ? $options['direction'] : 'asc';

            switch ($options['ordering']) {
                case 'created':
                    $q[] = "ORDER BY `a`.`created` $direction";
                break;

                default:
                case 'start':
                    $q[] = "ORDER BY `b`.`start` $direction";
                break;

                case 'end':
                    $q[] = "ORDER BY `b`.`end` $direction";
                break;

                case 'distance':
                    $q[] = "ORDER BY `a`.`distance` $direction";
                break;
            }
        }

        // Limit
        if (isset($options['limit'])) {
            $limit = $options['limit'];
            $limitstart = isset($options['limitstart']) ? $options['limitstart'] : JRequest::getInt('limitstart', 0);

            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $this->setState('limit', $limit);
            $this->setState('limitstart', $limitstart);

            $q[] = "LIMIT $limitstart, $limit";
        }

        $query = implode(' ', $q);

        $sql = $db->sql();
        $sql->raw($query);

        $db->setQuery($sql);

        $result = $db->loadObjectList('id');

        if (empty($result)) {
            return array();
        }

        $ids = array_keys($result);

        // Support for lightweight mode where we only want the ids
        if (isset($options['idonly']) && $options['idonly'] === true) {
            return $ids;
        }

        // FD::event() is array-ids-ready
        $events = FD::event($ids);

        // Manually assign the distance data
        if (!empty($options['location'])) {
            foreach ($events as $event) {
                $event->distance = round($result[$event->id]->distance, 1);
            }
        }

        return $events;
    }

    /**
     * Returns total number of event based on options filtering for frontend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array     $options Options to filter.
     * @return integer            Total number of event.
     */
    public function getTotalEvents($options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters', 'a');
        $sql->column('a.id', 'id', 'count distinct');

        $sql->leftjoin('#__social_events_meta', 'b');
        $sql->on('a.id', 'b.cluster_id');

        if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
            $sql->leftjoin( '#__social_block_users' , 'bus');
            $sql->on( 'a.creator_uid' , 'bus.user_id' );
            $sql->on( 'bus.target_id', JFactory::getUser()->id );
            $sql->isnull('bus.id');
        }

        $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

        // Filter by event type
        if (isset($options['type']) && $options['type'] !== 'all') {

            // We do this is because other than getting Open and Closed, we also need to get Invite but only if it is participated by the user

            if ($options['type'] === 'user') {
                $userid = isset($options['userid']) ? $options['userid'] : FD::user()->id;

                $sql->leftjoin('#__social_clusters_nodes', 'nodes');
                $sql->on('a.id', 'nodes.cluster_id');

                $sql->where('(');
                $sql->where('a.type', array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE), 'IN');
                $sql->where('(', '', '', 'or');
                $sql->where('a.type', SOCIAL_EVENT_TYPE_INVITE);
                $sql->where('nodes.uid', $userid);
                $sql->where(')');
                $sql->where(')');
            } else {
                if (is_array($options['type'])) {
                    if (count($options['type']) === 1) {
                        $sql->where('a.type', $options['type'][0]);
                    } else {
                        $sql->where('a.type', $options['type'], 'IN');
                    }
                } else {
                    $sql->where('a.type', $options['type']);
                }
            }
        }

        // Filter by category id
        if (isset($options['category']) && $options['category'] !== 'all') {
            $sql->where('a.category_id', $options['category']);
        }

        // Filter by featured
        if (isset($options['featured'])) {
            $sql->where('a.featured', (int) $options['featured']);
        }

        // Filter by creator
        if (isset($options['creator_uid'])) {
            $sql->where('a.creator_uid', $options['creator_uid']);

            $sql->where('a.creator_type', isset($options['creator_type']) ? $options['creator_type'] : SOCIAL_TYPE_USER);
        }

        // Filter by state
        if (isset($options['state'])) {
            $sql->where('a.state', $options['state']);
        }

        // Filter by guest state
        if (isset($options['guestuid'])) {
            $sql->leftjoin('#__social_clusters_nodes', 'c');
            $sql->on('a.id', 'c.cluster_id');

            $sql->where('c.uid', $options['guestuid']);

            if (isset($options['gueststate']) && $options['gueststate'] !== 'all') {
                $sql->where('c.state', $options['gueststate']);
            }
        }

        // Time filter
        // Filter by past, ongoing, or upcoming
        $now = FD::date()->toSql(true);
        if (!empty($options['past'])) {
            $sql->where('(');

            $sql->where('(');
            $sql->where('b.end', '0000-00-00 00:00:00', '!=');
            $sql->where('b.end', $now, '<');
            $sql->where(')');

            $sql->where('(', '', '', 'OR');
            $sql->where('b.end', '0000-00-00 00:00:00', '=');
            $sql->where('b.start', $now, '<');
            $sql->where(')');


            $sql->where(')');
        }
        if (!empty($options['ongoing']) && empty($options['upcoming'])) {
            $sql->where('b.start', $now, '<=');
            $sql->where('b.end', $now, '>=');
        }
        if (!empty($options['upcoming']) && empty($options['ongoing'])) {
            $sql->where('b.start', $now, '>=');
        }
        if (!empty($options['ongoing']) && !empty($options['upcoming'])) {
            // Upcoming
            $sql->where('(');
            $sql->where('b.start', $now, '>=');

            // Ongoing
            $sql->where('(', '', '', 'OR');
            $sql->where('b.start', $now, '<=');
            $sql->where('b.end', $now, '>=');
            $sql->where(')');
            $sql->where(')');
        }

        // Manual filter by start and end range
        if (!empty($options['start-before'])) {
            $sql->where('b.start', $options['start-before'], '<=');
        }
        if (!empty($options['start-after'])) {
            $sql->where('b.start', $options['start-after'], '>=');
        }
        if (!empty($options['end-before'])) {
            $sql->where('b.end', $options['end-before'], '<=');
        }
        if (!empty($options['end-after'])) {
            $sql->where('b.end', $options['end-after'], '>=');
        }

        // Group event filter
        // If no group_id set, then we check against the settings
        // By default we do not want group event in listing
        // If settings state to NOT include group events, then we have to filter by group_id = 0
        if (!isset($options['group_id']) && !FD::config()->get('events.listing.includegroup', false)) {
            $sql->where('b.group_id', 0);
        }
        // If there is group id specified, then we filter by group id
        if (isset($options['group_id']) && $options['group_id'] !== 'all') {
            $sql->where('b.group_id', $options['group_id']);
        }

        // Recurring event filter
        if (isset($options['parent_id'])) {
            $sql->where('a.parent_id', $options['parent_id']);
        }

        $db->setQuery($sql);

        $result = $db->loadResult();

        return (int) $result;
    }

    /**
     * Returns the total pending events for backend.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return integer    Number of pending events.
     */
    public function getPendingCount()
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters');
        $sql->where('cluster_type', SOCIAL_TYPE_EVENT);
        $sql->where('state', SOCIAL_CLUSTER_PENDING);

        $db->setQuery($sql->getTotalSql());

        $result = $db->loadResult();

        return (int) $result;
    }

    /**
     * Main function that initiates the required event's meta data.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array     $ids The event ids to load.
     * @return array          Array of event meta datas.
     */
    public function getMeta($ids = array())
    {
        static $loaded = array();

        $loadItems = array();

        foreach ($ids as $id) {
            $id = (int) $id;

            if (!isset($loaded[$id])) {
                $loadItems[] = $id;

                $loaded[$id] = false;
            }
        }

        if (!empty($loadItems)) {
            $db = FD::db();
            $sql = $db->sql();

            $sql->select('#__social_clusters', 'a');
            $sql->column('a.*');
            $sql->column('b.small');
            $sql->column('b.medium');
            $sql->column('b.large');
            $sql->column('b.square');
            $sql->column('b.avatar_id');
            $sql->column('b.photo_id');
            $sql->column('b.storage', 'avatarStorage');
            $sql->column('c.id', 'cover_id');
            $sql->column('c.uid', 'cover_uid');
            $sql->column('c.type', 'cover_type');
            $sql->column('c.photo_id', 'cover_photo_id');
            $sql->column('c.cover_id', 'cover_cover_id');
            $sql->column('c.x', 'cover_x');
            $sql->column('c.y', 'cover_y');
            $sql->column('c.modified', 'cover_modified');
            $sql->leftjoin('#__social_avatars', 'b');
            $sql->on('b.uid', 'a.id');
            $sql->on('b.type', 'a.cluster_type');
            $sql->leftjoin('#__social_covers', 'c');
            $sql->on('c.uid', 'a.id');
            $sql->on('c.type', 'a.cluster_type');

            if (count($loadItems) > 1) {
                $sql->where('a.id', $loadItems, 'IN');
            } else {
                $sql->where('a.id', $loadItems[0]);
            }

            $sql->where('a.cluster_type', SOCIAL_TYPE_EVENT);

            $db->setQuery($sql);

            $events = $db->loadObjectList('id');

            // Use array_replace instead of array_merge because the key of the array is integer, and array_merge won't replace if the key is integer.
            // array_replace is only supported php>5.3

            // $loaded = array_replace($loaded, $events);

            // While array_replace goes by base, replacement
            // Using + changes the order where base always goes last
            $loaded = $events + $loaded;
        }

        $data = array();

        foreach ($ids as $id) {
            $data[] = $loaded[$id];
        }

        return $data;
    }

    /**
     * Retrieves the total number of event guests from a particular event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalAttendees($id)
    {
        $db  = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('COUNT(1)');

        // exclude esad users
        $sql->innerjoin('#__social_profiles_maps', 'upm');
        $sql->on('a.uid', 'upm.user_id');

        $sql->innerjoin('#__social_profiles', 'up');
        $sql->on('upm.profile_id', 'up.id');
        $sql->on('up.community_access', '1');

        $sql->where('a.cluster_id', $id);
        $sql->where('a.state', SOCIAL_EVENT_GUEST_GOING);

        $db->setQuery($sql);
        $total = $db->loadResult();

        return $total;
    }

    /**
     * Alias method of getGuests to ensure compatibility with Groups model.
     *
     * @since   1.3
     * @access  public
     * @param  integer  $id         The event id.
     * @param  array    $options    Options to filter.
     * @return array                Array of SocialTableEventGuest objects.
     */
    public function getMembers($id, $options = array())
    {
        return $this->getGuests($id, $options);
    }

    /**
     * Retrieves a list of event guests from a particular event.
     *
     * @since   1.3
     * @access  public
     * @param  integer  $id         The event id.
     * @param  array    $options    Options to filter.
     * @return array                Array of SocialTableEventGuest objects.
     */
    public function getGuests($id, $options = array())
    {
        static $cache = array();

        ksort($options);

        $optionskey = serialize($options);

        if (!isset($cache[$id][$optionskey])) {
            $db = FD::db();
            $sql = $db->sql();

            $sql->select('#__social_clusters_nodes', 'a');
            $sql->column('a.*');

            if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
                $sql->leftjoin( '#__social_block_users' , 'bus');
                $sql->on( 'a.uid' , 'bus.user_id' );
                $sql->on( 'bus.target_id', JFactory::getUser()->id );
                $sql->isnull('bus.id');
            }

            // We should not fetch banned users
            $sql->innerjoin('#__users', 'u');
            $sql->on('a.uid', 'u.id');

            // exclude esad users
            $sql->innerjoin('#__social_profiles_maps', 'upm');
            $sql->on('u.id', 'upm.user_id');

            $sql->innerjoin('#__social_profiles', 'up');
            $sql->on('upm.profile_id', 'up.id');
            $sql->on('up.community_access', '1');

            $sql->where('a.cluster_id', $id);

            // When the user isn't blocked
            $sql->where('u.block', 0);

            if (isset($options['state'])) {
                $sql->where('a.state', $options['state']);
            }

            if (isset($options['admin'])) {
                $sql->where('a.admin', $options['admin']);
            }

            if (isset($options['exclude'])) {
                $exclude = $options['exclude'];

                if (is_array($exclude)) {
                    if (count($exlude) > 1) {
                        $sql->where('a.uid', $exclude, 'NOT IN');
                    } else {
                        $sql->where('a.uid', $exclude[0], '<>');
                    }
                } else {
                    $sql->where('a.uid', $exclude, '<>');
                }
            }

            if (isset($options['ordering'])) {
                $direction = isset($options['direction']) ? $options['direction'] : 'asc';

                $sql->order($options['ordering'], $direction);
            }

            if (isset($options['limit'])) {
                $limitstart = isset($options['limitstart']) ? $options['limitstart'] : 0;

                $sql->limit($limitstart, $options['limit']);
            }

            // echo $sql;

            $db->setQuery($sql);

            $result = $db->loadObjectList();

            $cache[$id][$optionskey] = $result;
        }

        if (!empty($options['users'])) {
            $users = array();

            foreach ($cache[$id][$optionskey] as $row) {
                $user = FD::user($row->uid);

                $users[] = $user;
            }
        } else {
            $users = $this->bindTable('EventGuest', $cache[$id][$optionskey]);
        }

        return $users;
    }

    /**
     * Generates a unique alias for the group
     *
     * @since   1.3
     * @access  public
     * @param   string  $title      The title of the group.
     * @param   int     $exclude    The integer of the cluster to exclude from checking.
     * @return  string              The generated alias.
     */
    public function getUniqueAlias($title, $exclude = null)
    {
        // Pass this back to Joomla to ensure that the permalink would be safe.
        $alias = JFilterOutput::stringURLSafe($title);

        $model = FD::model('Clusters');

        $i = 2;

        // Set this to a temporary alias
        $tmp = $alias;

        do {
            $exists = $model->clusterAliasExists($alias, $exclude, SOCIAL_TYPE_EVENT);

            if ($exists) {
                $alias  = $tmp . '-' . $i++;
            }

        } while ($exists);

        return $alias;
    }

    /**
     * Creates a new event based on the session.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  SocialTableStepSession $session The step session.
     * @return SocialEvent                     The SocialEvent object.
     */
    public function createEvent(SocialTableStepSession $session)
    {
        FD::import('admin:/includes/event/event');

        $event = new SocialEvent();
        $event->creator_uid = FD::user()->id;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $session->uid;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->created = FD::date()->toSql();

        $event->key = md5(JFactory::getDate()->toSql() . FD::user()->password . uniqid());

        $params = FD::registry($session->values);

        // Support for group event
        if ($params->exists('group_id')) {
            $group = FD::group($params->get('group_id'));

            $event->setMeta('group_id', $group->id);
        }

        $data = $params->toArray();

        // Recurring support
        // Check if there is a recurring flag in the $session->values;
        // If there is then we punch in $event->parent_id and $event->parent_type
        if (isset($data['parent_id'])) {
            $event->parent_id = $data['parent_id'];
            $event->parent_type = SOCIAL_TYPE_EVENT;
        }

        $customFields = FD::model('Fields')->getCustomFields(array('visible' => SOCIAL_EVENT_VIEW_REGISTRATION, 'group' => SOCIAL_TYPE_EVENT, 'uid' => $session->uid));

        $fieldsLib = FD::fields();

        $args = array(&$data, &$event);

        $callback = array($fieldsLib->getHandler(), 'beforeSave');

        $errors = $fieldsLib->trigger('onRegisterBeforeSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args, $callback);

        if (!empty($errors)) {
            $this->setError($errors);
            return false;
        }

        // Get the current user.
        $my = FD::user();

        $event->state = SOCIAL_CLUSTER_PENDING;

        // If the event is created by site admin or user doesn't need to be moderated, publish event immediately.
        if ($my->isSiteAdmin() || !$my->getAccess()->get('events.moderate')) {
            $event->state = SOCIAL_CLUSTER_PUBLISHED;
        }

        // Trigger apps
        FD::apps()->load(SOCIAL_TYPE_USER);

        $dispatcher  = FD::dispatcher();
        $triggerArgs = array(&$event, &$my, true);

        // @trigger: onEventBeforeSave
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventBeforeSave', $triggerArgs);

        $state = $event->save();

        if (!$state) {
            $this->setError($event->getError());
            return false;
        }

        // Notifies admin when a new event is created
        if ($event->state === SOCIAL_CLUSTER_PENDING || !$my->isSiteAdmin()) {
            $this->notifyAdmins($event);
        }

        // Recreate the event object
        SocialEvent::$instances[$event->id] = null;
        $event = FD::event($event->id);

        // Create a new owner object
        $event->createOwner($my->id);

        // Support for group event
        if ($event->isGroupEvent()) {
            // Check for transfer flag to insert group member as event guest
            $transferMode = isset($data['member_transfer']) ? $data['member_transfer'] : 'invite';

            if (!empty($transferMode) && $transferMode != 'none') {

                $nodeState = SOCIAL_EVENT_GUEST_INVITED;

                if ($transferMode == 'attend') {
                    $nodeState = SOCIAL_EVENT_GUEST_GOING;
                }

                /*

                insert into jos_social_clusters_nodes (cluster_id, uid, type, created, state, owner, admin, invited_by)
                select $eventId as cluster_id, uid, type, $now as created, $nodeState as state, 0 as owner, admin, $userId as invited_by from jos_social_clusters_nodes
                where cluster_id = $groupId
                and state = 1
                and type = 'user'
                and uid not in (select uid from jos_social_clusters_nodes where cluster_id = $eventId and type = 'user')

                */

                $eventId = $event->id;
                $groupId = $event->getMeta('group_id');
                $userId = $my->id;
                $now = FD::date()->toSql();

                //check this event is it create from group
                $reg = FD::registry();
                $reg->load($session->values);

                $groupId = $reg->get('group_id');

                if ($transferMode == 'invite') {

                    if (!empty($groupId)) {

                        // Notify invited or going users
                        $model = FD::model('Groups');
                        $options = array('exclude' => $my->id, 'state' => SOCIAL_GROUPS_MEMBER_PUBLISHED);
                        $targets = $model->getMembers($groupId , $options);
                        
                        // If the permalink alias is empty, we need to get default alias
                        if (empty($event->alias)) {
                            $event->alias = $this->getUniqueAlias($event->getName());

                            $event->save();
                        }

                        if (!empty($targets)) {
                            $emailOptions = (object) array(
                                'title' => 'COM_EASYSOCIAL_EMAILS_EVENT_GUEST_INVITED_SUBJECT',
                                'template' => 'site/event/guest.invited',
                                'event' => $event->getName(),
                                'eventName' => $event->getName(),
                                'eventAvatar' => $event->getAvatar(),
                                'eventLink' => $event->getPermalink(false, true),
                                'invitorName' => $my->getName(),
                                'invitorLink' => $my->getPermalink(false, true),
                                'invitorAvatar' => $my->getAvatar()
                            );

                            $systemOptions = (object) array(
                                'uid' => $event->id,
                                'actor_id' => $my->id,
                                'target_id' => $event->id,
                                'context_type' => 'events',
                                'type' => 'events',
                                'url' => $event->getPermalink(true, false, 'item', false),
                                'eventId' => $event->id
                            );

                            FD::notify('events.guest.invited', $targets, $emailOptions, $systemOptions);
                        }
                    }
                }

                $db = FD::db();
                $sql = $db->sql();

                $query = "INSERT INTO `#__social_clusters_nodes` (`cluster_id`, `uid`, `type`, `created`, `state`, `owner`, `admin`, `invited_by`)";
                $query .= " SELECT '$eventId' AS `cluster_id`, a.`uid`, `type`, '$now' AS `created`, '$nodeState' AS `state`, '0' AS `owner`, a.`admin`, '$userId' AS `invited_by`";
                $query .= " FROM `#__social_clusters_nodes` as a";

                //exclude esad users
                $query .= " INNER JOIN `#__social_profiles_maps` as upm on a.`uid` = upm.`user_id`";
                $query .= " INNER JOIN `#__social_profiles` as up on upm.`profile_id` = up.`id` and up.`community_access` = 1";

                $query .= " WHERE a.`cluster_id` = '$groupId' AND a.`state` = " . $db->Quote(SOCIAL_GROUPS_MEMBER_PUBLISHED);
                $query .= " AND a.`type` = " . $db->Quote(SOCIAL_TYPE_USER);
                $query .= " AND a.`uid` NOT IN (SELECT b.`uid` FROM `#__social_clusters_nodes` as b WHERE b.`cluster_id` = '$eventId' AND b.`type` = '" . SOCIAL_TYPE_USER . "')";

                $sql->raw($query);
                $db->setQuery($sql);
                $db->query();
            }
        }

        // Trigger the fields again
        $args = array(&$data, &$event);

        $fieldsLib->trigger('onRegisterAfterSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        $event->bindCustomFields($data);

        $fieldsLib->trigger('onRegisterAfterSaveFields', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        if (empty($event->alias)) {
            $event->alias = $this->getUniqueAlias($event->getName());

            $event->save();
        }

        // @trigger: onEventAfterSave
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        return $event;
    }

    /**
     * Notifies administrator when a new event is created.
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function notifyAdmins($event)
    {
        $params = array(
            'title' => $event->getName(),
            'creatorName' => $event->getCreator()->getName(),
            'creatorLink' => $event->getCreator()->getPermalink(false, true),
            'categoryTitle' => $event->getCategory()->get('title'),
            'avatar' => $event->getAvatar(SOCIAL_AVATAR_LARGE),
            'permalink' => JURI::root() . 'administrator/index.php?option=com_easysocial&view=events&layout=pending',
            'alerts' => false
        );

        $title = JText::sprintf('COM_EASYSOCIAL_EMAILS_MODERATE_EVENT_CREATED_TITLE', $event->getName());

        $template = 'site/event/created';

        if ($event->state === SOCIAL_CLUSTER_PENDING) {
            $params['reject'] = FRoute::controller('events', array('external' => true, 'task' => 'rejectEvent', 'id' => $event->id, 'key' => $event->key));
            $params['approve'] = FRoute::controller('events', array('external' => true, 'task' => 'approveEvent', 'id' => $event->id, 'key' => $event->key));

            $template = 'site/event/moderate';
        }

        $admins = FD::model('Users')->getSiteAdmins();

        foreach ($admins as $admin) {
            if (!$admin->sendEmail) {
                continue;
            }

            $mailer = FD::mailer();

            $params['adminName'] = $admin->getName();

            // Get the email template.
            $mailTemplate = $mailer->getTemplate();

            // Set recipient
            $mailTemplate->setRecipient($admin->getName(), $admin->email);

            // Set title
            $mailTemplate->setTitle($title);

            // Set the template
            $mailTemplate->setTemplate($template, $params);

            // Set the priority. We need it to be sent out immediately since this is user registrations.
            $mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

            // Try to send out email to the admin now.
            $state = $mailer->create($mailTemplate);
        }
    }

    public function getFilters($eventId, $userId = null)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_stream_filter');
        $sql->where('uid', $eventId);
        $sql->where('utype', SOCIAL_TYPE_EVENT);

        if (!empty($userId)) {
            $sql->where('user_id', $userId);
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        $filters = $this->bindTable('StreamFilter', $result);

        return $filters;
    }

    public function getFriendsInEvent($eventId, $options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $userId = isset($options['userId']) ? $options['userId'] : FD::user()->id;

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('a.uid', 'uid', 'distinct');
        $sql->innerjoin('#__social_friends', 'b');
        $sql->on('(');
        $sql->on('(');
        $sql->on('a.uid', 'b.actor_id');
        $sql->on('b.target_id', $userId);
        $sql->on(')');
        $sql->on('(', '', '', 'OR');
        $sql->on('a.uid', 'b.target_id');
        $sql->on('b.actor_id', $userId);
        $sql->on(')');
        $sql->on(')');
        $sql->on('b.state', SOCIAL_STATE_PUBLISHED);

        // exclude esad users
        $sql->innerjoin('#__social_profiles_maps', 'upm');
        $sql->on('a.uid', 'upm.user_id');

        $sql->innerjoin('#__social_profiles', 'up');
        $sql->on('upm.profile_id', 'up.id');
        $sql->on('up.community_access', '1');

        $sql->where('a.cluster_id', $eventId);

        if (isset($options['published'])) {
            $sql->where('a.state', $options['published']);
        }

        $db->setQuery($sql);
        $result = $db->loadColumn();

        $users = array();

        foreach ($result as $id) {
            $users[] = FD::user($id);
        }

        return $users;
    }

    public function getOnlineGuests($eventId)
    {
        $db = FD::db();
        $sql = $db->sql();

        // Get the session life time so we can know who is really online.
        $lifespan = FD::jConfig()->getValue('lifetime');
        $online = time() - ($lifespan * 60);

        $sql->select('#__session', 'a');
        $sql->column('b.id');
        $sql->innerjoin('#__users', 'b');
        $sql->on('a.userid', 'b.id');
        $sql->innerjoin('#__social_clusters_nodes', 'c');
        $sql->on('c.uid', 'b.id');
        $sql->on('c.type', SOCIAL_TYPE_USER);

        // exclude esad users
        $sql->innerjoin('#__social_profiles_maps', 'upm');
        $sql->on('c.uid', 'upm.user_id');

        $sql->innerjoin('#__social_profiles', 'up');
        $sql->on('upm.profile_id', 'up.id');
        $sql->on('up.community_access', '1');

        $sql->where('a.time', $online, '>=');
        $sql->where('b.block', 0);
        $sql->where('c.cluster_id', $eventId);
        $sql->group('a.userid');

        $db->setQuery($sql);

        $result = $db->loadColumn();

        if (!$result) {
            return array();
        }

        $users = FD::user($result);

        return $users;
    }

    /**
     * Retrieves a list of news item from a particular event
     *
     * @since   1.3
     * @access  public
     */
    public function getNews($eventId, $options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_news', 'a');
        $sql->where('a.cluster_id', $eventId);

        // If we should exclude specific items
        $exclude = isset($options['exclude']) ? $options['exclude'] : '';

        if ($exclude) {
            $sql->where('a.id', $exclude, 'NOT IN');
        }

        $sql->order('created', 'DESC');

        $limit = isset($options['limit']) ? $options['limit'] : '';

        if ($limit) {
            $this->setState('limit', $limit);

            // Get the limitstart.
            $limitstart = $this->getUserStateFromRequest('limitstart', 0);
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $this->setState('limitstart', $limitstart);

            // Run pagination here.
            $this->setTotal($sql->getTotalSql());

            $result = $this->getData($sql->getSql());
        } else {
            $db->setQuery($sql);
            $result = $db->loadObjectList();
        }

        $result = $db->loadObjectList();

        $news = $this->bindTable('EventNews', $result);

        return $news;
    }

    /**
     * Deletes all the child events.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer  $parentId   The event parent id to delete.
     * @return boolean              True if successful.
     */
    public function deleteRecurringEvents($parentId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters');
        $sql->column('id');
        $sql->where('cluster_type', SOCIAL_TYPE_EVENT);
        $sql->where('parent_id', $parentId);

        $db->setQuery($sql);

        $result = $db->loadColumn();

        $ids = array();

        foreach ($result as $id) {
            $ids[] = $db->quote($id);
        }

        if (empty($ids)) {
            return true;
        }

        $ids = implode(',', $ids);

        $sql->clear();

        // Delete stream items
        $query = "DELETE `a`, `b` FROM `#__social_stream_item` AS `a` INNER JOIN `#__social_stream` AS `b` ON `a`.`uid` = `b`.`id` WHERE `b`.`cluster_id` IN ($ids)";

        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete notification items
        $query = "DELETE FROM `#__social_notifications` WHERE (`uid` IN ($ids) AND `type` = 'event') OR (type = 'event' AND `context_ids` IN ($ids))";
        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete avatar
        $query = "DELETE FROM `#__social_avatars` WHERE `uid` IN ($ids) AND `type` = 'event'";
        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete cover
        $query = "DELETE FROM `#__social_covers` WHERE `uid` IN ($ids) AND `type` = 'event'";
        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete albums
        $query = "DELETE FROM `#__social_albums` WHERE `uid` IN ($ids) AND `type` = 'event'";
        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete photos, metas, and tags
        $query = "DELETE `a`, `b`, `c` FROM `#__social_photos` AS `a`";
        $query .= " LEFT JOIN `#__social_photos_meta` AS `b` ON `a`.`id` = `b`.`photo_id`";
        $query .= " LEFT JOIN `#__social_photos_tag` AS `c` ON `a`.`id` = `c`.`photo_id`";
        $query .= " WHERE `a`.`type` = 'event' AND `a`.`uid` IN ($ids)";

        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        $sql->clear();

        // Delete event item
        // Delete meta
        // Delete nodes
        // Delete news items
        $query = "DELETE `a`, `b`, `c`, `d` FROM `#__social_clusters` AS `a`";
        $query .= " LEFT JOIN `#__social_clusters_nodes` AS `b` ON `a`.`id` = `b`.`cluster_id`";
        $query .= " LEFT JOIN `#__social_clusters_news` AS `c` ON `a`.`id` = `c`.`cluster_id`";
        $query .= " LEFT JOIN `#__social_events_meta` AS `d` ON `a`.`id` = `d`.`cluster_id`";
        $query .= " WHERE `a`.`parent_id` = $parentId";

        $sql->raw($query);

        $db->setQuery($sql);

        $db->query();

        return true;
    }

    public function duplicateGuests($sourceId, $targetId)
    {
        $now = FD::date()->toSql();

        $db = FD::db();
        $sql = $db->sql();
        $sql->raw("INSERT INTO `#__social_clusters_nodes` (`cluster_id`, `uid`, `type`, `created`, `state`, `owner`, `admin`, `invited_by`) SELECT '$targetId' AS `cluster_id`, `uid`, `type`, '$now' AS `created`, `state`, `owner`, `admin`, `invited_by` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$sourceId' AND `uid` NOT IN (SELECT `uid` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$targetId')");

        $db->setQuery($sql);
        $db->query();
    }

    public function getRecurringSchedule($options = array())
    {
        // Options
        // eventStart = SocialDate
        // end = string
        // type = string
        // daily = array

        $eventStart = $options['eventStart'];

        $startUnix = $eventStart->toUnix();

        // Get the recur end
        $recurringEnd = FD::date($options['end'], false);
        // We plus 1 day ahead so that the day of recur end is also considered
        $recurringEndUnix = $recurringEnd->toUnix() + (60*60*24);

        // This stores all the start and end of the recurring events for event creation
        $schedule = array();

        // Based on the type, we calculate and prepare all the schedule

        if ($options['type'] === 'daily') {
            if (empty($options['daily'])) {
                return $schedule;
            }

            $recur = $options['daily'];
            $countRecur = count($recur);

            // Build a recur cycle array
            $cycle = array();

            // Calculate the total interval to move from day to day
            for ($i = 0; $i < $countRecur; $i++) {
                // $j is the next element in the recur array
                $j = $i === ($countRecur - 1) ? 0 : $i + 1;

                $cycle[] = ($recur[$i] < $recur[$j] ? $recur[$j] - $recur[$i] : 7 + $recur[$j] - $recur[$i]) * 60*60*24;
            }

            // Get today as integer
            // 0, 1, 2, 3, 4, 5, 6, with 0 being Sunday
            $startDay = $eventStart->format('w');

            // Calculate the next nearest day from $startDay

            // Get the first recur day
            $first = $recur[0];

            // Get the last recur day
            $last = $recur[$countRecur - 1];

            // Set the next possible day as the first recur day
            $next = $first;

            // Next possible day is always $first unless:
            // $countRecur > 1
            // $startDay >= $first
            // $startDay < $last
            if ($countRecur > 1 && $startDay >= $first && $startDay < $last) {
                // As long as $startDay is < than the recur day, then that recur day is our next possible day
                foreach ($recur as $r) {
                    if ($startDay < $r) {
                        $next = $r;
                        break;
                    }
                }

                // Now that $next is no longer $first, we have to reorganize the cycle array
                $offset = array_search($next, $recur);
                $spliced = array_splice($cycle, $offset);
                $cycle = array_merge($spliced, $cycle);
            }

            // Now that we have the correct next day, we need to get the interval between $startDay and $next
            $intervalToNext = ($startDay < $next ? $next - $startDay : 7 - $startDay + $next) * 60*60*24;

            // Now we shift the startUnix to the next possible day
            $startUnix += $intervalToNext;

            $counter = 0;

            do {
                // Store this data in the schedule
                if ($startUnix < $recurringEndUnix) {
                    $schedule[] = $startUnix;
                }

                // Get the next recur start
                $startUnix += $cycle[$counter % $countRecur];

                $counter++;
            } while ($startUnix < $recurringEndUnix);
        }

        if ($options['type'] === 'weekly') {
            do {
                $startUnix += 60*60*24*7;

                // Store this data in the schedule
                if ($startUnix < $recurringEndUnix) {
                    $schedule[] = $startUnix;
                }
            } while ($startUnix < $recurringEndUnix);
        }

        // If is monthly, this gets a bit tricky
        // Instead of adding the unit, we alter the date by 1 month
        // Then check if the day is valid on that month or not
        // If it is not valid, then fallback to the month's max day
        if ($options['type'] === 'monthly') {
            $year = $eventStart->format('Y');
            $month = $eventStart->format('n');
            $day = $eventStart->format('d');

            $time = $eventStart->format('H:i:s');

            $nextYear = $year;

            $nextMonth = $month;

            $nextDay = $day;

            do {
                $nextMonth += 1;

                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear += 1;
                }

                $maxDay = FD::date($nextYear . '-' . $nextMonth . '-01')->format('t');

                $nextDay = min($day, $maxDay);

                $startUnix = FD::date($nextYear . '-' . $nextMonth . '-' . $nextDay . ' ' . $time)->toUnix();

                // Store this data in the schedule
                if ($startUnix < $recurringEndUnix) {
                    $schedule[] = $startUnix;
                }
            } while ($startUnix < $recurringEndUnix);
        }

        // If it is yearly, we also need to perform a month check due to Feb end date changes
        if ($options['type'] === 'yearly') {
            $year = $eventStart->format('Y');
            $month = $eventStart->format('n');
            $day = $eventStart->format('d');

            $time = $eventStart->format('H:i:s');

            $nextYear = $year;

            $nextDay = $day;

            do {
                $nextYear += 1;

                // We only need to do this check if it is Feb
                if ($month == 2) {
                    $maxDay = FD::date($nextYear . '-' . $month . '-01')->format('t');

                    $nextDay = min($day, $maxDay);
                }

                $startUnix = FD::date($nextYear . '-' . $month . '-' . $nextDay . ' ' . $time)->toUnix();

                // Store this data in the schedule
                if ($startUnix < $recurringEndUnix) {
                    $schedule[] = $startUnix;
                }
            } while ($startUnix < $recurringEndUnix);
        }

        return $schedule;
    }

    /**
     * Creates a new recurring event based on the post data and parent event.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array        $data   The post data.
     * @param  SocialEvent  $parent The parent event object.
     * @return SocialEvent          The SocialEvent object.
     */
    public function createRecurringEvent($data, $parent)
    {
        FD::import('admin:/includes/event/event');

        $event = new SocialEvent();
        $event->creator_uid = $parent->creator_uid;
        $event->creator_type = SOCIAL_TYPE_USER;
        $event->category_id = $parent->category_id;
        $event->cluster_type = SOCIAL_TYPE_EVENT;
        $event->created = FD::date()->toSql();
        $event->parent_id = $parent->id;
        $event->parent_type = SOCIAL_TYPE_EVENT;
        $event->state = SOCIAL_CLUSTER_PUBLISHED;

        $event->key = md5(JFactory::getDate()->toSql() . FD::user()->password . uniqid());

        // Support for group event
        if (isset($data['group_id'])) {
            $group = FD::group($data['group_id']);

            $event->setMeta('group_id', $group->id);
        }

        $customFields = FD::model('Fields')->getCustomFields(array('visible' => SOCIAL_EVENT_VIEW_REGISTRATION, 'group' => SOCIAL_TYPE_EVENT, 'uid' => $parent->category_id));

        $fieldsLib = FD::fields();

        $args = array(&$data, &$event);

        $fieldsLib->trigger('onRegisterBeforeSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args, array($fieldsLib->getHandler(), 'beforeSave'));

        $state = $event->save();

        // Recreate the event object
        SocialEvent::$instances[$event->id] = null;
        $event = FD::event($event->id);

        // Support for group event
        if ($event->isGroupEvent()) {
            // Check for transfer flag to insert group member as event guest
            $transferMode = isset($data['member_transfer']) ? $data['member_transfer'] : 'invite';

            if (!empty($transferMode) && $transferMode != 'none') {
                $nodeState = SOCIAL_EVENT_GUEST_INVITED;

                if ($transferMode == 'attend') {
                    $nodeState = SOCIAL_EVENT_GUEST_GOING;
                }

                /*

                insert into jos_social_clusters_nodes (cluster_id, uid, type, created, state, owner, admin, invited_by)
                select $eventId as cluster_id, uid, type, $now as created, $nodeState as state, 0 as owner, admin, $userId as invited_by from jos_social_clusters_nodes
                where cluster_id = $groupId
                and state = 1
                and type = 'user'
                and uid not in (select uid from jos_social_clusters_nodes where cluster_id = $eventId and type = 'user')

                */

                $eventId = $event->id;
                $groupId = $event->getMeta('group_id');
                $userId = $parent->creator_uid;
                $now = FD::date()->toSql();

                $query = "INSERT INTO `#__social_clusters_nodes` (`cluster_id`, `uid`, `type`, `created`, `state`, `owner`, `admin`, `invited_by`) SELECT '$eventId' AS `cluster_id`, `uid`, `type`, '$now' AS `created`, '$nodeState' AS `state`, '0' AS `owner`, `admin`, '$userId' AS `invited_by` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$groupId' AND `state` = '" . SOCIAL_GROUPS_MEMBER_PUBLISHED . "' AND `type` = '" . SOCIAL_TYPE_USER . "' AND `uid` NOT IN (SELECT `uid` FROM `#__social_clusters_nodes` WHERE `cluster_id` = '$eventId' AND `type` = '" . SOCIAL_TYPE_USER . "') AND `uid` != '$userId'";

                $db = FD::db();
                $sql = $db->sql();
                $sql->raw($query);
                $db->setQuery($sql);
                $db->query();
            }
        }

        // Trigger the fields again
        $args = array(&$data, &$event);

        $fieldsLib->trigger('onRegisterAfterSave', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        $event->bindCustomFields($data);

        $fieldsLib->trigger('onRegisterAfterSaveFields', SOCIAL_FIELDS_GROUP_EVENT, $customFields, $args);

        if (empty($event->alias)) {
            $event->alias = $this->getUniqueAlias($event->getName());

            $event->save();
        }

        $dispatcher  = FD::dispatcher();
        $my = FD::user();

        // @trigger: onEventAfterSave
        // Put the recurring events in the calendar
        $triggerArgs = array(&$event, &$my, true);
        $dispatcher->trigger(SOCIAL_TYPE_USER, 'onEventAfterSave' , $triggerArgs);

        return $event;
    }

    public function getUpcomingReminder()
    {
        $db = ES::db();
        $sql = $db->sql();

        $now    = ES::date();

        $query = "select a.`cluster_id` as `event_id`, a.`start`, a.`end`, a.`all_day`, c.`uid` as `user_id`";
        $query .= ", b.`title`, b.`alias`, b.`description`, b.`address`";
        $query .= ", u.`name` as `user_name`, u.`email` as `user_email`";
        $query .= " from `#__social_events_meta` as a";
        $query .= " inner join `#__social_clusters` as b on a.cluster_id = b.id";
        $query .= " inner join `#__social_clusters_nodes` as c on a.cluster_id = c.cluster_id";
        $query .= " inner join `#__users` as u on c.`uid` = u.`id`";
        $query .= " where b.state = 1";
        $query .= " and c.`type` = 'user'";
        $query .= " and c.`state` = 1";
        $query .= " and c.`reminder_sent` = 0";
        $query .= " and a.`reminder` > 0";
        $query .= " and a.`start_gmt` <= date_add(" . $db->Quote($now->toMySQL()) . ", INTERVAL a.`reminder` DAY)";

        $sql->raw($query);
        $db->setQuery($sql);

        $results = $db->loadObjectList();

        // we need to group the events by users
        $items = array();

        if ($results) {

            $events = array();
            $users = array();

            foreach($results as $item) {

                if (! isset($events[$item->event_id])) {

                    $event = new stdClass();

                    $alias = $item->event_id . ':' . JFilterOutput::stringURLSafe($item->alias);

                    $event->id = $item->event_id;
                    $event->title = $item->title;
                    $event->permalik = $alias;
                    $event->description = $item->description;
                    $event->address = $item->address;
                    $event->start = $item->start;
                    $event->end = $item->end;
                    $event->all_day = $item->all_day;

                    $events[$item->event_id] = $event;
                }

                if (! isset($users[$item->user_id])) {
                    $user = new stdClass();
                    $user->id = $item->user_id;
                    $user->name = $item->user_name;
                    $user->email = $item->user_email;

                    $users[$item->user_id] = $user;
                }

                $items[$item->user_id]['user'] = $users[$item->user_id];
                $items[$item->user_id]['events'][] = $events[$item->event_id];
            }
        }

        return $items;
    }

    public function sendUpcomingReminder($items)
    {
        $count = 0;
        $jConfig    = FD::jConfig();
        $config = FD::config();

        if ($items) {

            // Push arguments to template variables so users can use these arguments
            $params     = array(
                                'siteName'  => $jConfig->getValue('sitename'),
                                'loginLink' => FRoute::events(array() , false)
                            );


            foreach ($items as $data)  {

                $user = $data['user'];
                $events = $data['events'];

                // Set the user's name.
                $params['recipientName']  = $user->name;

                // $params[ 'events' ]  = $events;

                $ids = array();
                foreach($events as $event) {
                    $ids[] = $event->id;
                }

                $theme = ES::themes();
                $theme->set( 'events', $events );
                $eventHtml = $theme->output( 'site/emails/html/event/upcoming.reminder.event' );

                $params['events']  = $eventHtml;
                $params['eventCount'] = count($events);

                $mailer     = FD::mailer();

                // Get the email template.
                $mailTemplate   = $mailer->getTemplate();

                // Set recipient
                $mailTemplate->setRecipient($user->name, $user->email);

                // Set title
                $title = JText::sprintf('COM_EASYSOCIAL_EMAILS_UPCOMING_EVENT_REMINDER_SUBJECT', $user->name);
                $mailTemplate->setTitle($title);

                // Set the template
                $mailTemplate->setTemplate('site/event/upcoming.reminder', $params);

                // Try to send out email to the admin now.
                $state      = $mailer->create($mailTemplate);

                if ($state) {

                    $ids = array();

                    foreach($events as $event) {
                        $ids[] = $event->id;
                    }

                    // need to update the reminder_sent flag
                    $this->updateReminderSentFlag($user->id, $ids, '1');

                    $count++;
                }

            }
        }

        return $count;
    }

    public function updateReminderSentFlag($userId, $eventIds, $flag)
    {
        $db = FD::db();
        $sql = $db->sql();

        $query = 'update `#__social_clusters_nodes` set `reminder_sent` = ' . $db->Quote($flag);
        $query .= ' where `type` = ' . $db->Quote('user');
        $query .= ' and `uid` = ' . $db->Quote($userId);
        if (count($eventIds) > 1) {
            $query .= ' and cluster_id IN (' . implode(',', $eventIds) . ')';
        } else {
            $query .= ' and cluster_id = ' . $eventIds[0];
        }

        $sql->raw($query);
        $db->setQuery($sql);
        $db->query();

        return true;
    }
}
