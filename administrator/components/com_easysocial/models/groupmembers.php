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

jimport('joomla.application.component.model');

FD::import('admin:/includes/model');

class EasySocialModelGroupMembers extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('groupmembers', $config);
    }

    public function initStates()
    {
        $ordering = $this->getUserStateFromRequest('ordering', 'a.id');
        $direction = $this->getUserStateFromRequest('direction', 'asc');

        $this->setState('ordering', $ordering);
        $this->setState('direction', $direction);

        parent::initStates();
    }

    public function getItems($options = array())
    {
        $db = FD::db();

        $sql = $db->sql();

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('a.*');

        $sql->leftjoin('#__users', 'b');
        $sql->on('a.uid', 'b.id');

        if (FD::config()->get('users.blocking.enabled') && !JFactory::getUser()->guest) {
            $sql->leftjoin( '#__social_block_users' , 'bus');
            $sql->on( 'b.id' , 'bus.user_id' );
            $sql->on( 'bus.target_id', JFactory::getUser()->id );
            $sql->isnull('bus.id');
        }

        $sql->where('b.block', 0);

        if (!empty($options['groupid'])) {
            $sql->where('cluster_id', $options['groupid']);
        }

        if (isset($options['state'])) {
            $sql->where('state', $state);
        }

        if (isset($options['admin'])) {
            $sql->where('admin', $options['admin']);
        }

        $ordering = $this->getState('ordering');

        if (!empty($ordering)) {
            $direction = $this->getState('direction');

            if($ordering == 'username') {
                $sql->order('b.username', $direction);
            } elseif ($ordering == 'name') {
                $sql->order('b.name', $direction);
            } else {
                $sql->order($ordering, $direction);
            }
        }

        $limit = $this->getState('limit');

        if ($limit > 0) {
            $this->setState('limit', $limit);

            // Get the limitstart.
            $limitstart = $this->getUserStateFromRequest('limitstart', 0);
            $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

            $this->setState('limitstart', $limitstart);

            // Set the total number of items.
            $this->setTotal( $sql->getTotalSql() );

            // Get the list of users
            $result = parent::getData( $sql->getSql() );
        } else {
            $db->setQuery($sql);

            $result = $db->loadObjectList();
        }

        $members = array();

        foreach ($result as $row) {
            $member = FD::table('GroupMember');
            $member->bind($row);

            $members[] = $member;
        }

        return $members;
    }
}
