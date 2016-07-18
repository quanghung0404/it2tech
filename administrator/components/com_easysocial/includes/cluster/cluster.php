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

FD::import('admin:/includes/indexer/indexer');

abstract class SocialCluster
{
    /**
     * The clusters's unique id.
     * @var int
     */
    public $id = null;

    /**
     * The cluster category id.
     * @var int
     */
    public $category_id = null;

    /**
     * The cluster type.
     * @var int
     */
    public $cluster_type = null;

    /**
     * The creator unique id.
     * @var int
     */
    public $creator_uid = null;

    /**
     * The creator type.
     * @var int
     */
    public $creator_type = null;

    /**
     * The cluster's title.
     * @var string
     */
    public $title = null;

    /**
     * The cluster's description.
     * @var string
     */
    public $description = null;

    /**
     * The cluster's alias.
     * @var string
     */
    public $alias = null;

    /**
     * The cluster's hits.
     * @var string
     */
    public $hits = null;

    /**
     * The cluster's state.
     * @var boolean
     */
    public $state = null;

    /**
     * The cluster's featured state.
     * @var boolean
     */
    public $featured = null;

    /**
     * The cluster's creation date.
     * @var string
     */
    public $created = null;

    /**
     * The cluster's type.
     * @var string
     */
    public $type = null;

    /**
     * Stores the avatar sizes available on a cluster.
     * @var string
     */
    public $avatarSizes = array('small', 'medium', 'large', 'square');

    /**
     * Stores the avatars of a cluster.
     * @var string
     */
    public $avatars = array('small' => '', 'medium' => '', 'large' => '', 'square' => '');

    /**
     * Stores the cover photo of a cluster.
     * @var string
     */
    public $cover = null;

    /**
     * Stores the params of a cluster.
     * @var string
     */
    public $params = null;

    /**
     * The group's secret key.
     * @var int
     */
    public $key = null;

    /**
     * Parent id of this cluster.
     * @var integer
     */
    public $parent_id = null;

    /**
     * Parent type of this cluster.
     * @var string
     */
    public $parent_type = null;

    /**
     * Longitude value of this cluster.
     * @var float
     */
    public $longitude = null;

    /**
     * Latitude value of this cluster.
     * @var float
     */
    public $latitude = null;

    /**
     * Address of this cluster.
     * @var string
     */
    public $address = null;

    /**
     * The group's fields.
     * @var Array
     */
    public $fields = array();

    /**
     * Stores the object mapping.
     * @var SocialTableCluster
     */
    protected $table = null;

    /**
     * Determines the storage type for the avatars
     * @var string
     */
    protected $avatarStorage = 'joomla';

    public function __construct()
    {
        $this->config = ES::config();
    }

    /**
     * Initializes the provided properties into the existing object. Instead of
     * trying to query to fetch more info about this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   object  $params     A standard object with key / value binding.
     */
    public function initParams(&$params)
    {
        // Get all properties of this object
        $properties = get_object_vars($this);

        // Bind parameters to the object
        foreach($properties as $key => $val) {
            if (isset($params->$key)) {
                $this->$key = $params->$key;
            }
        }

        // Bind params json object here
        $this->_params->loadString($this->params);

        // Bind user avatars here.
        foreach($this->avatars as $size => $value) {
            if (isset($params->$size)) {
                $this->avatars[$size] = $params->$size;
            }
        }
    }

    /**
     * Increments the hit counter.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  True if successful.
     */
    public function hit()
    {
        return $this->table->hit();
    }

    /**
     * Retrieves a list of apps for this cluster type.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  array   Array of apps object.
     */
    public function getApps()
    {
        // TODO: Use dbcache instead
        static $apps = array();

        if (!isset($apps[$this->cluster_type])) {
            $model = FD::model('Apps');
            $options = array('group' => $this->cluster_type, 'type' => SOCIAL_APPS_TYPE_APPS, 'state' => SOCIAL_STATE_PUBLISHED);
            $clusterApps = $model->getApps($options);

            $apps[$this->cluster_type] = $clusterApps;
        }

        return $apps[$this->cluster_type];
    }

    /**
     * Retrieve a single app for the cluster
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getApp($element)
    {
        static $apps = array();

        $index = $this->cluster_type . $element;

        if (!isset($apps[$index])) {
            $model = ES::model('Apps');
            $options = array('group' => $this->cluster_type, 'type' => SOCIAL_APPS_TYPE_APPS, 'state' => SOCIAL_STATE_PUBLISHED, 'element' => $element);

            $app = ES::table('App');
            $app->load($options);

            dump($app);
            $apps[$index] = $app;
        }

        return $apps[$index];
    }

    /**
     * Determines if this cluster is new or not.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if this cluster is a new cluster.
     */
    public function isNew()
    {
        return empty($this->id);
    }

    /**
     * Retrieves the join date of a node.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   integer $uid    The node id.
     * @param   string  $type   The node type.
     * @return  SocialDate      The date object of the joined date.
     */
    public function getJoinedDate($uid, $type = SOCIAL_TYPE_USER)
    {
        $node = FD::table('ClusterNode');
        $node->load(array('uid' => $uid, 'type' => $type, 'cluster_id' => $this->id));

        $date = FD::date($node->created);

        return $date;
    }

    /**
     * Creates a new node object for this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   integer $nodeId         The node id.
     * @param   string  $nodeType       The node type.
     * @param   integer $state          The state to set this node.
     * @return  SocialTableClusterNode  The cluster node table object.
     */
    public function createNode($nodeId, $nodeType, $state = SOCIAL_STATE_PUBLISHED)
    {
        $node = FD::table('ClusterNode');

        $node->cluster_id = $this->id;
        $node->uid = $nodeId;
        $node->type = $nodeType;
        $node->state = $state;

        $node->store();

        return $node;
    }

    /**
     * Determines if this cluster has an avatar.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if this cluster has an avatar.
     */
    public function hasAvatar()
    {
        if (isset($this->avatars['small']) && !empty($this->avatars['small'])) {
            return true;
        }

        if (!empty($this->avatar_id)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the default avatar location as it might have template overrides.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   string  $size   The avatar size to retrieve for.
     * @return  string          The avatar uri.
     */
    public function getDefaultAvatar($size)
    {
        static $defaults = null;

        if (!isset($defaults[$size])) {
            $app = JFactory::getApplication();
            $config = FD::config();
            $overriden = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/' . $this->cluster_type . '/' . $size . '.png';
            $uri = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easysocial/avatars/' . $this->cluster_type . '/' . $size . '.png';

            if (JFile::exists($overriden)) {
                $defaults[$size] = $uri;
            }
            else
            {
                $defaults[$size] = rtrim(JURI::root(), '/') . $config->get('avatars.default.' . $this->cluster_type . '.' . $size);
            }
        }

        return $defaults[$size];
    }

    /**
     * Retrieves the user's avatar location
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   string  $size   The avatar size to retrieve for.
     * @return  string          The avatar uri.
     */
    public function getAvatar($size = SOCIAL_AVATAR_MEDIUM)
    {
        $config = FD::config();

        // If the avatar size that is being requested is invalid, return default avatar.
        $default = $this->getDefaultAvatar($size);

        if (!$this->avatars[$size] || empty($this->avatars[$size])) {

            // Check if parent exist and call the parent.
            if ($this->hasParent()) {
                return $this->getParent()->getAvatar($size);
            }

            return $default;
        }

        // Get the path to the avatar storage.
        $container = FD::cleanPath($config->get('avatars.storage.container'));
        $location = FD::cleanPath($config->get('avatars.storage.' . $this->cluster_type));

        // Build the path now.
        $path = $container . '/' . $location . '/' . $this->id . '/' . $this->avatars[$size];

        if ($this->avatarStorage == SOCIAL_STORAGE_JOOMLA) {
            // Build final storage path.
            $absolutePath = JPATH_ROOT . '/' . $path;

            // Detect if this file really exists.
            if (!JFile::exists($absolutePath)) {
                return $default;
            }

            $uri = rtrim(JURI::root(), '/') . '/' . $path;
        }
        else
        {
            $storage = FD::storage($this->avatarStorage);
            $uri = $storage->getPermalink($path);
        }

        return $uri;
    }


    /**
     * Retrieves the photo table for the cluster's avatar.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialTablePhoto    The avatar photo table object.
     */
    public function getAvatarPhoto()
    {
        static $photos = array();

        if (!isset($photos[$this->id])) {
            $model = FD::model('Avatars');
            $photo = $model->getPhoto($this->id, $this->cluster_type);

            $photos[$this->id] = $photo;
        }

        return $photos[$this->id];
    }

    /**
     * Determines if this cluster has a cover photo.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if this cluster has a cover photo.
     */
    public function hasCover()
    {
        return !(empty($this->cover) || empty($this->cover->id));
    }

    /**
     * Get the cover table object for this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialTableCover    The cover table object for this cluster.
     */
    public static function getCoverObject($cluster = null)
    {
        $cover = FD::table('Cover');

        if (!empty($cluster->cover_id)) {
            $coverData = new stdClass();
            $coverData->id = $cluster->cover_id;
            $coverData->uid = $cluster->cover_uid;
            $coverData->type = $cluster->cover_type;
            $coverData->photo_id = $cluster->cover_photo_id;
            $coverData->cover_id = $cluster->cover_cover_id;
            $coverData->x = $cluster->cover_x;
            $coverData->y = $cluster->cover_y;
            $coverData->modified = $cluster->cover_modified;

            $cover->bind($coverData);
        } else {
            $cover->type = $cluster->cluster_type;
        }

        return $cover;
    }

    /**
     * Retrieves the group's cover position.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  integer The position of the cover.
     *
     */
    public function getCoverPosition()
    {
        if (!$this->cover) {
            if ($this->hasParent()) {
                return $this->getParent()->getCoverPosition();
            }

            return 0;
        }

        return $this->cover->getPosition();
    }

    /**
     * Retrieves this cluster's cover uri.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  string  The cover uri.
     *
     */
    public function getCover()
    {
        if (!$this->cover) {
            if ($this->hasParent()) {
                return $this->getParent()->getCover();
            }

            $cover = $this->getDefaultCover();
            return $cover;
        }

        return $this->cover->getSource();
    }

    /**
     * Returns the cover object.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return SocialTableCover    The cover object.
     */
    public function getCoverData()
    {
        if ((empty($this->cover) || empty($this->cover->id)) && $this->hasParent()) {
            return $this->getParent()->cover;
        }

        return $this->cover;
    }

    /**
     * Retrieves the default cover location as it might have template overrides.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  string  The default cover uri.
     */
    public function getDefaultCover()
    {
        static $default = null;

        if (!$default) {
            $app = JFactory::getApplication();
            $config = FD::config();
            $overriden = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/' . $this->cluster_type . '/default.png';
            $uri = rtrim(JURI::root(), '/') . '/templates/' . $app->getTemplate() . '/html/com_easysocial/covers/' . $this->cluster_type . '/default.png';

            if (JFile::exists($overriden)) {
                $default = $uri;
            }
            else {
                $default = rtrim(JURI::root(), '/') . $config->get('covers.default.' . $this->cluster_type . '.default');
            }
        }

        return $default;
    }

    /**
     * Allows deletion of cover.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  True if successful.
     */
    public function deleteCover()
    {
        $state = $this->cover->delete();

        // Reset this user's cover
        $this->cover = FD::table('Cover');

        return $state;
    }

    /**
     * Returns the last creation date of the cluster
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialDate      The created date object.
     */
    public function getCreatedDate()
    {
        $date = FD::get('Date', $this->created);

        return $date;
    }

    /**
     * Retrieves the params for a cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialRegistry  The registry object.
     */
    public function getParams()
    {
        $params = FD::registry($this->params);

        return $params;
    }

    /**
     * Method to standardize cluster object to be similar as a JUser object.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @param  string    $key The key to retrieve.
     * @return Mixed          The value of the key.
     */
    public function getParam($key)
    {
        return $this->getParams()->get($key);
    }

    /**
     * Retrieves the user's real name dependent on the system configurations.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  string  The cluster's title.
     */
    public function getName()
    {
        return $this->title;
    }

    /**
     * Allows caller to remove the cluster avatar.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  True if successful.
     */
    public function removeAvatar()
    {
        $avatar = FD::table('Avatar');
        $state = $avatar->load(array('uid' => $this->id, 'type' => $this->cluster_type));

        if ($state) {
            $state = $avatar->delete();
        }

        return $state;
    }


    /**
     * Override parent's delete implementation if necessary.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  bool    The delete state. True on success, false otherwise.
     */
    public function delete()
    {
        // If deletion was successful, we need to remove it from smart search
        $namespace = 'easysocial.' . $this->cluster_type . 's';

        JPluginHelper::importPlugin('finder');
        $dispatcher = JDispatcher::getInstance();

        $dispatcher->trigger('onFinderAfterDelete', array($namespace, &$this->table));

        $state = $this->table->delete();

        return $state;
    }


    /**
     * Retrieves the custom field value from this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   string  $key    The key of the field.
     * @return  Mixed           The value returned by the field.
     */
    public function getFieldValue($key)
    {
        static $processed = array();

        if (!isset($processed[$this->id])) {
            $processed[$this->id] = array();
        }

        if (!isset($processed[$this->id][$key])) {
            if (!isset($this->fields[$key])) {
                $result = FD::model('Fields')->getCustomFields(array('group' => $this->cluster_type, 'uid' => $this->category_id, 'data' => true , 'dataId' => $this->id , 'dataType' => $this->cluster_type, 'key' => $key));

                $this->fields[$key] = isset($result[0]) ? $result[0] : false;
            }

            $field = $this->fields[$key];

            // Initialize a default property
            $processed[$this->id][$key] = '';

            if ($field) {
                // Trigger the getFieldValue to obtain data from the field.
                $value = FD::fields()->getValue($field, $this->cluster_type);

                $processed[$this->id][$key] = $value;
            }
        }

        return $processed[$this->id][$key];
    }

    /**
     * Retrieves the custom field data from this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   string  $key    The key of the field.
     * @return  Mixed           The data returned by the field.
     */
    public function getFieldData($key)
    {
        static $processed = array();

        if (!isset($processed[$this->id])) {
            $processed[$this->id] = array();
        }

        if (!isset($processed[$this->id][$key])) {
            if (!isset($this->fields[$key])) {
                $result = FD::model('Fields')->getCustomFields(array('group' => $this->cluster_type, 'uid' => $this->category_id, 'data' => true , 'dataId' => $this->id , 'dataType' => $this->cluster_type, 'key' => $key));


                $this->fields[$key] = isset($result[0]) ? $result[0] : false;
            }

            $field = $this->fields[$key];

            // Initialize a default property
            $processed[$this->id][$key] = '';

            if ($field) {
                // Trigger the getFieldValue to obtain data from the field.
                $value = FD::fields()->getData($field, $this->cluster_type);

                $processed[$this->id][$key] = $value;
            }
        }

        return $processed[$this->id][$key];
    }

    /**
     * Retrieves the category of this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialTableClusterCategory  The cluster category table object.
     */
    public function getCategory()
    {
        static $categories = array();

        if (!isset($categories[$this->category_id])) {
            $category = FD::table('ClusterCategory');
            $category->load($this->category_id);

            $categories[$this->category_id] = $category;
        }

        return $categories[$this->category_id];
    }

    /**
     * Preprocess before storing data into the table object.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function save()
    {
        // Determine if this record is a new user by identifying the id.
        $isNew = $this->isNew();

        // Request parent to store data.
        $this->table->bind($this);

        // Try to store the item
        $state = $this->table->store();

        if ($isNew) {
            $this->id = $this->table->id;
        }

        if ($this->state == 1) {
            $namespace = 'easysocial.' . $this->cluster_type . 's';

            JPluginHelper::importPlugin('finder');
            $dispatcher = JDispatcher::getInstance();

            if ($this->type == SOCIAL_GROUPS_INVITE_TYPE) {
                // lets remove this item from smart search
                $dispatcher->trigger('onFinderAfterDelete', array($namespace, &$this->table));
            } else {
                $dispatcher->trigger('onFinderAfterSave', array($namespace, &$this->table, $isNew));
            }
        }

        return $state;
    }

    /**
     * Allows caller to remove all cluster associations with custom fields.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  True if successful.
     */
    public function deleteCustomFields()
    {
        $model = FD::model('Fields');
        $state = $model->deleteFields($this->id, $this->cluster_type);

        return $state;
    }


    /**
     * Delete stream related to this cluster
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function deleteStream()
    {
        $model = FD::model('Clusters');
        $state = $model->deleteClusterStream($this->id, $this->cluster_type);

        return $state;
    }

    /**
     * Deletes all the news from the cluster
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function deleteNews()
    {
        $model = FD::model('ClusterNews');
        $state = $model->delete($this->id);

        return $state;
    }

    /**
     * Determines if this cluster is featured.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  True if this cluster is featured.
     */
    public function isFeatured()
    {
        return (bool) $this->featured;
    }

    /**
     * Allows caller to set the cluster as a featured item.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function setFeatured()
    {
        $this->table->featured = true;

        $state = $this->table->store();

        // @TODO: Push into the stream that a group is set as featured group.
        if ($state) {
            $this->createStream(null, 'featured');
        }

        return $state;
    }

    /**
     * Allows caller to remove the cluster from being a featured item.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function removeFeatured()
    {
        $this->table->featured = false;

        $state = $this->table->store();

        return $state;
    }

    /**
     * Allows caller to switch owners.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   integer $newUserId  The new owner id.
     * @param   integer $type       The type of the owner.
     * @return  bool                True if successful.
     */
    public function switchOwner($newUserId, $type = SOCIAL_TYPE_USER)
    {
        $this->creator_uid = $newUserId;
        $this->creator_type = $type;

        $this->table->bind($this);
        $this->table->store();

        // Check if the member record exists for this table.
        $node = FD::table('ClusterNode');
        $exists = $node->load(array('cluster_id' => $this->id, 'uid' => $this->creator_uid, 'type' => $this->creator_type));

        // Remove other "owners" previously
        $model = FD::model('Clusters');
        $model->removeOwners($this->id);

        if (!$exists) {
            // Insert a new owner record
            $node->cluster_id = $this->id;
            $node->uid = $this->creator_uid;
            $node->type = $this->creator_type;
            $node->state = SOCIAL_STATE_PUBLISHED;
            $node->owner = SOCIAL_STATE_PUBLISHED;
            $node->admin = SOCIAL_STATE_PUBLISHED;
        } else {
            $node->owner = SOCIAL_STATE_PUBLISHED;
            $node->admin = SOCIAL_STATE_PUBLISHED;
        }

        return $node->store();
    }

    /**
     * Determines if the group is published.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if is published.
     */
    public function isPublished()
    {
        return $this->state == SOCIAL_CLUSTER_PUBLISHED;
    }

    public function isPending()
    {
        return $this->state == SOCIAL_CLUSTER_PENDING;
    }

    /**
     * Allows caller to remove a node item.
     *
     * @since   1.0
     * @access  public
     * @param   integer $nodeId     The node id.
     * @param   string  $nodeType   The node type.
     * @return  boolean             True if successful.
     */
    public function deleteNode($nodeId, $nodeType = SOCIAL_TYPE_USER)
    {
        $node = FD::table('ClusterNode');
        $node->load(array('cluster_id' => $this->id, 'uid' => $nodeId, 'type' => $nodeType));

        $state = $node->delete();

        return $state;
    }

    /**
     * Allows caller to remove all node item associations.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function deleteNodes()
    {
        $model = FD::model('Clusters');
        $state = $model->deleteNodeAssociation($this->id);

        return $state;
    }

    /**
     * Allows caller to remove videos
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function deleteVideos($pk = null)
    {
        $db = ES::db();
        $sql = $db->sql();

        // Delete cluster albums
        $sql->clear();

        $sql->select('#__social_videos');
        $sql->where('uid', $this->id);
        $sql->where('type', $this->cluster_type);
        $db->setQuery($sql);

        $videos = $db->loadObjectList();

        if (!$videos) {
            return true;
        }

        foreach ($videos as $row) {
            $video = ES::video($row->uid, $row->type, $row->id);
            $video->delete();
        }

        return true;
    }

    /**
     * Allows caller to remove all photos albums.
     *
     * @author  Sam <sam@stackideas.com>
     * @since   1.3
     * @access  public
     * @return  boolean True if successful.
     */
    public function deletePhotoAlbums( $pk = null )
    {
        $db     = FD::db();
        $sql    = $db->sql();

        // Delete cluster albums
        $sql->clear();
        $sql->select( '#__social_albums' );
        $sql->where( 'uid' , $this->id );
        $sql->where( 'type' , $this->cluster_type );
        $db->setQuery( $sql );

        $albums = $db->loadObjectList();

        if( $albums )
        {
            foreach( $albums as $row )
            {
                $album  = FD::table( 'Album' );
                $album->load( $row->id );

                $album->delete();
            }
        }

        return true;
    }


    /**
     * Allows caller to unpublish this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function unpublish()
    {
        $this->table->state = SOCIAL_CLUSTER_UNPUBLISHED;

        $state = $this->table->store();

        if ($state) {
            $this->state = SOCIAL_CLUSTER_UNPUBLISHED;
        }

        return $state;
    }

    /**
     * Allows caller to publish this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  boolean True if successful.
     */
    public function publish()
    {
        $this->table->state = SOCIAL_CLUSTER_PUBLISHED;

        $state = $this->table->store();

        if ($state) {
            $this->state = SOCIAL_CLUSTER_PUBLISHED;
        }

        return $state;
    }

    /**
     * Get the alias of this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  string  The alias of this cluster.
     */
    public function getAlias()
    {
        // Ensure that the name is a safe url.
        $alias = $this->id . ':' . JFilterOutput::stringURLSafe($this->alias);

        return $alias;
    }


    /**
     * Gets the SocialAccess object.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialAccess The SocialAccess object.
     *
     */
    public function getAccess()
    {
        static $data = null;

        if (!isset($data[$this->category_id])) {
            $access = FD::access($this->category_id, SOCIAL_TYPE_CLUSTERS);

            $data[$this->category_id] = $access;
        }

        return $data[$this->category_id];
    }

    /**
     * Returns the cluster type
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getType()
    {
        return $this->cluster_type;
    }

    /**
     * Returns the total number of videos in this cluster
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function getTotalVideos()
    {
        static $total = array();

        if (!isset($total[$this->id])) {
            $model = ES::model('Videos');
            $options = array('uid' => $this->id, 'type' => $this->cluster_type);

            $total[$this->id] = $model->getTotalVideos($options);
        }

        return $total[$this->id];
    }

    /**
     * Return the total number of albums in this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  integer  The total number of albums.
     */
    public function getTotalAlbums()
    {
        static $total = array();

        $sid = $this->id;

        if (!isset($total[$sid])) {
            $model = FD::model('Albums');
            $options = array('uid' => $this->id, 'type' => $this->cluster_type);

            $total[$sid] = $model->getTotalAlbums($options);
        }

        return $total[$sid];
    }

    /**
     * Return the total number of photos in this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   boolean  $daily  If true, only get total number of photos daily.
     * @return  integer          The total number of photos.
     */
    public function getTotalPhotos($daily = false)
    {
        static $total = array();

        $sid = $this->id . $daily;

        if (!isset($total[$sid])) {
            $model = FD::model('Photos');
            $options = array('uid' => $this->id, 'type' => $this->cluster_type);

            if ($daily) {
                $today = FD::date()->toMySQL();
                $date = explode(' ', $today);

                $options['day'] = $date[0];
            }

            $total[$sid] = $model->getTotalPhotos($options);
        }

        return $total[$sid];
    }

    /**
     * Binds the user custom fields.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   array   $data   An array of data that is being posted.
     * @return  bool            True on success, false otherwise.
     */
    public function bindCustomFields($data)
    {
        // Get the registration model.
        $model = FD::model('Fields');

        // Get the field id's that this profile is allowed to store data on.
        $fields = $model->getStorableFields($this->category_id , SOCIAL_TYPE_CLUSTERS);

        // If there's nothing to process, just ignore.
        if (!$fields) {
            return false;
        }

        // Let's go through all the storable fields and store them.
        foreach ($fields as $fieldId) {
            $key = SOCIAL_FIELDS_PREFIX . $fieldId;

            if (!isset($data[$key])) {
                continue;
            }

            $value = isset($data[$key]) ? $data[$key] : '';

            // Test if field really exists to avoid any unwanted input
            $field = FD::table('Field');

            // If field doesn't exist, just skip this.
            if (!$field->load($fieldId)) {
                continue;
            }

            // Let the table object handle the data storing
            $field->saveData($value, $this->id, $this->cluster_type);
        }
    }

    /**
     * Retrieve the creator of this group.
     * Need to support creator_type in the future. Assuming user for now.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @return  SocialUser   The creator object of this cluster.
     */
    public function getCreator()
    {
        $user = FD::user($this->creator_uid);

        return $user;
    }

    /**
     * Determines if the provided user id is the owner of this cluster.
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   int     $userId The user's id to check against.
     * @return  bool            True if the user is the owner.
     */
    public function isOwner($userId = null)
    {
        $userId = FD::user($userId)->id;

        // To test for ownership, just test against the uid and type
        if ($this->creator_uid == $userId && $this->creator_type == SOCIAL_TYPE_USER) {
            return true;
        }

        return false;
    }



    /**
     * Determines if the provided user id is an admin of this cluster
     *
     * @author  Mark Lee <mark@stackideas.com>
     * @since   1.2
     * @access  public
     * @param   int     $userId The user's id to check against.
     * @return  bool            True if the user is the owner.
     */
    public function isAdmin( $userId = null )
    {
        $user   = FD::user( $userId );
        $userId = $user->id;

        if (isset( $this->admins[ $userId ] ) || $user->isSiteAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Creates the owner node.
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     * @param  int  $userId The owner id.
     * @return bool         True if successful.
     */
    public function createOwner($userId = null)
    {
        if (empty($userId)) {
            $userId = FD::user()->id;
        }

        $member = FD::table('clusternode');

        $state = $member->load(array('cluster_id' => $this->id, 'uid' => $userId, 'type' => SOCIAL_TYPE_USER));

        $member->cluster_id = $this->id;
        $member->uid = $userId;
        $member->type = SOCIAL_TYPE_USER;
        $member->state = SOCIAL_STATE_PUBLISHED;
        $member->admin = true;
        $member->owner = true;

        $member->store();

        return $member;
    }

    /**
     * Returns a Google Maps link based on the address
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3
     * @access public
     * @return string    The url of Google Maps.
     */
    public function getAddressLink()
    {
        if (!empty($this->address)) {
            return 'https://maps.google.com/?q=' . urlencode($this->address);
        }

        return 'javascript:void(0);';
    }

    public function getParent()
    {
        if (empty($this->parent_id) || empty($this->parent_type)) {
            return false;
        }

        return FD::cluster($this->parent_type, $this->parent_id);
    }

    public function hasParent()
    {
        return !empty($this->parent_id) && !empty($this->parent_type);
    }
}
