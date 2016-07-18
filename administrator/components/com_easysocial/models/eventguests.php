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

class EasySocialModelEventGuests extends EasySocialModel
{
    public function __construct($config = array())
    {
        parent::__construct('eventmembers', $config);
    }

    public function initStates()
    {
        parent::initStates();
    }

    public function getItems($options = array())
    {
        $db = FD::db();

        $sql = $db->sql();

        $sql->select('#__social_clusters_nodes', 'a');
        $sql->column('a.*');

        $eventid = isset($options['eventid']) ? $options['eventid'] : 0;

        $sql->where('cluster_id', $eventid);

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
                $sql->leftjoin('#__users', 'b');
                $sql->on('a.uid', 'b.id');

                $sql->order('b.username', $direction);
            } elseif ($ordering == 'name') {
                $sql->leftjoin('#__users', 'b');
                $sql->on('a.uid', 'b.id');

                $sql->order('b.name', $direction);
            } else {
                $sql->order($ordering, $direction);
            }
        }

        $this->setTotal($sql->getTotalSql());

        $result = $this->getData($sql->getSql());

        $guests = $this->bindTable('EventGuest', $result);

        return $guests;
    }
}
