<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent view
FD::import( 'site:/views/views' );

class EasySocialViewGroups extends EasySocialSiteView
{
	/**
	 * Retrieves groups
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	An array of groups
	 */
	public function getGroups($groups = array() , $pagination = null , $featuredGroups = array(), $sorting = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		// Determines if we should add the category header
		$categoryId = $this->input->get('categoryId', '', 'int');
		$category = false;

		$theme = FD::themes();

		if ($categoryId) {
			$category = FD::table('GroupCategory');
			$category->load($categoryId);
		}

		// Filter
		$filter = $this->input->get('filter', 'all');

		$sort = JRequest::getVar('ordering');

		if ($sort) {
			$theme->set('showSorting', false);
			$theme->set('showCategoryHeader', false);
		}
		
		$theme->set('activeCategory', $category);
		$theme->set('filter', $filter);
		$theme->set('ordering', $sort);
		$theme->set('pagination', $pagination);
		$theme->set('featuredGroups', $featuredGroups);
		$theme->set('groups', $groups);

		// Retrieve items from the template
		$content = $theme->output('site/groups/default.items');

		return $this->ajax->resolve($content);
	}

	/**
	 * Responsible to output the application contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialAppTable	The application ORM.
	 */
	public function getAppContents( $app )
	{
		$ajax 	= FD::ajax();

		// If there's an error throw it back to the caller.
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the current logged in user.
		$groupId 	= JRequest::getInt( 'groupId' );
		$group 		= FD::group( $groupId );

		// Load the library.
		$lib		= FD::getInstance( 'Apps' );
		$contents 	= $lib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'groups' , $app , array( 'groupId' => $group->id ) );

		// Return the contents
		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the invite friend form
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function inviteFriends()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );
		$my 	= FD::user();

		// Get a list of friends that are already in this group
		$model 		= FD::model('Groups');
		$friends	= $model->getFriendsInGroup( $group->id , array( 'userId' => $my->id ) );
		$exclusion	= array();

		if ($friends) {

			foreach ($friends as $friend) {
				$exclusion[]	= $friend->id;
			}
		}

		$theme 	= FD::themes();
		$theme->set('exclusion', $exclusion);
		$theme->set('group', $group);

		$contents 	= $theme->output( 'site/groups/dialog.invite' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation dialog to set a group as featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFeatured()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.featured' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation dialog to set a group as featured
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.unfeature' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Post process after a user response to the invitation.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 */
	public function respondInvitation($group, $action)
	{
		return $this->ajax->resolve();
	}

	/**
	 * Displays the respond to invitation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRespondInvitation()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		// Get the group id from request
		$id = $this->input->get('id', 0, 'int');

		// Load up the group
		$group = FD::group($id);

		// Load the member
		$member = FD::table('GroupMember');
		$member->load(array('cluster_id' => $group->id, 'uid' => $this->my->id));

		// Get the inviter
		$inviter = FD::user($member->invited_by);

		$theme = FD::themes();
		$theme->set('group', $group);
		$theme->set('inviter', $inviter);

		$contents = $theme->output('site/groups/dialog.respond');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays the confirmation to delete a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to delete a group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnpublishGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.unpublish' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to withdraw application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmWithdraw()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		$theme 	= FD::themes();
		$theme->set( 'group' , $group );

		$contents 	= $theme->output( 'site/groups/dialog.withdraw' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to approve user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmApprove()
	{
		// Only logged in users are allowed here.
		ES::requireLogin();

		// Load the group
		$id = $this->input->get('id', 0, 'int');
		$group = ES::group($id);

		// Get the user id
		$userId = $this->input->get('userId', 0, 'int');
		$user = ES::user($userId);

		// Get the return url
		$permalink = $group->getPermalink(false);
		$return = $this->input->get('return', '', 'default');
		$return = !empty($return) ? base64_encode($return) : $return;
		$return = $return ? $return : $permalink;

		$theme = FD::themes();
		$theme->set('return', $return);
		$theme->set('group', $group);
		$theme->set('user', $user);

		$contents = $theme->output('site/groups/dialog.approve');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays the confirmation to remove user from group
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveMember()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the user id
		$userId = JRequest::getInt( 'userId' );
		$user 	= FD::user( $userId );

		$theme 	= FD::themes();
		$theme->set( 'group'	, $group );
		$theme->set( 'user'		, $user );

		$contents 	= $theme->output( 'site/groups/dialog.remove.member' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to reject user application
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmReject()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$group 	= FD::group( $id );

		// Get the user id
		$userId = JRequest::getInt( 'userId' );
		$user 	= FD::user( $userId );

		$theme 	= FD::themes();
		$theme->set( 'group'	, $group );
		$theme->set( 'user'		, $user );

		$contents 	= $theme->output( 'site/groups/dialog.reject' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the confirmation to reject invitation for user
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmCancelInvitation()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax = FD::ajax();

		// Get the group id from request
		$id = JRequest::getInt('id');

		// Load up the group
		$group = FD::group($id);

		// Get the user id
		$userId = JRequest::getInt('userId');
		$user = FD::user($userId);

		$theme = FD::themes();
		$theme->set('group', $group);
		$theme->set('user', $user);

		$contents = $theme->output('site/groups/dialog.cancel.invitation');

		return $ajax->resolve($contents);
	}	

	/**
	 * Displays the join group exceeded notice
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exceededJoin()
	{
		$ajax 	= FD::ajax();

		$my 		= FD::user();
		$allowed 	= $my->getAccess()->get( 'groups.join' );

		$theme 	= FD::themes();
		$theme->set( 'allowed'	, $allowed );
		$contents	= $theme->output( 'site/groups/dialog.join.exceeded' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the join group dialog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function joinGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		// Get the group id from request
		$id = $this->input->get('id', 0, 'int');

		// Determines if this is an api request
		$api = $this->input->get('api', false, 'bool');

		// Load up the group
		$group = ES::group($id);

		if (!$id || !$group) {
			return $this->ajax->reject();
		}

		// Try to load the member object
		$member = ES::table('GroupMember');
		$member->load(array('uid' => $this->my->id , 'type' => SOCIAL_TYPE_USER , 'cluster_id' => $group->id));

		// Determines which namespace we should be using
		$namespace = 'site/groups/dialog.join.open';

		// Check if the group is open or closed
		if ($group->isClosed()) {
			if ($member->state == SOCIAL_GROUPS_MEMBER_PUBLISHED) {
				$namespace = 'site/groups/dialog.join.invited';
			} else {
				$namespace = 'site/groups/dialog.join.closed';
			}
		}

		$theme = ES::themes();
		$theme->set('group', $group);

		$contents = $theme->output($namespace);

		return $this->ajax->resolve($contents);
	}

	/**
	 * Post process after a user is made an admin
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function makeAdmin()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve();
	}

	/**
	 * Displays the make admin confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRevokeAdmin()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax = FD::ajax();

		// Get the group id from request
		$id = JRequest::getInt('userId');

		// Load up the group
		$user = FD::user($id);

		$theme = FD::themes();
		$theme->set('user', $user);

		// Check if the group is open or closed
		$contents 	= $theme->output( 'site/groups/dialog.revoke.admin' );

		return $ajax->resolve( $contents );
	}


	/**
	 * Displays the make admin confirmation dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmMakeAdmin()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the group id from request
		$id 	= JRequest::getInt( 'id' );

		// Load up the group
		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		// Check if the group is open or closed
		$contents 	= $theme->output( 'site/groups/dialog.admin' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays the join group dialog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmLeaveGroup()
	{
		// Only logged in users are allowed here.
		FD::requireLogin();

		// Get the group id from request
		$id = $this->input->get('id', 0, 'int');

		// Load up the group
		$group = FD::group($id);

		$theme = FD::themes();
		$theme->set('group', $group);

		$contents = $theme->output('site/groups/dialog.leave');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Responsible to return the default output when a user really leaves a group
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function leaveGroup()
	{
		return $this->ajax->resolve();
	}

	/**
	 * Allows caller to re-render the stream items on the site.
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getStream($stream)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		// Get the group id from request
		$id = $this->input->get('id', 0, 'int');

		// Load up the group
		$group = FD::group($id);

		// RSS
		if ($this->config->get('stream.rss.enabled')) {
			$this->addRss(FRoute::groups(array('id' => $group->getAlias(), 'layout' => 'item'), false));
		}

		// Get the contents of the stream
		$theme = ES::themes();
		$theme->set('rssLink', $this->rssLink);
		$theme->set('stream', $stream);
		$contents = $theme->output('site/groups/item.feeds');

		return $this->ajax->resolve($contents);
	}


	/**
	 * Displays the stream filter form
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFilter( $filter, $groupId )
	{
		$ajax 		= FD::ajax();
		$group 		= FD::group( $groupId );

		$theme 		= FD::themes();

		$theme->set( 'controller'	, 'groups' );
		$theme->set( 'filter'		, $filter );
		$theme->set( 'uid'			, $group->id );

		$contents	= $theme->output( 'site/stream/form.edit' );

		return $ajax->resolve( $contents );
	}

	/**
	 * post processing for quicky adding group filter.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFilter( $filter, $groupId )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();

		$theme 		= FD::themes();

		$group 		= FD::group( $groupId );


		$theme->set( 'filter'	, $filter );
		$theme->set( 'group'	, $group );
		$theme->set( 'filterId'	, '0' );

		$content	= $theme->output( 'site/groups/item.filter' );

		return $ajax->resolve( $content, JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_SAVED' ) );
	}

	/**
	 * post processing after group filter get deleted.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFilter( $groupId )
	{
		$ajax 	= FD::ajax();

		FD::requireLogin();
		FD::info()->set( $this->getMessage() );

		$group 	= FD::group( $groupId );
		$url 	= FRoute::groups( array( 'layout' => 'item' , 'id' => $group->getAlias() ), false );

		return $ajax->redirect( $url );
	}

	public function initInfo($steps = null)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		return $ajax->resolve($steps);
	}

	public function getInfo($fields = null)
	{
		$ajax = FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$theme = FD::themes();

		$theme->set('fields', $fields);

		$contents = $theme->output('site/groups/item.info');

		return $ajax->resolve($contents);
	}

	/**
	 * Displays the suggest result
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function suggest($groups = array())
	{
		if (!$groups) {
			return $this->ajax->resolve($groups);
		}

		$data = array();

		// Load through the result list.
		foreach ($groups as $group) {

			$obj = new stdClass();
			$obj->avatar = $group->getAvatar(SOCIAL_AVATAR_SMALL);
			$obj->title = $group->title;
			$obj->permalink = $group->getPermalink();
			$obj->id = $group->id;

			$data[] = $obj;
		}

		return $this->ajax->resolve($data);
	}
}
