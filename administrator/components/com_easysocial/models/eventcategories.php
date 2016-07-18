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

class EasySocialModelEventCategories extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('eventcategories', $config);
    }

    public function initStates()
    {
        // Ordering, direction, search, limit, limitstart is handled by parent::initStates();
        parent::initStates();

        $state = $this->getUserStateFromRequest('state', 'all');

        $this->setState('state', $state);
    }

    /**
     * Returns an array of SocialTableEventCategory table object for backend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return array    Array of SocialTableEventCategory table objects.
     */
    public function getItems()
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_categories');

        $search = $this->getState('search');

        if (!empty($search)) {
            $sql->where('title', '%' . $search . '%', 'LIKE');
        }

        $state = $this->getState('state');

        if (isset($state) && $state !== 'all') {
            $sql->where('state', $state);
        }

        $sql->where('type', SOCIAL_TYPE_EVENT);

        $ordering = $this->getState('ordering');
        $direction = $this->getState('direction');

        $sql->order($ordering, $direction);

        $this->setTotal($sql->getTotalSql());

        $result = $this->getData($sql->getSql());

        $categories = $this->bindTable('EventCategory', $result);

        return $categories;
    }

    /**
     * Returns an array of SocialTableEventCategory table object for frontend listing.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  array     $options Options to filter.
     * @return array              Array of SocialTableEventCategory table objects.
     */
    public function getCategories($options = array())
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_categories');
        $sql->where('type', SOCIAL_TYPE_EVENT);

        if (isset($options['state']) && $options['state'] !== 'all') {
            $sql->where('state', $options['state']);
        }

        if (isset($options['ordering'])) {
            $direction = isset($options['direction']) ? $options['direction'] : 'asc';

            $sql->order($options['ordering'], $direction);
        }

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        $categories = $this->bindTable('eventCategory', $result);

        return $categories;
    }

    /**
     * Returns an array of SocialTableEventCategory table object based on profileId.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer    $profileId The profile id to check against.
     * @return array                 Array of SocialTableEventCategory table objects.
     */
    public function getCreatableCategories($profileId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $query = array();

        $query[] = "SELECT DISTINCT `a`.* FROM `#__social_clusters_categories` AS `a`";
        $query[] = "LEFT JOIN `#__social_clusters_categories_access` AS `b`";
        $query[] = "ON `a`.`id` = `b`.`category_id`";
        $query[] = "WHERE `a`.`type` = 'event'";
        $query[] = "AND `a`.`state` = '1'";

        if (!FD::user()->isSiteAdmin()) {
            $query[] = "AND (`b`.`profile_id` = " . $profileId;
            $query[] = "OR `a`.`id` NOT IN (SELECT `category_id` FROM `#__social_clusters_categories_access`))";
        }

        $query[] = "ORDER BY `a`.`ordering`";

        $db->setQuery($sql->raw(implode(' ', $query)));

        $result = $db->loadObjectList();

        $categories = $this->bindTable('EventCategory', $result);

        return $categories;
    }

    /**
     * Returns an array of SocialTableEventGuest object based on category.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer    $categoryId Category id to filter.
     * @return array                  Array of SocialTableEventGuest object.
     */
    public function getRandomCategoryGuests($categoryId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('a.uid', 'uid', 'distinct');
        $sql->leftjoin('#__social_clusters', 'b');
        $sql->on('a.cluster_id', 'b.id');
        $sql->where('b.category_id', $categoryId);
        $sql->where('b.type', array(SOCIAL_EVENT_TYPE_PUBLIC, SOCIAL_EVENT_TYPE_PRIVATE), 'IN');

        $sql->order('', 'ASC', 'rand');
        $sql->limit(10);

        $db->setQuery($sql);

        $result = $db->loadColumn();

        $users = FD::user($result);

        return $users;
    }

    /**
     * Returns an array of SocialTableAlbum object based on category.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer    $categoryId Category id to filter.
     * @return array                  Array of SocialTableAlbum object.
     */
    public function getRandomCategoryAlbums($categoryId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_albums', 'a');
        $sql->column('a.*');
        $sql->leftjoin('#__social_clusters', 'b');
        $sql->on('a.uid', 'b.id');
        $sql->on('a.type', 'b.cluster_type');
        $sql->where('b.category_id', $categoryId);
        $sql->where('b.type', SOCIAL_EVENT_TYPE_PUBLIC);
        $sql->order('', 'ASC', 'rand');
        $sql->limit(10);

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        $albums = $this->bindTable('Album', $result);

        return $albums;
    }

    /**
     * Returns the total number of albums in a category.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer    $categoryId Category id to filter.
     * @return integer                The total number of albums in a category.
     */
    public function getTotalAlbums($categoryId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $sql->select('#__social_albums', 'a');
        $sql->column('a.*');
        $sql->leftjoin('#__social_clusters', 'b');
        $sql->on('a.uid', 'b.id');
        $sql->on('a.type', 'b.cluster_type');
        $sql->where('b.category_id', $categoryId);
        $sql->where('b.type', SOCIAL_EVENT_TYPE_PUBLIC);

        $db->setQuery($sql->getTotalSql());

        return $db->loadResult();
    }

    /**
     * Returns event creation stats in this category.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  integer    $categoryId Category id to filter.
     * @return array                  The creation statistics.
     */
    public function getCreationStats($categoryId)
    {
        $db = FD::db();
        $sql = $db->sql();
        $dates = array();

        // Get the past 7 days
        $curDate = FD::date();
        for ($i = 0 ; $i < 7; $i++) {
            $obj = new stdClass();

            if ($i == 0) {
                $obj->date = $curDate->toSql();
            } else {
                $unixdate = $curDate->toUnix();
                $new_unixdate = $unixdate - ($i * 86400);
                $newdate = FD::date($new_unixdate);

                $obj->date = $newdate->toSql();
            }

            $dates[] = $obj;
        }

        // Reverse the dates
        $dates = array_reverse($dates);
        $result = array();

        foreach ($dates as &$row) {
            $date = FD::date($row->date)->format('Y-m-d');

            $query = array();
            $query[] = "SELECT COUNT(1) FROM `#__social_clusters`";
            $query[] = "WHERE DATE_FORMAT(`created`, GET_FORMAT(DATE, 'ISO')) = '$date'";
            $query[] = "AND `category_id` = $categoryId";
            $query[] = "AND `type` = " . SOCIAL_EVENT_TYPE_PUBLIC;
            $query[] = 'GROUP BY `category_id`';

            $query = implode(' ', $query);
            $sql->raw($query);

            $db->setQuery($sql);

            $total = $db->loadResult();

            $result[] = (int) $total;
        }

        return $result;
    }

    public function updateEventCategory($uid, $categoryId)
    {
        $cluster = FD::table('Cluster');
        $cluster->load($uid);

        $cluster->category_id = $categoryId;

        $cluster->store();

        $db = FD::db();
        $sql = $db->sql();

        $sql->update('#__social_fields_data', 'a');
        $sql->leftjoin('#__social_fields', 'b');
        $sql->on('a.field_id', 'b.id');
        $sql->leftjoin('#__social_fields', 'c');
        $sql->on('b.unique_key', 'c.unique_key');
        $sql->leftjoin('#__social_fields_steps', 'd');
        $sql->on('c.step_id', 'd.id');
        $sql->set('a.field_id', 'c.id', false);
        $sql->where('a.uid', $uid);
        $sql->where('a.type', 'event');
        $sql->where('d.type', 'clusters');
        $sql->where('d.uid', $categoryId) ;

        $db->setQuery($sql);

        return $db->query();
    }
}
