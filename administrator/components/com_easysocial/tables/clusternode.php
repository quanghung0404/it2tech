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

/**
 * Object mapping for `#__social_clusters_nodes` table.
 *
 * @author  Mark Lee <mark@stackideas.com>
 * @since   1.2
 */
class SocialTableClusterNode extends SocialTable
{
    /**
     * The unique id for this cluster mapping.
     * @var int
     */
    public $id = null;

    /**
     * The id of the cluster.
     * @var int
     */
    public $cluster_id = null;

    /**
     * The unique id that belongs to a cluster.
     * @var string
     */
    public $uid = null;

    /**
     * The unique type that belongs to a cluster.
     * @var string
     */
    public $type = null;

    /**
     * The creation date of the mapping.
     * @var datetime
     */
    public $created = null;

    /**
     * The state of the mapping.
     * @var int
     */
    public $state = null;

    /**
     * Determines if the node is the owner of the cluster.
     * @var int
     */
    public $owner = null;

    /**
     * Determines if the node is an admin of the cluster.
     * @var int
     */
    public $admin = null;

    /**
     * If the node is invited by a user, this will store the invitor.
     * @var int
     */
    public $invited_by  = null;

    /**
     * reminder sent flag. currently used for event guest only.
     * @var int
     */
    public $reminder_sent  = null;

    public function __construct(& $db)
    {
        parent::__construct('#__social_clusters_nodes' , 'id' , $db);
    }

    /**
     * Makes the current user an admin.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True is successful.
     */
    public function makeAdmin()
    {
        $this->admin = true;

        return $this->store();
    }

    /**
     * Revokes the current user as an admin.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True is successful.
     */
    public function revokeAdmin()
    {
        $this->admin = false;

        return $this->store();
    }

    /**
     * Checks if the user is a cluster admin.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean   True if the user is an admin.
     */
    public function isAdmin()
    {
        return (bool) $this->admin;
    }

    /**
     * Checks if the user is ONLY an admin and not an owner.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return boolean   True if user is only an admin and not an owner.
     */
    public function isStrictlyAdmin()
    {
        return $this->isAdmin() && !$this->isOwner();
    }

    /**
     * Checks if the user is the owner of the cluster.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean   True if the user is the owner.
     */
    public function isOwner()
    {
        return (bool) $this->owner;
    }

    /**
     * Makes this user the owner of the cluster, and revokes all other owner.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  boolean    True if successful.
     */
    public function makeOwner()
    {
        $this->owner = true;

        $state = $this->store();

        if ($state) {
            // Mark all other owner off
            $db = FD::db();
            $sql = $db->sql();

            $sql->update($this->_tbl);
            $sql->set('owner', 0);
            $sql->where('cluster_id', $this->cluster_id);
            $sql->where('type', $this->type);
            $sql->where('uid', $this->uid, '<>');

            $db->setQuery($sql);
            $db->query();

            $sql->clear();
            $sql->update('#__social_clusters');
            $sql->set('creator_uid', $this->uid);
            $sql->set('creator_type', $this->type);
            $sql->where('id', $this->cluster_id);

            $db->setQuery($sql);
            $db->query();
        }

        return $state;
    }
}
