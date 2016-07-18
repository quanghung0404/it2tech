<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogPost extends EasyBlog
{
	// @debug: Port list (remove when done)
	static $_methodsToPort = array();
	static $_propsToPort = array();

	// Data properties
	public $access;
	public $uid;
	public $id;
	public $created_by;
	public $created;
	public $modified;
	public $title;
	public $permalink;
	public $content;
	public $intro;
	public $excerpt;
	public $category_id;
	public $published;
	public $state;
	public $publish_up;
	public $publish_down;
	public $ordering;
	public $vote;
	public $hits;
	public $allowcomment;
	public $subscription;
	public $frontpage;
	public $isnew;
	public $blogpassword;
	public $latitude;
	public $longitude;
	public $address;
	public $posttype;
	public $source_id;
	public $source_type;
	public $robots;
	public $copyrights;
	public $image;
	public $language;
	public $locked;
	public $ip;
	public $doctype;
	public $document;
	public $revision_id;
	public $categories;
	public $tags;
	public $fields;
	public $keywords;
	public $description;
	public $custom_title;
	public $send_notification_emails;
	public $autoposting;
	public $association;

	public $meta;

	static $enumerations = array(
		'id'           => array('bindable' => false, 'linked' => false),
		'created_by'   => array('bindable' => true , 'linked' => false),
		'created'      => array('bindable' => true , 'linked' => false),
		'modified'     => array('bindable' => false, 'linked' => false),
		'title'        => array('bindable' => true , 'linked' => false),
		'permalink'    => array('bindable' => true , 'linked' => false),
		'content'      => array('bindable' => true , 'linked' => false),
		'intro'        => array('bindable' => true , 'linked' => false),
		'excerpt'      => array('bindable' => true , 'linked' => false),
		'category_id'  => array('bindable' => true , 'linked' => false),
		'published'    => array('bindable' => true , 'linked' => false),
		'state'        => array('bindable' => false, 'linked' => false), // 5.0
		'publish_up'   => array('bindable' => true , 'linked' => false),
		'publish_down' => array('bindable' => true , 'linked' => false),
		'ordering'     => array('bindable' => true , 'linked' => false),
		'vote'         => array('bindable' => false, 'linked' => false),
		'hits'         => array('bindable' => false, 'linked' => false),
		'access'       => array('bindable' => true , 'linked' => false),
		'allowcomment' => array('bindable' => true , 'linked' => false),
		'subscription' => array('bindable' => true , 'linked' => false),
		'frontpage'    => array('bindable' => true , 'linked' => false),
		'isnew'        => array('bindable' => false, 'linked' => false),
		'blogpassword' => array('bindable' => true , 'linked' => false),
		'latitude'     => array('bindable' => true , 'linked' => false),
		'longitude'    => array('bindable' => true , 'linked' => false),
		'address'      => array('bindable' => true , 'linked' => false),
		'posttype'     => array('bindable' => true , 'linked' => false), // This is microblog.
		'source_id'    => array('bindable' => true , 'linked' => false), // 5.0
		'source_type'  => array('bindable' => true , 'linked' => false), // 5.0
		'robots'       => array('bindable' => true , 'linked' => false),
		'copyrights'   => array('bindable' => true , 'linked' => false),
		'image'        => array('bindable' => true , 'linked' => false),
		'language'     => array('bindable' => true , 'linked' => false),
		'locked'       => array('bindable' => false, 'linked' => false),
		'ip'           => array('bindable' => false, 'linked' => false),
		'doctype'      => array('bindable' => true , 'linked' => false), // 5.0
		'document'     => array('bindable' => true , 'linked' => false), // 5.0
		'revision_id'  => array('bindable' => true , 'linked' => false), // 5.0
		'autoposting'  => array('bindable' => true , 'linked' => false),
		'categories'   => array('bindable' => true , 'linked' => true),
		'tags'         => array('bindable' => true , 'linked' => true),
		'fields'       => array('bindable' => true , 'linked' => true), // 5.0
		'keywords'     => array('bindable' => true , 'linked' => true),
		'description'  => array('bindable' => true , 'linked' => true),
		'custom_title' => array('bindable' => true , 'linked' => true),
		'send_notification_emails' => array('bindable' => true, 'linked' => false),
		'association' => array('bindable' => false, 'linked' => false)
	);

	// Bind options
	static $defaultBindOptions = array(

		// If true, allow binding even from non-bindable properties.
		// This is useful for migrators.
		'force' => false,

		// TODO: Rename blog_contribute to isssitewide in composer form.
		'remap' => array(
			array('eb_language'    , 'language')
		)
	);

	// Save options
	static $defaultSaveOptions = array(
		'normalizeData' => true,
		'validateData' => true,
		'updateModifiedTime' => true,
		'applyDateOffset' => false,
		'checkEmptyTitle' => true,
		'checkBlockedWords' => true,
		'checkMinContentLength' => true,
		'logUserIpAddress' => true,
		'skipCreateRevision' => false,
		'useAuthorAsRevisionOwner' => false,
		'skipCustomFields' => false,
		'skipNotifications' => false,
		'triggerPlugins' => true,
		'processAutopost' => true,
		'silent' => false // Quietly save into db without executing postSave.
	);

	// Data source
	static $blank;
	public $original;
	public $workbench;
	public $revision;
	public $post;


	private $saveOptions = array();
	private $_debug = false;

	// Cache items, used to store data from caller.
	public static $commentCounts = array();
	public static $comments = array();
	public static $ratings = array();
	public static $customFields = array();
	public static $postMetas = array();
	public static $postVotes = array();

	// This stores the formatted contents for this post.
	private $formattedContents = array();
	private $formattedIntros = array();

	// Extended from EasyBlog class
	public $config;
	public $doc;
	public $app;
	public $input;
	public $my;


	// Globals
	public $user;
	public $acl;
	public $debug = false;


	public function __construct($uid = null, $userId = null)
	{
		// Load site's language file
		EB::loadLanguages();

		// This will call EasyBlog class to construct $config, $doc, $app, $input, $my.
		parent::__construct();

		// Globals
		$this->uid = $uid;

		// The author of this item
		$this->user = EB::user($userId);

		// The acl of the author
		$this->acl = EB::acl($this->user->id);

		// If this is a new post, we want to create a new workbench
		if (!$uid) {
			$this->createNewWorkbench();
		} else {
			$this->load($uid);
		}

		// Set the post object to the router so that they can easily retrieve it.
		EBR::setPost($this);
	}

	/**
	 * Parses the UID <postId>.<revisionId> into an object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function parseUid($uid)
	{
		// Extract id & revision from uid.
		$parts = explode(".", $uid);

		$obj = new stdClass();
		$obj->postId = $parts[0];
		$obj->revisionId = isset($parts[1]) ? $parts[1] : null;

		return $obj;
	}

	/**
	 * Loads a post item given the uid of the item.
	 * UID is a string representation of:
	 * <postId>.<revisionId>
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load($uid)
	{
		// Get the uid object
		$uid = self::parseUid($uid);
		$postId = $uid->postId;
		$revisionId = $uid->revisionId;

		// Load post
		$post = $this->loadPost($postId);

		if (!$post) {
			return;
		}

		$this->post = $post;

		// we need to reassign few property from the post jtable
		$this->isnew = $post->isnew;

		// If revisionId is not given, assume current revision used by post.
		if (empty($revisionId)) {
			$revisionId = (int) $post->revision_id;
			$this->uid = $post->id . '.' . $revisionId;
		}


		// If revisionId is still empty, this might be a legacy post with no revision binded to them.
		if (empty($revisionId)) {

			$this->checkoutFromPost();

			// This is most likely a legacy post as we need to create a new revision for and existing post without any revisions.
			$this->saveRevision();
			return;

		} else {

			$revision = self::loadRevision($revisionId);

			$this->revision = $revision;
			$this->revision_id = $revisionId;
		}

		$this->checkout();

		$this->hits = (int) $post->hits;
		$this->vote = $post->vote;
		$this->ordering = $post->ordering;
		$this->locked = $post->locked;

		// this is to ensure the source_type will be loaded correctly.
		$this->source_type = ($this->source_type) ? $this->source_type : $post->source_type;
		$this->posttype = ($this->posttype) ? $this->posttype : $post->posttype;
		$this->doctype = ($this->doctype) ? $this->doctype : $post->doctype;

		// Try to load the meta now
		$this->loadMeta();

		if ($this->meta->id) {
			$this->keywords = $this->meta->keywords;
			$this->description = $this->meta->description;
		}

	}

	/**
	 * Loads the JTable Post object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadPost($postId)
	{
		$post = null;
		$exists = false;

		if (EB::cache()->exists($postId, 'post') ) {
			$post = EB::cache()->get($postId, 'post');
			$exists = true;
		} else {
			$post = EB::table('Post');
			$exists = $post->load($postId);
		}

		if (!$exists) {

			// Perhaps the given id is an alias, try to look it up
			$state = $post->load(array('permalink' => $postId));

			if (!$state) {
				return false;
			}

			// Set the post id and revision id.
			$this->uid = $post->id;
			$this->revision_id = $post->revision_id;
		}

		return $post;
	}

	/**
	 * Loads a JTable Revision object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function loadRevision($revisionId)
	{
		$revision = null;

		if (EB::cache()->exists($revisionId, 'revision') ) {
			$revision = EB::cache()->get($revisionId, 'revision');
			$exists = true;
		} else {

			$revision = EB::table('Revision');
			$revision->load($revisionId);
		}

		return $revision;
	}

	/**
	 * Given a permalink, find the post id and load the post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadByPermalink($permalink)
	{
		$db = EB::db();

		// Try to look for the permalink
		$query = array();
		$query[] = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName('#__easyblog_post');
		$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);


		$query = implode(' ', $query);
		$db->setQuery($query);
		$id = (int) $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if (!$id) {
			$permalink = JString::str_ireplace(':', '-', $permalink);

			// Try to look for the permalink
			$query = array();
			$query[] = 'SELECT ' . $db->quoteName('id') . ' FROM ' . $db->quoteName('#__easyblog_post');
			$query[] = 'WHERE ' . $db->quoteName('permalink') . '=' . $db->Quote($permalink);

			$query = implode(' ', $query);

			$db->setQuery($query);
			$id = (int) $db->loadResult();
		}

		if ($id) {
			return $this->load($id);
		}

		return false;
	}

	/**
	 * Renders the post type icon for this post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getIcon($classname = '')
	{
		$posttype = $this->posttype ? $this->posttype : 'standard';

		$theme = EB::template();
		$theme->set('classname', $classname);

		return $theme->output('site/posttype/' . $posttype);
	}

	/**
	 * Loads meta data about the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadMeta()
	{
		// Store the meta data for this post now
		if (isset(self::$postMetas[$this->id])) {
			$meta = self::$postMetas[$this->id];
		} else {
			$meta = EB::table('Meta');
			$meta->loadByType(META_TYPE_POST, $this->id);
		}

		$this->meta = $meta;
	}

	/**
	 * Retrieves a list of fields
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadFields()
	{
	}

	/**
	 * Switches the post to the revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function updateToRevision()
	{

	}

	/**
	 * Creates a new workbench object of itself.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createNewWorkbench()
	{
        // Create blank object
        // Original object clones from blank object when working on a new post.
        $workbench = new stdClass();

        foreach (self::$enumerations as $prop => $enum) {
            $workbench->$prop = null;
        }

        // If this is a new workbench, the isnew state should always be true
        $workbench->isnew = true;

        $this->setWorkbench($workbench);

        $this->categories = array();
        $this->tags = array();
        $this->fields = array();
	}

	/**
	 * Sets the workbench for this current instance
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setWorkbench($workbench)
	{
		// Assign to workbench
		$this->bind($workbench, array('force' => true));

		// Remember original values so that we can
		// check modifications made to this post
		// and normalize the values before saving.
		$this->original = clone $workbench;

		return $workbench;
	}

	/**
	 * Checkout from a specific revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkout()
	{
		// Checkout from revision
		// We check out from revision table instead of post table because it faster.
		// Even revision currently being used by post table will always be identical.
		$this->checkoutFromRevision();
	}

	/**
	 * Checkout from a specific revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkoutFromRevision()
	{
		$workbench = $this->revision->getContent();

		if (! $workbench) {

			// somehow the revision id is exists but the revisions.content is empty.
			// we need to regenerate the revisions.content.
			$this->checkoutFromPost();

		} else {
			$this->setWorkbench($workbench);
		}

		$this->revision_id = $this->revision->id;
	}

	/**
	 * Checkout from a specific post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkoutFromPost()
	{
		$workbench = new stdClass();

		// Get the post data from the property
		$post = $this->post;

		// Populate table properties
		foreach (self::$enumerations as $prop => $enum) {

			// Skip linked properties because they are not part of post table.
			if ($enum['linked']) {
				continue;
			}

			// TO BE REMOVED
			// temporary suppress the notice warning.
			@$workbench->$prop = $post->$prop;
		}

		// Populate categories
		// $categories = $post->getCategories();
		// foreach ($categories as $category) {
		// 	$this->categories[] = $category->id;
		// }

		// // Populate tags
		// $tags = $post->getTags();
		// foreach ($tags as $tag) {
		// 	$this->tags[] = $tag->title;
		// }

		// Get the meta of the post
		$this->loadMeta();

		// Get the fields related to the post
		$this->loadFields();

		// Deal with legacy posts (might not be needed with migrator in place)
		if (empty($this->source_id) && empty($this->source_type)) {
				$this->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;
				$this->source_id   = 0;
		}

		return $this->setWorkbench($workbench);
	}

	/**
	 * Creates a new post whenever the editor is initialized
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($options = array())
	{

		if (!empty($this->uid)) {
			throw EB::exception('Create method cannot be executed on an existing post.');
		}

		$checkACL = isset($options['checkAcl']) ? $options['checkAcl'] : true;

		if ($checkACL) {
			// Ensure that the current user is allowed to create new post
			if (!$this->canCreate()) {
				throw EB::exception('User not allowed to create post.');
			}
		}

		// Set the document type
		$this->doctype = $this->config->get('layout_editor') == 'composer' ? 'ebd' : 'legacy';

		// if there is the doctype overriding flag passed in, we use that.
		if (isset($options['overrideDoctType']) && $options['overrideDoctType']) {
			$this->doctype = $options['overrideDoctType']; // accept 'ebd' or 'legacy' ONLY
		} else {
			// Respect the author's settings
			if ($this->user->getEditor() != 'composer') {
				$this->doctype = 'legacy';
			}
		}

		// Ensures the current state of this post to be blank so that we know this is a new post.
		$this->published = EASYBLOG_POST_BLANK;

		// Save options. lets declare here.
		$saveOptions = array('validateData' => false);

		if (isset($options['skipCustomFields'])) {
			$saveOptions['skipCustomFields'] = $options['skipCustomFields'];
		}

		if (isset($options['overrideAuthorId']) && $options['overrideAuthorId']) {
			$saveOptions['overrideAuthorId'] = $options['overrideAuthorId'];
		}

		if (isset($options['checkAcl']) && $options['checkAcl']) {
			$saveOptions['checkAcl'] = $options['checkAcl'];
		}

		if (isset($options['triggerPlugins'])) {
			$saveOptions['triggerPlugins'] = $options['triggerPlugins'];
		}

		// if (isset($options['useAuthorAsRevisionOwner'])) {
		// 	$saveOptions['useAuthorAsRevisionOwner'] = $options['useAuthorAsRevisionOwner'];
		// }

		// Save post and skip validation while doing so.
		$this->save($saveOptions);

		$this->checkout();
	}

	/**
	 * Binds a posted data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function bind($data, $options = array(), $debug = false)
    {

        $bindOptions = array_merge_recursive(array(), self::$defaultBindOptions, $options);

        // Convert array to object
        if (is_array($data)) {
            $data = (object) $data;
        }

        // Remap data properties.
        foreach ($bindOptions['remap'] as $map) {

            $source = $map[0];
            $target = $map[1];

            if (isset($data->$source)) {
                $data->$target = $data->$source;

                unset($data->source);
            }
        }

        // Go through the list of properties of the post data
        foreach (self::$enumerations as $prop => $enum) {

            // Skip if this property is not bindable.
            // Non-bindable properties are usually those we do not want malicious users
            // to hijack just by altering post request values. For example, we don't want
            // users to alter the id, revision id, publishing state of a post.
            if (!$enum['bindable'] && !$bindOptions['force']) {
                continue;
            }

            if (isset($data->$prop)) {
                $this->$prop = $data->$prop;
            }
        }

        // bind association
		$this->association = array();

		if (isset($data->assoc_postids) && $data->assoc_postids) {
			for($i = 0; $i < count($data->assoc_postids); $i++) {
				if ($data->assoc_postids[$i]) {

					$obj = new stdClass();
					$obj->code = $data->assoc_code[$i];
					$obj->id = $data->assoc_postids[$i];
					$obj->post = $data->assoc_post[$i];

					$this->association[] = $obj;
				}
			}

			if ($this->language != '*' && $this->association) {
				// current selected language.
				$obj = new stdClass();
				$obj->code = $this->language;
				$obj->id = $this->id;
				$obj->post = $this->title;

				$this->association[] = $obj;
			}
		}
    }

	/**
	 * Archives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function trash()
	{
		$this->state = EASYBLOG_POST_TRASHED;

		// we need to set this date to empty so that the unpublish task will not pick up this entry.
		$this->publish_down	= '0000-00-00 00:00:00';

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false);

		return $this->save($options);
	}

	/**
	 * Archives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function archive()
	{
		$this->state = EASYBLOG_POST_ARCHIVED;

		$options = array('validateData' => false, 'normalizeData' => false);
		return $this->save($options);
	}

	/**
	 * Unarchives a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function unarchive()
	{
		$this->state = EASYBLOG_POST_NORMAL;

		$options = array('validateData' => false, 'normalizeData' => false);
		return $this->save($options);
	}

	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function lock()
	{
		$this->locked 	= true;

		$options = array('validateData' => false, 'normalizeData' => false);
		return $this->save($options);
	}


	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function unlock()
	{
		$this->locked = false;

		$options = array('validateData' => false, 'normalizeData' => false);

		return $this->save($options);
	}

	/**
	 * Duplicates a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function duplicate()
	{
		// Export the current data
		$data = $this->toData();

		$data->id = null;
		$data->published = EASYBLOG_POST_UNPUBLISHED;
		$data->title = JText::sprintf('COM_EASYBLOG_DUPLICATE_OF_POST', $this->title);
		$data->revision_id = null;
		$data->autoposting = null;
		$data->revision_id = null;

		$post = EB::post();
		$post->bind($data);

		$state = $post->save(array('applyDateOffset' => true, 'normalizeData' => false));

		return $state;
	}

	/**
	 * Moves the current blog post to a new category
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function move($categoryId)
	{
		$this->category_id = $categoryId;
		$this->categories = array($categoryId);

		// Check the publish state and respect that
		if ($this->isUnpublished()) {
			return $this->unpublish();
		}

		return $this->publish();
	}


	/**
	 * Allows moderator to reject this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject($message, $userId = null)
	{
		$actor = JFactory::getUser($userId);

		// The state doesn't change.
		// // Set the state of the post to draft again.
		$this->published = EASYBLOG_POST_DRAFT;

		$state = $this->save();

		return $state;
	}

	/**
	 * Allows moderator to approve this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve()
	{
		// Here we need to check if this post should be scheduled
		$this->published = EASYBLOG_POST_PUBLISHED;

		// Clear up any reject messages for this post if necessary
		$model = EB::model('PostReject');
		$model->clear($this->id);

		$this->save();
	}

	/**
	 * Publishes a blog post on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @return	bool	Determines if the storing state is success
	 */
	public function publish($options = array(), $forceNotNew = false)
	{
		// Set the publishing state
		$this->published = EASYBLOG_POST_PUBLISHED;
		$this->state = EASYBLOG_POST_NORMAL;

		// if that is schedule post update the isnew to 0
		if ($forceNotNew) {
			$this->isnew = false;
		}

		// Set the save options
		$options = array_merge(array(), array('validateData' => false, 'normalizeData' => false), $options);

		return $this->save($options);
	}

	/**
	 * Auto posts this post into social network sites
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function autopost($type, $force = false)
	{
		// If the post is not published, disallow this
		if (!$this->isPublished()) {
			EB::exception('COM_EASYBLOG_AUTOPOST_PLEASE_PUBLISH_BLOG', 'error');

			return false;
		}

		// Ensure that the auto posting for this type is allowed
		$key = $this->config->get('integrations_' . $type . '_api_key');
		$secret = $this->config->get('integrations_' . $type . '_secret_key');

		if (!$key || !$secret) {
			EB::exception(JText::sprintf('COM_EASYBLOG_AUTOPOST_KEYS_INVALID', ucfirst($type)), 'error');

			return false;
		}

		// First we need to auto post to the system authentications
		EB::autoposting()->shareSystem($this, $type, $force);

		// Then, we auto post for the respective user
		EB::autoposting()->shareUser($this, array($type), $force);
	}

	/**
	 * Unpublishes a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		// Set the state
		$this->published = EASYBLOG_POST_UNPUBLISHED;

		$options = array('validateData' => false);

		// Store the post
		return $this->save($options);
	}

	/**
	 * Override functionality of JTable's hit method as we want to limit the hits based on the session.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function hit($pk = null)
	{
		$pages = $this->input->get('pagestart', '', 'default');
		$allpages = $this->input->get('showall', '', 'default');

		// We know this is coming from pagebreak plugin. so do not count the hit.
		if ($pages || $allpages) {
			return true;
		}

		// Determines if we should check against the session table
		if ($this->config->get('main_hits_session')) {

			// Get users ip address
			$ip = $this->input->server->get('REMOTE_ADDR');

			// Match only known browsers
			$agent = $this->input->server->get('HTTP_USER_AGENT', '', 'default');
			$pattern = '/(Mozilla.*(Gecko|KHTML|MSIE|Presto|Trident)|Opera).*/i';

			preg_match($pattern, $agent, $trackHits);

			if ($ip && !empty($this->id) && !empty($trackHits)) {

				$token = md5($ip . $this->id);
				$session = JFactory::getSession();
				$exists = $session->get($token, false);

				// If user was logged before, skip it
				if ($exists) {
					return true;
				}

				$session->set($token, 1);
			}
		}

		// Load language files
		EB::loadLanguages();

		// AUP
		EB::aup()->assignPoints('plgaup_easyblog_read_blog', $this->my->id, JText::sprintf('COM_EASYBLOG_AUP_READ_BLOG', $this->title));
		EB::aup()->assignPoints('plgaup_easyblog_read_blog_author', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_READ_BLOG_AUTHOR', $this->title));

		// Deduct points from respective systems
		// @rule: Integrations with EasyDiscuss
		EB::easydiscuss()->log('easyblog.view.blog', $this->my->id, JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_VIEW_BLOG' , $this->title ));
		EB::easydiscuss()->addPoint('easyblog.view.blog', $this->my->id );
		EB::easydiscuss()->addBadge('easyblog.view.blog', $this->my->id);

		// Only give points if the viewer is viewing another person's blog post.
		if ($this->my->id != $this->created_by) {
			EB::easysocial()->assignBadge('blog.read', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_READ_BLOG'));
			EB::easysocial()->assignPoints('blog.read');
		}

		// Mark notifications item in EasyDiscuss when the blog entry is viewed
		if ($this->config->get('integrations_easydiscuss_notification_blog')) {
			EB::easydiscuss()->readNotification($this->id, EBLOG_NOTIFICATIONS_TYPE_BLOG);
		}

		if ($this->config->get('integrations_easydiscuss_notification_comment')) {
			EB::easydiscuss()->readNotification($this->id, EBLOG_NOTIFICATIONS_TYPE_COMMENT);
		}

		if ($this->config->get('integrations_easydiscuss_notification_rating')) {
			EB::easydiscuss()->readNotification($this->id , EBLOG_NOTIFICATIONS_TYPE_RATING);
		}

		return $this->post->hit($pk);
	}

	/**
	 * Saves a blog post on the site. This method ensures that all saving process goes through the same routine.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	Array 	An array of options.
	 * @return
	 */
	public function save($options = array(), $debug = false)
	{
		// Set the save options
		$options = array_merge(array(), self::$defaultSaveOptions, $options);

		$this->saveOptions = $options;

		// Execute pre-saving routines
		$this->preSave($debug);

		// This needs to happen prior to saving the post so that the new revision get's created first.
		// If this post is going to be a draft and the existing revision is already finalized we should create a new revision and assign to this post first
		$createNewRevision = $this->isBeingDrafted() || $this->isBeingSubmittedForApproval();

		// Check to see if the prior revision was already finalized and we need to create a new revision.
		if ($createNewRevision && $this->revision->isFinalized()) {
			$this->createNewRevision();
		}

		// We need to save the post first in order to link the revision id with the post table.
		$this->savePost();

		$this->saveRevision();

		// Store the categories that is associated with the post.
		$this->saveCategories();

		// Store the tags that is associated with the post.
		$this->saveTags();

		// Store the meta data that is associated with the post.
		$this->saveMeta();

		// Store the posts association that is associated with the post.
		$this->saveAssociation();

		// Stores the relationship of this post with any other sources.
		$this->saveRelation();

		// Store the fields that are related to the post
		if (!$this->saveOptions['skipCustomFields']) {
			$this->saveFields();
		}

		// Execute post-saving routines
		// TODO: Do not execute is silent save option is true.
		$this->postSave();
	}

	/**
	 * Before a blog post is stored, we want to perform specific operations here
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preSave()
	{
		// Trigger content plugins before saving
		$triggerPlugins = isset($this->saveOptions['triggerPlugins']) ? $this->saveOptions['triggerPlugins'] : true;

		if ($triggerPlugins) {
			$this->triggerBeforeSave();
		}

		//apply date timezone offset if needed
		if ($this->saveOptions['applyDateOffset']) {
			$this->applyDateOffset();
		}

		// Normalize data
		if ($this->saveOptions['normalizeData']) {
			$this->normalize();
		}

		// Update post modified time
		if ($this->saveOptions['updateModifiedTime']) {
			$this->modified = EB::date()->toSql();
		}

		// Log ip to the last user modifying this post
		if ($this->saveOptions['logUserIpAddress']) {
			$this->ip = @$_SERVER['REMOTE_ADDR'];
		}

		// Validate data
		if ($this->saveOptions['validateData']) {
			$this->validate();
		}
	}

	/**
	 * Post processing when a blog post is saved
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function postSave()
	{
		// do not do anything if the post is a blank post.
		if ($this->isBlank()) {
			return;
		}

		// Whenever the blog post is stored, we need to clear the cache.
		$this->clearCache();

		// Triggers plugins after the post is stored
		$triggerPlugins = isset($this->saveOptions['triggerPlugins']) ? $this->saveOptions['triggerPlugins'] : true;

		if ($triggerPlugins) {
			$this->triggerAfterSave();
		}

		// When this post is being submitted for approval we want to notify site administrator's
		if ($this->isBeingSubmittedForApproval()) {
			$this->notify(true, false);
		}

		// When this post is being approval and this post still under scheduled post we need to notify to post owner
		if ($this->isBeingScheduledForPublishing()) {
			$this->notify(false, EASYBLOG_POST_SCHEDULED, false, true);
		}		

		// Add activity stream for Jomsocial, regardless if it is being published or not, we should let the client check this.
		if (!$this->isDraft() && !$this->isPending() && !$this->isScheduled()) {
			EB::jomsocial()->insertActivity($this);
		}

		// Process the autopost
		if ($this->saveOptions['processAutopost']) {
			
			// When this post is saved and published we need to perform the auto posting for the system
			if ($this->isPublished()) {
				EB::autoposting()->shareSystem($this);
			}

			// When this post is saved, we might need to perform the auto posting
			if ($this->autoposting && $this->isPublished()) {
				EB::autoposting()->shareUser($this, $this->autoposting);
			}
		}

		// When this post is being published, we should add post actions here.
		if ($this->isBeingPublished()) {

			// Send notifications to subscribers
			if (!$this->isPasswordProtected() && !$this->saveOptions['skipNotifications']) {
				$this->notify();
			}

			// EasySocial Integrations
			// If $this->isBeingPublished, it will be always a new post.
			if (!isset($this->saveOptions['saveFromEasysocialStory'])) {
				EB::easysocial()->createBlogStream($this, true);
			}

			EB::easysocial()->updateBlogPrivacy($this->post);
			EB::easysocial()->assignPoints('blog.create', $this->created_by);
			EB::easysocial()->notifySubscribers($this, 'new.post');
			EB::easysocial()->addIndexerNewBlog($this);
			EB::easysocial()->assignBadge('blog.create', JText::_('COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_BLOG_POST'));

			// EasyDiscuss Integrations
			EB::easydiscuss()->log('easyblog.new.blog', $this->created_by, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_BLOG', $this->title));
			EB::easydiscuss()->addPoint('easyblog.new.blog', $this->created_by);
			EB::easydiscuss()->addBadge('easyblog.new.blog', $this->created_by);
			EB::easydiscuss()->insertNotification('new.blog', $this);

			// Alpha User Points
			EB::aup()->assignPoints('plgaup_easyblog_add_blog', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_NEW_BLOG_CREATED', $this->getPermalink(), $this->title));

			// Points should only be rewarded when the post is being published
			EB::jomsocial()->assignPoints('com_easyblog.blog.add', $this->created_by);

			// Ping to Pingomatic args: $post, $debug.
			EB::pingomatic()->ping($this, false);

			// Automatically feature the blog post if required
			$isFeaturedAuthor = EB::isFeatured('blogger', $this->created_by);
			$isFeaturedPost = EB::isFeatured('post', $this->id);

			if ($this->config->get('main_autofeatured', 0) && $isFeaturedAuthor && !$isFeaturedPost) {
				$this->setFeatured('post', $this->id);
			}

		} else {
			if ($this->isPublished()) {
				// This action is an edit post
				EB::easysocial()->createBlogStream($this, false);
			}
		}

		// When the post is approved, we want to notify the author
		if ($this->isBeingApproved()) {
			// We do not need to notify the world that the post is published because it's already handled above under
			$this->notify(false, '1', false, true);
		}

		// When this post is being unpublished, we should add triggers here.
		if ($this->isBeingUnpublished()) {

			// If the post is being unpublished, remove them from the stream
			EB::jomsocial()->removePostStream($this);
		}

		// When this post is rejected by the moderator, we should add triggers here
		if ($this->isBeingRejected()) {

			// When a post is rejected, add the necessary data on the reject table so we can determine why it's being rejected
			$reject = EB::table('PostReject');
			$reject->post_id = $this->id;
			$reject->created_by = $this->created_by;
			$reject->created = EB::date()->toSql();

			// @TODO: How should we get the reject message from the composer?
			$reject->message = $this->input->get('message', '', 'default');
			$reject->store();

			// Send notify
			$reject->notify();
		}

	}

	/**
	 * Triggers plugins after a blog post is saved
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function triggerAfterSave()
	{
		// cache this post so that other plugins can get the data without having load the data from db again.
		EB::cache()->cachePosts(array($this->post));

		// Import plugins
		JPluginHelper::importPlugin('finder');
		JPluginHelper::importPlugin('easyblog');

		$dispatcher = JDispatcher::getInstance();

		$this->introtext = '';
		$this->text = '';

		$dispatcher->trigger('onAfterEasyBlogSave', array(&$this, $this->isNew()));

		// Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext = $this->intro;
		$this->text = $this->content;

		$dispatcher->trigger('onContentAfterSave', array('easyblog.blog', &$this, $this->isNew()));

		// finder index
        $dispatcher->trigger('onFinderAfterSave', array('easyblog.blog', &$this, $this->isNew()));


		// Revert back these properties
		$this->intro = $this->introtext;
		$this->content = $this->text;

		unset($this->introtext);
		unset($this->text);
	}

	/**
	 * Triggers plugins before a blog post is saved
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function triggerBeforeSave()
	{
		// Import plugins
		JPluginHelper::importPlugin('content');
		JPluginHelper::importPlugin('easyblog');

		// Load up dispatcher
		$dispatcher = JDispatcher::getInstance();

		// Try to mimic Joomla's com_content behavior.
		require_once(JPATH_ROOT . '/components/com_content/helpers/route.php');

		$dispatcher->trigger('onBeforeEasyBlogSave', array(&$this, $this->isNew()));

		// Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext = $this->intro;
		$this->text = $this->content;

		$dispatcher->trigger('onContentBeforeSave', array('easyblog.blog', &$this, $this->isNew()));

		// Since Joomla content plugins are expecting that this is an article object, we need to get the introtext and text value back
		$this->intro = $this->introtext;
		$this->content = $this->text;

		unset($this->introtext);
		unset($this->text);
	}

	/**
	 * Saves the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function savePost()
	{
		// If this a new post, instantiate a new post table.
		if (!$this->post) {
			$this->post = EB::table('Post');
		}

		// If the revision is not the current post and it isn't being finalized we shouldn't do anything
		// as the revision should still be on draft state.
		if (!$this->isCurrent() && !$this->isFinalized() && !$this->isBlank()) {
			return;
		}

		// Get post data
		$data = $this->toPostData();

		if ($this->doctype == 'ebd') {
			// if this is a ebd, then we need to update the content column from easyblog_post table as well.
			$document = EB::document($this->document);
			$contents = $document->getContent();

			$data->content = $contents;
		}

		// Bind post data
		$this->post->bind($data);

		if (isset($this->saveOptions['overrideAuthorId']) && $this->saveOptions['overrideAuthorId']) {
			$this->post->created_by = $this->saveOptions['overrideAuthorId'];
		}

		// Store post
		$state = $this->post->store();

		// If failed to store post, throw exception.
		if (!$state) {
			throw EB::exception('Unable to store post.');
		}

		// If this post is being created, assign post id to workbench.
		$this->id = $this->post->id;
		// if ($this->isBeingCreated()) {
		// 	$this->id = $this->post->id;
		// }

		return true;
	}

	/**
	 * Creates a new revision for the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createNewRevision()
	{
		$revision = EB::table('Revision');
		$revision->created_by = $this->user->id;

		if (isset($this->saveOptions['overrideAuthorId']) && $this->saveOptions['overrideAuthorId']) {
			$revision->created_by = $this->saveOptions['overrideAuthorId'];
		}

		// Set revision post id
		$revision->post_id = $this->post->id;

		// Set revision content
		$revision->setContent($this->toRevisionData());

		// Set revision state
		// Draft     => Blank, Draft
		// Pending   => Pending
		// Finalized => Published, Scheduled, Unpublished
		if ($this->isBlank() || $this->isDraft()) {
			$revision->state = EASYBLOG_REVISION_DRAFT;
		} else if ($this->isPending()) {
			$revision->state = EASYBLOG_REVISION_PENDING;
		} else {
			$revision->state = EASYBLOG_REVISION_FINALIZED;
		}

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			throw EB::exception('Unable to store revision.');
		}

		// Assign the newly created revision to the post library
		$this->revision = $revision;

		// Update revision_id & uid.
		$this->revision_id = $revision->id;
		$this->uid = $this->id . '.' . $this->revision_id;

		// If we managed to reach here, it means both revision and post
		// are successfully saved, so we'll just need to return true.
		return true;
	}

	/**
	 * Determines if the revision should be created as a new revision or update on existing revision.
	 *
	 * @since	5.0
	 * @access	public
	 * @return
	 */
	public function saveRevision()
	{
		// By default we don't want to change the post's revision
		$setAsCurrentRevision = false;

		// If this is a new post or legacy post w/o revision, create a new revision and set as current revision.
		if (!$this->revision) {

			$revision = EB::table('Revision');
			$setAsCurrentRevision = true;

			// Ensure that the revision author is created by the current user.
			$revision->created_by = $this->user->id;

		} else if ($this->revision->isFinalized() && !$this->saveOptions['skipCreateRevision']) {

			// If this revision is already finalized, create a new revision
			$revision = EB::table('Revision');

			// Set the revision author to the current user
			$revision->created_by = $this->user->id;

			// If this finalized revision is also the current revision used by post, set this revision as current revision.
			if ($this->isCurrent()) {
				$setAsCurrentRevision = true;
			}

		} else {

			// Else reuse current revision
			$revision = $this->revision;

			// Whenever we reuse an existing revision, we want to log the user's actions
			// as an audit in case they need to re-load these data again.
		}

		// If caller pass in useAuthorAsRevisionOwner flag, then we will use blog author as revision author.
		if (isset($this->saveOptions['useAuthorAsRevisionOwner']) && $this->saveOptions['useAuthorAsRevisionOwner']) {
			$revision->created_by = $this->created_by;
		}

		// Set revision post id
		$revision->post_id = $this->post->id;

		// Set revision content
		$revision->setContent($this->toRevisionData());

		// Set revision state
		// Draft     => Blank, Draft
		// Pending   => Pending
		// Finalized => Published, Scheduled, Unpublished
		if ($this->isBlank() || $this->isDraft()) {
			$revision->state = EASYBLOG_REVISION_DRAFT;
		} else if ($this->isPending()) {
			$revision->state = EASYBLOG_REVISION_PENDING;
		} else {
			$revision->state = EASYBLOG_REVISION_FINALIZED;
		}

		// If current is blank state then set a default title
		if ($this->isBlank()) {
			$revision->title = JText::_('COM_EASYBLOG_COMPOSER_INITIAL_POST');
		}

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			throw EB::exception('Unable to store revision.');
		}

		// If we should set this revision as current revision
		if ($setAsCurrentRevision) {
			$this->post->revision_id = $revision->id;
			$state = $this->post->store();


			if (!$state) {
				throw EB::exception('Unable to set as revision as current revision on post.');
			}
		}

		// Assign revision back to instance
		// in case we've created a new revision.
		$this->revision = $revision;

		// Update revision_id & uid.
		$this->revision_id = $revision->id;
		$this->uid = $this->id . '.' . $this->revision_id;

		// If we managed to reach here, it means both revision and post
		// are successfully saved, so we'll just need to return true.
		return true;
	}

	/**
	 * Save the categories associated with the post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveCategories()
	{
		// We should not be saving any categories if this was the first copy being initialized.
		if ($this->isBlank()) {
			return;
		}

		// If the current post and revision matches each other, we know that the revision is the copy used as the post.
		if ($this->isCurrent()) {

			// Delete any categories that are associated with this post id
			$model = EB::model('Categories');
			$model->deleteAssociation($this->id);

			foreach ($this->categories as $id) {
				$id = (int) $id;

				$postCatTbl = EB::table('PostCategory');
				$postCatTbl->post_id = $this->id;
				$postCatTbl->category_id = $id;
				$postCatTbl->primary = $this->category_id == $id;
				$postCatTbl->store();
			}

		}

	}

	/**
	 * Saves the tags when the blog post is stored.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveTags()
	{
		// Assuming that tags are a comma separated keywords
		$tags = $this->tags;

		// Assuming that this is the current copy.
		if (!empty($this->tags)) {

			// Ensure that the tags are in an array
			$tags = explode(',', $this->tags);

			// Delete any existing tags associated with this post first
			$postTagModel = EB::model('PostTag');

			// Delete related tags with this post first.
			$postTagModel->deletePostTag($this->id);

			// Get a list of default tags on the site
			$model = EB::model('Tags');
			$defaultTags = $model->getDefaultTagsTitle();

			if (!empty($defaultTags)) {
				foreach ($defaultTags as $title) {
					$tags[] = $title;
				}
			}

			//remove spacing from start and end of the tags title.
			if ($tags) {
				for($i = 0; $i < count($tags); $i++) {
					$tags[$i] = JString::trim($tags[$i]);
				}
			}

			// Ensure that the tags are unique
			$tags = array_unique($tags);

			if ($tags) {
				foreach ($tags as $tag) {
					$tag = trim($tag);

					// Ensure that the tag is valid
					if (empty($tag)) {
						continue;
					}

					$table = EB::table('Tag');
					$exists = $table->load($tag, true);

					// If the tag does not exist and the user does not have any privileges to create any tag,
					// we shouldn't allow them to create them.
					if (!$exists && !$this->acl->get('create_tag')) {
						continue;
					}

					// When the tag does not exist, create a new tag first
					if (!$exists) {

						$table->created_by = $this->created_by;
						$table->title = $tag;
						$table->created = EB::date()->toSql();
						$table->published = true;
						$table->status = '';
						$state = $table->store();

						if (!$state) {
							EB::ajax()->notify($tag . ':' . $table->getError(), 'debug');
						}
					}

					// Add the association of tags here.
					$postTagModel->add($table->id, $this->id, EB::date()->toSql());
				}
			}
		}
	}

	/**
	 * Saves the posts association
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveAssociation()
	{
		$db = EB::db();

		if (EB::isAssociationEnabled()) {

			// TOTO:: delete the existing association.
			$query = "delete a from `#__easyblog_associations` as a";
			$query .= " inner join `#__easyblog_associations` as b on a.`key` = b.`key`";
			$query .= " where b.`post_id` = " . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();

			if ($this->association) {

				// Adding new association for these items
				$key = md5(json_encode($this->association));

				$query = "insert into `#__easyblog_associations` (`id`, `post_id`, `key`) values ";

				$arr = array();
				foreach($this->association as $assoc) {
					$arr[] = "(null, " . $db->Quote($assoc->id) . "," . $db->Quote($key) . ")";
				}

				$values = implode(",", $arr);

				$query .= $values;

				$db->setQuery($query);
				$db->query();
			}

		}
	}


	public function getAssociation()
	{

		$db = EB::db();

		$query = "select a.`post_id`, a.`key`, p.`language`, p.`title`";
		$query .= " from `#__easyblog_associations` as a";
		$query .= " inner join `#__easyblog_associations` as b on a.`key` = b.`key`";
		$query .= " inner join `#__easyblog_post` as p on a.`post_id` = p.`id`";
		$query .= " where b.`post_id` = " . $db->Quote($this->id);

		$db->setQuery($query);

		$results = $db->loadObjectList();

		$assocs = array();

		if ($results) {
			foreach($results as $item) {
				$assocs[$item->language] = $item;
			}
		}

		return $assocs;
	}



	/**
	 * Saves the meta data of the blog post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveMeta()
	{
		// Get the keywords
		$keywords = $this->keywords;
		$description = $this->description;
		$customTitle = $this->custom_title;

		// Try to get the meta id for this post
		$model = EB::model('Blog');
		$metaId = $model->getMetaId($this->id);

		// Store the meta data for this post now
		$meta = EB::table('Meta');
		$meta->load($metaId);

		$meta->content_id = $this->id;
		$meta->title = $customTitle;
		$meta->keywords = $keywords;
		$meta->description = $description;
		$meta->type = META_TYPE_POST;

		$meta->store();
	}

	/**
	 * Associates the post with a different source.
	 *
	 * E.g: team blog, jomsocial group, jomsocial event
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveRelation()
	{
		// @TODO: Determine if there's any relation for this post.
		// @TODO: What will the composer be passing to us?

		// @TODO: Update this to use just the post table.
		return;

		// $relation = EB::table('PostRelation');

		// assuming this is finalized copy
		if ($this->isReady()) {
			// assuming there will be always one to one relation to the either teamblog / JS group / JS event,
			// then we will load the record based on post_id

			$relation->load(array('post_id' => $this->id));
			$isNew = ($relation->id) ? false : true;

			if ($isNew) {
				$relation->post_id = $this->id;
				$relation->created = EB::date()->toSql();
			}

			$relation->external_id = $this->source_id;
			$relation->external_source = $this->source_type;
			$relation->store();
		}

	}

	/**
	 * Saves the fields that are related to the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function saveFields()
	{
		$fields = $this->fields;

		// Delete any existing field data for this post
		$model = EB::model('Fields');
		$model->deleteBlogFields($this->id);

		if ($fields) {

			foreach ($fields as $id => $value) {

				// This is most likely a multiple value field
				if (is_array($value)) {
					foreach ($value as $subValue) {

						$table = EB::table('FieldValue');
						$table->field_id = $id;
						$table->post_id = $this->id;
						$table->value = $subValue;
						$table->store();
					}

					continue;
				}

				$table = EB::table('FieldValue');
				$table->field_id = $id;
				$table->post_id = $this->id;

				// If this is just a normal value, just store the value
				$table->value = $value;
				$table->store();
			}
		}
	}

	/**
	 * Sets a debug status
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function debug()
	{
		$this->_debug = true;
	}

	/**
	 * Deletes a post from the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Load site's language file just in case the blog post was deleted from the back end
		EB::loadLanguages();

		// Load our own plugins
		JPluginHelper::importPlugin('finder');
		JPluginHelper::importPlugin('easyblog');
		$dispatcher = JDispatcher::getInstance();

		// Trigger
		$dispatcher->trigger('onBeforeEasyBlogDelete', array(&$this));

		// Delete the post from the db now
		$state = $this->post->delete();

		// Trigger
		$dispatcher->trigger('onAfterEasyBlogDelete', array(&$this));

		// Delete from finder.
	    $dispatcher->trigger('onFinderAfterDelete', array('easyblog.blog', $this->post));

		// Delete all relations with this post
		$this->deleteRatings();
		$this->deleteReports();
		$this->deleteRevisions();
		$this->deleteCategoryRelations();
		$this->deleteBlogTags();
		$this->deleteMetas();
		$this->deleteComments();
		$this->deleteTeamContribution();
		$this->deleteAssets();
		$this->deleteFeedHistory();

		// Delete all subscribers to this post
		$this->deleteSubscribers();

		// Delete from featured table
		$this->deleteFeatured();

		// Relocate media files into "My Media"
		$this->relocateMediaFiles();

		// Delete all other 3rd party integrations
		$this->deleteOtherRelations();

		return $state;
	}

	/**
	 * Relocate media files into "My Media"
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function relocateMediaFiles()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/mediamanager/mediamanager.php');

		// Get a list of folders for this post
		$uri = 'post:' . $this->id;

		// Get the absolute path to the post's folder
		$path = EBMM::getPath($uri);

		// Check if it exists.
		jimport('joomla.filesystem.folder');

		// If it doesn't exist, we wouldn't want to do anything
		if (!JFolder::exists($path)) {
			return true;
		}

		// Construct the new uri
		$newUri = 'user:' . $this->created_by;
		$newPath = EBMM::getPath($newUri);

		// We need to create a new folder first with the name of this current title
		$title = JFile::makeSafe($this->title);
		$newPath = $newPath . '/' . $title;

		// Move the old folder to the new folder now
		$state = JFolder::move($path, $newPath);

		return $state;
	}

	/**
	 * Delete all other relations with the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteOtherRelations()
	{
		// Delete relationships from jomsocial stream
		EB::jomsocial()->removePostStream($this);
		EB::jomsocial()->assignPoints('com_easyblog.blog.remove', $this->created_by);

		// Deduct points from respective systems
		EB::easydiscuss()->log('easyblog.delete.blog', $this->created_by, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_BLOG', $this->title));
		EB::easydiscuss()->addPoint('easyblog.delete.blog', $this->created_by);
		EB::easydiscuss()->addBadge('easyblog.delete.blog', $this->created_by);

		// Integrations with EasySocial
		EB::easysocial()->assignPoints('blog.remove', $this->created_by);
		EB::easysocial()->removePostStream($this);

		// Integrations with AUP
		EB::aup()->assignPoints('plgaup_easyblog_delete_blog', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_BLOG_DELETED', $this->title));
	}

	/**
	 * Delete category relations
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteCategoryRelations()
	{
		$model = EB::model('Categories');
		return $model->deleteAssociation($this->id);
	}

	/**
	 * Delete revisions associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRevisions()
	{
		$model = EB::model('Revisions');

		return $model->deleteRevisions($this->id);
	}

	/**
	 * Delete subscribers for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteSubscribers()
	{
		$model = EB::model('Subscriptions');

		return $model->deleteSubscriptions($this->id, 'entry');
	}

	/**
	 * Remove any featured items for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFeatured()
	{
		$model = EB::model('Featured');
		$state = $model->removeFeatured(EBLOG_FEATURED_BLOG, $this->id);

		return $state;
	}

	/**
	 * Delete ratings associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteRatings()
	{
		$model = EB::model('Ratings');
		return $model->removeRating('entry', $this->id);
	}

	/**
	 * Delete reports associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteReports()
	{
		$model = EB::model('Reports');
		return $model->deleteReports($this->id, EBLOG_REPORTING_POST);
	}

	/**
	 * Delete team associations with this current post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteTeamContribution()
	{
		// @TODO: Sam, replace this
	}

	/**
	 * Delete any tags associated with the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteBlogTags()
	{
		$model = EB::model('Tags');

		return $model->deleteAssociation($this->id);
	}

	/**
	 * Delete meta tags associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteMetas()
	{
		$model = EB::model('Metas');

		return $model->deleteMetas($this->id, META_TYPE_POST);
	}

	/**
	 * Delete comments related to this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteComments()
	{
		$model = EB::model('Comments');

		return $model->deletePostComments($this->id);
	}

	/**
	 * Delete assets that are related to the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteAssets()
	{
		$model = EB::model('Assets');

		return $model->deleteAssets($this->id);
	}

	/**
	 * Deletes any association with the feed history table
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFeedHistory()
	{
		$history = EB::table('FeedHistory');
		$exists = $history->load(array('post_id' => $this->id));

		if ($exists) {
			return $history->delete();
		}

		return false;
	}

	/**
	 * Returns the data of a post in a standard object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toPostData()
	{
		$data = new stdClass();

		foreach (self::$enumerations as $prop => $enum) {

			// If this is a linked property, skip.
			// Linked property are not part of post table.
			if ($enum['linked']) {
				continue;
			}

			$data->$prop = $this->$prop;
		}

		return $data;
	}

	/**
	 * Exports current properties of the post that should be inserted into the revision data.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toRevisionData()
	{
		$data = $this->toData();

		if ($this->doctype == 'ebd') {
			// if this is a ebd, then we need to update the content attribute as well.
			$document = EB::document($this->document);
			$contents = $document->getContent();

			$data->content = $contents;
		}

		// We do not want to store the post id
		unset($data->revision);
		unset($data->uid);
		unset($data->revision_id);

		return $data;
	}

	/**
	 * Exports the post library to a standard object that can be sent back to the javascripts.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toData()
	{
		$data = new stdClass();

		foreach (self::$enumerations as $prop => $enum) {
			$data->$prop = $this->$prop;
		}

		// Add additional properties that is not part of enumerations
		$data->uid = $this->uid;
		$data->revision = $this->revision;

		return $data;
	}

	/**
	 * Exports the post library to email data
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toEmailData()
	{
		static $data = false;

		if (!$data) {
			$author = $this->getAuthor();
			$category = $this->getPrimaryCategory();

			$blogIntro = $this->getIntro();
			$blogContent = $this->getContent();

			// Truncate the content inside email.
			if ($this->config->get('notification_blog_truncate', false)) {
				$blogIntro = JHTML::_('string.truncate', $blogIntro, $this->config->get('notification_blog_truncate_limit', 300));
				$blogContent = JHTML::_('string.truncate', $blogContent, $this->config->get('notification_blog_truncate_limit', 300));
			}

			// Send email notifications to subscribers
			$data = array(
						'blogTitle' => $this->title,
						'blogAuthor' => $author->getName(),
						'blogAuthorAvatar' => $author->getAvatar(),
						'blogAuthorLink' => $author->getExternalPermalink(),
						'blogAuthorEmail' => $author->user->email,
						'blogIntro' => $blogIntro,
						'blogContent' => $blogContent,
						'blogCategory' => $category->getTitle(),
						'blogLink' => $this->getExternalPermalink(),
						'blogDate' => $this->getCreationDate()->format(JText::_('DATE_FORMAT_LC')),
						'blogCover' => $this->getImage('original', false, true)
					);

			// if user upload photo from eb media manager
			$pattern = '/src=\"(\/\/.*?)\"/';
			preg_match_all($pattern, $data['blogIntro'] . $data['blogContent'], $matches);

			$uri = JURI::getInstance();
			$scheme = $uri->toString(array('scheme'));
			$scheme = str_replace('://', ':', $scheme);

			foreach ($matches as $match) {

				if ($match) {
					$data['blogIntro'] = str_replace('src="//', 'src="' . $scheme . '//', $data['blogIntro']);
					$data['blogContent'] = str_replace('src="//', 'src="' . $scheme . '//', $data['blogContent']);
				}
			}

			// if user upload photo from e.g. JCE editor
			$pattern2 = '/src="(.*?)"/';
			preg_match_all($pattern2, $data['blogIntro'] . $data['blogContent'], $matches2);

			foreach ($matches2 as $match) {

				foreach($match as $imageurl) {

					// find the image url which do not exist http/https
					if (strpos($imageurl, 'http://') === false && strpos($imageurl, 'https://') === false) {

						$segments = explode('=', $imageurl);

						if (count($segments) > 1) {

							$url = $segments[1];

							$url = ltrim($url, '"');
							$url = rtrim($url, '"');

							$newurl = 'src="' . rtrim(JURI::root(), '/') . '/' . ltrim($url, '/') . '"';

							$data['blogIntro'] = str_replace($imageurl, $newurl, $data['blogIntro']);
							$data['blogContent'] = str_replace($imageurl, $newurl, $data['blogContent']);
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * Normalize all posted / binded data.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalize()
	{
		// Normalize the post title
		$this->normalizeTitle();

		// Ensure that the author is set correctly
		$this->normalizeAuthor();

		// Ensure that the post's permalink is set correctly.
		$this->normalizeAlias();

		$this->normalizeSource();
		$this->normalizeDocument();
		$this->normalizeContent();

		// Normalize blog images
		$this->normalizeBlogImage();


		// For blank posts, we shouldn't normalize the dates
		$this->normalizeDate();

		// Normalize categories for the post
		$this->normalizeCategories();

		// Ensure that the tags are properly set
		$this->normalizeTags();
		$this->normalizeFrontpage();
		$this->normalizePrivacy();
		$this->normalizeState();

		// Normalizes publishing state
		$this->normalizePublishingState();

		$this->normalizeNewState();

		$this->normalizeOthers();
	}

	/**
	 * Ensures that the author is set correctly.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeAuthor()
	{
		// If author hasn't been set yet, reset it to the original author.
		if (!isset($this->created_by)) {
			$this->created_by = $this->original->created_by;
		}

		// If author is still invalid, set the current user as the author.
		if (!isset($this->created_by)) {
			$this->created_by = $this->user->id;
		}
	}

	/**
	 * Ensures that the blog image data is appropriate
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeBlogImage()
	{
		// Try to convert the image property to an object
		$image = json_decode($this->image);

		// @legacy fix
		// We only want to store the URI for blog images.
		if (is_object($image)) {
			$this->image = $image->place . $image->path;
		}
	}

	/**
	 * Normalize the alias of the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeAlias()
	{
		// If permalink hasn't been set yet, reset it to the original permalink.
		if (!isset($this->permalink)) {
			$this->permalink = $this->original->permalink;
		}

		// The user might be sending a messed up permalink, try to fix it here
		if ($this->permalink) {
			$model = EB::model('Blog');
			$this->permalink = $model->normalizePermalink($this->permalink, $this->id);
		}

		// If the permalink is still invalid, generate a permalink for this post.
		if (!$this->permalink && $this->title && !$this->isBlank()) {
			$this->permalink = $this->generatePermalink();
		}


	}

	/**
	 * Normalize the source
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeSource()
	{
		// If source type is invald, revert to original value.
		if (!isset($this->source_type)) {
			$this->source_type = $this->original->source_type;
		}

		// If original source type is also invalid,
		if (!isset($this->source_type)) {
			$this->source_type = EASYBLOG_POST_SOURCE_SITEWIDE;
		}

		// If source type is easyblog sitewide, source id is always 0.
		if ($this->source_type==EASYBLOG_POST_SOURCE_SITEWIDE) {
			$this->source_id = 0;
		}

		// TODO: What's the strategy to normalize external source?
	}

	/**
	 * Normalizes the blog post's title.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeTitle()
	{
		// Trim whitespace on blog title
		if (isset($this->title)) {
			$this->title = JString::trim($this->title);
		}
	}

	/**
	 * Normalizes the document type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeDocument()
	{
		if ($this->document == null) {
			return;
		}

		// Sanitize the document json string and format it accordingly.
		$document = EB::document($this->document);

		// Enforce doctype
		$this->doctype = $document->type;

		// Normalize it back to json string
		$this->document = $document->toJSON();

		// We might not need this already since @normalizeContent already fixes things?
		// TODO: Translate document into intro & content.
		// Open back this intro is because when the user create post using composer, it will not store post introtext in the db when use readmore block
		$this->intro   = $document->getIntro();
		// $this->content = $document->getContent();
	}

	/**
	 * Normalizes the content of the post by cleaning and filtering the html codes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeContent()
	{
		if ($this->content == null) {
			return;
		}

		// We want to skip this part if the blog post is being unpublished or republished to avoid the missing intro
		// when readmore break is exist.
		if ($this->isBeingUnpublished() || $this->isBeingRepublished()) {
			return;
		}

		$content = $this->content;
		
		// Search for readmore tags using Joomla's mechanism
		$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
		$pos = preg_match($pattern, $content);

		if ($pos == 0) {
			$this->intro = $content;

			// @rule: Since someone might update this post, we need to clear out the content
			// if it doesn't contain anything.
			$this->content = '';
		} else {
			list($intro, $main) = preg_split($pattern, $content, 2);

			$this->intro = $intro;
			$this->content = $main;
		}

		// Remove editor generated html like <br mce_bogus="1">
		$this->intro = $this->string->cleanHtml($this->intro);
		$this->content = $this->string->cleanHtml($this->content);

		// Strip tags & attributes that are not allowed.
		$this->intro = $this->string->filterHtml($this->intro);
		$this->content = $this->string->filterHtml($this->content);
	}

	/**
	 * Ensures that all the dates associated with the post is set
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeDate()
	{
		// If creation date is invalid, revert to original value.
		if (!isset($this->created)) {
			$this->created = $this->original->created;
		}

		// If original creation date is also invalid, assign date now.
		if (!isset($this->created)) {
			$this->created = EB::date()->toSql();
		}

		// If publish date is invalid, revert to original value.
		if (!isset($this->publish_up)) {
			$this->publish_up = $this->original->created;
		}

		// If original publish date is also invalid, use creation date.
		if (!isset($this->publish_up)) {
			$this->publish_up = $this->created;
		}

		// If unpublish date is invalid, revert to original value.
		if (!isset($this->publish_down)) {
			$this->publish_down = $this->original->publish_down;
		}

		// If original unpublish date is also invalid, remove unpublish date.
		if (!isset($this->publish_down)) {
			$this->publish_down = EASYBLOG_NO_DATE;
		}

		// If unpublish date is an empty string, it means remove unpublish date.
		if ($this->publish_down=='') {
			$this->publish_down = EASYBLOG_NO_DATE;
		}
	}

	/**
	 * Normalize categories
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeCategories()
	{
		// If primary category is invalid, revert to original value.
		if (!isset($this->category_id)) {
			$this->category_id = $this->original->category_id;
		}

		// If the original value is also invalid, set the default category
		if (!isset($this->category_id)) {
			$model = EB::model('Category');
			$this->category_id = $model->getDefaultCategoryId();
		}

		// If categories is invalid, revert to original value.
		if (!isset($this->categories)) {
			$this->categories = $this->original->categories;
		}

		// If original categories is also invalid,
		// create a new array of categories with
		// primary category as its member.
		if (!isset($this->categories)) {
			$this->categories = array($this->category_id);
		}


		// If primary category is not a member of the array of categories, add it in.
		if (!in_array($this->category_id, $this->categories)) {
			$this->categories[] = $this->category_id;
		}
	}

	/**
	 * Ensures that all of the default tags are being assigned to the blog.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeTags()
	{
		// If tags is invalid, revert to original tags.
		if (!isset($this->tags)) {
			$this->tags = $this->original->tags;
		}

		// If original tags is also invalid, assign to empty array.
		if (!isset($this->tags) || empty($this->tags)) {
			$this->tags = array();
		}

		if ($this->tags) {
			$tags = explode(',', $this->tags);
		} else {
			$tags = array();
		}

		// Check against catagories for tags that are mandatory.
		if ($this->categories && is_array($this->categories)) {

			foreach ($this->categories as $id) {
				$id = (int) $id;

				$category = EB::table('Category');
				$category->load($id);

				// Get the default tags from a category
				$tags = array_merge($tags, $category->getDefaultTags());
			}
		}

		// Get a list of default tags on the site
		$model = EB::model('Tags');
		$tags = array_merge($tags, $model->getDefaultTagsTitle());

		//remove spacing from start and end of the tags title.
		if ($tags) {
			for($i = 0; $i < count($tags); $i++) {
				$tags[$i] = JString::trim($tags[$i]);
			}
		}

		$tags = array_unique($tags);
		$this->tags = implode(',', $tags);
	}

	/**
	 * Ensures that the front page state is set correctly
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeFrontpage()
	{

		$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;

		if ($checkACL) {
			// If user is not allowed to modify the frontpage property, revert to original value.
			if ($this->hasChanged('frontpage') && !$this->acl->get('contribute_frontpage')) {
				$this->frontpage = $this->original->frontpage;
			}
		}

		// If this is a new post, assign default frontpage value for new post.
		if ($this->isBlank()) {
			$this->frontpage = $this->config->get('main_newblogonfrontpage', 1);
		}
	}

	/**
	 * Normalize other properties of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeOthers()
	{
		if (!isset($this->description)) {
			$this->description = '';
		}

		if (!isset($this->keywords)) {
			$this->keywords = '';
		}

		$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;
		if ($checkACL) {
			// Determines if comments are allowed for this post.
			if ($this->hasChanged('allowcomment') && !$this->acl->get('change_setting_comment')) {
				$this->allowcomment = $this->original->allowcomment;
			}

			if ($this->hasChanged('subscription') && !$this->acl->get('change_setting_subscription')) {
				$this->subscription = $this->original->subscription;
			}
		}

		if ($this->isBlank()) {
			$this->allowcomment = $this->config->get('main_defaultallowcomment', 1);
			$this->send_notification_emails = $this->config->get('main_sendemailnotifications', 1);
			$this->subscription = $this->config->get('main_subscription', 1);
		}
	}

	/**
	 * Normalizes the state column
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeState()
	{
		if (is_null($this->state)) {
			$this->state = EASYBLOG_POST_NORMAL;
		}
	}

	/**
	 * Normalizes the new state of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizeNewState()
	{
		// If blog post is published, isnew should always be false.
		if ($this->isPublished()) {
			$this->isnew = false;
		}
	}

	/**
	 * Normalizes the privacy state
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizePrivacy()
	{
		// If user is not allowed to change the private property, revert it.
		if ($this->hasChanged('access') && !$this->acl->get('enable_privacy')) {
			$this->access = $this->original->access;
		}

		// If private property is not assigned,
		// or admin enforces a value on the private property, assign it.
		if (!isset($this->access) || !$this->config->get('main_blogprivacy_override')) {
			$this->access = $this->config->get('main_blogprivacy');
		}
	}

	/**
	 * Normalizes the publishing states
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizePublishingState()
	{
		// If this entry is being published, but the publish date is set in
		// the future, change the published state to scheduled state.
		if ($this->isBeingPublished()) {
			$checkACL = isset($this->saveOptions['checkAcl']) ? $this->saveOptions['checkAcl'] : true;

			if ($checkACL) {
				// If this entry is being published, but the user has no permission
				// to publish entry, change the published state to pending state.
				if (!$this->acl->get('publish_entry')) {
					$this->published = EASYBLOG_POST_PENDING;
				}
			} else {
				$this->published = EASYBLOG_POST_PUBLISHED;
			}
		}

		// If the post is being submitted for approval, it should not see the publishing date
		if (!$this->isBeingSubmittedForApproval()) {

			// The publish state should be dependent on the `publish_up` column
			$today = EB::date();
			$publishDate = $this->getPublishDate();

			if ($publishDate->toUnix() > $today->toUnix()) {
				$this->published = EASYBLOG_POST_SCHEDULED;
			}
		}
	}

	public function applyDateOffset($offset=null)
	{
		// If offset is not provided, use server date offset.
		if (!isset($offset)) {
			$offset = EB::date()->getOffset();
		}

		// if modified date and create date is the same,
		// also apply offset on modified date.
		if ($this->modified && $this->modified == $this->created) {
			$tmpDate = new JDate($this->modified, $offset);
			$this->modified = $tmpDate->toSql();
		}

		// Apply offset on creation date.
		if ($this->created && $this->created != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->created, $offset);
			$this->created = $tmpDate->toSql();
		}


		// Apply offset on publish date
		if ($this->publish_up && $this->publish_up != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->publish_up, $offset);
			$this->publish_up = $tmpDate->toSql();
		}

		// Apply offset on unpublish date
		if ($this->publish_down && $this->publish_down != EASYBLOG_NO_DATE) {
			$tmpDate = new JDate($this->publish_down, $offset);
			$this->publish_down = $tmpDate->toSql();
		}
	}

	/**
	 * Generate an alias for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function generatePermalink()
	{
		$model = EB::model('Blog');
		$permalink = $model->normalizePermalink($this->title);

		return $permalink;
	}

	/**
	 * Validates the current blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate()
	{
		// Checks if the post title is valid
		$this->validateTitle();

		// Checks if the post content is valid
		$this->validateCategory();

		// Checks if the post content is valid
		$this->validateContent();

		// Checks if the custom fields are all entered correctly.
		if (!$this->saveOptions['skipCustomFields']) {
			$this->validateFields();
		}
	}

	/**
	 * Ensures that the post title is valid
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateCategory()
	{
		// // Check for empty title
		// if ($this->saveOptions['checkEmptyTitle'] && empty($this->title)) {
		// 	throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR');
		// }
	}

	/**
	 * Ensures that the post title is valid
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateTitle()
	{
		// Check for empty title
		if ($this->saveOptions['checkEmptyTitle'] && empty($this->title)) {
			throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR');
		}

		$blockedWord = $this->string->hasBlockedWords($this->title);
		// Check for blocked words in title
		if ($this->saveOptions['checkBlockedWords'] && $blockedWord !== false) {
			throw EB::exception(JText::sprintf('COM_EASYBLOG_BLOG_TITLE_CONTAIN_BLOCKED_WORDS', $blockedWord));
		}
	}

	/**
	 * Validates the content and ensures that the content contains valid data.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateContent()
	{
		$content = "";

		// Skip validation for ebd until we figure out
		// how to do normalizeDocument().
		if ($this->isEbd()) {

			$blocks = $this->getBlocks();

			// If there is no blocks at all, throw an error
			if (!$blocks) {
				throw EB::exception('COM_EASYBLOG_COMPOSER_EMPTY_BLOCKS_CONTENT_ERROR');
			}

			// The blocks might contain empty data because of it's placeholders and stuffs like that
			$valid = array();

			foreach ($blocks as $block) {
				$lib = EB::blocks()->getBlockByType($block->type);

				$isValid = $lib->validate($block);

				if ($isValid) {

					$output = EB::blocks()->renderViewableBlock($block);

					// convert html entities back to it string. e.g. &nbsp; back to empty space
					$output = html_entity_decode($output);

					// strip html tags to precise length count.
					$output = strip_tags($output, '<iframe>');

					// remove any blank space.
					$output = trim($output);

					$content .= $output;

				}

				$valid[] = $isValid;
			}

			// Display an error message when the content is empty.
			if (!in_array(true, $valid)) {
				throw EB::exception('COM_EASYBLOG_COMPOSER_EMPTY_BLOCKS_CONTENT_ERROR');
			}

	 	} else {
	 		// legacy post
	 		$content = $this->intro . $this->content;

			// strip html tags to precise length count.
	 		$content = strip_tags($content);
	 	}

		// Do not allow blank content
		if (empty($content)) {
			// TODO: I don't like both the language string and the translated value.
			throw EB::exception('COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR');
		}

		// Ensure content exceeds minimum required length.
		if ($this->saveOptions['checkMinContentLength'] && $this->config->get('main_post_min')) {

			$length = JString::strlen($content);
			$minLength = $this->config->get('main_post_length');

			if ($length < $minLength) {
				throw EB::exception(JText::sprintf('COM_EASYBLOG_CONTENT_LESS_THAN_MIN_LENGTH', $minLength));
			}
		}

		$blockedWord = $this->string->hasBlockedWords($content);

		// Check for blocked words in content.
		if ($this->saveOptions['checkBlockedWords'] && $blockedWord !== false) {
			throw EB::exception(JText::sprintf('COM_EASYBLOG_BLOG_POST_CONTAIN_BLOCKED_WORDS', $blockedWord));
		}
	}

	/**
	 * Validates the custom fields for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validateFields()
	{
		foreach ($this->categories as $categoryId) {

			// Get a list of fields that are associated with the category
			$model = EB::model('Categories');
			$fields = $model->getCustomFields($categoryId);
			$isValid = true;

			foreach ($fields as $field) {

				if ($field->required) {

					if (is_array($this->fields) && (!isset($this->fields[$field->id]) || empty($this->fields[$field->id]))) {
						$isValid = false;
					}

					if (is_object($this->fields) && (!isset($this->fields->{$field->id}) || empty($this->fields->{$field->id}))) {
						$isValid = false;
					}
				}

			}

			if (!$isValid) {
				throw EB::exception('COM_EASYBLOG_FIELDS_REQUIRED_FIELDS_NOT_PROVIDED', EASYBLOG_MSG_ERROR, false, EASYBLOG_ERROR_CODE_FIELDS);
			}
		}
	}

	/**
	 * Retrieves the author of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAuthor()
	{
		// lets try to get from cache
		if (EB::cache()->exists($this->created_by, 'author')) {
			return EB::cache()->get($this->created_by, 'author');
		} else {
			return EB::user($this->created_by);
		}
	}

	/**
	 * Retrieves asset associated with the post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAsset($key)
	{
		static $items = array();

		$index = $this->id . $key;

		if (!isset($items[$index])) {
			$asset = EB::table('BlogAsset');
			$asset->load(array('post_id' => $this->id, 'key' => $key));

			$items[$index] = $asset;
		}

		return $items[$index];
	}

	/**
	 * Retrieves assets associated with the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssets()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = EB::model('Assets', true);
			$assets = $model->getPostAssets($this->id);

			$items[$this->id] = $assets;
		}

		return $items[$this->id];
	}

	/**
	 * Retrieves the current revision that is being displayed on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCurrentRevisionId()
	{
		return $this->post->revision_id;
	}

	/**
	 * Retrieves the active revision for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getWorkingRevision()
	{
		return $this->revision;
	}

	/**
	 * Retrieves all revisions that are associated with this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRevisions()
	{
		$model = EB::model('Revisions');

		$revisions = $model->getAllRevisions($this->id);

		return $revisions;
	}

	/**
	 * Retrieves the creation date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getCreationDate($withOffset = false)
	{
		return EB::date($this->created, $withOffset);
	}

	/**
	 * Retrieve a list of blocks in this block post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlocks($limit = null)
	{
		$document = EB::document($this->document);
		$blocks = $document->getBlocks();

		if (!is_null($limit)) {
			$blocks = array_slice($blocks, 0, $limit);
		}

		return $blocks;
	}

	/**
	 * Checks if this is a normal posting and is not related to an external or team source
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isStandardSource()
	{
		return $this->source_type == EASYBLOG_POST_SOURCE_SITEWIDE ? true : false;
	}

	/**
	 * check if this post is a teamblog post or not
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return true or false
	 */
	public function isTeamBlog()
	{
		return ($this->source_type == EASYBLOG_POST_SOURCE_TEAM) ? true : false;
	}

	/**
	 * Retrieves the blog contribution
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogContribution()
	{
		if (!$this->source_type || $this->source_type == EASYBLOG_POST_SOURCE_SITEWIDE) {
			return false;
		}

		$contributor = EB::contributor()->load($this->source_id, $this->source_type);

		return $contributor;
	}

	/**
	 * Retrieves comments for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getComments($limit = null)
	{
		if (!isset(self::$comments[$this->id])) {
			self::$comments[$this->id] = EB::comment()->getBlogComment($this->id, $limit, 'desc', true);
		}

		// If there's already data, we splice it out
		if (self::$comments[$this->id] && !is_null($limit)) {

			$comments = self::$comments[$this->id];

			if (count($comments) > $limit) {
				
				// if comment sorting set to desc
				if ($this->config->get('comment_sort') == 'desc') {
					$comments = array_reverse($comments);
				}

				array_splice($comments, $limit);

				return $comments;
			}
		}

		return self::$comments[$this->id];
	}

	/**
	 * Get total number of comments for this blog post
	 *
	 * @access	public
	 * @param	null
	 * @return	int
	 */
	public function getTotalComments()
	{
		if (!isset(self::$commentCounts[$this->id])) {

			$count = EB::comment()->getCommentCount($this);

			self::$commentCounts[$this->id] = $count;
		}

		return self::$commentCounts[$this->id];
	}

	/**
	 * Check whether to display comment count or not
	 *
	 * @access	public
	 * @param	null
	 * @return	int
	 */
	public function displayCommentCount()
	{
		if (!$this->config->get('main_comment') || !$this->allowcomment) {
			return false;
		}

		// if Easyblog using Easysocial Comment, don't display the comment counter
		if ($this->config->get('comment_easysocial')) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve the location image
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLocationImage()
	{
		$url = '//maps.googleapis.com/maps/api/staticmap?size=1280x1280&sensor=true&scale=2&zoom=15&center=' . $this->latitude . ',' . $this->longitude . '&markers=' . $this->latitude . ',' . $this->longitude;

		return $url;
	}

	/**
	 * Displays the display date
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	// @ported to getModifiedDate
	public function getDisplayDate($column = 'created', $withOffset = true)
	{
		if (!isset($this->$column)) {
			$column = 'created';
		}

		$value = $this->$column;

		$date = EB::date($value, $withOffset);

		return $date;
	}

	/**
	 * Retrieves the date time value
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFormDateValue($column = 'created')
	{
		$value = $this->$column;

		if (!$value) {
			return '';
		}

		$date = EB::date($value, true);
		$value = $date->toSQL(true);

		return $value;
	}

	/**
	 * Get the intro text of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
    public function getIntro($stripTags = false, $truncate = true, $source = 'all', $limit = null, $options = array())
	{
		$index = $stripTags ? 'stripped' : 'raw';

		if (isset($this->formattedIntros[$index]) && $this->formattedIntros[$index]) {
			return $this->formattedIntros[$index];
		}

		// We need to specify if this post has a cover picture or not to avoid
		// multiple images being shown on the "listings"
		if (!isset($options['hasCover'])) {
			$options['hasCover'] = $this->hasImage();
		}

		// If this is an EBD document, we need to render the contents using blocks
		if ($this->isEbd()) {

			$doc = EB::document($this->document);
			$intro = $doc->getIntro($stripTags, $limit, $options);

			if ($this->config->get('main_anchor_nofollow')) {
				$intro = EB::string()->addNoFollow($intro);
			}

			$this->formattedIntros[$index] = $intro;
			return $this->formattedIntros[$index];
		}


		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->intro = EB::string()->addNoFollow($this->intro);
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Set the text attribute first
		if ($source == 'all') {
			$this->text = $this->intro . $this->content;
		} else {
			$this->text = $this->$source;
		}

		// Truncate the content only in listing views (legacy posts)
		if ($truncate) {
			EB::truncater()->truncate($this, $limit);
		}

		// Determines if we should trigger plugins
		$triggerPlugins = isset($options['triggerPlugins']) ? $options['triggerPlugins'] : true;

		if ($triggerPlugins) {
			// Perform legacy formatting for legacy posts
			// Trigger plugins to prepare the content.
			$this->prepareContent();
		}

		if ($stripTags) {
			$this->text = strip_tags($this->text);
		}

		$this->formattedIntros[$index] = $this->text;

		return $this->formattedIntros[$index];

	}

	/**
	 * Retrieves the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The type of display mode. list (May contain automated truncation). entry (Contains the full blog post content)
	 * @return
	 */
	public function getContent($type = 'list', $triggerPlugin = true)
	{

		$idx = $type . (int) $triggerPlugin;

		// The reason we need to cache the content is to avoid javascripts from the blocks being collected multiple times.
		// Until we solve the issue with the javascript in the block handlers being collected more than once, we need to cache this contents.
		if (isset($this->formattedContents[$idx]) && $this->formattedContents[$idx]) {
			return $this->formattedContents[$idx];
		}

		// If this is a listing type, the contents might need to be truncated
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);


			// Listing view where we only retrieve the intro part of a document
			if ($type == 'list') {
				$contents = $document->getIntro();

				// we need this so that content plugins can do their jobs.
				$this->intro = $contents;
			}

			// We need this so that content plugins can do their jobs.
			if ($type == 'entry') {

				// Since we are getting the entire contents from the block, the intro should be reset to empty
				$this->intro = '';

				$contents = $document->getContent();

				// Process any adsense codes
				$contents = EB::adsense()->process($contents, $this->created_by);

				$this->content = $contents;
			}
		}

		// Legacy post on entry view
		if ($this->doctype != 'ebd' && $type == 'entry') {

			// Process videos in the intro
			$this->intro = EB::videos()->processVideos($this->intro);
			$this->content	= EB::videos()->processVideos($this->content);

			// Process audio files.
			$this->intro = EB::audio()->process($this->intro);
			$this->content = EB::audio()->process($this->content);

			// Process any adsense codes
			$this->intro = EB::adsense()->process($this->intro, $this->created_by);
			$this->content = EB::adsense()->process($this->content, $this->created_by);

			// Process gallery codes in the content
			$this->intro = EB::gallery()->process($this->intro, $this->created_by);
			$this->content = EB::gallery()->process($this->content, $this->created_by);

			// Process album codes in the content
			$this->intro = EB::album()->process($this->intro, $this->created_by);
			$this->content = EB::album()->process($this->content, $this->created_by);

			// Hide introtext if necessary
			if ($this->config->get('main_hideintro_entryview') && !empty($this->content)) {
				$this->intro = '';
			}
		}

		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->intro = EB::string()->addNoFollow($this->intro);
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Truncate the contents first
		$this->text = $this->intro . $this->content;

		if ($this->doctype != 'ebd' && $type == 'list') {
			$this->text = EB::truncater()->truncate($this);
		}

		// Trigger plugins to prepare the content.
		if ($triggerPlugin) {
			$this->prepareContent();
		}

		// Cache the item so the document will not be rendered more than once.
		$this->formattedContents[$idx] = $this->text;

		return $this->formattedContents[$idx];
	}

	/**
	 * Retrieves the content of the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string	The type of display mode. list (May contain automated truncation). entry (Contains the full blog post content)
	 * @return
	 */
	public function getPlainContent()
	{
		static $cache = array();


		$idx = $this->id;

		if (isset($cache[$idx])) {
			return $cache[$idx];
		}

		$content = "";

		// If this is a listing type, the contents might need to be truncated
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);
			$contents = $document->getContent();

			// Process any adsense codes
			$contents = EB::adsense()->process($contents, $this->created_by);
			$content = $contents;
		} else {

			$content = $this->intro . $this->content;

			// Process videos in the intro
			$content	= EB::videos()->processVideos($content);

			// Process audio files.
			$content = EB::audio()->process($content);

			// Process any adsense codes
			$content = EB::adsense()->process($content, $this->created_by);

			// Process gallery codes in the content
			$content = EB::gallery()->process($content, $this->created_by);

			// Process album codes in the content
			$content = EB::album()->process($content, $this->created_by);
		}

		$cache[$idx] = $content;
		return $content;
	}


	/**
	 * Prepares the content without intro text.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function getContentWithoutIntro($type = 'entry', $triggerPlugin = true)
	{
		$index = 'non-intro-' . $type;

		// echo $this->content;exit;

		// The reason we need to cache the content is to avoid javascripts from the blocks being collected multiple times.
		// Until we solve the issue with the javascript in the block handlers being collected more than once, we need to cache this contents.
		if (isset($this->formattedContents[$index]) && $this->formattedContents[$index]) {
			return $this->formattedContents[$index];
		}

		// If this is a listing type, the contents might need to be truncated
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);

			$contents = $document->getContentWithoutIntro();

			// we need this so that content plugins can do their jobs.
			$this->intro = '';
			$this->content = $contents;

		} else {

			// Process videos in the intro
			$this->intro = EB::videos()->processVideos($this->intro);
			$this->content	= EB::videos()->processVideos($this->content);

			// Process audio files.
			$this->intro = EB::audio()->process($this->intro);
			$this->content = EB::audio()->process($this->content);

			// Process any adsense codes
			$this->intro = EB::adsense()->process($this->intro, $this->created_by);
			$this->content = EB::adsense()->process($this->content, $this->created_by);

			// Process gallery codes in the content
			$this->intro = EB::gallery()->process($this->intro, $this->created_by);
			$this->content = EB::gallery()->process($this->content, $this->created_by);

			// Process album codes in the content
			$this->intro = EB::album()->process($this->intro, $this->created_by);
			$this->content = EB::album()->process($this->content, $this->created_by);

			$textLen = strip_tags($this->content);
			$textLen = str_replace(array(' ','&nbsp;', "\n", "\t", "\r","\r\n"), '', $textLen);

			if (empty($textLen)) {
				$this->content = $this->intro;
			} else {
				$this->intro = '';
			}
		}

		// If necessary, add nofollow to the anchor links of the blog post
		if ($this->config->get('main_anchor_nofollow')) {
			$this->intro = EB::string()->addNoFollow($this->intro);
			$this->content = EB::string()->addNoFollow($this->content);
		}

		// Truncate the contents first
		$this->text = $this->intro . $this->content;

		if ($this->doctype != 'ebd' && $type == 'list') {
			$this->text = EB::truncater()->truncate($this);
		}

		// Trigger plugins to prepare the content.
		if ($triggerPlugin) {
			$this->prepareContent();
		}

		// lets get the contents after content plugins processed the content.
		$contents = $this->content;

		// Cache the item so the document will not be rendered more than once.
		$this->formattedContents[$index] = $contents;

		return $this->formattedContents[$index];
	}




	/**
	 * Prepares the content before displaying it out.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function prepareContent($type = 'list')
	{
		static $prepareContent = array();

		if (!isset($prepareContent[$this->id])) {

			// Get the application
			$app = JFactory::getApplication();

			// Load up Joomla's dispatcher
			$dispatcher	= JDispatcher::getInstance();

			// @trigger: onEasyBlogPrepareContent
			JPluginHelper::importPlugin('easyblog');
			EB::triggers()->trigger('easyblog.prepareContent', $this);

			// @trigger: onEasyBlogPrepareContent
			JPluginHelper::importPlugin('content');
			EB::triggers()->trigger('prepareContent', $this);

			// For additional joomla content triggers, we need to store the output in various "sections"
			//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
			$this->event = new stdClass();

			// @trigger: onAfterDisplayTitle / onContentAfterTitle
			$results = EB::triggers()->trigger('afterDisplayTitle', $this);
			$this->event->afterDisplayTitle = JString::trim(implode("\n", $results));

			// @trigger: onBeforeDisplayContent / onContentBeforeDisplay
			$results = EB::triggers()->trigger('beforeDisplayContent', $this);
			$this->event->beforeDisplayContent = JString::trim(implode("\n", $results));

			// @trigger: onAfterDisplayContent / onContentAfterDisplay
			$results = EB::triggers()->trigger('afterDisplayContent', $this);
			$this->event->afterDisplayContent = JString::trim(implode("\n", $results));

			// Once the whole fiasco of setting the attributes back and forth is done, unset unecessary attributes.
			// unset($this->introtext);
			// unset($this->text);
			$prepareContent[$this->id] = true;
		}

		return $prepareContent[$this->id];
	}

	/**
	 * Retrieves the modified date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getModifiedDate()
	{
		// @ported getDisplayDate
		return EB::date($this->modified);
	}

	/**
	 * Retrieves the published date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getPublishDate()
	{
		// @ported getPublishingDate
		return EB::date($this->publish_up);
	}

	/**
	 * Retrieves the unpublished date of the item
	 *
	 * @since	5.0
	 * @access	public
	 * @return	EasyBlogDate
	 */
	public function getUnpublishDate()
	{
		return EB::date($this->publish_down);
	}


	/**
	 * Retrieves a list of subscribers for this particular post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getSubscribers($excludeEmails = array())
	{
		$db = EB::db();

		$exclusion = '';

		if (!empty($excludeEmails)) {

			foreach ($excludeEmails as $email) {
				$exclusion .= empty($exclusion) ? $db->Quote($email) : ',' . $db->Quote($email);
			}
		}

		$query = array();
		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_subscriptions');
		$query[] = 'WHERE ' . $db->quoteName('uid') . '=' . $db->Quote($this->id);
		$query[] = 'AND ' . $db->quoteName('utype') . '=' . $db->Quote(EBLOG_SUBSCRIPTION_ENTRY);


		if (!empty($exclusion)) {
			$query[] = 'AND ' . $db->quoteName('email') . ' NOT IN(' . $exclusion . ')';
		}

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	/**
	 * Determines if the current item that is being saved is on the same revision
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isCurrent()
	{
		// Returns true if current revision is being used by post table
		return isset($this->revision) && ($this->post->revision_id == $this->revision->id);
	}

	/**
	 * Determines if the post is either published, scheduled or unpublished.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFinalized()
	{
		return $this->isPublished() || $this->isScheduled() || $this->isUnpublished();
	}

	/**
	 * Determines if the post's revision is finalized and also being used by the current post table.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isReady()
	{
		// If this revision is being used by the post table and the revision is finalized.
		return $this->isCurrent() && $this->isFinalized();
	}

	/**
	 * Determines if the post is available or in other words, visible on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAvailable()
	{
		return $this->isCurrent() && $this->isPublished();
	}

	/**
	 * Determines if the post is accessible by the current user viewing the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isAccessible()
	{
		$allowed = EB::privacy()->checkPrivacy($this);

		if (!$allowed->allowed) {
			return $allowed;
		}

		// Check against the primary category permissions
		$category = $this->getPrimaryCategory();

		if ($category->private != 0) {
			$allowed = $category->checkPrivacy();
		}

		return $allowed;
	}

	/**
	 * When initiating a new post on the site the current publishing state of the blog post is blank
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBlank()
	{
		return $this->published == EASYBLOG_POST_BLANK;
	}

	/**
	 * Determines when a blog post is on the draft state
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isDraft()
	{
		return $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 * Determines if this post has any revisions that is waiting for approval
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasRevisionWaitingForApproval()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = EB::model('Revisions');
			$items[$this->id] = $model->isWaitingApproval($this->id);
		}

		return $items[$this->id];
	}

	/**
	 * Detmermines if the blog post is pending
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPending()
	{
		// Note: "ispending" column is deprecated.
		// During upgrade we'll need to fetch all ispending=1 posts
		// and change the published state to pending.
		return $this->published == EASYBLOG_POST_PENDING;
	}

	/**
	 * Determines if the blog post is scheduled to be published
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isScheduled()
	{
		return $this->published == EASYBLOG_POST_SCHEDULED;
	}

	/**
	 * Determines if this post belongs to the current logged in user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isMine()
	{
		return $this->created_by == $this->my->id;
	}

	/**
	 * Determines if the post item is published
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPostPublished()
	{
		return $this->post->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if the blog post is published
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPublished()
	{
		// Note: This logic is different from the table version.
		return $this->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 * Determines if the blog post is unpublished on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isUnpublished()
	{
		return $this->published == EASYBLOG_POST_UNPUBLISHED;
	}

	/**
	 * Determines if the item is trashed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isTrashed()
	{
		return $this->state == EASYBLOG_POST_TRASHED;
	}

	/**
	 * Determines if the item is archived
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isArchived()
	{
		return $this->state == EASYBLOG_POST_ARCHIVED;
	}

	/**
	 * Determines if the item is being created.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingCreated()
	{
		return $this->published == EASYBLOG_POST_BLANK;
	}

	/**
	 * Determines if this post is being drafted
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingDrafted()
	{
		return $this->original->published != EASYBLOG_POST_DRAFT && $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 * Determines if this current item is being submitted for approval
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingSubmittedForApproval()
	{
		return $this->original->published != EASYBLOG_POST_PENDING
			&& $this->published == EASYBLOG_POST_PENDING;
	}

	/**
	 * Determines if this item is being approved by a moderator.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingApproved()
	{
		return $this->original->published == EASYBLOG_POST_PENDING
			&& $this->published == EASYBLOG_POST_PUBLISHED;
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingRejected()
	{
		return $this->original->published == EASYBLOG_POST_PENDING
			&& $this->published == EASYBLOG_POST_DRAFT;
	}

	/**
	 *
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingScheduledForPublishing()
	{
		return $this->original->published != EASYBLOG_POST_SCHEDULED
			&& $this->published == EASYBLOG_POST_SCHEDULED;
	}

	/**
	 * Determines if the post is being published on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingPublished()
	{
		return $this->original->published != EASYBLOG_POST_PUBLISHED
			&& $this->published == EASYBLOG_POST_PUBLISHED
			&& $this->isNoLongerNew();
	}

	/**
	 * Determines if the post is being unpublished
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingUnpublished()
	{
		return $this->original->published != EASYBLOG_POST_UNPUBLISHED
			&& $this->published == EASYBLOG_POST_UNPUBLISHED;
	}

	/**
	 * Determines if the post is being republished again.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingRepublished()
	{
		return $this->original->published != EASYBLOG_POST_PUBLISHED
			&& $this->published == EASYBLOG_POST_PUBLISHED
			&& !$this->isnew;
	}

	/**
	 * Determines if the post is being archived
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingArchived()
	{
		return $this->original->state != EASYBLOG_POST_ARCHIVED
			&& $this->state == EASYBLOG_POST_ARCHIVED;
	}

	/**
	 * Determines if the post is being trashed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isBeingTrashed()
	{
		return $this->original->state != EASYBLOG_POST_TRASHED
			&& $this->state == EASYBLOG_POST_TRASHED;
	}

	/**
	 * Determines if this is a new post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isNew()
	{
		$isNew = false;

		if ($this->isnew) {
			$isNew = true;
		} else if ($this->original->isnew) {
			$isNew = true;
		}

		return $isNew;
	}

	/**
	 * Determines if this is an 'ebd' post type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isEbd()
	{
		return $this->doctype == 'ebd';
	}

	/**
	 * Determines if this is a legacy post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isLegacy()
	{
		if ($this->doctype == 'legacy') {
			return true;
		}

		return false;
	}

	/**
	 * Determines if this post is no longer a new post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isNoLongerNew()
	{
		return $this->original->isnew && !$this->isnew;
	}

	/**
	 * Verifies a password
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function verifyPassword()
	{
		// If the author is viewing their own post we do not need to verify the password.
		if (EB::user()->id == $this->getAuthor()->id) {
			return true;
		}

		$session = JFactory::getSession();
		$password = $session->get('PROTECTEDBLOG_' . $this->id, '', 'EASYBLOG');

		if ($password == $this->blogpassword) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if this post is password protected
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPasswordProtected()
	{
		if ($this->config->get('main_password_protect', true) && !empty($this->blogpassword)) {
			if (!EB::verifyBlogPassword($this->blogpassword, $this->id)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Determines if this post is featured
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeatured()
	{
		if (!$this->id) {
			return false;
		}

		static $featured = array();

		if (!isset($featured[$this->id])) {

			$model = EB::model('Blog');
			$isFeatured	= $model->isFeatured($this->id);

			$featured[$this->id]	= $isFeatured;
		}

		return $featured[$this->id];
	}

	/**
	 * Determines if this post is from a feed
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFromFeed()
	{
		$db = EB::db();
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_feeds_history');
		$query[] = 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($this->id);

		$query = implode(' ', $query);

		$db->setQuery($query);

		$imported = $db->loadResult();

		return $imported;
	}

	/**
	 * Retrieves the preview link of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPreviewLink($xhtml = true)
	{
		$url = 'index.php?option=com_easyblog&view=entry&layout=preview&uid=' . $this->id . '.' . $this->revision_id;
		$url = EBR::getRoutedURL($url, $xhtml, true);

		return $url;
	}

	/**
	 * Retrieves the edit link of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditLink($xhtml = true)
	{
		// If this is an admin view, route it through /administrator/index.php
		if ($this->app->isAdmin()) {

			$url = 'index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $this->id . '.' . $this->revision_id;

			return $url;
		}

		$url = EBR::getRoutedURL('index.php?option=com_easyblog&view=composer&tmpl=component&uid=' . $this->id . '.' . $this->revision_id, $xhtml, true);

		return $url;
	}

	/**
	 * Retrieves the alias of a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		static $permalinks = array();

		if (!isset($permalinks[$this->id])) {

			$date = EB::date($this->created);

			// Default permalink
			$permalink = $this->permalink;

			// Ensure that the permalink is valid.
			$permalink = EBR::normalizePermalink($permalink);

			if ($this->config->get('main_sef_unicode') || !EBR::isSefEnabled()) {
				$permalink = $this->id . '-' . $permalink;
			}

			// Date based permalink
			$datePermalink = $date->format('Y') . '/' . $date->format('m') . '/' . $date->format('d');

			// Date based SEF settings
			if ($this->config->get('main_sef') == 'date') {
				$permalink = $datePermalink . '/' . $permalink;
			}

			// Category based permalink type
			if ($this->config->get('main_sef') == 'datecategory' || $this->config->get('main_sef') == 'category') {

				// Get the current primary category
				$category = $this->getPrimaryCategory();

				$categoryPermalink = $category->getAlias();

				// Date and category based permalink type
				if ($this->config->get('main_sef') == 'datecategory') {
					$permalink = $categoryPermalink . '/' . $datePermalink . '/' . $permalink;
				} else {
					$permalink = $categoryPermalink . '/' . $permalink;
				}
			}

			// Custom based permalink type
			if ($this->config->get('main_sef') == 'custom') {
				$permalink = EBR::getCustomPermalink($this);
			}

			$permalinks[$this->id] = $permalink;
		}

		return $permalinks[$this->id];
	}

	/**
	 * Retrieves the print link for a post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrintLink()
	{
		$url = 'index.php?option=com_easyblog&view=entry&id=' . $this->id;
		$url = EBR::_($url, false);

		if (EBR::isSefEnabled()) {
			$url .= '?tmpl=component&print=1&format=print';

		} else {
			$url .= '&tmpl=component&print=1&format=print';
		}

		return $url;
	}

	/**
	 * Retrieves the permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	bool
	 * @return	string
	 */
	public function getPermalink($xhtml = true, $external = false, $format = null)
	{
		if ($external) {
			$url = $this->getExternalPermalink();
		} else {
			$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $this->id, $xhtml);
		}

		$url = EBR::appendFormatToQueryString($url, $format);

		return $url;
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink()
	{
		static $link = array();

		if (!isset($link[$this->id])) {

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404 = EBR::isSh404Enabled();
			$url = 'index.php?option=com_easyblog&view=entry&id=' . $this->id;

			$link[$this->id] = EBR::getRoutedURL($url, true, true);
			$app = JFactory::getApplication();

			// If this is being submitted from the back end we do not want to use the sef links because the URL will be invalid
			if ($app->isAdmin() && $sh404) {
				$link[$this->id] = $url;
				$link[$this->id] = rtrim(JURI::root(), '/') . '/' . $link[$this->id];
			}
		}

		return $link[$this->id];
	}

	/**
	 * Get permalink to the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalBlogLink($url)
	{
		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists = EasyBlogRouter::isSh404Enabled();

		$link = EasyBlogRouter::getRoutedURL($url, false, true);
		$app = JFactory::getApplication();

		if ($app->isAdmin() && $sh404exists) {
			$link = rtrim(JURI::root() , '/') . $url;
		}

		return $link;
	}

	/**
	 * Determines if this post has an image associated
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasImage()
	{
		$hasImage = !empty($this->image);

		return $hasImage;
	}

	/**
	 * Retrieves the blog image for this blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImage($size = 'original', $showPlaceholder = true, $protocol = false)
	{
		static $cache	= array();

		$index = $this->id . '-' . $size . $protocol;

		// Default blog image
		$default = false;

		// Display a default place holder
		if ($showPlaceholder) {
			$default = EB::getPlaceholderImage();
		}

		if (!isset($cache[$index])) {

			// If there's no image data for this post, skip this altogether
			if (!$this->image) {
				$cache[$index] = $default;
				return $cache[$index];
			}

			// Ensure that the image is normalized
			$this->normalizeBlogImage();

			// Load up the media manager library
			$mm = EB::mediamanager();

			$url = $mm->getUrl($this->image);
			$path = $mm->getPath($this->image);
			$fileName = $mm->getFilename($this->image);

			// Ensure that the item really exist before even going to do anything on the original image.
			// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
			$exists = JFile::exists($path);

			// If the blog image file doesn't exist, we use the default
			if (!$exists) {
				$cache[$index] = $default;

				return $cache[$index];
			}

			$image = EB::blogimage($path, $url);

			$cache[$index] = $image->getSource($size, false, $protocol);
		}


		return $cache[$index];
	}

	public function getContentImage()
	{
		$content = $this->getPlainContent();
		$img = EB::string()->getImage($content);
		return $img;
	}


	/**
	 * Retrieves first image from blog content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */

	/**
	 * Retrieves the primary category for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPrimaryCategory()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$category = null;

			// lets try to load from cache
			if (EB::cache()->exists($this->id,'posts')) {
				$data = EB::cache()->get($this->id,'posts');

				if (isset($data['primarycategory'])) {
					$category = $data['primarycategory'];
				}
			}

			if (! $category) {
				$model = EB::model('Categories');
				$category = $model->getPrimaryCategory($this->id);

				// Detect legacy category which uses `category_id`
				if ($category === false && $this->category_id) {
					$category = EB::table('Category');
					$category->load($this->category_id);
				}
			}

			$items[$this->id] = $category;
		}

		return $items[$this->id];
	}

	/**
	 * Determines if the blog post is associated with a team
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTeamAssociation()
	{
		if ($this->source_id && $this->source_type == EASYBLOG_POST_SOURCE_TEAM) {
			return $this->source_id;
		}

		return false;
	}

	/**
	 * Retrieves a list of users that subscribed to this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRegisteredSubscribers($type = 'new', $exclusion = array())
	{
		if ($type == 'new') {
			$model = EB::model('Subscription');
			$subscribers = $model->getSiteSubscribers();

			$categoryModel = EB::model('Category');
			$subscribers = array_merge($subscribers, $categoryModel->getCategorySubscribers($this->category_id));
		}

		$result = array();

		if (!$subscribers) {
			return $result;
		}

		foreach ($subscribers as $subscriber) {
			if ($subscriber->user_id && !in_array($subscriber->user_id, $exclusion)) {
				$result[] = $subscriber->user_id;
			}
		}

		$result = array_unique($result);

		return $result;
	}

	/**
	 * This method will intelligently determine which menu params this post should be inheriting from
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMenuParams()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$model = EB::model('Menu');

			// If there is an article menu item associated with this post, use this
			$menuId = $model->getMenusByPostId($this->id);

			$params = null;
			$catParams = $this->category->getParams();

			$arrCatParams = $catParams->toArray();

			if (empty($arrCatParams)) {
				// look like the params in category is empty. let try to get from Entry xml file.
				// Because this is entry view. Previously, we get from category listing xml.
				// $catParams = $this->category->getDefaultParams();

				$tmpParams = $model->getDefaultEntryXMLParams();
				$arrCatParams = $tmpParams->toArray();
			}

			foreach($arrCatParams as $key => $val) {
				if ($val == '-1') {
					// this mean we inherit from global setting.
					$catParams->set($key, $this->config->get('layout_' . $key));
				}
			}

			if ($menuId) {

				/*
				 * seems like menu->params will return u null if the value is a negative value. We will need to manually retrive via db sql.
				 */
				// $menu = JFactory::getApplication()->getMenu();
				// $params = $menu->getItem($menuId)->params;

				$params = $model->getMenuParamsById($menuId);
				$arrParams = $params->toArray();

				if (! isset($arrParams['show_intro'])) {
					// this mean this menu item created prior to 5.0. we will use category param.
					$items[$this->id] = $catParams;
					return $items[$this->id];
				}

				foreach($arrParams as $key => $val) {
					if ($val == '-1') {
						// this mean we inherit from category setting. lets get the value from category params.
						$params->set($key, $catParams->get($key));

					}
				}

				$items[$this->id] = $params;

			} else {
				// If there's no menu associated with the post, associate the params with the primary category
				$items[$this->id] = $catParams;
			}

		}

		return $items[$this->id];
	}

	/**
	 * Retrieves the rating of the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRatings()
	{
		// If we don't have a copy of the ratings, we should get it.
		if (!isset(self::$ratings[$this->id])) {
			$model = EB::model('Ratings');
			$ratings = $model->preloadRatings(array($this->id));

			if (!$ratings) {
				self::$ratings[$this->id] = new stdClass();
				self::$ratings[$this->id]->ratings = 0;
				self::$ratings[$this->id]->total = 0;

				return self::$ratings[$this->id];
			}
			self::$ratings[$this->id] = $ratings[$this->id];
		}

		return self::$ratings[$this->id];
	}

	/**
	 * Retrieves the uid for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUid()
	{
		return $this->uid;
	}

	/**
	 * Retrieves a list of seo keywords for this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getKeywords()
	{
		if (!$this->keywords) {
			return array();
		}

		$keywords = explode(',', $this->keywords);

		return $keywords;
	}

	/**
	 * Retrieves the post type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getType()
	{
		return $this->posttype;
	}

	/**
	 * Get a list of tag objects that are associated with this blog post.
	 *
	 * @access	public
	 * @param	null
	 * @return	Array	An Array of TableTag objects.
	 */
	public function getTags()
	{
		static $instances = array();

		if (!isset($instances[$this->id])) {

			$tags = array();

			// lets load from cache
			if (EB::cache()->exists($this->id, 'posts')) {
				$data = EB::cache()->get($this->id, 'posts');

				if (isset($data['tag'])) {
					$tags = $data['tag'];
				}
			} else {
				$model = EB::model('PostTag');
				$tags = $model->getBlogTags($this->id);
			}

			$instances[$this->id]	= $tags;
		}

		return $instances[$this->id];
	}

	/**
	 * Retrieves a list of categories associated with this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategories()
	{
		static $categories = array();

		if (!isset($categories[$this->id])) {

			$results = array();

			// lets load from cache if there is any
			if (EB::cache()->exists($this->id, 'posts')) {
				$data = EB::cache()->get($this->id, 'posts');

				if ($data['category']) {
					$results = $data['category'];
				}
			} else {
				$model 	= EB::model('Categories');
				$results = $model->getBlogCategories($this->id);
			}

			$categories[$this->id] = $results;
		}

		return $categories[$this->id];
	}


	/**
	 * Retrieves a list of categories associated with this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRevisionCount($type = EASYBLOG_REVISION_DRAFT)
	{
		$model 	= EB::model('Revisions');

		return $model->getRevisionCount($this->id, $type);
	}



	/**
	 * Retrieves a list of custom fields associated to this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFields()
	{
		static $items = array();

		if (!isset($items[$this->id])) {
			$categories = $this->getCategories();

			if (!$categories) {
				$items[$this->id] = false;
				return false;
			}

			$fields = array();
			$hasFields = false;

			foreach ($categories as $category) {
				$categoryFields = $category->getCustomFields();

				if ($categoryFields !== false) {
					$fields[] = $categoryFields;
					$hasFields = true;
				}
			}

			if (!$hasFields) {
				$items[$this->id] = false;
				return false;
			}

			$items[$this->id] = $fields;

		}

		return $items[$this->id];
	}

	/**
	 * Determines if the current visitor has voted on this item
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasVoted($userId = null)
	{
		if (isset(self::$postVotes[$this->id])) {
			return self::$postVotes[$this->id];
		}

		if (is_null($userId)) {
			$userId = $this->my->id;
		}

		$hash   = '';
		$ipaddr = '';
		if (empty($userId)) {
			//mean this is a guest.
			$hash = JFactory::getSession()->getId();
			$ipaddr = @$_SERVER['REMOTE_ADDR'];
		}

		$model = EB::model('ratings');
		return $model->hasVoted($this->id, EASYBLOG_RATINGS_ENTRY, $userId, $hash, $ipaddr);
	}

	/**
	 * Determines if a property of the item has changed or not.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasChanged($prop)
	{
		return $this->$prop != $this->original->$prop;
	}

	/**
	 * Determines if the post has location
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasLocation()
	{
		if ($this->address && $this->latitude && $this->longitude) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if there is a readmore tag in this content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasReadmore()
	{
		// By default, display the read more link if not configured to respect read more.
		if (!$this->config->get('layout_respect_readmore')) {
			return true;
		}

		// If this is ebd document type, we need to utilize the document library to determine if there's a read more link
		if ($this->doctype == 'ebd') {

			$document = EB::document($this->document);

			return $document->hasReadmore();
		}

		// If this is a legacy document, and we know that the intro and content is not empty, there's definitely a read more
		if ($this->intro && $this->content) {
			return true;
		}

		// Get the maximum character before read more kicks in.
		$max = $this->config->get('layout_maxlengthasintrotext', 150);

		// If there's a read more attribute injected on this library, we need to respect that
		if (isset($this->readmore)) {
			return $this->readmore;
		}

		// Final fallback is to check the characters length.
		$length	= JString::strlen(strip_tags($this->intro));

		if ($length > $max && $this->config->get('layout_blogasintrotext')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the user needs to login to read the post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function requiresLoginToRead()
	{
		// If the settings requires the user to be logged in, do not allow guests here.
		if ($this->my->guest && $this->config->get('main_login_read')) {

			$currentUri = JRequest::getURI();
			$uri = base64_encode($currentUri);

			$url = EBR::_('index.php?option=com_easyblog&view=login&return=' . $uri, false);

			return $this->app->redirect($url);
		}

		return false;
	}

	/**
	 * Determines if the current user can view this post or not.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkView()
	{
		// If the blog post is already deleted, we shouldn't let it to be accessible at all.
		if ($this->isTrashed()) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// Check if the blog post is trashed
		if (!$this->isPublished() && $this->my->id != $this->created_by && !EB::isSiteAdmin() && (!$this->acl->get('manage_pending') && !$this->acl->get('moderate_entry'))) {
			return EB::exception('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND', 'error');
		}

		// Check if the user is allowed to view
		if (!$this->checkTeamPrivacy()) {
			return EB::exception('COM_EASYBLOG_TEAMBLOG_MEMBERS_ONLY', 'error');
		}

		// Check if the blog post is accessible.
		$accessible = $this->isAccessible();

		if (!$accessible->allowed) {
			return EB::exception($accessible->error, 'error');
		}

		return true;
	}

	/**
	 * Determines if the user is allowed to view this post if this post is associated with a team.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function checkTeamPrivacy()
	{
		$id = $this->getTeamAssociation();

		// This post is not associated with any team, so we do not need to check anything on the privacy
		if (!$id) {
			return true;
		}

		$team = EB::table('TeamBlog');
		$team->load($id);

		// If the team access is restricted to members only
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER && !$team->isMember($this->my->id) && !EB::isSiteAdmin()) {
			return false;
		}

		// If the team access is restricted to registered users, ensure that they are logged in.
		if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED && $this->my->guest) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the current user can create a new post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canCreate()
	{
		return $this->acl->get('add_entry') || EB::isSiteAdmin($this->user->id);
	}

	/**
	 * Determines if the user can moderate the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canModerate()
	{
		if (EB::isSiteAdmin($this->user->id)) {
			return true;
		}

		if ($this->acl->get('moderate_entry')) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can delete this post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canDelete()
	{
		// If the user is a site admin or has moderation access, they should always be able to delete entry
		if (EB::isSiteAdmin() || $this->acl->get('moderate_entry')) {
			return true;
		}

		if ($this->created_by == $this->user->id && $this->acl->get('delete_entry')) {
			return true;
		}

		$model = EB::model('TeamBlogs');

		if ($this->source_type == EASYBLOG_POST_SOURCE_TEAM && $model->isTeamAdmin($this->source_id)) {
			return true;
		}

		// Get the contribution type
		if (!$this->isStandardSource()) {
			$contribution = $this->getBlogContribution();

			if ($contribution->canDelete()) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	 * Determines if the current viewer can edit this post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canEdit()
	{
		if ($this->created_by == $this->user->id || $this->acl->get('moderate_entry') || EB::isSiteAdmin()) {
			return true;
		}

		// If this is a team blog posting, ensure that the user has access to edit this
		if ($this->source_type == EASYBLOG_POST_SOURCE_TEAM) {

			$model = EB::model('Teamblogs');

			if ($model->isTeamAdmin($this->source_id)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determines if the current viewer is allowed to publish this post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canPublish()
	{
		if ($this->acl->get('publish_entry') || EB::isSiteAdmin()) {
			return true;
		}

		return false;
	}

	/**
	 * Generates a list of class names for composer
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderClassnames($user=null)
	{
		$classes = array();

		if ($this->acl->get('publish_entry')) {
			$classes[] = 'can-publish';
		} else {
			$classes[] = 'can-save';
		}

		// If user can moderate this post
		if ($this->acl->get('moderate_entry')) {
			$classes[] = 'can-moderate';
		}

		// Revision state
		$classes[] = $this->revision->getCssState();

		return implode(' ', $classes);
	}

	/**
	 * Used during post editing so we can render the necessary blocks
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderEditorContent()
	{
		if ($this->doctype=='ebd') {

			// If this is a empty document,
			// start with a text block.
			if (empty($this->document)) {

				$blocks = EB::blocks();
				$block = $blocks->createBlock("text");
				$content = $blocks->renderEditableBlock($block);

			// If this is an existing document,
			// get editable content.
			} else {
				$document = EB::document($this->document);
				$content = $document->getEditableContent();
			}

		} else {

			// Format the post content now
			$content = $this->intro;

			// Append the readmore if necessary
			if (!empty($this->intro) && !empty($this->content)) {
				$content .=  '<hr id="system-readmore" />';
			}

			// Append the rest of the contents
			$content .= $this->content;
		}

		return $content;
	}

	/**
	 * Initializes the header of the html page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderHeaders()
	{
		// Load meta data
		EB::setMeta($this->id, META_TYPE_POST);

		// If there's robots set on the page, initialize it
		if ($this->robots) {
			$this->doc->setMetaData('robots', $this->robots);
		}

		// If there's a copyright notice, add it into the header
		if ($this->copyrights) {
			$this->doc->setMetaData('rights', $this->copyrights);
		}

		// Determines if the user wants to print this page
		$print = $this->input->get('print', 0, 'int');

		// Add noindex for print view by default.
		if ($print) {
			$this->doc->setMetadata('robots', 'noindex,follow');
		}

		$postTitle = $this->title;

		// If a custom title is set, we need to set them here
		if (isset($this->custom_title) && !empty($this->custom_title)) {
			$postTitle = $this->custom_title;
		}

		$this->doc->setTitle($postTitle);

		// Get the page title
		$title = EB::getPageTitle($this->config->get('main_title'));

		if ($this->config->get('main_pagetitle_autoappend_entry') && $title) {
			$this->doc->setTitle($postTitle . ' - ' . $title );
		}

		// Add opengraph tags if required.
		EB::facebook()->addOpenGraphTags($this);

		// Add Twitter card details on page.
		EB::twitter()->addCard($this);
	}

	/**
	 * Clears the cache in Joomla for EasyBlog related items
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function clearCache()
	{
		$cache = EB::getCache();
		$cache->clean('com_easyblog');
		$cache->clean('_system');
		$cache->clean('page');
	}

	/**
	 * Sets the revision id for the post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function useRevision()
	{
		// Only allow finalized revisions to be used as the current
		if (!$this->isFinalized()) {
			return false;
		}

		// Save the post
		$saveOptions = array('skipCreateRevision' => true,
							'applyDateOffset' => false,
							'normalizeData' => false,
							'updateModifiedTime' => false,
							'validateData' => false
						);

		return $this->save($saveOptions);
	}


	/**
	 * Notify subscribers when a new blog post is published
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notify($underApproval = false, $published = '1', $featured = false, $approved = false)
	{
		// Load site's language file
		JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

		if ($this->blogpassword) {
			return false;
		}

		$author = $this->getAuthor();

		// Export the data for email
		$data = $this->toEmailData();

		// Prepare the post title
		$title = JString::substr($this->title, 0, $this->config->get('main_mailtitle_length'));
		$subject = JText::sprintf('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_ADDED_WITH_TITLE', $title) . JText::_('COM_EASYBLOG_ELLIPSES');

		// Prepare ignored emails. We do not want to send notifications to the author
		$ignored = array($author->user->email);

		// Prepare emails
		$emails = array();

		// Load up notification library
		$notification = EB::notification();

		// If configured to notify custom emails, use that instead
		if ($this->config->get('notification_blogadmin')) {
			$notification->getAdminNotificationEmails($emails);
		}

		// If the blog post has been approved, we need to notify the author.
		if ($approved && ($published || $published == EASYBLOG_POST_SCHEDULED)) {

			$authorEmail = array();
			$obj = new stdClass();
			$obj->unsubscribe = false;
			$obj->email = $author->user->email;

			$authorEmail[$author->user->email] = $obj;

			$subject = JText::sprintf('COM_EASYBLOG_NOTIFICATION_NEW_BLOG_APPROVED', $title) . JText::_('COM_EASYBLOG_ELLIPSES');

			$notification->send($authorEmail, $subject, 'post.approved', $data);

			return true;
		}

		// Mailchimp integrations
		if (!$underApproval && $published && $this->config->get('mailchimp_campaign')) {
			EB::mailchimp()->notify($subject, $data, $this);
			return true;
		}

		if ($published) {
			// Send custom emails
			if ($emails) {
				$notification->send($emails, $subject, 'post.new', $data);

				foreach($emails as $el => $obj) {
					$ignored[] = $el;
				}
			}

			$notification->sendSubscribers($subject, 'post.new', $data, $this, $ignored);
		}

		// If the blog post is featured, send notification to the author
		if ($featured) {

			$subject = JText::_('COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_FEATURED');
			$email = new stdClass();
			$email->unsubscribe = false;
			$email->email = $author->user->email;

			$notification->send(array($email), 'COM_EASYBLOG_EMAIL_TITLE_POST_FEATURED', 'post.featured', $data);
		}

		// Logics for email notifications if this post is being submitted for approval
		if ($underApproval) {

			// If this blog post is submitted for approval, send the author an email letting them know that it is being moderated.
			$email = new stdClass();
			$email->unsubscribe = false;
			$email->email = $author->user->email;

			$notification->send(array($author->user->email => $email), 'COM_EASYBLOG_EMAIL_TITLE_POST_REQUIRES_APPROVAL', 'post.moderated', $data);

			// Send a notification to the site admin that a new post is made on the site and requires moderation.
			$emails = array();
			$notification->getAdminNotificationEmails($emails);

			$notification->send($emails, 'COM_EASYBLOG_EMAIL_TITLE_POST_REQUIRES_MODERATION', 'post.moderation', $data);
		}
	}

	/**
	 * Set's the post to be available on the front page.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFrontpage()
	{
		$this->frontpage = 1;

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false);

		return $this->save($options);
	}

	/**
	 * Remove the post from the frontpage.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFrontpage()
	{
		$this->frontpage = 0;

		// We do not want to run any validation since it's going to be trashed.
		$options = array('validateData' => false, 'normalizeData' => false);

		return $this->save($options);
	}

	/**
	 * Reset post hits
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function resetHits()
	{
		$this->hits = 0;

		$options = array('validateData' => false, 'normalizeData' => false);

		return $this->save($options);
	}

	/**
	 * Sets this post as a featured post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFeatured()
	{
		$model = EB::model('Featured');

		$state = $model->makeFeatured(EBLOG_FEATURED_BLOG, $this->id);

		if (!$state) {
			return false;
		}

		// @EasySocial Integrations
		EB::easysocial()->createFeaturedBlogStream($this);

		// @JomSocial Integrations
		EB::jomsocial()->createFeaturedBlogStream($this);

		// Notify author of the blog post that their blog post is featured on the site
		$this->notify(false, 0, true);

		return true;
	}

	/**
	 * Removes this post as a featured post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		return $this->deleteFeatured();
	}


	/*
	 * reassign blog author only.
	 *
	 */
	public function reassignAuthor($authorId)
	{

		$this->post->created_by = $authorId;
		// Store post
		$state = $this->post->store();

		// If failed to store post, throw exception.
		if (!$state) {
			return false;
		}

		$this->created_by = $authorId;

		// now we need to update author from the revisions.
		$revision = $this->revision;
		$revision->created_by = $authorId;
		$revision->setContent($this->toRevisionData());

		// Store revision
		$state = $revision->store();

		// If failed to store revision, throw exception.
		if (!$state) {
			return false;
		}

		// Assign revision back to instance
		$this->revision = $revision;

		return true;
	}


}
