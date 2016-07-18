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

// Import main table to assis this table.
FD::import('admin:/tables/table');

/**
 * Tasks Milestone object relation mapper.
 *
 * @since   1.2
 * @author  Mark Lee <mark@stackideas.com>
 */
class SocialTableMilestone extends SocialTable
{
    /**
     * The unique task id.
     * @var int
     */
    public $id = null;

    /**
     * The unique task id.
     * @var int
     */
    public $uid = null;

    /**
     * The unique task id.
     * @var int
     */
    public $type = null;

    /**
     * The owner of the task.
     * @var int
     */
    public $owner_id = null;

    /**
     * The responsible person
     * @var int
     */
    public $user_id = null;

    /**
     * The task title.
     * @var string
     */
    public $title = null;

    /**
     * The task title.
     * @var string
     */
    public $description = null;

    /**
     * The date time this task has been created.
     * @var datetime
     */
    public $created = null;

    /**
     * The state of the task
     * @var string
     */
    public $due = null;

    /**
     * The state of the task
     * @var string
     */
    public $state = null;

    /**
     * Class Constructor.
     *
     * @since   1.0
     * @access  public
     */
    public function __construct(&$db)
    {
        parent::__construct('#__social_tasks_milestones', 'id', $db);
    }

    /**
     * Retrieves the assignee object
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getAssignee()
    {
        static $assignees = array();

        if (!isset($assignees[$this->id])) {
            $assignees[$this->id] = FD::user($this->user_id);
        }

        return $assignees[$this->id];
    }

    /**
     * Check if this milestone has an assignee.
     * @return boolean True if this milestone has an assignee.
     */
    public function hasAssignee()
    {
        $ass = $this->getAssignee();

        return !empty($ass->id);
    }

    /**
     * Retrieves the total tasks this milestone has
     *
     * @since   1.2
     * @access  public
     * @return  int
     */
    public function getTotalTasks()
    {
        static $tasks = array();

        if (!isset($tasks[$this->id])) {
            $tasks[$this->id] = FD::model('Tasks')->getTotalTasks($this->id);
        }

        return $tasks[$this->id];
    }

    /**
     * Override parent's delete behavior
     *
     * @since   1.2
     * @access  public
     * @return  boolean
     */
    public function delete($pk = null)
    {
        $state = parent::delete($pk);

        if ($state) {
            FD::model('Tasks')->deleteTasks($this->id);

            $this->removeStream('createMilestone');
        }

        return $state;
    }

    /**
     * Determines if there's a due date set for the mileston
     *
     * @since   1.2
     * @access  public
     * @return  boolean
     */
    public function hasDueDate()
    {
        return $this->due !== '0000-00-00 00:00:00';
    }

    /**
     * Determines if the milestone is due
     *
     * @since   1.2
     * @access  public
     * @return  boolean
     */
    public function isDue()
    {
        if ($this->isCompleted()) {
            return false;
        }

        if (!$this->hasDueDate()) {
            return false;
        }

        $due = FD::date($this->due)->toUnix();
        $now = FD::date()->toUnix();

        return $now > $due;
    }

    /**
     * Determines if the milestone is due
     *
     * @since   1.2
     * @access  public
     * @return  boolean
     */
    public function isCompleted()
    {
        return $this->state == 2;
    }

    /**
     * Alias method for isCompleted().
     * @return  boolean True if the task is resolved.
     */
    public function isResolved()
    {
        return $this->isCompleted();
    }

    /**
     * Retrieves a list of tasks for the milestone
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getTasks()
    {
        static $tasks = array();

        if (!isset($tasks[$this->id])) {
            $model = FD::model('Tasks');

            $tasks[$this->id] = $model->getTasks($this->id);
        }

        return $tasks[$this->id];
    }

    /**
     * Generates a new stream item
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function createStream($verb, $actorId = null)
    {
        $stream = FD::stream();
        $tpl = $stream->getTemplate();
        $actor = FD::user($actorId);

        $registry = FD::registry();
        $registry->set('milestone', $this);

        if ($this->type == SOCIAL_TYPE_USER) {
            $user = FD::user($this->uid);

            // Cache the user data into the params
            $registry->set('user', $user);
        } else {
            // Get the cluster depending on the type
            $cluster = FD::cluster($this->type, $this->uid);

            // this is a cluster stream and it should be viewable in both cluster and user page.
            $tpl->setCluster($cluster->id, $this->type, $cluster->type);

            // Cache the cluster data into the params
            $registry->set($this->type, $cluster);
        }

        // Set the actor
        $tpl->setActor($actor->id, SOCIAL_TYPE_USER);

        // Set the context
        $tpl->setContext($this->id, 'tasks');

        // Set the verb
        $tpl->setVerb($verb);

        // Set the params to cache the group data
        $tpl->setParams($registry);

        // since this is a cluster and user stream, we need to call setPublicStream
        // so that this stream will display in unity page as well
        // This stream should be visible to the public
        $tpl->setAccess('core.view');

        $stream->add($tpl);
    }

    /**
     * Central method to remove previously created stream.
     * @param  string   $verb   The verb for the stream.
     */
    public function removeStream($verb)
    {
        FD::stream()->delete($this->id, 'tasks', '', $verb);
    }

    /**
     * Retrieves the content
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getContent()
    {
        // Apply e-mail replacements
        $content = FD::string()->replaceEmails($this->description);

        // Apply hyperlinks
        $content = FD::string()->replaceHyperlinks($content);

        // Apply bbcode
        $content = FD::string()->parseBBCode($content, array('code' => true, 'escape' => false));

        // Apply line break to the message
        $content = nl2br($content);

        return $content;
    }
}
