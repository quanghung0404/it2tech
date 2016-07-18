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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelVideos extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('videos', $config);
    }

    /**
     * Initializes all the generic states from the form
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function initStates()
    {
        $filter = $this->getUserStateFromRequest('filter', 'all');
        $ordering = $this->getUserStateFromRequest('ordering', 'id');
        $direction = $this->getUserStateFromRequest('direction', 'ASC');

        $this->setState('filter', $filter);

        parent::initStates();

        // Override the ordering behavior
        $this->setState('ordering', $ordering);
        $this->setState('direction', $direction);
    }

    /**
     * Retrieves a list of profiles that has access to a category
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getCategoryAccess($categoryId, $type = 'create')
    {
        $db = ES::db();

        $sql = $db->sql();
        $sql->select('#__social_videos_categories_access');
        $sql->column('profile_id');
        $sql->where('category_id', $categoryId);
        $sql->where('type', $type);

        $db->setQuery($sql);

        $ids = $db->loadColumn();

        return $ids;
    }

    /**
     * Inserts new access for a cluster category
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function insertCategoryAccess($categoryId, $type = 'create', $profiles = array())
    {
        $db = FD::db();

        // Delete all existing access type first
        $sql = $db->sql();
        $sql->delete('#__social_videos_categories_access');
        $sql->where('category_id', $categoryId);
        $sql->where('type', $type);

        $db->setQuery($sql);
        $db->Query();

        if (!$profiles) {
            return;
        }

        foreach ($profiles as $id) {
            $sql->clear();
            $sql->insert('#__social_videos_categories_access');
            $sql->values('category_id', $categoryId);
            $sql->values('type', $type);
            $sql->values('profile_id', $id);

            $db->setQuery($sql);
            $db->Query();
        }

        return true;
    }

    /**
     * Retrieves the total featured videos available on site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalUserVideos($userId = null)
    {
        $user = ES::user($userId);
        $userId = $user->id;

        $sql = $this->db->sql();

        $query = "select count(1) from `#__social_videos` as a";
        $query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
        $query .= " and a.user_id = " . $this->db->Quote($userId);
        $query .= " and (a.`type` = " . $this->db->Quote('user');
        $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

    // echo $query;exit;

        $sql->raw($query);
        $this->db->setQuery($sql);
        $total = (int) $this->db->loadResult();

        return $total;
    }

    /**
     * Retrieves the total featured videos available on site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalPendingVideos($userId = null)
    {
        $user = ES::user($userId);
        $userId = $user->id;

        $sql = $this->db->sql();

        // $sql->select('#__social_videos', 'a');
        // $sql->column('COUNT(1)');
        // $sql->where('state', SOCIAL_VIDEO_PENDING);
        // $sql->where('user_id', $userId);

        $query = "select count(1) from `#__social_videos` as a";
        $query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PENDING);
        $query .= " and a.user_id = " . $this->db->Quote($userId);
        $query .= " and (a.`type` = " . $this->db->Quote('user');
        $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

        $sql->raw($query);
        $this->db->setQuery($sql);
        $total = (int) $this->db->loadResult();

        return $total;
    }


    /**
     * Retrieves the total featured videos available on site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalFeaturedVideos($options = array())
    {
        $sql = $this->db->sql();

        $uid = $this->normalize($options, 'uid', null);
        $type = $this->normalize($options, 'type', null);
        $userid = $this->normalize($options, 'userid', null);
        $privacy = $this->normalize($options, 'privacy', true);


        $query = "select count(1) from `#__social_videos` as a";

        if ($privacy) {
            $tmpTable = $this->genCounterTableWithPrivacy();
            $query = "select count(1) from $tmpTable as a";
        }

        $query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
        $query .= " and a.featured = " . $this->db->Quote(SOCIAL_VIDEO_FEATURED);

        if ($userid) {
            $query .= " and a.user_id = " . $this->db->Quote($userid);
        }

        if ($uid && $type) {
            $query .= " and a.uid = " . $this->db->Quote($uid);
            $query .= " and a.type = " . $this->db->Quote($type);
        } else {
            $query .= " and (a.`type` = " . $this->db->Quote('user');
            $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";
        }

        $sql->raw($query);
        $this->db->setQuery($sql);
        $total = (int) $this->db->loadResult();

        return $total;
    }

    /**
     * Retrieves the total videos available on site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    /**
     * Retrieves the total videos available on site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalVideos($options = array())
    {
        $sql = $this->db->sql();

        $uid = $this->normalize($options, 'uid', null);
        $type = $this->normalize($options, 'type', null);
        $userid = $this->normalize($options, 'userid', null);
        $state = $this->normalize($options, 'state', SOCIAL_VIDEO_PUBLISHED);
        $privacy = $this->normalize($options, 'privacy', true);

        $viewer = ES::user()->id;

        $cond = array();

        $query = "select count(1) from `#__social_videos` as a";

        if (!FD::user()->isSiteAdmin() && $privacy) {
            if ($type == 'user' || is_null($type)) {
                $tmpTable = $this->genCounterTableWithPrivacy();
                $query = "select count(1) from $tmpTable as a";
            } else {
                $query .= " inner join `#__social_clusters` as cls on a.`uid` = cls.`id` and a.`type` = cls.`cluster_type`";
            }
        }

        if ($state != 'all') {
            $cond[] = "a.state = " . $this->db->Quote($state);
        }


        if ($userid) {
            $cond[] = "a.user_id = " . $this->db->Quote($userid);
        }

        if ($uid && $type) {
            $cond[] = "a.uid = " . $this->db->Quote($uid);
            $cond[] = " a.type = " . $this->db->Quote($type);
        } else {
            $tmp = "(a.`type` = " . $this->db->Quote('user');
            $tmp .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";

            $cond[] = $tmp;
        }

        if (!FD::user()->isSiteAdmin() && $privacy && $type != 'user' && !is_null($type)) {
            $tmp = "(";
            $tmp .= " (cls.`type` = 1) OR";
            $tmp .= " (cls.`type` > 1) AND " . $this->db->Quote($viewer) . " IN ( select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = a.`uid` and scn.`type` = " . $this->db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
            $tmp .= ")";

            $cond[] = $tmp;
        }


        if ($cond) {
            if (count($cond) == 1) {
                $query .= " where " . $cond[0];
            } else if (count($cond) > 1) {

                $whereCond = array_shift($cond);

                $query .= " where " . $whereCond;
                $query .= " and " . implode(" and ", $cond);
            }
        }

        $sql->raw($query);

        $this->db->setQuery($sql);
        $total = (int) $this->db->loadResult();

        return $total;
    }

    /**
     * Retrieves the list of videos for the back end
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getItems()
    {
        $sql = $this->db->sql();

        $filter = $this->getState('filter');

        $sql->select('#__social_videos');

        if ($filter != 'all') {
            $sql->where('state', $filter);
        }

        // Set the total records for pagination.
        $this->setTotal($sql->getTotalSql());

        $result = $this->getData($sql->getSql());

        if (!$result) {
            return $result;
        }

        $videos = array();

        foreach ($result as $row) {

            $tmp = (array) $row;

            $row = ES::table('Video');
            $row->bind($tmp);

            $video = ES::video($row);

            $videos[] = $video;
        }

        return $videos;
    }

    private function genCounterTableWithPrivacy()
    {
        $db = ES::db();
        $viewer = FD::user()->id;

        $accessColumn = $this->getAccessColumn('access', 'ct');
        $accessCustomColumn = $this->getAccessColumn('customaccess', 'ct');

        $table = "(select * from (select ct.*, $accessColumn, $accessCustomColumn from `#__social_videos` as ct) as x";
        // privacy here.
        $table .= " WHERE (";

        //public
        $table .= " (x.`access` = " . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ") OR";

        //member
        $table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0 ) ) OR ";

        //friends
        $table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ") > 0 ) ) OR ";

        //only me
        $table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND ( x.`user_id` = " . $viewer . " ) ) OR ";

        // custom
        $table .= " ( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND ( x.`custom_access` LIKE " . $db->Quote( '%,' . $viewer . ',%' ) . "    ) ) OR ";

        // my own items.
        $table .= " (x.`user_id` = " . $viewer . ")";

        // privacy checking end here.
        $table .= " ))";

        return $table;
    }


    private function getAccessColumn($type = 'access', $prefix = 'a')
    {
        $column = '';
        if ($type == 'access') {
            $column = "(select pri.value as `access` from `#__social_privacy_items` as pri";
            $column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'videos'";
            $column .= " UNION ALL ";
            $column .= " select prm.value as `access`";
            $column .= " from `#__social_privacy_map` as prm";
            $column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
            $column .= "  left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
            $column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
            $column .= "  and pp.type = 'videos' and pp.rule = 'view'";
            $column .= " union all ";
            $column .= " select prm.value as `access`";
            $column .= " from `#__social_privacy_map` as prm";
            $column .= "  inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
            $column .= "  inner join `#__social_profiles_maps` pmp on prm.uid = pmp.profile_id";
            $column .= " where prm.utype = 'profiles' and pmp.user_id = " . $prefix . ".user_id";
            $column .= "  and pp.type = 'videos' and pp.rule = 'view'";
            $column .= " limit 1";
            $column .= ") as access";

        } else if ($type == 'customaccess') {

            $column = "(select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access` from `#__social_privacy_items` as pri";
            $column .= " left join `#__social_privacy_customize` as prc on pri.id = prc.uid and prc.utype = 'item' where pri.uid = " . $prefix . ".id and pri.`type` = 'videos'";
            $column .= " UNION ALL ";
            $column .= " select concat(',', group_concat(prc.user_id SEPARATOR ','), ',') as `custom_access`";
            $column .= " from `#__social_privacy_map` as prm";
            $column .= "    inner join `#__social_privacy` as pp on prm.privacy_id = pp.id";
            $column .= "    left join `#__social_privacy_customize` as prc on prm.id = prc.uid and prc.utype = 'user'";
            $column .= " where prm.uid = " . $prefix . ".user_id and prm.utype = 'user'";
            $column .= "    and pp.type = 'videos' and pp.rule = 'view'";
            $column .= " limit 1";
            $column .= ") as custom_access";

        }

        return $column;
    }

    /**
     * Retrieves a list of videos from the site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getVideosForCron($options = array())
    {
        // search criteria
        $filter = $this->normalize($options, 'filter', '');
        $sort = $this->normalize($options, 'sort', 'latest');
        $limit = $this->normalize($options, 'limit', false);

        $db = ES::db();
        $sql = $db->sql();

        $query[] = "select a.* from `#__social_videos` as a";

        if ($filter == 'processing') {
            $query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_VIDEO_PROCESSING);
        } else {
            $query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PENDING);
        }

        if ($sort) {
            switch ($sort) {
                case 'popular':
                    $query[] = "order by a.hits desc";
                    break;

                case 'alphabetical':
                    $query[] = "order by a.title asc";
                    break;

                case 'random':
                    $query[] = "order by RAND()";
                    break;

                case 'likes':
                    $query[] = "order by likes desc";
                    break;

                case 'commented':
                    $query[] = "order by totalcomments desc";
                    break;

                case 'latest':
                default:
                    $query[] = "order by a.created desc";
                    break;
            }
        }

        if ($limit) {
            $query[] = "limit $limit";
        }

        $query = implode(' ', $query);
        $sql->raw($query);

        $db->setQuery($sql);
        $results = $db->loadObjectList();

        $videos = array();

        if ($results) {
            foreach ($results as $row) {
                $video = ES::video($row->uid, $row->type);
                $video->load($row);

                $videos[] = $video;
            }
        }

        return $videos;
    }

    /**
     * Retrieves the list of items which stored in Amazon
     *
     * @since   1.4.6
     * @access  public
     * @param   string
     * @return
     */
    public function getVideosStoredExternally($storageType = 'amazon')
    {
        // Get the number of files to process at a time
        $config = ES::config();
        $limit = $config->get('storage.amazon.limit', 10);

        $db = FD::db();
        $sql = $db->sql();
        $sql->select('#__social_videos');
        $sql->where('storage', $storageType);
        $sql->limit($limit);

        $db->setQuery($sql);

        $result = $db->loadObjectList();

        return $result;
    }

    /**
     * Retrieves a list of videos from the site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getVideos($options = array())
    {
        $db = ES::db();
        $sql = $db->sql();

        $accessColumn = $this->getAccessColumn('access', 'a');
        $accessCustomColumn = $this->getAccessColumn('customaccess', 'a');

        $likeCountColumn = "(select count(1) from `#__social_likes` as exb where exb.uid = a.id and exb.type = 'videos.user.create') as likes";
        $commentCountColumn = "(select count(1) from `#__social_comments` as exb where exb.uid = a.id and exb.element = 'videos.user.create') as totalcomments";

        // search criteria
        $privacy = $this->normalize($options, 'privacy', true);
        $filter = $this->normalize($options, 'filter', '');
        $featured = $this->normalize($options, 'featured', null);
        $category = $this->normalize($options, 'category', '');
        $sort = $this->normalize($options, 'sort', 'latest');
        $maxlimit = $this->normalize($options, 'maxlimit', 0);
        $limit = $this->normalize($options, 'limit', false);

        $storage = $this->normalize($options, 'storage', false);
        $uid = $this->normalize($options, 'uid', null);
        $type = $this->normalize($options, 'type', null);
        $source = $this->normalize($options, 'source', false);

        $userid = $this->normalize($options, 'userid', null);


        $useLimit = true;

        $query = array();

        $isSiteAdmin = ES::user()->isSiteAdmin();

        if (!$isSiteAdmin && $privacy) {
            $query[] = "select * from (";
        }

        $query[] = "select a.*";

        if (!$isSiteAdmin && $privacy) {
            if ($type == 'user' || is_null($type)) {
                $query[] = ", $accessColumn, $accessCustomColumn";
            } else {
                $query[] = ", cls.`type` as `access`";
            }
        }

        if ($sort == 'likes') {
            $query[] = ", $likeCountColumn";
        }

        if ($sort == 'commented') {
            $query[] = ", $commentCountColumn";
        }

        $query[] = "from `#__social_videos` as a";

        if ($type != 'user' && !is_null($type)) {
            $query[] = " inner join `#__social_clusters` as cls on a.`uid` = cls.`id` and a.`type` = cls.`cluster_type`";
        }

        if ($filter == 'pending') {
            $query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PENDING);
        } else if ($filter == 'processing') {
            $query[] = 'WHERE a.`state`=' . $db->Quote(SOCIAL_VIDEO_PROCESSING);
        } else {
            $query[] = "where a.`state` = " . $db->Quote(SOCIAL_VIDEO_PUBLISHED);
        }

        if ($uid && $type) {
            $query[] = 'AND a.`uid`=' . $db->Quote($uid);
            $query[] = 'AND a.`type`=' . $db->Quote($type);
        } else {
            $query[] = 'and (a.`type` = ' . $db->Quote('user');
            $query[] = '    or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = '. $db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) .') > 0))';
        }

        if ($filter == 'mine') {
            $my = ES::user();
            $query[] = "and a.`user_id` = " . $db->Quote($my->id);
        }

        if ($filter == 'pending' && $userid) {
            $query[] = "and a.`user_id` = " . $db->Quote($userid);
        }

        if ($filter == SOCIAL_TYPE_USER) {
            $query[] = "and a.`user_id` = " . $db->Quote($userid);
        }

        if ($category) {
            $query[] = "and a.`category_id` = " . $db->Quote($category);
        }


        $exclusion = $this->normalize($options, 'exclusion', null);

        if ($exclusion) {

            $exclusion = ES::makeArray($exclusion);
            $exclusionIds = array();

            foreach ($exclusion as $exclusionId) {
                $exclusionIds[] = $db->Quote($exclusionId);
            }

            $exclusionIds = implode(',', $exclusionIds);

            $query[] = 'AND a.' . $db->qn('id') . ' NOT IN (' . $exclusionIds . ')';
        }

        // featured filtering
        if ($filter == 'featured') {
            $query[] = "and a.`featured` = " . $db->Quote(SOCIAL_VIDEO_FEATURED);
        }

        // featured
        if (! is_null($featured)) {
            $query[] = "and a.`featured` = " . $db->Quote((int) $featured);
        }


        if ($storage !== false) {
            $query[] = 'AND a.`storage` = ' . $db->Quote($storage);
        }

        if ($source !== false) {
            $query[] = 'AND a.`source`=' . $db->Quote($source);
        }

        if ($sort) {
            switch ($sort) {
                case 'popular':
                    $query[] = "order by a.hits desc";
                    break;

                case 'alphabetical':
                    $query[] = "order by a.title asc";
                    break;

                case 'random':
                    $query[] = "order by RAND()";
                    break;

                case 'likes':
                    $query[] = "order by likes desc";
                    break;

                case 'commented':
                    $query[] = "order by totalcomments desc";
                    break;

                case 'latest':
                default:
                    $query[] = "order by a.created desc";
                    break;
            }
        }

        if (!$isSiteAdmin && $privacy) {

            $viewer = FD::user()->id;

            $query[] = ") as x";

            if ($type != 'user' && !is_null($type)) {
                // cluster privacy
                $query[] = " WHERE (";
                $query[] = " (x.`access` = 1) OR";
                $query[] = " (x.`access` > 1) AND " . $db->Quote($viewer) . " IN ( select scn.`uid` from `#__social_clusters_nodes` as scn where scn.`cluster_id` = x.`uid` and scn.`type` = " . $db->Quote(SOCIAL_TYPE_USER) . " and scn.`state` = 1)";
                $query[] = ")";

            } else {

                // privacy here.
                $query[] = " WHERE (";

                //public
                $query[] = "(x.`access` = " . $db->Quote( SOCIAL_PRIVACY_PUBLIC ) . ") OR";

                //member
                $query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_MEMBER) . ") AND (" . $viewer . " > 0 ) ) OR ";

                //friends
                $query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_FRIEND) . ") AND ( (" . $this->generateIsFriendSQL( 'x.`user_id`', $viewer ) . ") > 0 ) ) OR ";

                //only me
                $query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_ONLY_ME) . ") AND ( x.`user_id` = " . $viewer . " ) ) OR ";

                // custom
                $query[] = "( (x.`access` = " . $db->Quote(SOCIAL_PRIVACY_CUSTOM) . ") AND ( x.`custom_access` LIKE " . $db->Quote( '%,' . $viewer . ',%' ) . "    ) ) OR ";

                // my own items.
                $query[] = "(x.`user_id` = " . $viewer . ")";

                // privacy checking end here.
                $query[] = ")";
            }
        }



        if ($maxlimit) {
            $useLimit = false;
            $query[] = "limit $maxlimit";
        }

        $query = implode(' ', $query);
        $sql->raw($query);

        // dump($sql->debug());

        if (!$maxlimit && $limit) {

            $this->setState( 'limit' , $limit );

            // Get the limitstart.
            $limitstart = $this->getUserStateFromRequest( 'limitstart' , 0 );
            $limitstart = ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

            $this->setState( 'limitstart' , $limitstart );

            // Set the total number of items.
            $this->setTotal( $sql->getSql() , true );
        } else {
            $useLimit = false;
        }

        $this->db->setQuery($sql);
        $result = $this->getData($sql, $useLimit);

        if (!$result) {
            return $result;
        }

        $videos = array();

        foreach ($result as $row) {
            $video = ES::video($row->uid, $row->type);
            $video->load($row);

            $videos[] = $video;
        }

        return $videos;
    }



    /**
     * Overriding parent getData method so that we can specify if we need the limit or not.
     *
     * If using the pagination query, child needs to use this method.
     *
     * @since   1.4
     * @access  public
     */
    protected function getData($query , $useLimit = true)
    {
        if ($useLimit) {
            return parent::getData($query);
        } else {
            $this->db->setQuery($query);
        }

        return $this->db->loadObjectList();
    }


    public function generateIsFriendSQL( $source, $target )
    {
        $query = "select count(1) from `#__social_friends` where ( `actor_id` = $source and `target_id` = $target) OR (`target_id` = $source and `actor_id` = $target) and `state` = 1";
        return $query;
    }


    /**
     * Retrieves the default category
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getDefaultCategory()
    {
        $db = $this->db;
        $sql = $db->sql();

        $sql->select('#__social_videos_categories');
        $sql->where('default', 1);

        $db->setQuery($sql);

        $result = $db->loadObject();

        if (!$result) {
            return false;
        }

        $category = ES::table('VideoCategory');
        $category->bind($result);

        return $category;
    }

    /**
     * Retrieves a list of video categories from the site
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getCategories($options = array())
    {
        $db = ES::db();
        $sql = $db->sql();

        $query = array();
        $query[] = 'SELECT a.* FROM ' . $db->qn('#__social_videos_categories') . ' AS a';

        // Filter for respecting creation access
        $respectAccess = $this->normalize($options, 'respectAccess', false);
        $profileId = $this->normalize($options, 'profileId', 0);

        if ($respectAccess && $profileId) {
            $query[] = 'LEFT JOIN ' . $db->qn('#__social_videos_categories_access') . ' AS b';
            $query[] = 'ON a.id = b.category_id';
        }

        $query[] = 'WHERE 1 ';

        // Filter for searching categories
        $search = $this->normalize($options, 'search', '');

        if ($search) {
            $query[] = 'AND ';
            $query[] = $db->qn('title') . ' LIKE ' . $db->Quote('%' . $search . '%');
        }

        // Respect category creation access
        if ($respectAccess && $profileId) {
            $query[] = 'AND (';
            $query[] = '(b.`profile_id`=' . $db->Quote($profileId) . ')';
            $query[] = 'OR';
            $query[] = '(a.' . $db->qn('id') . ' NOT IN (SELECT `category_id` FROM `#__social_videos_categories_access`))';
            $query[] = ')';
        }

        // Ensure that the videos are published
        $state = $this->normalize($options, 'state', true);

        // Ensure that all the categories are listed in backend
        $adminView = $this->normalize($options, 'administrator', false);

        if (!$adminView) {
            $query[] = 'AND ' . $db->qn('state') . '=' . $db->Quote($state);
        }
        
        $query = implode(' ', $query);
        $sql->raw($query);

        // Set the total records for pagination.
        $totalSql = str_ireplace('a.*', 'COUNT(1)', $query);
        $this->setTotal($totalSql);

        // Runt he main query now
        $db->setQuery($sql);

        // We need to go through our paginated library
        $result = $this->getData($sql->getSql());

        if (!$result) {
            return $result;
        }

        $categories = array();

        foreach ($result as $row) {
            $category = ES::table('VideoCategory');
            $category->bind($row);

            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * Retrieves the total number of videos from a category
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalVideosFromCategory($categoryId, $cluster = false, $uid = null, $type = null)
    {
        $db = $this->db;
        $sql = $this->db->sql();


        // $query = "select count(1) from `#__social_videos` as a";

        $tmpTable = $this->genCounterTableWithPrivacy();
        $query = "select count(1) from $tmpTable as a";

        $query .= " where a.state = " . $this->db->Quote(SOCIAL_VIDEO_PUBLISHED);
        $query .= " and a.category_id = " . $this->db->Quote($categoryId);

        if (!is_null($uid) && !is_null($type)) {
            if ($type == SOCIAL_TYPE_USER) {
                $query .= " and a.user_id = " . $db->Quote($uid);
            }

            if ($cluster && !($cluster instanceof SocialUser)) {
                $query .= " and a.uid = " . $db->Quote($cluster->id);
                $query .= " and a.type = " . $db->Quote($cluster->getType());
            } else {
                $query .= " and (a.`type` = " . $this->db->Quote('user');
                $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`uid` and c.`cluster_type` = a.`type` and c.type = " . $this->db->Quote(SOCIAL_GROUPS_PUBLIC_TYPE) . ") > 0))";
            }
        }

        $sql->raw($query);
        $this->db->setQuery($sql);
        $total = $this->db->loadResult();

        return $total;
    }

    /**
     * Determines if the video should be associated with the stream item
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getStreamId($videoId, $verb)
    {
        $db     = ES::db();
        $sql    = $db->sql();

        $sql->select('#__social_stream_item', 'a');
        $sql->column('a.uid');
        $sql->where('a.context_type', SOCIAL_TYPE_VIDEOS);
        $sql->where('a.context_id', $videoId);
        $sql->where('a.verb', $verb);

        $db->setQuery($sql);

        $uid    = (int) $db->loadResult();

        return $uid;
    }

}
