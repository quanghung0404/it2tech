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

class SocialLikes extends EasySocial
{
	public $data = array();
	public $uid = null;
	public $element = null;
	public $group = null;
	public $verb = null;
	public $options = array();
	public $stream_id = null;

	public function __construct( $uid = null, $element = null, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $options = array() )
	{
		parent::__construct();
		
		$this->uid = $uid;
		$this->element = $element;
		$this->group = $group;
		$this->verb = $verb;

		if (!is_null($uid) && !is_null($element)) {
			$this->get($uid, $element, $verb, $group, $options);
		}
	}

	public static function factory( $uid = null, $element = null, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $options = array() )
	{
		return new self( $uid, $element, $verb, $group, $options );
	}

	/**
	 * Determines if the provided user has liked the object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The user id to check upon
	 * @param	int 		The target unique id.
	 * @param	string		The target unique type.
	 * @return	bool		True if user has liked the item, false otherwise.
	 */
	public function hasLiked( $uid = null , $element = null, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( is_null( $uid ) )
		{
			$uid = $this->uid;
		}

		if( is_null( $element ) )
		{
			$element = $this->element;
		}

		if( is_null( $verb ) )
		{
			$verb = $this->verb;
		}


		if( is_null( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$model 		= FD::model( 'Likes' );
		$hasLiked 	= $model->hasLiked( $uid , $this->formKeys( $element, $group, $verb ) , $userId );

		return $hasLiked;
	}

	/**
	 * Get's the likes data for a particular item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id to lookup for.
	 * @param	string	The unique element to lookup for
	 * @return	SocialLikes		Return itself for chaining.
	 */
	public function getCount( $uid = null , $element = null, $verb = null, $group = null )
	{
		$likeCnt 	= 0;

		if( is_null( $uid ) )
		{
			$uid = $this->uid;
		}

		if( is_null( $element ) )
		{
			$element = $this->element;
		}

		if( is_null( $verb ) )
		{
			$verb = $this->verb;
		}

		if( is_null( $group ) )
		{
			$group = $this->group;
		}

		if (empty($group)) {
			$group = SOCIAL_APPS_GROUP_USER;
		}

		$model = FD::model( 'Likes' );
		$likeCnt = $model->getLikesCount( $uid, $this->formKeys( $element, $group, $verb ) );

		return $likeCnt;
	}

	/**
	 * Get's the likes data for a particular item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id to lookup for.
	 * @param	string	The unique element to lookup for
	 * @return	SocialLikes		Return itself for chaining.
	 */
	public function get( $id , $element, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $useStreamId = false )
	{
		$model 			= FD::model( 'Likes' );

		// Get the likes
		if ($useStreamId) {
			// var_dump( $useStreamId );
			$this->data			= $model->getLikes( $useStreamId , 'stream' );
			$this->stream_id 	= $useStreamId;
		} else {

			// Build the key
			$key 			= $this->formKeys( $element , $group, $verb );
			$this->data		= $model->getLikes( $id , $key );
		}
		$this->uid		= $id;
		$this->element	= $element;
		$this->group	= $group;
		$this->verb		= $verb;

		return $this;
	}

	/**
	 * Get's the likes data based on stream item.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The unique stream id to lookup for.
	 * @return	SocialLikes		Return itself for chaining.
	 */
	public function getStreamLike( $streamId )
	{
		$model 			= FD::model( 'Likes' );
		$this->data		= $model->getLikes( $streamId , 'stream' );

		if ($this->data) {
			$tmp = $this->data[0];

			$tmpData = explode( '.', $tmp->type);

			$this->uid		= $tmp->uid;
			$this->element	= $tmpData[0];
			$this->group	= $tmpData[1];

			unset($tmpData[0]);
			unset($tmpData[1]);

			$this->verb		= implode('.', $tmpData);
		}
	}



	private function formKeys( $element , $group, $verb = '' )
	{
		$key = $element . '.' . $group;

		if( $verb )
		{
			$key = $key . '.' . $verb;
		}

		return $key;
	}

	public function setOption( $key, $value )
	{
		$this->options[ $key ] = $value;
	}

	/**
	 * Generates the like link.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function button()
	{
		$model = ES::model('Likes');

		// We should respect the stream id or if it is photos, we won't use the stream id
		$streamId = !$this->stream_id || $this->element == 'photos' ? false : $this->stream_id;

		// Determines if the user has liked the item before
		$liked = $model->hasLiked($this->uid, $this->formKeys($this->element, $this->group, $this->verb), $this->my->id, $streamId);

		// Generate the button text
		$text = JText::_('COM_EASYSOCIAL_LIKES_LIKE');

		if ($liked) {
			$text = JText::_('COM_EASYSOCIAL_LIKES_UNLIKE');
		}

		// Should we inject the stream id
		$streamid = '';

		if (!empty($this->options['streamid'])) {
			$streamid = $this->options['streamid'];
		} else {
			$streamid = $this->stream_id;
		}

		$theme = ES::themes();

		$theme->set('text', $text);
		$theme->set('uid', $this->uid);
		$theme->set('element', $this->element);
		$theme->set('group', $this->group);
		$theme->set('verb', $this->verb);
		$theme->set('streamid', $streamid);

		$output = $theme->output('site/likes/action');

		return $output;
	}

	/**
	 * Generates the likes output
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html()
	{
		// Get the count.
		$count = 0;
		$text = '';

		if ($this->data !== false) {
			$count = count( $this->data );
			$text  = $this->toString();
		}

		$theme = ES::themes();

		$theme->set('text', $text);
		$theme->set('count', $count);
		$theme->set('uid', $this->uid);
		$theme->set('element', $this->element);
		$theme->set('group', $this->group);
		$theme->set('verb', $this->verb);

		$output = $theme->output('site/likes/item');

		return $output;
	}

	/**
	 * Deprecated. Use $likes->html();
	 *
	 * @deprecated 1.4
	 */
	public function toHTML()
	{
		return $this->html();
	}

	/**
	 * Retrieves the likes text
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The viewer's id (optional)
	 * @param	bool	Determines if we should use normal plain text
	 * @return
	 */
	public function toString( $viewerId = null, $plaintext = false )
	{
		// Default return text.
		$text 			= '';

		// If there's no likes at all, we should just return an empty string.
		if( !$this->data )
		{
			return $text;
		}

		// Get current logged in user as we need to know if the viewer is themselves or not.
		//$viewerId   = ($viewerId) ? $viewerId : FD::user()->id;
		$viewer 	= FD::user( $viewerId );

		// Ensure that the data is an array
		$data 		= !is_array( $this->data ) ? array( $this->data ) : $this->data;

		// List of users which liked this item.
		$users 		= array();

		// Retrieve only users
		foreach( $data as $like )
		{
			$users[]	= $like->created_by;
		}

		$users = array_unique( $users );

		// Determines if we should use the term YOU in the language string
		$useYou 	= in_array( $viewer->id , $users );

		// Default to use 3rd party view of likes
		$language 	= 'COM_EASYSOCIAL_LIKES_STREAM_USER_';

		// Unique the result
		$users 		= array_unique($users);

		// Get the total users in the likes
		$total 		= count($users);
		$remainder 	= 0;

		// If we need to use "you" within the language string
		if( $useYou )
		{
			$language 	= 'COM_EASYSOCIAL_LIKES_STREAM_YOU_';
		}

		// Possibilities
		// 1. You like this
		if( $total == 1 && $useYou )
		{
			$language 	.= 'LIKE_THIS';
		}

		// Possibilities
		// 1. user1 likes this
		if( $total == 1 && !$useYou )
		{
			$language 	.= 'LIKES_THIS';
		}

		// Possibilities
		// 1. You and user1 likes this
		// 2. user1 and user2 likes this
		if( $total == 2 )
		{
			$language 	.= 'AND_1USER_LIKES_THIS';
		}

		// Possibilities
		// 1. You,user1 and user2 likes this
		// 2. user1 , user2 and user3 likes this
		if( $total == 3 )
		{
			$language 	.= 'AND_2USERS_LIKES_THIS';
		}

		// Possibilities
		// 1. You,user1, user2 and user3 likes this
		// 2. user1, user2, user3 and user4 likes this
		if( $total == 4 )
		{
			$language 	.= 'AND_3USERS_LIKES_THIS';
		}

		// Possibilities
		// 1. You, user1, user2 and 24 others like this
		// 2. user1, user2, user3 and 24 others like this
		if( $total > 4 )
		{
			$language 	.= 'AND_OTHERS_LIKE_THIS';

			$remainder	= $total - 3;
		}

		// Determines if we should use plain text
		if( $plaintext )
		{
			$language 	.= '_PLAINTEXT';
		}

		// If user is in the list, we need to relocate the viewer to the first index
		if( $useYou )
		{
			// Get the current viewer
			$key 	= array_search( $viewer->id , $users );

			if( $key !== false )
			{
				unset( $users[ $key ] );

				array_unshift( $users , $viewer->id );
			}
		}

		// Get users
		$userlist 		= FD::user( $users );

		// somehow FD::users messed up the user ordering.
		// let resort the user list.
		$tmpUsers = array();
		foreach( $users as $id){
			$tmpUsers[] = FD::user($id);
		}
		$users = $tmpUsers;

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'total'	, $total );
		$theme->set( 'language'	, $language );
		$theme->set( 'users'	, $users );
		$theme->set( 'verb'		, $this->verb );
		$theme->set( 'uid' 		, $this->uid );
		$theme->set( 'element'	, $this->element );
		$theme->set( 'group'	, $this->group );
		$theme->set( 'remainder', $remainder );

		$text 		= $theme->output( 'site/likes/string' );
		return $text;
	}

	public function toArray()
	{
		return $this->data;
	}

	/**
	 * Allows 3rd party implementation to delete likes related to an object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The unique item id that is being liked
	 * @param	string		The unique item element that is being liked
	 * @param	int 		The current user that liked the item
	 * @return 	boolean 	true or false.
	 */
	public function delete( $uid = null , $element = null, $verb = null, $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( is_null( $uid ) )
		{
			$uid = $this->uid;
		}

		if( is_null( $element ) )
		{
			$element = $this->element;
		}

		if( is_null( $verb ) )
		{
			$verb = $this->verb;
		}

		if( is_null( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$like 	= FD::table( 'Likes' );

		// Check if the user has already liked this item before.
		$exists = $like->load( array( 'uid' => $uid , 'type' => $this->formKeys( $element, $group, $verb ) , 'created_by' => $userId ) );

		// If item has been liked before, return false.
		if( !$exists )
		{
			return false;
		}

		$state 	= $like->delete();

		if( !$state )
		{
			return false;
		}

		$key 		= $uid . '.' . $this->formKeys( $element, $group, $verb );
		$likeModel = FD::model( 'Likes' );
		$likeModel->removeLikeItem( $key, $userId );

		return true;
	}

	/**
	 * Allows 3rd party implementation to toggle likes to an object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The unique item id that is being liked
	 * @param	string		The unique item element that is being liked
	 * @param	int 		The current user that liked the item
	 * @return	SocialTableLikes
	 */
	public function toggle( $uid = null , $element = null , $verb = null, $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( is_null( $uid ) )
		{
			$uid = $this->uid;
		}

		if( is_null( $element ) )
		{
			$element = $this->element;
		}

		if( is_null( $verb ) )
		{
			$verb = $this->verb;
		}

		if( is_null( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$like 	= FD::table( 'Likes' );

		// Check if the user has already liked this item before.
		$exists = $like->load( array( 'uid' => $uid , 'type' => $this->formKeys( $element, $group, $verb ) , 'created_by' => $userId ) );

		// If item has been liked before, return false.
		if( $exists )
		{
			$state 	= $this->delete( $uid , $element , $verb, $group, $userId );

			return $state;
		}

		return $this->add( $uid , $element , $verb, $group, $userId );
	}

	/**
	 * Allows 3rd party implementation to add likes to an object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The unique item id that is being liked
	 * @param	string		The unique item element that is being liked
	 * @param	int 		The current user that liked the item
	 * @return	SocialTableLikes
	 */
	public function add( $uid = null , $element = null , $verb = null, $group = SOCIAL_APPS_GROUP_USER, $userId = null )
	{
		if( is_null( $uid ) )
		{
			$uid = $this->uid;
		}

		if( is_null( $element ) )
		{
			$element = $this->element;
		}

		if( is_null( $verb ) )
		{
			$verb = $this->verb;
		}

		if( is_null( $userId ) )
		{
			$userId = FD::user()->id;
		}

		$like 	= FD::table( 'Likes' );

		// Check if the user has already liked this item before.
		$exists = $like->load( array( 'uid' => $uid , 'type' => $this->formKeys( $element, $group, $verb ) , 'created_by' => $userId ) );

		// If item has been liked before, return false.
		if( $exists )
		{
			return false;
		}

		$like->uid 	= $uid;
		$like->type = $this->formKeys( $element, $group, $verb );
		$like->created_by  = $userId;

		$state 	= $like->store();

		if( !$state )
		{
			return false;
		}

		// add into static variable
		$key 		= $uid . '.' . $this->formKeys( $element, $group, $verb );
		$likeModel = FD::model( 'Likes' );
		$likeModel->setLikeItem( $key, $like );

		return $like;
	}

	/**
	 * Retrieve a list of users who liked this item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determines if we should return a user object.
	 * @return
	 */
	public function getParticipants( $userObject = true )
	{
		$model = FD::model( 'likes' );
		$users = $model->getLikerIds( $this->uid, $this->formKeys( $this->element, $this->group, $this->verb ) );

		$objects = array();

		if( $users && $userObject )
		{
			foreach( $users as $user )
			{
				$objects[] = FD::user( $user );
			}

			return $objects;
		}

		return $users;
	}

	public function getLikedUsersDialog()
	{
		$users = $this->getParticipants();

		$theme = FD::themes();

		$theme->set( 'users', $users );

		$html = $theme->output( 'site/likes/dialog.likedUsers' );

		return $html;
	}

}
