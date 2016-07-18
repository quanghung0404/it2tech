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

class EasySocialModelPolls extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('polls', $config);
    }

    public function isVoted($pollId, $userId) {

        $db = ES::db();
        $sql = $db->sql();

        $query = "select count(1) from `#__social_polls_users`";
        $query .= " where `poll_id` = " . $db->Quote($pollId);
        $query .= " and `user_id` = " . $db->Quote($userId);

        $sql->raw($query);
        $db->setQuery($sql);

        $result = $db->loadResult();

        return ($result) ? true : false;
    }

    public function getAllPolls()
    {
        $db = ES::db();
        $sql = $db->sql();

        $my = ES::user();

        $query = "select * from `#__social_polls`";

        // Determines if we need to search for something
        $search = $this->getState( 'search' );

        if ($search) {
            $query .= "WHERE `title` LIKE " . $db->Quote($search);
        }

        $sql->raw($query);

        $db->setQuery($sql);

        $results = $db->loadObjectList();

        return $results;
    }

    public function getPolls($options = array())
    {

        $db = ES::db();
        $sql = $db->sql();

        $limit = isset($options['limit']) ? $options['limit'] : 0;
        $clusterId = isset($options['cluster_id']) ? $options['cluster_id'] : 0;

        $query = "select a.* from `#__social_polls` as a";
        $query .= " where (a.`cluster_id` is null";
        $query .= " or ((select count(1) from `#__social_clusters` as c where c.`id` = a.`cluster_id` and c.type = 1) > 0))";

        if ($clusterId) {
            $query .= " and a.`cluster_id` = " . $clusterId;
        }

        $query .= " order by a.`created` desc";
        
        if ($limit) {
            $query .= " limit $limit";
        }

        $sql->raw($query);
        $db->setQuery($sql);

        $polls = $db->loadObjectList();


        $items = array();
        if ($polls) {
            $ids = array();
            $userIds = array();
            // lets get the polls items
            foreach($polls as $ps) {
                $ids[] = $ps->id;
                $userIds[] = $ps->created_by;
            }

            // preload users
            ES::user($userIds);

            $query = "select a.*";
            $query .= ", (select sum(b.`count`) from `#__social_polls_items` as b where b.`poll_id` = a.`poll_id`) as `total`";
            $query .= " from `#__social_polls_items` as a";
            $query .= " where a.`poll_id` IN (" . implode(',', $ids) . ")";

            $sql->clear();
            $sql->raw($query);

            $db->setQuery($sql);
            $resultItems = $db->loadObjectList();

            // foreach($resultItems as $ri) {
            //     $items[$ri->poll_id][] = $ri;
            // }

            if ($resultItems) {
                for($i = 0; $i < count($resultItems); $i++) {
                    $ri =& $resultItems[$i];
                    if ($ri->total) {
                        $ri->percentage = round(($ri->count / $ri->total) * 100);
                    } else {
                        $ri->percentage = 0;
                    }

                    $items[$ri->poll_id][] = $ri;
                }
            }

            // now lets merge the items into polls container
            for($i = 0; $i < count($polls); $i++) {
                $p =& $polls[$i];

                $p->items = array();
                if (isset($items[$p->id])) {
                    $p->items = $items[$p->id];
                }
            }
        }

        return $polls;
    }

    public function getItems($pollId)
    {
        $db = ES::db();
        $sql = $db->sql();

        $my = ES::user();

        $query = "select a.*";
        $query .= ", (select sum(b.`count`) from `#__social_polls_items` as b where b.`poll_id` = a.`poll_id`) as `total`, u.`id` as `voted`";
        $query .= " from `#__social_polls_items` as a";
        $query .= " left join `#__social_polls_users` as u on u.`poll_itemid` = a.`id` and u.`user_id` = " . $db->Quote($my->id);
        $query .= " where a.`poll_id` = " . $db->Quote($pollId);
        $query .= " order by a.`id` asc";

        $sql->raw($query);

        $db->setQuery($sql);

        $results = $db->loadObjectList();

        if ($results) {
            for($i = 0; $i < count($results); $i++) {
                $item =& $results[$i];
                if ($item->total) {
                    $item->percentage = round(($item->count / $item->total) * 100);
                } else {
                    $item->percentage = 0;
                }
            }
        }

        return $results;
    }

    public function getVoterIds($pollId, $pollItemId = '')
    {
        $db = ES::db();
        $sql = $db->sql();

        $query = "select `user_id` from `#__social_polls_users`";
        $query .= " where `poll_id` = " . $db->Quote($pollId);
        if ($pollItemId) {
            $query .= " and `poll_itemid` = " . $db->Quote($pollItemId);
        }

        $sql->raw($query);
        $db->setQuery($sql);

        $results = $db->loadColumn();

        return $results;
    }

    public function updateStreamPrivacy($streamId, $privacyId)
    {
        $db = ES::db();
        $sql = $db->sql();

        $query = "update #__social_stream set privacy_id = " . $db->Quote($privacyId);
        $query .= " where id = " . $db->Quote($streamId);

        $sql->raw($query);
        $db->setQuery($sql);

        $state = $db->query();

        return $state;
    }

    public function deleteItemUsers($pollItemId)
    {
        $db = ES::db();
        $sql = $db->sql();

        $query = "delete from `#__social_polls_users` where `poll_itemid` = " . $db->Quote($pollItemId);
        $sql->raw($query);

        $db->setQuery($sql);
        $state = $db->query();

        return $state;
    }

    public function deletePollStreams($pollId)
    {
        $db = FD::db();
        $sql = $db->sql();

        $query = "delete a, b from `#__social_stream` as a";
        $query .= "     inner join `#__social_stream_item` as b on a.`id` = b.`uid`";
        $query .= " where a.`context_type` = 'polls'";
        $query .= " and b.`context_id` = '$pollId'";

        $sql->raw($query);
        $db->setQuery($sql);

        $db->query();

        return true;
    }
}
