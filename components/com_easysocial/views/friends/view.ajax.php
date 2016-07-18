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

FD::import( 'site:/views/views' );

class EasySocialViewFriends extends EasySocialSiteView
{
	/**
	 * Displays confirmation dialog to delete a user from a list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmRemoveFromList()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.delete.list.user' );

		return $ajax->resolve( $contents );
	}

	/**
	 * This returns the html block for items generated via the data api
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function popboxRequest($friendId = null)
	{
		if (is_object($friendId)) {
			$friendId = $friendId->id;
		}

		$theme 	= FD::themes();
		$theme->set('friendId', $friendId);
		$contents = $theme->output('site/friends/request.popbox');

		return $this->ajax->resolve($contents);
	}

	/**
	 * This returns the html dialog because the user exceeded their friend request limit.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function exceeded($friend = null)
	{
		$theme = FD::themes();

		$contents = $theme->output('site/friends/dialog.exceeded');

		return $this->ajax->resolve($contents);
	}

	/**
	 * This returns the html block on friend request made on users listing
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function usersRequest( $friend = null )
	{
		$ajax 	= FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$theme 	= FD::themes();

		$contents 	= $theme->output( 'site/users/button.pending' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Post processing after setting item as default
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function setDefault()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}
		return $ajax->resolve();
	}

	/**
	 * Displays confirmation to delete a friend list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignList()
	{
		// Only registered users allowed here
		ES::requireLogin();

		// Get the target id.
		$id = $this->input->get('id', 0, 'int');

		if (!$id) {
			return $this->ajax->reject();
		}

		$list = ES::table('List');
		$list->load($id);

		// Get a list of users that are already in this list.
		$users = $list->getMembers();
		$users = json_encode($users);

		$theme = ES::themes();
		$theme->set('list', $list);
		$theme->set('users', $users);

		$contents = $theme->output('site/friends/dialog.list.assign');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Displays confirmation to delete a friend list
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDeleteList()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$list 	= FD::table( 'List' );
		$list->load( $id );

		$theme 	= FD::themes();
		$theme->set( 'list' , $list );

		$contents	= $theme->output( 'site/friends/dialog.delete.list' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation to reject a friend request
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmReject()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.reject' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation that the friend has been removed.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function friendRemoved()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.removed' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation dialog to remove a friend.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmUnfriend()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.unfriend' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation dialog to remove a friend.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function requestCancelled()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.request.cancelled' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Displays confirmation dialog to remove a friend.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmCancelRequest()
	{
		// Only registered users allowed here
		FD::requireLogin();

		$ajax 	= FD::ajax();

		// Get the target id.
		$id 	= JRequest::getInt( 'id' );

		if( !$id )
		{
			// Throw error here.
		}

		$user 	= FD::user( $id );

		$theme 	= FD::themes();
		$theme->set( 'user' , $user );

		$contents	= $theme->output( 'site/friends/dialog.cancel.request' );

		return $ajax->resolve( $contents );
	}


	/**
	 * Post processing after a friend request is rejected
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reject()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the standard button
		$theme 	= FD::themes();

		$button	= $theme->output( 'site/profile/button.friends.add' );

		return $ajax->resolve( $button );
	}

	/**
	 * Displays the dialog content when a friend is rejected
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function friendRejected()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();

		$output	= $theme->output( 'site/profile/dialog.friends.rejected' );


		return $ajax->resolve( $output );
	}

	/**
	 * Display confirmation message before cancelling the friend request.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmCancel()
	{
		// Require user to be logged in.
		FD::requireLogin();

		$ajax	= FD::ajax();

		// Get dialog
		$theme	= FD::themes();

		$output = $theme->output( 'site/profile/dialog.friends.cancel' );

		return $ajax->resolve( $output );
	}

	/**
	 * Cancels a friend request.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function cancelRequest($friendId = null)
	{
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		// Get the new button that should be applied
		$theme = FD::themes();
		$theme->set('friendId', $friendId);
		$button	= $theme->output('site/profile/button.friends.add');

		return $this->ajax->resolve($button);
	}

	/**
	 * Returns a JSON formatted value of the list item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array 	An array of SocialTableList
	 */
	public function getLists( $lists )
	{
		$ajax 	= FD::ajax();

		// Format the result.
		$result 	= array();

		if( !$lists )
		{
			return $ajax->resolve( $result );
		}

		foreach( $lists as $list )
		{
			$obj 		= new stdClass();

			$obj->id 		= $list->id;
			$obj->title 	= $list->title;
			$obj->count 	= $list->getCount();

			$result[]		= $obj;
		}

		return $ajax->resolve( $result );
	}

	/**
	 * Cancels a friend request.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getListCounts( $lists = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$result 	= array();

		if( !$lists )
		{
			return $ajax->resolve( $result );
		}

		foreach( $lists as $list )
		{
			$data 			= new stdClass();
			$data->id		= $list->id;
			$data->count	= $list->getCount();
			$result[]		= $data;
		}

		return $ajax->resolve( $result );
	}

	/**
	 * Executes when a user is removed from the list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function removeFromList()
	{
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}

	/**
	 * Assigns a user into a list.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function assign( $users = array() )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$contents 	= array();

		$activeUser	= FD::user( JRequest::getInt( 'userId' ) );

		foreach( $users as $user )
		{
			$theme 	= FD::themes();

			$theme->set( 'activeUser'	, $activeUser );
			$theme->set( 'user'		, $user );
			$theme->set( 'filter'	, 'list' );

			$contents[] 	= $theme->output( 'site/friends/default.item' );
		}

		return $ajax->resolve( $contents );
	}

	/**
	 * Returns a JSON formatted value of result when item is added to the list.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string	JSON object.
	 */
	public function getListFriends( $list , $items = array() , $pagination )
	{
		$ajax 	= FD::getInstance( 'Ajax' );

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		$theme 	= FD::themes();

		$friends 	= array();

		if( $items )
		{
			$friends	= FD::user( $items );
		}

		$theme->set( 'filter'		, 'list' );
		$theme->set( 'activeList' 	, $list );
		$theme->set( 'friends'		, $friends );
		$theme->set( 'pagination'	, $pagination );

		$output 	= $theme->output( 'site/friends/default.items' );

		return $ajax->resolve( $output );
	}

	/**
	 * Responsible to return html codes to the ajax calls.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function filter($filter, $friends = array(), $pagination)
	{
		$ajax 	= FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$theme = FD::themes();

		$theme->set('pagination', $pagination);
		$theme->set('friends', $friends);
		$theme->set('activeList', '');
		$theme->set('filter', $filter);

		if ($filter == 'invites') {
			$output = $theme->output('site/friends/default.invites');
		} else {
			$output = $theme->output('site/friends/default.items');
		}

		return $ajax->resolve($output);
	}

	/**
	 * Process request calls
	 *
	 * @param	null
	 * @return	null
	 **/
	public function request($friend = null)
	{
		// Reject the request since there was some errors here.
		if ($this->hasErrors()) {
			return $this->ajax->reject($this->getMessage());
		}

		// Get the new button that should be applied
		$theme = FD::themes();
		$theme->set('friend', $friend);
		$button	= $theme->output( 'site/profile/button.friends.sent' );

		return $this->ajax->resolve($friend->id, $button);
	}

	/**
	 * This displays the request form when adding a particular user.
	 *
	 * Example calling using Ajax:
	 *
	 * <code>
	 * EasySocial.ready(function($){
	 *		EasySocial.ajax( 'site.views.friends.requestForm' , {}, function(){
	 * 			console.log( 'do something here' );
	 * 		})
	 * });
	 *</code>
	 *
	 * @since	1.0
	 * @param	null
	 * @return	JSON		JSON data.
	 */
	public function requestForm()
	{
		// Guests are not allowed here.
		FD::requireLogin();

		$id 	= JRequest::getInt( 'id' );
		$user 	= FD::user( $id );
		$my 	= FD::user();

		// Get current user's lists
		$listsModel	= FD::model( 'Lists' );
		$lists 		= $listsModel->getLists( array( 'user_id' => $my->id ) );

		// Let's get the theme.
		$theme	= FD::get( 'Themes' );
		$theme->set( 'user' 	, $user );
		$theme->set( 'lists'	, $lists );

		$output = $theme->output( 'site/friends/request' );

		$ajax 	= FD::getInstance( 'Ajax' );
		$ajax->success( $output );
	}

	/**
	 * Retrieve the counts
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getCounters( $totalFriends , $totalPendingFriends , $totalRequestSent , $totalSuggestedFriends )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		return $ajax->resolve( $totalFriends , $totalPendingFriends , $totalRequestSent , $totalSuggestedFriends );
	}

	/**
	 * This view is responsible to output back to the notifications bar
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function notificationsApprove( $friend )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the current logged in user.
		$my 			= FD::user();

		// Get the initiator's information
		$user 			= FD::user( $friend->actor_id );
		$totalMutual	= $user->getTotalMutualFriends( $my->id );

		// Get the buttons
		$theme	= FD::themes();
		$theme->set( 'user' , $user );
		$contents = $theme->output( 'site/toolbar/friends.accepted' );

		// Get the mutual friends result
		$theme 	= FD::themes();
		$theme->set( 'user' , $user );
		$mutualContent 	= $theme->output( 'site/toolbar/friends.mutual' );

		return $ajax->resolve( $contents , $mutualContent );
	}

	/**
	 * This view is responsible to approve pending friend requests.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function approve( $friend )
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the initiator's information
		$user 	= FD::user( $friend->actor_id );

		// Get the buttons
		$theme	= FD::themes();
		$button	= $theme->output( 'site/profile/button.friends.friends' );

		return $ajax->resolve( $button );
	}

	/**
	 * Called when the friend is deleted
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfriend()
	{
		$ajax 	= FD::ajax();

		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the new button that should be applied
		$theme 	= FD::themes();
		$button	= $theme->output( 'site/profile/button.friends.add' );

		return $ajax->resolve( $button );
	}

	/**
	 * Suggest a mixin between users and friend lists.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of user id's.
	 * @param	Array	An array of friend list id's.
	 * @return
	 */
	public function suggestWithList( $friends , $friendLists )
	{
		$ajax 	= FD::ajax();

		// Format result to SocialUser object.
		$result 	= array();

		// If there's nothing, just return the empty object.
		if( !$friends && !$friendLists )
		{
			return $ajax->resolve( $result );
		}



		// Load through the result list.
		if( $friendLists )
		{
			$inputName 	= JRequest::getVar( 'friendListName' );
			foreach( $friendLists as $list )
			{
				$obj 				= new stdClass();

				$obj->id 			= 'list-' . $list->id;
				$obj->title 		= $list->title;

				// Get the item's html output
				$theme 		= FD::themes();
				$theme->set( 'list' , $list );
				$theme->set( 'inputName' , $inputName );
				$obj->html 	= $theme->output( 'site/friends/suggest.list.item' );

				$obj->menuHtml	= $list->title;
				$obj->className		= 'list';

				$result[]	= $obj;
			}
		}

		// Load through the result list.
		if( $friends )
		{
			$inputName 	= JRequest::getVar( 'inputName' );

			foreach( $friends as $user )
			{
				$obj 		= new stdClass();

				$obj->id 	= 'user-' . $user->id;
				$obj->title = $user->getName();

				// Get the item's html output
				$theme 		= FD::themes();
				$theme->set( 'user' , $user );
				$theme->set( 'inputName' , $inputName );
				$obj->html 	= $theme->output( 'site/friends/suggest.friend.item' );

				// Get the item's dropdown output
				$obj->menuHtml	 = $user->getName();

				$obj->className		= 'user';

				$result[]	= $obj;
			}
		}

		return $ajax->resolve( $result );
	}

	/**
	 * Responsible to output the JSON object of a result when searched.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 */
	public function suggest( $result )
	{
		$ajax 	= FD::ajax();

		// If there's nothing, just return the empty object.
		if( !$result )
		{
			return $ajax->resolve(array());
		}

		// Format result to SocialUser object.
		$friends 	= array();

		// Load through the result list.
		foreach( $result as $user )
		{
			$obj 				= new stdClass();
			$obj->avatar		= $user->getAvatar( SOCIAL_AVATAR_SMALL );
			$obj->screenName 	= $user->getName();
			$obj->permalink     = $user->getPermalink();
			$obj->id 			= $user->id;

			$friends[]	= $obj;
		}

		return $ajax->resolve( $friends );
	}
}
