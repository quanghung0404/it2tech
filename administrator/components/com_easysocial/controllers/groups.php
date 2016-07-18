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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller
FD::import( 'admin:/controllers/controller' );

class EasySocialControllerGroups extends EasySocialController
{
    /**
     * Class constructor
     *
     * @since   1.0
     * @access  public
     */
    public function __construct()
    {
        parent::__construct();

        // Map the alias methods here.
        $this->registerTask( 'unpublishCategory', 'togglePublishCategory' );
        $this->registerTask( 'publishCategory'  , 'togglePublishCategory' );

        $this->registerTask( 'publish' , 'togglePublish' );
        $this->registerTask( 'unpublish' , 'togglePublish' );

        $this->registerTask( 'applyCategory'    , 'saveCategory' );
        $this->registerTask( 'saveCategoryNew'  , 'saveCategory' );
        $this->registerTask( 'saveCategory'     , 'saveCategory' );

        $this->registerTask( 'apply' , 'store' );
        $this->registerTask( 'save' , 'store' );
        $this->registerTask( 'savenew' , 'store' );
        $this->registerTask( 'savecopy' , 'store' );

        $this->registerTask('makeFeatured', 'toggleDefault');
        $this->registerTask('removeFeatured', 'toggleDefault');
    }

    /**
     * Saves a group
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function store()
    {
        // Check for request forgeries
        FD::checkToken();

        // Load front end's language file
        FD::language()->loadSite();

        // Get the current view
        $view = $this->getCurrentView();

        // Get the current task
        $task = $this->getTask();

        // Determines if this group is being edited.
        $id = $this->input->get('id', 0, 'int');
        $cid = $id;

        // Flag to see if this is new or edit
        $isNew = empty($id);

        $isCopy = $task == 'savecopy' ? true : false;

        // Get the posted data
        $post = $this->input->getArray('post');
		$options = array();

        if ($isNew || $isCopy) {
            // Include group library
            FD::import('admin:/includes/group/group');

            $group = new SocialGroup();
            $categoryId = $this->input->get('category_id', 0, 'int');

            if ($isCopy) {
                $cgroup = FD::group($id);
                $categoryId = $cgroup->category_id;

                // lets unset the id here.
                $post['id'] = 0;
            }
        } else {
            $group = FD::group($id);

            $options['data'] = true;
            $options['dataId'] = $group->id;
            $options['dataType'] = SOCIAL_FIELDS_GROUP_GROUP;
            $categoryId = $group->category_id;
        }

        // Set the necessary data
        $options['uid'] = $categoryId;
        $options['group'] = SOCIAL_FIELDS_GROUP_GROUP;

        // Get fields model
        $fieldsModel = FD::model('Fields');

        // Get the custom fields
        $fields = $fieldsModel->getCustomFields($options);

        // Initialize default registry
        $registry = FD::registry();

        // Get disallowed keys so we wont get wrong values.
        $disallowed = array(FD::token() , 'option' , 'task' , 'controller', 'autoapproval');

        // Process $_POST vars
        foreach ($post as $key => $value) {

            if (!in_array($key, $disallowed)) {

                if (is_array($value)) {
                    $value = json_encode($value);
                }

                $registry->set($key, $value);
            }
        }

        // Convert the values into an array.
        $data = $registry->toArray();

        // Get the fields lib
        $fieldsLib = FD::fields();

        // Build arguments to be passed to the field apps.
        $args = array(&$data, &$group, &$isCopy);

        // @trigger onAdminEditValidate
        $errors = $fieldsLib->trigger('onAdminEditValidate', $options['group'], $fields, $args);

        // If there are errors, we should be exiting here.
        if (is_array($errors) && count($errors) > 0) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_FORM_SAVE_ERRORS'), SOCIAL_MSG_ERROR);

            // We need to set the data into the post again because onEditValidate might have changed the data structure
            JRequest::set($data, 'post');

            return $view->call('form', $errors);
        }

        // @trigger onAdminEditBeforeSave
        $errors = $fieldsLib->trigger('onAdminEditBeforeSave', $options['group'], $fields, $args);

        // If there are errors, we should be exiting here.
        if (is_array($errors) && count($errors) > 0) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_FORM_SAVE_ERRORS'), SOCIAL_MSG_ERROR);

            // We need to set the data into the post again because onEditValidate might have changed the data structure
            JRequest::set($data, 'post');

            return $view->call('form', $errors);
        }

        // Initialise group data for new group
        if ($isNew || $isCopy) {
            // Set the category id for the group
            $group->category_id = $categoryId;
            $group->creator_uid = $this->my->id;
            $group->creator_type = SOCIAL_TYPE_USER;
            $group->state = SOCIAL_STATE_PUBLISHED;
            $group->hits = 0;

            // Generate a unique key for this group which serves as a password
            $group->key = md5(FD::date()->toSql() . $this->my->password . uniqid());
        }

        // Bind the user object with the form data.
        $group->bind($data);

        // Save the group
        $group->save();

        // After the group is created, assign the current user as the node item
        if ($isNew || $isCopy) {
            FD::access()->log('groups.limit', $this->my->id, $group->id, SOCIAL_TYPE_GROUP);

            $group->createOwner($this->my->id);
        }

        // Reconstruct args
        $args = array(&$data, &$group);

        // @trigger onEditAfterSave
        $fieldsLib->trigger('onAdminEditAfterSave', $options['group'], $fields, $args);


        // Bind the custom fields for the group.
        $group->bindCustomFields($data);


        // Reconstruct args
        $args = array(&$data, &$group);

        // @trigger onEditAfterSaveFields
        $fieldsLib->trigger('onAdminEditAfterSaveFields', $options['group'], $fields, $args);

        // Now we need to copy the avatar and cover if there are any.
        if ($isCopy) {
            $group->copyAvatar($cid);
            $group->copyCover($cid);
        }

        $message = JText::_('COM_EASYSOCIAL_GROUPS_FORM_CREATE_SUCCESS');

        if ($isCopy) {
            $message = JText::_( 'COM_EASYSOCIAL_GROUPS_FORM_COPIED_SUCCESS' );
        } else if ($id) {
            $message = JText::_( 'COM_EASYSOCIAL_GROUPS_FORM_SAVE_UPDATE_SUCCESS' );
        }


        $view->setMessage( $message , SOCIAL_MSG_SUCCESS );

        return $view->call( __FUNCTION__ , $task , $group );
    }

    /**
     * Allows admin to toggle featured groups
     *
     * @since   1.3
     * @access  public
     */
    public function toggleDefault()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current task since there are a couple of tasks being proxied here.
        $task = $this->getTask();

        // Default message
        $message = 'COM_EASYSOCIAL_GROUPS_SET_FEATURED_SUCCESSFULLY';

        // Get the group object
        $ids = $this->input->get('cid', array(), 'array');

        foreach ($ids as $id) {

            // Load the group
            $group = FD::group($id);

            if ($task == 'toggleDefault') {

                if ($group->featured) {
                    $group->removeFeatured();
                    $message = 'COM_EASYSOCIAL_GROUPS_REMOVED_FEATURED_SUCCESSFULLY';
                }

                if (!$group->featured) {
                    $group->setFeatured();
                }
            }

            if ($task == 'makeFeatured') {
                $group->setFeatured();
            }

            if ($task == 'removeFeatured') {
                $group->removeFeatured();

                $message = 'COM_EASYSOCIAL_GROUPS_REMOVED_FEATURED_SUCCESSFULLY';
            }
        }

        $this->view->setMessage($message, SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__);
    }

    /**
     * Removes the group category avatar
     *
     * @since   1.2
     * @access  public
     */
    public function removeCategoryAvatar()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the category object.
        $id         = JRequest::getInt( 'id' );
        $category   = FD::table( 'GroupCategory' );
        $category->load( $id );

        // Try to remove the avatar
        $category->removeAvatar();
    }

    /**
     * Deletes a list of group from the site.
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function delete()
    {
        // Check for request forgeries
        FD::checkToken();

        $ids    = JRequest::getVar( 'cid' );

        // Get the current view
        $view   = $this->getCurrentView();

        if( !$ids )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_DELETE_FAILED' ) ,  SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        // Go through each of the category id's.
        foreach( $ids as $id )
        {
            $id         = (int) $id;

            $group      = FD::group( $id );
            $group->delete();
        }

        $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_DELETED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );

        return $view->call( __FUNCTION__ );
    }

    /**
     * Deletes a group category
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function deleteCategory()
    {
        // Check for request forgeries
        FD::checkToken();

        $ids = JRequest::getVar('cid');

        // Get the current view
        $view = $this->getCurrentView();

        if (!$ids) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_CATEGORY_DELETED_FAILED'), SOCIAL_MSG_ERROR);
            return $view->call(__FUNCTION__);
        }

        // Go through each of the category id's.
        foreach($ids as $id) {
            $id = (int) $id;
            $category = FD::table('GroupCategory');
            $category->load($id);

            $total = $category->getTotalGroups();

            // Check if deleting the category having the group will throw error.
            if ($total) {
                $view->setMessage(JText::_('COM_EASYSOCIAL_CATEGORIES_DELETE_ERROR_GROUP_NOT_EMPTY'), SOCIAL_MSG_ERROR);
                return $view->call(__FUNCTION__);
            }

            // Perform the action now
            $state = $category->delete();
        }

        $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_CATEGORY_DELETED_SUCCESS'), SOCIAL_MSG_SUCCESS);

        return $view->call( __FUNCTION__ );
    }

    /**
     * Toggles publishing
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function togglePublish()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the id's from the request
        $ids    = JRequest::getVar( 'cid' );

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the action
        $action = $this->getTask();

        foreach( $ids as $id )
        {
            $group  = FD::table( 'Group' );
            $group->load( $id );

            $group->$action();
        }

        $message    = JText::_( 'COM_EASYSOCIAL_GROUPS_PUBLISHED_SUCCESS' );

        if( $task == 'unpublish' )
        {
            $message    = JText::_( 'COM_EASYSOCIAL_GROUPS_UNPUBLISHED_SUCCESS' );
        }

        $view->setMessage( $message , SOCIAL_MSG_SUCCESS );
        $view->call( __FUNCTION__ );
    }

    /**
     * Publishes / unpulishes a group
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function togglePublishCategory()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the id's from the request
        $ids    = JRequest::getVar( 'cid' );

        // Get the current view.
        $view   = $this->getCurrentView();

        foreach( $ids as $id )
        {
            $id         = (int) $id;
            $category   = FD::table( 'ClusterCategory' );
            $category->load( $id );

            $task       = $this->getTask() == 'publishCategory' ? 'publish' : 'unpublish';

            // Perform the action now
            $state      = $category->$task();
        }

        if( $this->getTask() == 'publishCategory' )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_PUBLISHED_SUCCESS' ) ,  SOCIAL_MSG_SUCCESS );
        }
        else
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_UNPUBLISHED_SUCCESS' ) ,  SOCIAL_MSG_SUCCESS );
        }


        return $view->call( __FUNCTION__ );
    }

    /**
     * Allows caller to approve a group
     *
     * @since   1.2
     * @access  public
     */
    public function approve()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the posted group ids
        $ids    = JRequest::getVar( 'id' );
        $ids    = FD::makeArray( $ids );

        // Get other props
        $email  = JRequest::getVar( 'email' );

        // Get the current view
        $view   = $this->getCurrentView();

        // Prevent errors
        if( !$ids )
        {
            $view->setMessage( JText::_( 'Sorry, but the group id provided is invalid.' ) );
            return $view->call( __FUNCTION__ );
        }

        foreach( $ids as $id )
        {
            $id     = (int) $id;
            $group  = FD::group( $id );

            // Perform the rejection
            $group->approve( $email );
        }

        $view->setMessage( JText::_( 'Group has been approved successfully.' ) );
        return $view->call( __FUNCTION__ );
    }

    /**
     * Allows caller to change a group owner
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function switchOwner()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view   = $this->getCurrentView();

        // Get the list of groups to process
        $ids    = JRequest::getVar( 'ids' );
        $ids    = FD::makeArray( $ids );
        $userId = JRequest::getInt( 'userId' );

        if (!$ids || !$userId) {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_IDS' ) , SOCIAL_MSG_ERROR );
            return $view->call( __FUNCTION__ );
        }

        foreach ($ids as $id) {
            $group  = FD::group( $id );

            FD::access()->switchLogAuthor('groups.limit', $group->getCreator()->id, $group->id, SOCIAL_TYPE_GROUP, $userId );

            $group->switchOwner( $userId );
        }

        $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_GROUP_OWNER_UPDATED_SUCCESS' ) , SOCIAL_MSG_SUCCESS );
        return $view->call( __FUNCTION__ );
    }

    /**
     * Allows caller to reject a group
     *
     * @since   1.2
     * @access  public
     */
    public function reject()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the posted group ids
        $ids    = JRequest::getVar( 'id' );
        $ids    = FD::makeArray( $ids );

        // Get other props
        $email  = JRequest::getVar( 'email' );
        $delete = JRequest::getVar( 'delete' );

        // Get the reason
        $reason     = JRequest::getVar( 'reason' );

        // Get the current view
        $view   = $this->getCurrentView();

        // Prevent errors
        if( !$ids )
        {
            $view->setMessage( JText::_( 'COM_EASYSOCIAL_GROUPS_INVALID_IDS' ) );
            return $view->call( __FUNCTION__ );
        }

        foreach( $ids as $id )
        {
            $id     = (int) $id;
            $group  = FD::group( $id );

            // Perform the rejection
            $group->reject( $reason , $email , $delete );
        }

        $view->setMessage( JText::_( 'Group has been rejected successfully.' ) );
        return $view->call( __FUNCTION__ );
    }

    /**
     * Stores a group category ( Cluster category )
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function saveCategory()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the posted data
        $post = $this->input->getArray('post');

        // Get the id if there is any
        $id = $this->input->get('id', 0, 'int');

        // Try to load up the category
        $category = FD::table('GroupCategory');
        $category->load($id);
        $category->bind($post);

        // Try to store the category
        $state = $category->store();

        // Bind the group creation access
        if ($state) {
            $categoryAccess = $this->input->get('create_access', '', 'default');
            $category->bindCategoryAccess('create', $categoryAccess);
        }

        // Store the avatar for this profile.
        $file = $this->input->files->get('avatar', '');

        // Try to upload the profile's avatar if required
        if (!empty($file['tmp_name'])) {
            $category->uploadAvatar($file);
        }

        // Get fields data separately as we need allowraw here
        $fields = $this->input->get('fields', null, 'raw');
        // $postfields = JRequest::getVar( 'fields', $default = null, $hash = 'POST', $type = 'none', $mask = JREQUEST_ALLOWRAW );

        // Set the fields for this group category.
        if (!empty($fields)) {
            $data = FD::json()->decode($fields);

            $lib = FD::fields();
            $lib->saveFields($category->id, SOCIAL_TYPE_CLUSTERS, $data);
        }

        // Bind the access
        if (isset($post['access']) && !empty($post['access'])) {
            $category->bindAccess($post['access']);
        }

        // Set the message
        $this->view->setMessage('COM_EASYSOCIAL_GROUPS_CATEGORY_SAVED_SUCCESS', SOCIAL_MSG_SUCCESS);

        return $this->view->call(__FUNCTION__, $category);
    }

    /**
     * Add members into this group
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     */
    public function addMembers()
    {
        $groupid = JRequest::getInt('id');

        $userids = JRequest::getString('members');

        // Json decode the user ids
        $userids = FD::json()->decode($userids);

        $count = 0;

        foreach ($userids as $id) {
            $member = FD::table('GroupMember');
            $state = $member->load(array('uid' => $id, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $groupid));

            if ($state) {
                continue;
            }

            // Admin adding members shouldn't worry about pending state. It should all go through regardless of the group openness.
            $member->cluster_id = $groupid;
            $member->uid = $id;
            $member->type = SOCIAL_TYPE_USER;
            $member->created = FD::date()->toSql();
            $member->state = SOCIAL_STATE_PUBLISHED;
            $member->owner = 0;
            $member->admin = 0;
            $member->invited_by = 0;

            $member->store();

            $count++;
        }

        $view = $this->getCurrentView();

        $view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_ADD_MEMBERS_SUCCESS', $count), SOCIAL_MSG_SUCCESS);

        $view->call(__FUNCTION__);
    }

    /**
     * Remove members from this group
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.2
     * @access public
     */
    public function removeMembers()
    {
        // Check for request forgeries
        FD::checkToken();

        // Get the current view
        $view = $this->getCurrentView();

        $ids = JRequest::getVar('cid');

        $count = 0;

        foreach ($ids as $id) {
            $member = FD::table('GroupMember');
            $member->load($id);

            if ($member->isAdmin() || $member->isOwner()) {
                $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_REMOVE_MEMBERS_REMOVE_ADMIN_FAILED'), SOCIAL_MSG_ERROR);
                continue;
            }

            $member->delete();

            $count++;
        }

        if ($count > 0) {
            $view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_REMOVE_MEMBERS_SUCCESS', $count), SOCIAL_MSG_SUCCESS);
        }

        $view->call(__FUNCTION__);
    }

    public function publishUser()
    {
        // Check for request forgeries
        FD::checkToken();

        $view = $this->getCurrentView();

        $cids = JRequest::getVar('cid');

        if (empty($cids)) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_PUBLISH_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        foreach ($cids as $cid) {
            $node = FD::table('GroupMember');
            $node->load($cid);

            if ($node->state == 1) {
                continue;
            }

            $node->state = 1;

            if (!$node->store()) {
                $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_PUBLISH_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
                $view->call(__FUNCTION__);
            }
        }

        $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_PUBLISH_MEMBERS_MEMBERS_SUCCESS'), SOCIAL_MSG_SUCCESS);

        $view->call(__FUNCTION__);
    }

    public function unpublishUser()
    {
        // Check for request forgeries
        FD::checkToken();

        $view = $this->getCurrentView();

        $cids = JRequest::getVar('cid');

        if (empty($cids)) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_UNPUBLISH_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        foreach ($cids as $cid) {
            $node = FD::table('GroupMember');
            $node->load($cid);

            if ($node->state == 0 || $node->isAdmin() || $node->isOwner()) {
                continue;
            }

            $node->state = 0;

            if (!$node->store()) {
                $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_UNPUBLISH_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
                $view->call(__FUNCTION__);
            }
        }

        $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_UNPUBLISH_MEMBERS_MEMBERS_SUCCESS'), SOCIAL_MSG_SUCCESS);

        $view->call(__FUNCTION__);
    }

    public function moveUp()
    {
        return $this->move(-1);
    }

    public function moveDown()
    {
        return $this->move(1);
    }

    private function move($index)
    {
        // Group and Group Categories both shares the same view and controller, so here we need to check for layout first to decide which ordering to move up and down

        // $layout could be categories (to add group in the future)

        $layout = JRequest::getString('layout');

        $tablename = $layout === 'categories' ? 'groupcategory' : '';

        if (empty($tablename)) {
            return $this->view->move();
        }

        $ids = JRequest::getVar('cid');

        if (!$ids) {
            $this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_CATEGORIES_INVALID_IDS'), SOCIAL_MSG_ERROR);
            return $this->view->move($layout);
        }

        $db = FD::db();

        $filter = $db->nameQuote('type') . ' = ' . $db->quote(SOCIAL_TYPE_GROUP);

        foreach ($ids as $id) {
            $table = FD::table($tablename);
            $table->load($id);

            $table->move($index, $filter);
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_CATEGORIES_ORDERED_SUCCESSFULLY'), SOCIAL_MSG_SUCCESS);

        return $this->view->move($layout);
    }

    public function promoteMembers()
    {
        FD::checkToken();

        $view = $this->getCurrentView();

        $groupid = JRequest::getInt('id');

        $cids = JRequest::getVar('cid');

        if (empty($cids)) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_PROMOTE_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        FD::language()->loadSite();

        $my = FD::user();

        $group = FD::group($groupid);

        $user = FD::table('GroupMember');
        $user->load(array('cluster_id' => $group->id, 'uid' => $my->id, 'type' => SOCIAL_TYPE_USER));

        if (!$my->isSiteAdmin() && !$user->isAdmin() && !$user->isOwner()) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_PROMOTE_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        $count = 0;

        foreach ($cids as $id) {
            $member = FD::table('GroupMember');
            $member->load($id);

            $member->makeAdmin();

            $group->createStream($member->uid, 'makeadmin');

            // Notify the person that they are now a group admin
            $emailOptions   = array(
                'title'         => 'COM_EASYSOCIAL_GROUPS_EMAILS_PROMOTED_AS_GROUP_ADMIN_SUBJECT',
                'template'      => 'site/group/promoted',
                'permalink'     => $group->getPermalink(true, true),
                'actor'         => $my->getName(),
                'actorAvatar'   => $my->getAvatar(SOCIAL_AVATAR_SQUARE),
                'actorLink'     => $my->getPermalink(true, true),
                'group'         => $group->getName(),
                'groupLink'     => $group->getPermalink(true, true)
            );

            $systemOptions  = array(
                'context_type'  => 'groups.group.promoted',
                'url'           => $group->getPermalink(false, false, 'item', false),
                'actor_id'      => $my->id,
                'uid'           => $group->id
            );

            // Notify the owner first
            FD::notify('groups.promoted', array($member->uid), $emailOptions, $systemOptions);

            $count++;
        }

        if ($count > 0) {
            $view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_PROMOTE_MEMBERS_SUCCESS', $count), SOCIAL_MSG_SUCCESS);
        }

        $view->call(__FUNCTION__);
    }

    public function demoteMembers()
    {
        FD::checkToken();

        $view = $this->getCurrentView();

        $groupid = JRequest::getInt('id');

        $cids = JRequest::getVar('cid');

        if (empty($cids)) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_DEMOTE_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        $my = FD::user();

        $group = FD::group($groupid);

        $user = FD::table('GroupMember');
        $user->load(array('cluster_id' => $group->id, 'uid' => $my->id, 'type' => SOCIAL_TYPE_USER));

        if (!$my->isSiteAdmin() && !$user->isOwner()) {
            $view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_DEMOTE_MEMBERS_FAILED'), SOCIAL_MSG_ERROR);
            $view->call(__FUNCTION__);
        }

        $count = 0;

        foreach ($cids as $id) {
            $member = FD::table('GroupMember');
            $member->load($id);

            $member->revokeAdmin();

            $count++;
        }

        if ($count > 0) {
            $view->setMessage(JText::sprintf('COM_EASYSOCIAL_GROUPS_DEMOTE_MEMBERS_SUCCESS', $count), SOCIAL_MSG_SUCCESS);
        }

        $view->call(__FUNCTION__);
    }

    /**
     * Allows admin to switch a group's category
     *
     * @since   4.0
     * @access  public
     * @param   string
     * @return  
     */
    public function switchCategory()
    {
        FD::checkToken();

        $ids = FD::makeArray($this->input->get('cid'));

        $categoryId = $this->input->getInt('category');

        $categoryModel = FD::model('GroupCategories');

        foreach ($ids as $id) {
            $categoryModel->updateGroupCategory($id, $categoryId);
        }

        $this->view->setMessage(JText::_('COM_EASYSOCIAL_GROUPS_SWITCH_CATEGORY_SUCCESSFUL'));
        return $this->view->call(__FUNCTION__);
    }
}
