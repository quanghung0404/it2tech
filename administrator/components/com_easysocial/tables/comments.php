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
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/tables/table');

class SocialTableComments extends SocialTable
{
    public $id = null;
    public $element = null;
    public $uid = null;
    public $comment = null;
    public $created_by = null;
    public $created = null;
    public $depth = null;
    public $parent = null;
    public $child = null;
    public $lft = null;
    public $rgt = null;
    public $params = null;
    public $stream_id = null;

    // flag to tell if store need to trigger onBeforeCommentSave and onAfterCommentSave
    public $_trigger = true;

    public function __construct($db)
    {
        parent::__construct('#__social_comments', 'id', $db);
    }

    /**
     * Retrieves the comment author object
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getAuthor()
    {
        $user = FD::user($this->created_by);

        return $user;
    }

    public function store($updateNulls = false)
    {
        if (!$this->params instanceof SocialRegistry) {
            $this->params = FD::registry($this->params);
        }

        $this->params = $this->params->toString();

        $isNew = false;

        if (empty($this->id)) {
            $isNew = true;
        }

        // Get the necessary group
        $namespace  = explode('.', $this->element);
        $group      = isset($namespace[1]) ? $namespace[1] : SOCIAL_APPS_GROUP_USER;

        FD::apps()->load($group);

        if ($isNew && $this->_trigger) {
            if (!empty($this->parent)) {
                $parent = $this->getParent();

                if ($parent) {
                    $this->depth = $parent->depth + 1;

                    $parent->addChildCount();
                }
            }

            $this->setBoundary();

            // Get the dispatcher object
            $dispatcher     = FD::dispatcher();
            $args           = array(&$this);

            // @trigger: onBeforeCommentSave
            $dispatcher->trigger($group, 'onBeforeCommentSave', $args);
        }

        $state = parent::store();

        if (!$state) {
            return false;
        }

        if ($isNew && $this->_trigger) {
            // @trigger: onAfterCommentSave
            $dispatcher->trigger($group, 'onAfterCommentSave', $args);
        }

        return $state;
    }

    /*
     * tell store function not to trigger onBeforeCommentSave and onAfterCommentSave
     */
    public function offTrigger()
    {
        $this->_trigger = false;
    }

    // No chainability
    public function update(array $newData)
    {
        // IMPORTANT:
        // No escape is required here as we store the data as is

        // General loop to update the rest of the new data
        foreach($newData as $key => $value)
        {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        $state = $this->store();

        if (!$state) {
            return false;
        }

        return true;
    }

    /**
     * Overwrite of the original delete function to include more hooks
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return  
     */
    public function delete($pk = null)
    {
        $arguments  = array(&$this);

        // Trigger beforeDelete event
        $dispatcher = FD::dispatcher();
        $dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onBeforeDeleteComment', $arguments);

        $state = parent::delete($pk);

        if ($state) {
            // Clear out all the likes for this comment
            $likesModel = FD::model('likes');
            $likesModel->delete($this->uid, 'comments');

            // Delete files related to this comment
            $filesModel = ES::model('Files');
            $filesModel->deleteFiles($this->id, 'comments');

            // Trigger afterDelete event
            $dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onAfterDeleteComment', $arguments);
        }

        return $state;
    }

    public function like()
    {
        $dispatcher = FD::dispatcher();

        $likesLib = FD::likes($this->id, 'comments', 'like', SOCIAL_APPS_GROUP_USER);

        $hasLiked = $likesLib->hasLiked();

        $likesTable = FD::table('likes');
        $likesTable->load(array('type' => 'comments.user.like', 'uid' => $this->id));

        $beforeTrigger = $hasLiked ? 'onBeforeUnlikeComment' : 'onBeforeLikeComment';

        $dispatcher->trigger(SOCIAL_APPS_GROUP_USER, $beforeTrigger, array($this->element, $this->uid, $this, $likesTable));

        $state = $likesLib->toggle();

        if (!$state) {
            return false;
        }

        $afterTrigger = $hasLiked ? 'onAfterUnlikeComment' : 'onAfterLikeComment';

        $dispatcher->trigger(SOCIAL_APPS_GROUP_USER, $afterTrigger, array($this->element, $this->uid, $this, $likesTable));

        return $likesLib;
    }

    /**
     * Displays a single block of comments
     *
     * @since   1.2
     * @access  public
     * @param   Array   An array of arguments to override comment behavior.
     * @return  string  html codes
     */
    // This will return HTML of 1 single comment block
    public function renderHTML($options = array())
    {
        $user = ES::user($this->created_by);
        $isAuthor = $this->isAuthor();
        $likes = ES::likes($this->id, 'comments', 'like', SOCIAL_APPS_GROUP_USER);

        $theme = ES::themes();

        // Determines if the viewer can delete the comment
        $deleteable = isset($options['deleteable']) ? $options['deleteable'] : $isAuthor;

        // Get attachments associated with this comment
        $model = ES::model('Files');
        $attachments = $model->getFiles($this->id, SOCIAL_TYPE_COMMENTS);

        $theme->set('attachments', $attachments);
        $theme->set('deleteable', $deleteable);
        $theme->set('comment', $this);
        $theme->set('user', $user);
        $theme->set('isAuthor', $isAuthor);
        $theme->set('likes', $likes);

        $html = $theme->output('site/comments/item');

        return $html;
    }

    public function getPermalink()
    {
        $base = $this->getParams()->get('url');

        if (empty($base)) {
            return false;
        }

        // FRoute it
        // $base = FRoute::_($base);

        $base .= '#commentid=' . $this->id;

        return $base;
    }

    /**
     * Processes the comments
     *
     * @since   1.0
     * @access  public
     *
     * @return  string  The comment's formatted string.
     */
    public function getComment()
    {
        // Set the comment data on a variable
        $comment = $this->comment;

        // Load up the string library
        $stringLib = ES::get('string');

        // Determine if read more is needed.
        $readmore = JString::strlen($comment) > 150;

        // 1.2.17 Update
        // We truncate to get a short preview content but in actual, we prepare 2 copies of data here.
        // Instead of separating the comments into Shorten and Balance, we do Shorten and Full instead.
        // Shorten contains first 150 character in raw.
        // Full contains the full comment, untruncated and processed.
        // The display part switches the shorten into the full content with JS.
        // Preview doesn't need to be processed.

        // If there's a read more, then we prepare a short preview content
        $preview = '';

        if ($readmore) {
            $preview = JString::substr($comment, 0, 150);
        }

        // Generate a unique id.
        $uid = uniqid();

        $model = ES::model('Tags');
        $tags = $model->getTags($this->id, 'comments');

        $comment = $stringLib->escape($comment);
        $preview = $stringLib->escape($preview);

        // Only process the tags when necessary
        if ($tags) {
            $comment = $stringLib->processTags($tags, $comment, true);
        }

        // Apply hyperlinking on the comment
        $comment = $stringLib->replaceHyperlinks($comment);

        if ($tags) {
            $comment = $stringLib->processSimpleTags($comment);
        }

        // Apply bbcode on the comment
        $config = ES::config();
        $comment = $stringLib->parseBBCode($comment, array('escape' => false, 'emoticons' => $config->get('comments.smileys')));

        $html = $comment;

        if ($readmore) {
            $html = $preview;

            $html .= '<span data-es-comment-full style="display: none;">' . $comment . '</span>';
            $html .= '<span data-es-comment-readmore-' . $uid . ' data-es-comment-readmore>&nbsp;';
            $html .= '<a href="javascript:void(0);" data-es-comment-readmore>&nbsp;' . JText::_('COM_EASYSOCIAL_MORE_LINK') . '</a>';
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Retrieves the date the comment was posted
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return  
     */
    public function getDate($format = '')
    {
        $config = FD::config();

        $date = FD::date($this->created);

        $elapsed = $config->get('comments_elapsed_time', true);

        // If format is passed in as true or false, this means disregard the elapsed time settings and obey the decision of format
        if ($format === true || $format === false) {
            $elapsed = $format;

            $format = '';
        }

        if ($elapsed && empty($format)) {
            return $date->toLapsed();
        }

        if (empty($format)) {
            return $date->toSql(true);
        }

        return $date->format($format);
    }

    public function getApp()
    {
        static $apps = array();

        if (empty($apps[$this->element])) {
            $app = FD::table('apps');

            $app->loadByElement($this->element, SOCIAL_APPS_GROUP_USER, SOCIAL_APPS_TYPE_APPS);

            $apps[$this->element] = $app;
        }

        return $apps[$this->element];
    }

    public function isAuthor($userid = null)
    {
        if (is_null($userid)) {
            $userid = FD::user()->id;
        }

        return $this->created_by == $userid;
    }

    public function getParams()
    {
        if (!$this->params instanceof SocialRegistry) {
            $this->params = FD::registry($this->params);
        }

        return $this->params;
    }

    public function setParam($key, $value)
    {
        if (!$this->params instanceof SocialRegistry) {
            $this->params = FD::registry($this->params);
        }

        $this->params->set($key, $value);

        return true;
    }

    public function getParticipants($options = array())
    {
        $model = FD::model('Comments');

        $recipients = $model->getParticipants($this->uid, $this->element);

        if (!empty($options['excludeSelf'])) {
            $total = count($recipients);
            for($i = 0; $i < $total; $i++)
            {
                if ($recipients[$i] == $this->created_by) {
                    unset($recipients[$i]);
                    break;
                }
            }
        }

        $recipients = array_values($recipients);

        return $recipients;
    }

    public function addChildCount()
    {
        $this->child = $this->child + 1;

        return $this->store();
    }

    public function getParent()
    {
        if (empty($this->parent)) {
            return false;
        }

        $parent = FD::table('Comments');
        $state = $parent->load($this->parent);

        if (!$state) {
            return false;
        }

        return $parent;
    }

    public function setBoundary()
    {
        $model = FD::model('Comments');
        $lastSibling = $model->getLastSibling($this->parent);

        $node = 0;

        if (empty($lastSibling)) {
            $parent = $this->getParent();

            if ($parent) {
                $node = $parent->lft;
            }
        }
        else {
            $node = $lastSibling->rgt;
        }

        if ($node > 0) {
            $model->updateBoundary($node);
        }

        $this->lft = $node + 1;
        $this->rgt = $node + 2;

        return true;
    }

    public function hasChild()
    {
        return $this->child > 0;
    }
}
