<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'admin:/includes/cluster/cluster' );
FD::import( 'admin:/includes/indexer/indexer' );

class SocialGroup extends SocialCluster
{
	public $cluster_type 	= SOCIAL_TYPE_GROUP;
	/**
	 * Keeps a list of groups that are already loaded so we
	 * don't have to always reload the user again.
	 * @var Array
	 */
	static $instances	= array();


	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function __construct( $params = array() , $debug = false )
	{
		// Create the user parameters object
		$this->_params = FD::registry();

		// Initialize user's property locally.
		$this->initParams( $params );

		$this->table 	= FD::table( 'Group' );
		$this->table->bind( $this );
	}

	public function initParams(&$params)
	{
		// We want to map the members data
		$this->members		= isset( $params->members ) ? $params->members : array();
		$this->admins 		= isset( $params->admins ) ? $params->admins : array();
		$this->pending 		= isset( $params->pending ) ? $params->pending : array();

		return parent::initParams($params);
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   $id     int/Array     Optional parameter
	 * @return  SocialUser   The person object.
	 */
	public static function factory( $ids = null , $debug = false )
	{
		$items	= self::loadGroups( $ids , $debug );

		return $items;
	}

	/**
	 * Loads a given group id or an array of id's.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * // Loads current logged in user.
	 * $my 		= FD::get( 'User' );
	 * // Shorthand
	 * $my 		= FD::user();
	 *
	 * // Loads a single user.
	 * $user	= FD::get( 'User' , 42 );
	 * // Shorthand
	 * $user 	= FD::user( 42 );
	 *
	 * // Loads multiple users.
	 * $users 	= FD::get( 'User' , array( 42 , 43 ) );
	 * // Shorthand
	 * $users 	= FD::user( array( 42 , 43 ) );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int|Array	Either an int or an array of id's in integer.
	 * @return	SocialUser	The user object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadGroups( $ids = null , $debug = false )
	{
		if( is_object( $ids ) )
		{
			$obj 	= new self;
			$obj->bind( $ids );

			self::$instances[ $ids->id ]	= $obj;

			return self::$instances[ $ids->id ];
		}

		// Determine if the argument is an array.
		$argumentIsArray	= is_array( $ids );

		// Ensure that id's are always an array
		$ids = FD::makeArray($ids);

		// Reset the index of ids so we don't load multiple times from the same user.
		$ids = array_values($ids);

		if (empty($ids)) {
			return false;
		}

		// Get the metadata of all groups
		$model 	= FD::model('Groups');
		$groups	= $model->getMeta($ids);

		if( !$groups )
		{
			return false;
		}

		// preload members
		$model->getMembers( $ids , array( 'users' => false ));

		// Format the return data
		$result 	= array();

		foreach( $groups as $group )
		{
			if( $group === false )
			{
				continue;
			}

			// Set the cover for the group
			$group->cover 	= self::getCoverObject( $group );

			// Pre-load list of members for the group
			$members 		= $model->getMembers( $group->id , array( 'users' => false ));
			$group->members		= array();
			$group->admins 		= array();
			$group->pending 	= array();

			if( $members )
			{
				foreach( $members as $member )
				{
					if( $member->state == SOCIAL_GROUPS_MEMBER_PUBLISHED )
					{
						$group->members[ $member->uid ]	= $member->uid;
					}

					if( $member->admin )
					{
						$group->admins[ $member->uid ]	= $member->uid;
					}

					if( $member->state == SOCIAL_GROUPS_MEMBER_PENDING )
					{
						$group->pending[ $member->uid ]	= $member->uid;
					}
				}
			}


			// Attach custom fields for this group
			// $group->fileds 	= $model->getCustomFields( $group->id );

			// Create an object
			$obj 	= new SocialGroup( $group );

			self::$instances[ $group->id ]	= $obj;

			$result[]	= self::$instances[ $group->id ];
		}

		if( !$result )
		{
			return false;
		}

		if( !$argumentIsArray && count( $result ) == 1 )
		{
			return $result[ 0 ];
		}

		return $result;
	}

	/**
	 * Return the total number of members in this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalMembers()
	{
		// Since the $this->members property is cached, we just calculate this.
		$total = count($this->members);

		return $total;
	}

	/**
	 * Retrieves a list of apps for a user
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getApps()
	{
		static $apps = null;

		if (!$apps) {
			$model = ES::model('Apps');
			$apps = $model->getGroupApps($this->id);
		}

		return $apps;
	}

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getPermalink($xhtml = true, $external = false, $layout = 'item', $sef = true)
	{
		$options = array('id' => $this->getAlias(), 'layout' => $layout);

		if ($external) {
			$options['external'] = true;
		}

		$options['sef'] = $sef;

		$url = FRoute::groups($options, $xhtml);

		return $url;
	}

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getAppsPermalink($appId, $xhtml = true, $external = false, $layout = 'item', $sef = true)
	{
		$options = array('id' => $this->getAlias(), 'layout' => $layout, 'appId' => $appId);

		if ($external) {
			$options['external'] = true;
		}

		$options['sef'] = $sef;

		$url = FRoute::groups($options, $xhtml);

		return $url;
	}

    /**
     * Retrieves the description about an event
     *
     * @since   1.3
     * @access  public
     * @param   string
     * @return
     */
    public function getDescription()
    {
        return nl2br($this->description);
    }

	/**
	 * Centralized method to retrieve a person's profile link.
	 * This is where all the magic happens.
	 *
	 * @access	public
	 * @param	null
	 *
	 * @return	string	The url for the person
	 */
	public function getEditPermalink( $xhtml = true , $external = false , $layout = 'edit' )
	{
		$url 	= $this->getPermalink( $xhtml , $external , $layout );

		return $url;
	}

	/**
	 * Create bind method
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind( $data )
	{
		// Bind the table data first.
		$this->table->bind( $data );

		$keyToArray = array( 'avatars', 'members', 'admins', 'pending' );

		foreach( $data as $key => $value )
		{
			if( property_exists( $this, $key ) )
			{
				if( in_array( $key, $keyToArray) && is_object( $value ) )
				{
					$value = FD::makeArray( $value );
				}

				$this->$key 	= $value;
			}
		}
	}

	/**
	 * Retrieve the creator of this group
	 *
	 * @since	1.2
	 * @access	public
	 * @return	SocialUser
	 */
	public function getInvitor( $userId )
	{
		static $invites 	= array();

		if( !isset( $invites[ $userId ] ) )
		{
			$member	= FD::table( 'GroupMember' );
			$member->load( array( 'uid' => $userId , 'cluster_id' => $this->id ) );

			$invitor	= FD::user( $member->invited_by );

			$invites[ $userId ]	= $invitor;
		}


		return $invites[ $userId ];
	}


	public function deleteMemberStream($userId)
	{

		$model = FD::model('Groups');
		$model->deleteUserStreams($this->id, $userId);
	}

	/**
	 * Allows caller to remove a member from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteMember( $userId )
	{
		$state 	= $this->deleteNode( $userId , SOCIAL_TYPE_USER );

		if ($state) {
			$this->deleteMemberStream($userId);
		}

		return $state;
	}

	/**
	 * Allows caller to depart the user from the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user's id.
	 * @return
	 */
	public function leave($id = null)
	{
		$my = FD::user($id);

		// Delete the user from the cluster members relation
		$state = $this->deleteNode( $my->id);

		if (!$state) {
			return $state;
		}

		// Delete stream from this user.
		$this->deleteMemberStream($my->id);

		// Additional triggers to be processed when the page starts.
		FD::apps()->load(SOCIAL_TYPE_GROUP);
		$dispatcher = FD::dispatcher();

		// Trigger: onComponentStart
		$dispatcher->trigger('user', 'onLeaveGroup', array($my->id, $this));

		// @points: groups.leave
		// Deduct points when user leaves the group
		$points = FD::points();
		$points->assign( 'groups.leave' , 'com_easysocial' , $my->id );

		// Add activity stream
		$this->createStream($my->id, 'leave');

		return $state;
	}

	/**
	 * Logics for deleting a group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Load group apps.
		ES::apps()->load(SOCIAL_TYPE_GROUP);

		// @trigger onBeforeDelete
		$dispatcher = ES::dispatcher();

		// @points: groups.remove
		// Deduct points when a group is deleted
		$points = ES::points();
		$points->assign('groups.remove', 'com_easysocial', $this->getCreator()->id);

		// remove the access log for this action
		ES::access()->removeLog('groups.limit', $this->getCreator()->id, $this->id, SOCIAL_TYPE_GROUP);

		// Set the arguments
		$args = array(&$this);

		// @trigger onBeforeStorySave
		$dispatcher->trigger(SOCIAL_TYPE_GROUP, 'onBeforeDelete', $args);

		// Delete all members from the cluster nodes.
		$this->deleteNodes();

		// Delete custom fields data for this cluster.
		$this->deleteCustomFields();

        // Delete photos albums for this cluster.
        $this->deletePhotoAlbums();

        // Delete videos for this cluster
        $this->deleteVideos();

		// Delete stream items for this group
		$this->deleteStream();

		// Delete all group news
		$this->deleteNews();

		// delete all user notification associated with this group.
		$this->deleteNotifications();

		// Delete from the cluster
		$state = parent::delete();

		$args[]	= $state;

		// @trigger onAfterDelete
		$dispatcher->trigger(SOCIAL_TYPE_GROUP, 'onAfterDelete', $args);

		return $state;
	}

	/**
	 * Delete notifications related to this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteNotifications()
	{
		$model		= FD::model( 'Clusters' );
		$state		= $model->deleteClusterNotifications( $this->id, $this->cluster_type, SOCIAL_TYPE_GROUPS);

		return $state;
	}

	/**
	 * Creates a new member for the group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createMember($userId, $onRegister = false, $registrationType = null)
	{
		$member = FD::table('GroupMember');

		// Try to load the user record if it exists
		$member->load(array('uid' => $userId, 'type' => SOCIAL_TYPE_USER, 'cluster_id' => $this->id));

		$member->cluster_id = $this->id;
		$member->uid = $userId;
		$member->type = SOCIAL_TYPE_USER;
		$member->admin = false;
		$member->owner = false;

		// If the group type is open group, just add the member
		if ($this->isOpen()) {
			$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
		}

		// If the group type is closed group, we need the group admins to approve the application.
		// Unless if the user is invited, then the user can just join directly
		if ($this->isClosed()) {
			if ($member->state == SOCIAL_GROUPS_MEMBER_INVITED) {
				$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
			} else {
				$member->state = SOCIAL_GROUPS_MEMBER_PENDING;
			}
		}

		// If the user is set to join the group after user registration the user state should be publish immediately.
		if ($onRegister) {
			$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
		}

		// If the user is set to join the group after user registration the user state should be publish immediately.
		// Check profile type as well
		if ($onRegister && ($registrationType == 'auto' || $registrationType == 'login')) {
			$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
		}

		if ($onRegister && ($registrationType == 'verify' || $registrationType == 'approvals')) {
			$member->state = SOCIAL_GROUPS_MEMBER_BEING_JOINED;
		}

		$state = $member->store();

		if ($state) {
			if ($member->state == SOCIAL_GROUPS_MEMBER_PUBLISHED) {
				// Additional triggers to be processed when the page starts.
				FD::apps()->load(SOCIAL_TYPE_GROUP);
				$dispatcher = FD::dispatcher();

				// Trigger: onComponentStart
				$dispatcher->trigger('user', 'onJoinGroup', array($userId, $this));

				// @points: groups.join
				// Add points when user joins a group
				$points = FD::points();
				$points->assign('groups.join', 'com_easysocial', $userId);

				// If it is an open group, notify members
				$this->notifyMembers( 'join' , array( 'userId' => $userId ) );

				// Create a stream for the user
				$this->createStream( $userId , 'join' );
			}

			// Send notification e-mail to the admin
			if ($member->state == SOCIAL_GROUPS_MEMBER_PENDING) {
				$this->notifyGroupAdmins( 'request' , array( 'userId' => $userId ) );
			}
		}

		return $member;
	}

	/**
	 * Invites another user to join this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function invite( $userId , $invitorId )
	{
		// Get the actor's user object
		$actor 		= FD::user( $invitorId );

		// Get the target user's object
		$target		= FD::user( $userId );

		$node 				= FD::table( 'ClusterNode' );

		$node->cluster_id 	= $this->id;
		$node->uid 			= $userId;
		$node->type 		= SOCIAL_TYPE_USER;
		$node->state 		= SOCIAL_GROUPS_MEMBER_INVITED;
		$node->invited_by	= $invitorId;

		$node->store();

		$params 				= new stdClass();
		$params->invitorName	= $actor->getName();
		$params->invitorLink	= $actor->getPermalink( false , true );
		$params->groupName		= $this->getName();
		$params->groupAvatar 	= $this->getAvatar();
		$params->groupLink 		= $this->getPermalink( false , true );
		$params->acceptLink		= FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'respondInvitation' , 'id' => $this->id, 'email' => 1, 'action' => 'accept') );
		$params->group 			= $this->getName();

		// Send notification e-mail to the target
		$options 			= new stdClass();
		$options->title 	= 'COM_EASYSOCIAL_EMAILS_USER_INVITED_YOU_TO_JOIN_GROUP_SUBJECT';
		$options->template 	= 'site/group/invited';
		$options->params 	= $params;

		// Set the system alerts
		$system 				= new stdClass();
		$system->uid 			= $this->id;
		$system->actor_id 		= $actor->id;
		$system->target_id		= $target->id;
		$system->context_type	= 'groups';
		$system->type 			= SOCIAL_TYPE_GROUP;
		$system->url 			= $this->getPermalink(true, false, 'item', false);

		// @points: groups.invite
		// Assign points when user invites another user to join the group
		$points = FD::points();
		$points->assign( 'groups.invite' , 'com_easysocial' , $invitorId );

		FD::notify( 'groups.invited' , array( $target->id ) , $options , $system );

		// Send
		return $node;
	}

	/**
	 * Determines if the provided user can view the group's items
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function canViewItem( $userId = null )
	{
		$user 	= FD::user( $userId );

		if (($this->isInviteOnly() || $this->isClosed()) && !$this->isMember($user->id) && !$user->isSiteAdmin()) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isOpen()
	{
		return $this->type == SOCIAL_GROUPS_PUBLIC_TYPE;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isClosed()
	{
		return $this->type == SOCIAL_GROUPS_PRIVATE_TYPE;
	}

	/**
	 * Determines if the group is invite only
	 *
	 * @since	1.0
	 * @access	public
	 * @return	bool	True if invite only.
	 */
	public function isInviteOnly()
	{
		return $this->type == SOCIAL_GROUPS_INVITE_TYPE;
	}

	/**
	 * Determines if the user is pending invitation
	 *
	 * @access	private
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	public function isPendingInvitationApproval( $uid = null )
	{
		static $pending	= array();

		if( !isset( $pending[ $uid ] ) )
		{
			$user 	= FD::user( $uid );

			$node 	= FD::table( 'ClusterNode' );
			$node->load( array( 'uid' => $user->id , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $this->id ) );

			$pending[ $uid ]	= false;

			if( $node->invited_by && $node->state == SOCIAL_GROUPS_MEMBER_INVITED )
			{
				$pending[ $uid ]	= true;
			}
		}

		return $pending[ $uid ];
	}


	/**
	 * Determines if the node is invited by another user
	 *
	 * @access	private
	 * @param	null
	 * @return	boolean	True on success false otherwise.
	 */
	public function isInvited($uid = null)
	{
		static $invited = array();

		$key = $uid . $this->id;

		if (!isset($invited[$key])) {
			$user = FD::user($uid);

			$node = FD::table('ClusterNode');
			$node->load(array('uid' => $user->id , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $this->id));

			$invited[$key] = false;

			if ($this->isInviteOnly() && $node->invited_by) {
				$invited[$key] = true;
			}
		}

		return $invited[$key];
	}

	/**
	 * Approves a user's registration application
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function approve( $email = true )
	{
		// Upda the group's state first.
		$this->state = SOCIAL_CLUSTER_PUBLISHED;

		$state = $this->save();

		$dispatcher = FD::dispatcher();

		// Set the arguments
		$args = array(&$this);

		// @trigger onGroupAfterApproved
		$dispatcher->trigger(SOCIAL_TYPE_GROUP, 'onAfterApproved', $args);
		$dispatcher->trigger(SOCIAL_TYPE_USER, 'onGroupAfterApproved', $args);

		// Activity logging.
		// Announce to the world when a new user registered on the site.
		$config = FD::config();

		// If we need to send email to the user, we need to process this here.
		if ($email) {
			FD::language()->loadSite();

			// Push arguments to template variables so users can use these arguments
			$params = array(
							'title'	=> $this->getName(),
							'name' => $this->getCreator()->getName(),
							'avatar' => $this->getAvatar(SOCIAL_AVATAR_LARGE),
							'groupUrl' => $this->getPermalink(false, true),
							'editUrl' => FRoute::groups(array('external' => true, 'layout' => 'edit', 'id' => $this->getAlias()), false)
							);

			// Get the email title.
			$title = JText::sprintf('COM_EASYSOCIAL_EMAILS_GROUP_APPLICATION_APPROVED', $this->getName());

			// Immediately send out emails
			$mailer = FD::mailer();

			// Get the email template.
			$mailTemplate = $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient($this->getCreator()->getName(), $this->getCreator()->email);

			// Set title
			$mailTemplate->setTitle($title);

			// Set the contents
			$mailTemplate->setTemplate('site/group/approved', $params);

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority(SOCIAL_MAILER_PRIORITY_IMMEDIATE);

			// Try to send out email now.
			$mailer->create($mailTemplate);
		}

		// Once a group is approved, generate a stream item for it.
		// Add activity logging when a user creates a new group.
		if ($config->get('groups.stream.create')) {
			$stream = FD::stream();
			$streamTemplate = $stream->getTemplate();

			// Set the actor
			$streamTemplate->setActor($this->creator_uid, SOCIAL_TYPE_USER);

			// Set the context
			$streamTemplate->setContext($this->id, SOCIAL_TYPE_GROUPS);

			$streamTemplate->setVerb('create');
			$streamTemplate->setSiteWide();
			$streamTemplate->setAccess('core.view');

			// Set the params to cache the group data
			$registry = FD::registry();
			$registry->set('group', $this);

			// Set the params to cache the group data
			$streamTemplate->setParams($registry);

			$streamTemplate->setCluster($this->id, SOCIAL_TYPE_GROUP, $this->type);

			// Add stream template.
			$stream->add($streamTemplate);
		}

		return true;
	}

	/**
	 * Approves the user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user id
	 * @return
	 */
	public function approveUser( $userId )
	{
		$member 	= FD::table( 'GroupMember' );
		$member->load( array( 'cluster_id' => $this->id , 'uid' => $userId ) );

		$member->state 	= SOCIAL_GROUPS_MEMBER_PUBLISHED;

		$state 	= $member->store();

		// Additional triggers to be processed when the page starts.
		FD::apps()->load(SOCIAL_TYPE_GROUP);
		$dispatcher = FD::dispatcher();

		// Trigger: onComponentStart
		$dispatcher->trigger('user', 'onJoinGroup', array($userId, $this));

		// @points: groups.join
		// Add points when user joins a group
		$points = FD::points();
		$points->assign( 'groups.join' , 'com_easysocial' , $userId );

		// Publish on the stream
		if ($state) {
			// Add stream item so the world knows that the user joined the group
			$this->createStream( $userId , 'join' );
		}

		// Notify the user that his request to join the group has been approved
		$this->notifyMembers('approved', array('targets' => array($userId)));

		// Send notifications to group members when a new member joined the group
		$this->notifyMembers('join' , array( 'userId' => $userId ));

		return $state;
	}

	/**
	 * Notify admins of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyGroupAdmins( $action , $data = array() )
	{
		$model 		= FD::model( 'Groups' );
		$targets 	= $model->getMembers( $this->id , array( 'admin' => true ) );

		if( $action == 'request' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params = new stdClass();
			$params->actor = $actor->getName();
			$params->userName = $actor->getName();
			$params->userLink = $actor->getPermalink(false, true);
			$params->userAvatar = $actor->getAvatar(SOCIAL_AVATAR_LARGE);
			$params->groupName = $this->getName();
			$params->groupAvatar = $this->getAvatar();
			$params->groupLink = $this->getPermalink( false , true );
			$params->approve = FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'approve' , 'userId' => $actor->id , 'id' => $this->id , 'key' => $this->key ) );
			$params->reject = FRoute::controller( 'groups' , array( 'external' => true , 'task' => 'reject' , 'userId' => $actor->id , 'id' => $this->id , 'key' => $this->key ) );
			$params->group = $this->getName();

			// Send notification e-mail to the target
			$options = new stdClass();
			$options->title = 'COM_EASYSOCIAL_EMAILS_USER_REQUESTED_TO_JOIN_GROUP_SUBJECT';
			$options->template = 'site/group/moderate.member';
			$options->params = $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink(false, true, 'item', false);

			FD::notify( 'groups.requested' , $targets , $options , $system );
		}
	}

	/**
	 * Notify members of the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyMembers($action, $data = array())
	{
		$model = ES::model('Groups');
		$targets = isset($data['targets']) ? $data['targets'] : false;

		if ($targets === false) {
			$exclude = isset( $data[ 'userId' ] ) ? $data[ 'userId' ] : '';
			$options = array( 'exclude' => $exclude, 'state' => SOCIAL_GROUPS_MEMBER_PUBLISHED);
			$targets = $model->getMembers( $this->id , $options );
		}


		// If there is nothing to send, just skip this altogether
		if (!$targets) {
			return;
		}

		if ($action == 'video.create') {
			$actor = ES::user($data['userId']);

			$params = new stdClass();
			$params->actor = $actor->getName();
			$params->userName = $actor->getName();
			$params->userLink = $actor->getPermalink(false, true);
			$params->groupName = $this->getName();
			$params->groupAvatar = $this->getAvatar();
			$params->groupLink = $this->getPermalink(false, true);
			$params->videoTitle = $data['title'];
			$params->videoDescription = $data['description'];
			$params->videoLink = $data['permalink'];

			$options = new stdClass();
			$options->title = 'COM_EASYSOCIAL_EMAILS_GROUP_VIDEO_CREATED_SUBJECT';
			$options->template = 'site/group/video.create';
			$options->params = $params;

			// Set the system alerts
			$system = new stdClass();
			$system->uid = $this->id;
			$system->title = '';
			$system->actor_id = $actor->id;
			$system->context_ids = $data['id'];
			$system->context_type = 'groups';
			$system->type = SOCIAL_TYPE_GROUP;
			$system->url = $params->videoLink;
			$system->image = $this->getAvatar();

			ES::notify('groups.video.create', $targets, $options, $system);
		}

		if ($action == 'task.completed') {

			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->milestoneName	= $data[ 'milestone' ];
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_COMPLETED_SUBJECT';
			$options->template 	= 'site/group/task.completed';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.task.completed' , $targets , $options , $system );
		}

		if ( $action == 'task.create' ) {
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->milestoneName	= $data[ 'milestone' ];
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_CREATED_SUBJECT';
			$options->template 	= 'site/group/task.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.task.create' , $targets , $options , $system );
		}

		if( $action == 'milestone.create' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_TASK_CREATED_MILESTONE_SUBJECT';
			$options->template 	= 'site/group/milestone.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= '';
			$system->actor_id 		= $actor->id;
			$system->context_ids 	= $data[ 'id' ];
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->image 			= $this->getAvatar();

			FD::notify( 'groups.milestone.create' , $targets , $options , $system );
		}

		if( $action == 'discussion.reply' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'title' ];
			$params->content 		= $data[ 'content' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_REPLIED_TO_DISCUSSION_SUBJECT';
			$options->template 	= 'site/group/discussion.reply';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_REPLY_DISCUSSION' , $actor->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->context_ids 	= $data['discussionId'];

			FD::notify( 'groups.discussion.reply' , $targets , $options , $system );
		}

		if( $action == 'discussion.create' )
		{
			$actor 					= FD::user( $data[ 'userId' ] );
			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->title 			= $data[ 'discussionTitle' ];
			$params->content 		= $data[ 'discussionContent' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_DISCUSSION_SUBJECT';
			$options->template 	= 'site/group/discussion.create';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_NEW_DISCUSSION' , $actor->getName() , $this->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;
			$system->context_ids 	= $data['discussionId'];

			FD::notify( 'groups.discussion.create' , $targets , $options , $system );
		}

		if( $action == 'file.uploaded' )
		{
			$actor 		= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor			= $actor->getName();
			$params->actorLink 		= $actor->getPermalink(false, true);
			$params->actorAvatar	= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->group			= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->fileTitle 		= $data['fileName'];
			$params->fileSize 		= $data['fileSize'];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_FILE_SUBJECT';
			$options->template 	= 'site/group/file.uploaded';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'file.group.uploaded';
			$system->context_ids 	= $data['fileId'];
			$system->type 			= 'groups';
			$system->url 			= $params->permalink;

			FD::notify('groups.updates' , $targets, $options, $system);
		}

		if( $action == 'news.create' )
		{
			$actor 		= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );
			$params->newsTitle 		= $data[ 'newsTitle' ];
			$params->newsContent 	= $data[ 'newsContent' ];
			$params->permalink 		= $data[ 'permalink' ];

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_ANNOUNCEMENT_SUBJECT';
			$options->template 	= 'site/group/news';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->context_ids 	= $data['newsId'];
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $params->permalink;

			FD::notify( 'groups.news' , $targets , $options , $system );
		}

		if( $action == 'leave' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_LEFT_GROUP';
			$options->template 	= 'site/group/leave';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.leave' , $targets , $options , $system );
		}

		if( $action == 'user.remove' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			// targets should be the user being removed.
			$targets = array($actor->id);

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_YOU_REMOVED_FROM_GROUP';
			$options->template 	= 'site/group/user.removed';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->cmd 			= 'groups.user.removed';
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.user.removed' , $targets , $options, $system );
		}


		// Admin approves the user
		if ($action == 'approved') {

			// The actor is always the current user.
			$actor 	= FD::user();

			// There is a situation where action approved been made via email,
			// and the admin did not logged in to the site (frontend).
			// So, if actor for this action is a Guest, 
			// we get the group creator to be the actor.
			if (!$actor->id) {
				$actor = FD::user($this->creator_uid);
			}

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title		= 'COM_EASYSOCIAL_EMAILS_SUBJECT_GROUPS_APPROVED_JOIN_GROUP';
			$options->template 	= 'site/group/user.approved';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify('groups.approved' , $targets , $options , $system );
		}

		if( $action == 'join' )
		{
			$actor 	= FD::user( $data[ 'userId' ] );

			$params 				= new stdClass();
			$params->actor 			= $actor->getName();
			$params->group 			= $this->getName();
			$params->userName		= $actor->getName();
			$params->userLink		= $actor->getPermalink( false , true );
			$params->userAvatar		= $actor->getAvatar( SOCIAL_AVATAR_LARGE );
			$params->groupName		= $this->getName();
			$params->groupAvatar 	= $this->getAvatar();
			$params->groupLink 		= $this->getPermalink( false , true );

			// Send notification e-mail to the target
			$options 			= new stdClass();
			$options->title 	= 'COM_EASYSOCIAL_EMAILS_GROUP_JOINED_GROUP_SUBJECT';
			$options->template 	= 'site/group/joined';
			$options->params 	= $params;

			// Set the system alerts
			$system 				= new stdClass();
			$system->uid 			= $this->id;
			$system->title 			= JText::sprintf( 'COM_EASYSOCIAL_GROUPS_NOTIFICATION_JOIN_GROUP' , $actor->getName() , $this->getName() );
			$system->actor_id 		= $actor->id;
			$system->target_id		= $this->id;
			$system->context_type	= 'groups';
			$system->type 			= SOCIAL_TYPE_GROUP;
			$system->url 			= $this->getPermalink();

			FD::notify( 'groups.joined' , $targets , $options , $system );
		}

	}

	public function createStream( $actorId = null , $verb )
	{
		$stream		= FD::stream();
		$tpl		= $stream->getTemplate();
		$actor 		= FD::user( $actorId );

		// this is a cluster stream and it should be viewable in both cluster and user page.
		$tpl->setCluster( $this->id, SOCIAL_TYPE_GROUP, $this->type );

		// Set the actor
		$tpl->setActor( $actor->id , SOCIAL_TYPE_USER );

		// Set the context
		$tpl->setContext( $this->id , SOCIAL_TYPE_GROUPS );

		// Set the verb
		$tpl->setVerb( $verb );

		// Set the params to cache the group data
		$registry	= FD::registry();
		$registry->set( 'group' , $this );

		// Set the params to cache the group data
		$tpl->setParams( $registry );

		// since this is a cluster and user stream, we need to call setPublicStream
		// so that this stream will display in unity page as well
		// This stream should be visible to the public
		$tpl->setAccess( 'core.view' );

		$stream->add( $tpl );
	}

	/**
	 * Rejects the group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject( $reason = '' , $email = false , $delete = false )
	{
		// Announce to the world when a new user registered on the site.
		$config 			= FD::config();

		// If we need to send email to the user, we need to process this here.
		if( $email )
		{
			// Push arguments to template variables so users can use these arguments
			$params 	= array(
									'title'			=> $this->getName(),
									'name'			=> $this->getCreator()->getName(),
									'reason'		=> $reason,
									'manageAlerts'	=> false
								);

			// Load front end language file.
			FD::language()->loadSite();

			// Get the email title.
			$title      = JText::_( 'COM_EASYSOCIAL_EMAILS_GROUP_REJECTED_EMAIL_TITLE' );

			// Immediately send out emails
			$mailer 	= FD::mailer();

			// Get the email template.
			$mailTemplate	= $mailer->getTemplate();

			// Set recipient
			$mailTemplate->setRecipient( $this->getCreator()->getName() , $this->getCreator()->email );

			// Set title
			$mailTemplate->setTitle( $title );

			// Set the contents
			$mailTemplate->setTemplate( 'site/group/rejected' , $params );

			// Set the priority. We need it to be sent out immediately since this is user registrations.
			$mailTemplate->setPriority( SOCIAL_MAILER_PRIORITY_IMMEDIATE );

			// Try to send out email now.
			$mailer->create( $mailTemplate );
		}

		// If required, delete the user from the site.
		if( $delete )
		{
			$this->delete();

			// remove the access log for this action
			FD::access()->removeLog('groups.limit', $this->getCreator()->id, $this->id, SOCIAL_TYPE_GROUP);
		}

		return true;
	}

	/**
	 * Rejects the user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The user id
	 * @return
	 */
	public function rejectUser($userId)
	{
		$member 	= FD::table( 'GroupMember' );
		$member->load( array( 'cluster_id' => $this->id , 'uid' => $userId ) );

		$state 		= $member->delete();

		// Notify the user that they have been rejected :(
		$mailOptions	= array();
		$mailOptions['title']		= 'COM_EASYSOCIAL_GROUPS_APPLICATION_REJECTED';
		$mailOptions['template']	= 'site/group/user.rejected';


		$systemOptions 	= array();
		$systemOptions['context_type']	= 'groups';
		$systemOptions['cmd']			= 'groups.user.rejected';
		$systemOptions['url']			= $this->getPermalink(true, false, 'item', false);
		$systemOptions['actor_id']		= FD::user()->id;
		$systemOptions['uid']			= $this->id;

		FD::notify('groups.user.rejected', array($userId), $mailOptions, $systemOptions);

		return $state;
	}

	/**
	 * Cancel user invitation from the group
	 *
	 * @since	1.3
	 * @access	public
	 * @param	int		The user id
	 * @return
	 */
	public function cancelInvitation($userId)
	{
		$member = FD::table('GroupMember');
		$member->load( array('cluster_id' => $this->id, 'uid' => $userId));

		$state = $member->delete();

		return $state;
	}

	/**
	 * Determines if the provided user id is a pending member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isPendingMember( $userId = null )
	{
		$userId	= FD::user( $userId )->id;

		if( isset( $this->pending[ $userId ] ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Determines if the provided user id is a member of this group
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id to check against.
	 * @return	bool	True if he / she is a member already.
	 */
	public function isMember($userId = null)
	{
		$userId = ES::user($userId)->id;

		if (isset($this->members[$userId])) {
			return true;
		}

		return false;
	}

	/**
	 * Gets group member's filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	null
	 * @return	SocialAccess
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getFilters( $userId )
	{
		$model		= FD::model( 'Groups' );
		$filters	= $model->getFilters( $this->id,  $userId );

		return $filters;
	}

	public function canCreateEvent($userId = null)
	{
		if (is_null($userId)) {
			$userId = FD::user()->id;
		}

		if ($this->isOwner($userId) || FD::user()->getAccess()->get('events.create') || FD::user($userId)->isSiteAdmin()) {
			return true;
		}

		// Check access
		if (!$this->getAccess()->get('events.groupevent', true)) {
			return false;
		}

		$allowed = FD::makeArray($this->getParams()->get('eventcreate', '[]'));

		if (in_array('admin', $allowed) && $this->isAdmin($userId)) {
			return true;
		}

		if (in_array('member', $allowed) && $this->isMember($userId)) {
			return true;
		}

		return false;
	}

	/**
	 * Copies avatar from the temporary location
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function copyAvatar($targetGroupId)
	{
		// get avatar from target group
		// create album for current group
		// create photos for current group
		// duplicate physical files from target group
		//
		$my = ES::user();

		$targetAvatar = ES::table('Avatar');
		$targetAvatar->load(array('uid' => $targetGroupId, 'type' => SOCIAL_TYPE_GROUP));

		if (! $targetAvatar->id) {
			return false;
		}

		if ($targetAvatar->storage != SOCIAL_STORAGE_JOOMLA) {
			return false;
		}

		$targetPhoto = ES::table('Photo');
		$targetPhoto->load($targetAvatar->photo_id);

		// now lets create album for this new group.
		$album = ES::table('Album');
		$album->uid = $this->id;
		$album->type = SOCIAL_TYPE_GROUP;
		$album->user_id = $my->id;

		$album->title = 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR';
		$album->caption = 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR_DESC';
		$album->created = ES::date()->toMySQL();
		$album->core = SOCIAL_ALBUM_PROFILE_PHOTOS;
		$album->store();

		// now we need to create photo
		$photo = ES::table('Photo');

		$photo->uid = $this->id;
		$photo->type = SOCIAL_TYPE_GROUP;
		$photo->user_id = $my->id;
		$photo->album_id = $album->id;
		$photo->title = $targetPhoto->title;
		$photo->caption = $targetPhoto->caption;
		$photo->created = ES::date()->toMySQL();
		$photo->state = 1;
		$photo->storage = SOCIAL_STORAGE_JOOMLA;
		$photo->total_size = $targetPhoto->total_size;
		$photo->store();

		// update cover photo of the album.
		$album->cover_id = $photo->id;
		$album->store();


		$avatar = ES::table('Avatar');
		$avatar->uid = $this->id;
		$avatar->type = SOCIAL_TYPE_GROUP;
		$avatar->photo_id = $photo->id;
		$avatar->small = $targetAvatar->small;
		$avatar->medium = $targetAvatar->medium;
		$avatar->square = $targetAvatar->square;
		$avatar->large = $targetAvatar->large;
		$avatar->modified = ES::date()->toMySQL();
		$avatar->storage = SOCIAL_STORAGE_JOOMLA;
		$avatar->store();

		// lets copy the avatar images.
		$config 	= FD::config();
		// Get the avatars storage path.
		$avatarsPath 	= FD::cleanPath($config->get('avatars.storage.container'));

		// Let's construct the final path.
		$sourcePath	= JPATH_ROOT . '/' . $avatarsPath . '/group/' . $targetGroupId;
		$targetPath	= JPATH_ROOT . '/' . $avatarsPath . '/group/' . $this->id;

		if (! JFolder::exists($targetPath)) {
			// now we are save to copy.
			if (JFolder::exists($sourcePath)) {
				JFolder::copy($sourcePath, $targetPath);
			}
		}

		// now we copy the photos
		// Get the avatars storage path.
		$photosPath 	= FD::cleanPath($config->get('photos.storage.container'));

		// Let's construct the final path.
		$sourcePath	= JPATH_ROOT . '/' . $photosPath . '/' . $targetPhoto->album_id . '/' . $targetPhoto->id;
		$targetPath	= JPATH_ROOT . '/' . $photosPath . '/' . $photo->album_id . '/' . $photo->id;

		if (! JFolder::exists($targetPath)) {
			// now we are save to copy.
			if (JFolder::exists($sourcePath)) {
				JFolder::copy($sourcePath, $targetPath);

				// now we need to insert into photo meta
        		$model = FD::model( 'Photos' );
        		$metas = $model->getMeta( $targetPhoto->id , SOCIAL_PHOTOS_META_PATH );

        		if ($metas) {
        			foreach ($metas as $meta) {

            			$relative   = $photosPath . '/' . $photo->album_id . '/' . $photo->id . '/' . basename( $meta->value );

            			$photoMeta = ES::table('PhotoMeta');
            			$photoMeta->photo_id = $photo->id;
            			$photoMeta->group = $meta->group;
            			$photoMeta->property = $meta->property;
            			$photoMeta->value = $relative;

            			$photoMeta->store();
        			}
        		}

			}
		}

		return true;
	}


	public function copyCover($targetGroupId)
	{
		// get avatar from target group
		// create album for current group
		// create photos for current group
		// duplicate physical files from target group
		//
		$my = ES::user();

		$targetCover = ES::table('Cover');
		$targetCover->load(array('uid' => $targetGroupId, 'type' => SOCIAL_TYPE_GROUP));

		if (! $targetCover->id) {
			return false;
		}

		$targetPhoto = ES::table('Photo');
		$targetPhoto->load($targetCover->photo_id);

		if ($targetPhoto->storage != SOCIAL_STORAGE_JOOMLA) {
			return false;
		}

		// now lets create album for this new group.
		$album = ES::table('Album');
		$album->uid = $this->id;
		$album->type = SOCIAL_TYPE_GROUP;
		$album->user_id = $my->id;

		$album->title = 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER';
		$album->caption = 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER_DESC';
		$album->created = ES::date()->toMySQL();
		$album->core = SOCIAL_ALBUM_PROFILE_COVERS;
		$album->store();

		// now we need to create photo
		$photo = ES::table('Photo');

		$photo->uid = $this->id;
		$photo->type = SOCIAL_TYPE_GROUP;
		$photo->user_id = $my->id;
		$photo->album_id = $album->id;
		$photo->title = $targetPhoto->title;
		$photo->caption = $targetPhoto->caption;
		$photo->created = ES::date()->toMySQL();
		$photo->state = 1;
		$photo->storage = SOCIAL_STORAGE_JOOMLA;
		$photo->total_size = $targetPhoto->total_size;
		$photo->store();

		// update cover photo of the album.
		$album->cover_id = $photo->id;
		$album->store();

		$cover = ES::table('Cover');
		$cover->uid = $this->id;
		$cover->type = SOCIAL_TYPE_GROUP;
		$cover->photo_id = $photo->id;
		$cover->x = $targetCover->x;
		$cover->y = $targetCover->y;
		$cover->modified = ES::date()->toMySQL();
		$cover->store();

		// now we copy the photos
		$config 	= FD::config();
		// Get the avatars storage path.
		$photosPath 	= FD::cleanPath($config->get('photos.storage.container'));

		// Let's construct the final path.
		$sourcePath	= JPATH_ROOT . '/' . $photosPath . '/' . $targetPhoto->album_id . '/' . $targetPhoto->id;
		$targetPath	= JPATH_ROOT . '/' . $photosPath . '/' . $photo->album_id . '/' . $photo->id;

		if (! JFolder::exists($targetPath)) {
			// now we are save to copy.
			if (JFolder::exists($sourcePath)) {
				JFolder::copy($sourcePath, $targetPath);

				// now we need to insert into photo meta
        		$model = FD::model( 'Photos' );
        		$metas = $model->getMeta( $targetPhoto->id , SOCIAL_PHOTOS_META_PATH );

        		if ($metas) {
        			foreach ($metas as $meta) {

            			$relative   = $photosPath . '/' . $photo->album_id . '/' . $photo->id . '/' . basename( $meta->value );

            			$photoMeta = ES::table('PhotoMeta');
            			$photoMeta->photo_id = $photo->id;
            			$photoMeta->group = $meta->group;
            			$photoMeta->property = $meta->property;
            			$photoMeta->value = $relative;

            			$photoMeta->store();
        			}
        		}

			}
		}

		return true;
	}

	/**
	 * Approves the user via email/user management which is use auto join group feature
	 *
	 * @since	1.4
	 * @access	public
	 * @param	int	The user id
	 * @return
	 */
	public function createMemberViaAutoJoinGroups($userId)
	{
		$member = FD::table('GroupMember');
		$member->load(array('cluster_id' => $this->id, 'uid' => $userId));

		$member->state = SOCIAL_GROUPS_MEMBER_PUBLISHED;
		$state = $member->store();

		// Additional triggers to be processed when the page starts.
		FD::apps()->load(SOCIAL_TYPE_GROUP);
		$dispatcher = FD::dispatcher();

		// Trigger: onComponentStart
		$dispatcher->trigger('user', 'onJoinGroup', array($userId, $this));

		// @points: groups.join
		// Add points when user joins a group
		$points = FD::points();
		$points->assign('groups.join', 'com_easysocial', $userId);

		// Publish on the stream
		if ($state) {
			// Add stream item so the world knows that the user joined the group
			$this->createStream($userId, 'join');
		}

		// Send notifications to group members when a new member joined the group
		$this->notifyMembers('join', array('userId' => $userId));

		return $state;
	}
}
