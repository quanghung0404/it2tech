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
 * Object mapping for `#__social_clusters` table.
 *
 * @author  Mark Lee <mark@stackideas.com>
 * @since   1.2
 */
class SocialTableClusterCategory extends SocialTable
{
    /**
     * The unique id of the cluster
     * @var int
     */
    public $id          = null;

    /**
     * The cluster type
     * @var string
     */
    public $type        = null;

    /**
     * The title of the category.
     * @var string
     */
    public $title       = null;

    /**
     * The alias of the category.
     * @var string
     */
    public $alias = null;

    /**
     * The description of the category.
     * @var string
     */
    public $description = null;

    /**
     * The creation date of the category
     * @var int
     */
    public $created     = null;

    /**
     * The state of the category.
     * @var string
     */
    public $state = null;

    /**
     * The creator's id.
     * @var int
     */
    public $uid = null;

    /**
     * The ordering of the category.
     * @var int
     */
    public $ordering = null;

    /**
     * Multi site support
     * @var int
     */
    public $site_id = null;

    public function __construct(& $db)
    {
        parent::__construct('#__social_clusters_categories' , 'id' , $db);
    }

    public function load( $keys = null, $reset = true )
    {

        if (! is_array($keys)) {

            // attempt to get from cache
            $catKey = 'cluster.category.'. $keys;

            if (FD::cache()->exists($catKey)) {
                $state = parent::bind(FD::cache()->get($catKey));
                return $state;
            }
        }

        $state = parent::load( $keys, $reset );
        return $state;
    }

    /**
     * Override parent's store function
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     */
    public function store($updateNulls = null)
    {
        // Store this flag first because there are some actions that we might need to do only after saving, and that point isNew() won't return the correct flag.
        $isNew = $this->isNew();

        // Check alias
        $alias = !empty($this->alias) ? $this->alias : $this->title;
        $alias = JFilterOutput::stringURLSafe($alias);

        $model = FD::model('clusters');

        $i = 2;

        do {
            $aliasExists = $model->clusterCategoryAliasExists($alias, $this->id);

            if ($aliasExists) {
                $alias .= '-' . $i++;
            }
        } while($aliasExists);

        $this->alias = $alias;

        if (empty($this->ordering)) {
            $this->ordering = $this->getNextOrder('type = ' . FD::db()->quote($this->type));
        }

        if (empty($this->created)) {
            $this->created = FD::date()->toSql();
        }

        if (empty($this->uid)) {
            $this->uid = FD::user()->id;
        }

        $state = parent::store($updateNulls);

        if ($isNew) {
            // Create default fields
            FD::model('fields')->createDefaultItems($this->id, SOCIAL_TYPE_CLUSTERS, $this->type);
        }

        return $state;
    }

    /**
     * Removes the category avatar
     *
     * @since   1.2
     * @access  public
     * @return  bool    Returns the state of the action
     */
    public function removeAvatar()
    {
        $avatar = FD::Table('Avatar');
        $state = $avatar->load(array('uid' => $this->id , 'type' => SOCIAL_TYPE_CLUSTERS));

        if ($state) {
            return $avatar->delete();
        }

        return false;
    }

    /**
     * Retrieves the ACL for this category
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getAcl()
    {
        $acl = FD::access($this->id, SOCIAL_TYPE_CLUSTERS);

        return $acl;
    }

    /**
     * Retrieves the total number of nodes contained within this category.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalNodes($options = array())
    {
        static $total   = array();

        $index  = $this->id;

        if (!isset($total[$index])) {
            $model = FD::model('Clusters');

            $total[$index] = $model->getTotalNodes($this->id , $options);
        }

        return $total[$index];
    }

    /**
     * Retrieves the permalink of a category
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAlias()
    {
        return $this->id . ':' . $this->alias;
    }

    /**
     * Gets the sequence from the current index (sequence does not obey published state while index is reordered from published state)
     *
     * @since   1.0
     * @access  public
     * @param   int     Current index
     * @param   string  Mode/event to check against
     * @return  int     The reverse mapped sequence
     */
    public function getSequenceFromIndex($index, $mode = null)
    {
        $steps = $this->getSteps($mode);

        if (!isset($steps[$index - 1])) {
            return 1;
        }

        return $steps[$index - 1]->sequence;
    }

    /**
     * Logics to store a profile avatar.
     *
     * @since   1.0
     * @access  public
     * @author  Mark Lee <mark@stackideas.com>
     */
    public function uploadAvatar($file)
    {
        $avatar = FD::table('Avatar');
        $state = $avatar->load(array('uid' => $this->id , 'type' => SOCIAL_TYPE_CLUSTERS));

        if (!$state) {
            $avatar->uid = $this->id;
            $avatar->type = SOCIAL_TYPE_CLUSTERS;

            $avatar->store();
        }

        // Determine the state of the upload.
        $state = $avatar->upload($file);

        if (!$state) {
            $this->setError(JText::_('COM_EASYSOCIAL_GROUPS_CATEGORY_ERROR_UPLOADING_AVATAR'));
            return false;
        }

        // Store the data.
        $avatar->store();

        return;
    }

    /**
     * Retrieves the total number of steps for this particular profile type.
     *
     * Example:
     * <code>
     * <?php
     * $table   = FD::table('Profile');
     * $table->load(JRequest::getInt('id'));
     *
     * // Returns the count in integer.
     * $table->getTotalSteps();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  int     The number of steps involved for this profile type.
     */
    public function getTotalSteps($mode = null)
    {
        static $total = array();

        $totalKey = empty($mode) ? 'all' : $mode;

        if (!isset($total[$totalKey])) {
            $model = FD::model('Fields');
            $total[$totalKey] = $model->getTotalSteps($this->id , SOCIAL_TYPE_CLUSTERS , $mode);
        }

        return $total[$totalKey];
    }

    /**
     * Checks if this step is valid depending on the mode/event
     *
     * @since   1.0
     * @access  public
     * @param   int     Step id
     * @param   string  Mode/event to check against
     * @return  bool    True if it is valid
     */
    public function isValidStep($step, $mode = null)
    {
        $db = FD::db();

        $sql = $db->sql();

        $sql->select('#__social_fields_steps')
            ->where('uid', $this->id)
            ->where('type', SOCIAL_TYPE_CLUSTERS)
            ->where('state', 1)
            ->where('sequence', $step);

        if (!empty($mode)) {
            $sql->where('visible_' . $mode, 1);
        }

        $db->setQuery($sql);

        $result = $db->loadResult();

        return !empty($result);
    }

    /**
     * Retrieves the list of steps for this particular profile type.
     *
     * Example:
     * <code>
     * <?php
     * $table   = FD::table('Profile');
     * $table->load(JRequest::getInt('id'));
     *
     * // Returns the steps for a particular profile type.
     * $table->getSteps();
     * ?>
     * </code>
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return  array   An array of SocialTableWorkflow objects.
     */
    public function getSteps($type = null)
    {
        // Load language file from the back end as the steps title are most likely being translated.
        JFactory::getLanguage()->load('com_easysocial' , JPATH_ROOT . '/administrator');

        $model = FD::model('Steps');
        $steps = $model->getSteps($this->id , SOCIAL_TYPE_CLUSTERS , $type);

        return $steps;
    }

    /**
     * Check if this profile have avatar uploaded
     *
     * @since   1.0
     * @access  public
     * @return  bool    True if this profile have avatar uploaded
     */
    public function hasAvatar()
    {
        $avatar = FD::Table('Avatar');
        $state = $avatar->load(array('uid' => $this->id , 'type' => SOCIAL_TYPE_CLUSTERS));

        return (bool) $state;
    }

    /**
     * Retrieves the profile avatar.
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function getAvatar($size = SOCIAL_AVATAR_MEDIUM)
    {
        $avatar = FD::Table('Avatar');
        $state = $avatar->load(array('uid' => $this->id , 'type' => SOCIAL_TYPE_CLUSTERS));

        if (!$state) {
            return $this->getDefaultAvatar($size);
        }

        return $avatar->getSource($size);
    }

    /**
     * Bind the access for a category node.
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function bindCategoryAccess($type = 'create', $profiles)
    {
        // Delete all existing create access for this category first.
        $model = FD::model('ClusterCategory');

        $model->insertAccess($this->id, $type, $profiles);

        return true;
    }

    /**
     * Binds the access for this group category
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function bindAccess($post)
    {
        if (!is_array($post) || empty($post)) {
            return false;
        }

        // Load up the access table binding.
        $access = FD::table('Access');

        // Try to load the access records.
        $access->load(array('uid' => $this->id, 'type' => SOCIAL_TYPE_CLUSTERS));

        // Load the registry
        $registry = FD::registry($access->params);

        foreach ($post as $key => $value) {
            $key = str_ireplace('_', '.', $key);

            $registry->set($key, $value);
        }

        $access->uid = $this->id;
        $access->type = SOCIAL_TYPE_CLUSTERS;
        $access->params = $registry->toString();

        // Try to store the access item
        if (!$access->store()) {
            $this->setError($access->getError());

            return false;
        }

        return true;
    }


    /**
     * Retrieves the category access
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function hasAccess($type = 'create', $profileId)
    {
        // Delete all existing create access for this category first.
        $model = FD::model('ClusterCategory');

        $accessible = $model->hasAccess($this->id, $type, $profileId);

        return $accessible;
    }

    /**
     * Retrieves the category access
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function getAccess($type = 'create')
    {
        $model = FD::model('ClusterCategory');

        $ids = $model->getAccess($this->id, $type);

        return $ids;
    }

    /**
     * Override parent's delete behavior
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function delete($pk = null)
    {
        $state = parent::delete($pk);

        if (!$state) {
            return false;
        }

        // Delete all existing create access for this category first.
        FD::model('ClusterCategory')->deleteAccess($this->id);

        // Delete this categories fields.
        FD::model('Fields')->deleteFields($this->id, SOCIAL_TYPE_CLUSTERS);

        return $state;
    }

    /**
     * Retrieves the default avatar for this cluster category
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   string  $size   The size of the avatar to retrieve.
     * @return  string          The uri of the avatar.
     */
    public function getDefaultAvatar($size = SOCIAL_AVATAR_MEDIUM)
    {
        $app = JFactory::getApplication();

        $file = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/clusterscategory/' . $size . '.png';
        $uri = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/clusterscategory/' . $size . '.png';

        if (JFile::exists($file)) {
            $default = $uri;
        } else {
            $default = rtrim(JURI::root() , '/') . FD::config()->get('avatars.default.clusterscategory.' . $size);
        }

        return $default;
    }

    /**
     * Check if this record is new.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean   True if it is a new record.
     */
    public function isNew()
    {
        return empty($this->id);
    }
}
