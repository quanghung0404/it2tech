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
defined('_JEXEC') or die('Unauthorized Access');

require_once(dirname(__FILE__) . '/option.php');


class SocialPrivacy extends EasySocial
{
	private $target = null;
	private $type = null;

	private $data = null;
	public static $keys = array(
								'public'			=> SOCIAL_PRIVACY_PUBLIC,	// 0
								'member'			=> SOCIAL_PRIVACY_MEMBER,	// 10
								'friends_of_friend'	=> SOCIAL_PRIVACY_FRIENDS_OF_FRIEND,	// 20
								'friend'			=> SOCIAL_PRIVACY_FRIEND,	// 30
								'only_me'			=> SOCIAL_PRIVACY_ONLY_ME,	// 40
								'custom'			=> SOCIAL_PRIVACY_CUSTOM	// 100
							);

	public static $icons	= array(
								'public'			=> 'fa fa-globe',
								'member'			=> 'fa fa-user',	// 10
								'friends_of_friend'	=> 'fa fa-users',	// 20
								'friend'			=> 'fa fa-users',	// 30
								'only_me'			=> 'fa fa-lock',	// 40
								'custom'			=> 'fa fa-wrench'	// 100
							);

	public static $resetMap 	= array(
									'story.view',
									'photos.view',
									'albums.view',
									'core.view',
									'easyblog.blog.view'
								);




	public static $userPrivacy	= array();

	/**
	 * Class constructor
	 *F
	 * @since	3.0
	 * @access	public
	 */
	public function __construct($target = '', $type = SOCIAL_PRIVACY_TYPE_USER)
	{
		$this->target = $target;
		$this->type = $type;

		parent::__construct();
	}

	/**
	 * Loads the privacy object for a particular node item.
	 *
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique id of the item.
	 * @param	string	The unique type of the item.
	 * @param	string	The unique component element.
	 *
	 */
	public static function factory( $target = '', $type = SOCIAL_PRIVACY_TYPE_USER )
	{
		$obj 	= new self( $target , $type );
		return $obj;
	}

	/**
	 * Given a privacy value in string, convert it back to integer.
	 *
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The unique key identifier.
	 */
	public function toValue( $key )
	{
		$key 	= JString::strtolower( $key );

		$value 	= 0;

		if( array_key_exists( $key , self::$keys ) )
		{
			$value 	= self::$keys[ $key ];
		}

		return $value;
	}

	/**
	 * Given a privacy value in integer, convert it back to a string identifier.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	sting 	'user' or 'all'
	 * @return	array	The privacy rule string.
	 */
	public function getResetMap( $type = 'user' )
	{
		$map = self::$resetMap;

		if( $type == 'all' )
		{
			$model 		= FD::model( 'Privacy' );
			$commands 	= $model->getAllRulesCommand();

			if( $commands )
			{
				$map = $commands;
			}
		}

		return $map;
	}

	/**
	 * Given a privacy value in integer, convert it back to a string identifier.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The privacy value in integer. (E.g: 0,10,20,30,40,100)
	 * @return	string	The privacy string.
	 */
	public static function toKey( $value = '0' )
	{
		return self::getKey( $value );
	}

	/**
	 * Given a privacy value in integer, convert it back to a string identifier.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int 	The privacy value in integer. (E.g: 0,10,20,30,40,100)
	 * @return	string	The privacy string.
	 */
	public static function getKey( $value )
	{
		$rkey = 'public';

		if( self::$keys )
		{
			foreach( self::$keys as $key => $kval )
			{
				if( $kval == $value )
				{
					$rkey = $key;
					break;
				}

			}
		}

		return $rkey;
	}

	/**
	 * Retrieves the raw data of the privacy object.
	 *
	 * Example:
	 * <code>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getData()
	{
		if (!$this->data) {
			$this->data = $this->getPrivacyData();
		}

		return $this->data;
	}

	/**
	 * Retrieves an object's privacy.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The object unique id.
	 * @param	string	The object definition.
	 */
	public function getPrivacyData()
	{
		static $items = array();

		if (!$this->target || !$this->type) {
			return false;
		}

		$key = $this->target . $this->type;

		if (!isset($items[$key])) {
			$model = FD::model('Privacy');
			$items[$key] = $model->getData($this->target, $this->type);
		}

		return $items[$key];
	}


	/**
	 * add privacy on object
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string		type.rule eg. profiles.view
	 * @param	int			The user id
	 * @param	int			The object unique id.
	 * @param	string		The object type.
	 * @param 	int / string privacy value.
	 * @return  boolean
	 */
	public function add( $rule, $uid, $utype, $pvalue, $userId = null, $custom = '' )
	{
		// lets get the privacy id based on the $rule.
		$rules = explode( '.', $rule );

		$element = array_shift( $rules );
		$rule = implode( '.', $rules );

		$model = ES::model('Privacy');
		$privacyId = $model->getPrivacyId($element, $rule, true);

		if (is_numeric($pvalue)) {
			$pvalue = $this->toKey($pvalue);
		}

		if (is_null($userId) || !$userId) {
			$userId = $this->target;
		}

		// if still empty, then we will just use the current logged in user id.
		if (is_null($userId) || !$userId) {
			$userId = $this->my->id;
		}

		$state = $model->update($userId, $privacyId, $uid, $utype, $pvalue, $custom);
		return $state;
	}

	/**
	 * Retrieves an object's privacy.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The object id.
	 * @param	string	The object type
	 * @param	int		The object's creator
	 * @return  SocialPrivacyOptionItem		default option
	 */
	public function getOption( $uid, $utype = '', $ownerId = '', $command = null )
	{
		if( empty( $utype ) && empty( $ownerId ) )
		{
			//when this two param is empty, this mean we want to get user's privacy.
			$utype 		= SOCIAL_TYPE_USER;
			$ownerId 	= $uid;
		}

		if( $utype == SOCIAL_TYPE_USER)
		{
			$option = new SocialPrivacyOption();
			$option->type 		= $utype;
			$option->uid 		= $uid;
			$option->user_id 	= empty( $ownerId ) ? $uid : $ownerId;
			return $option;
		}

		// getting object's privacy
		$model		= FD::model( 'Privacy' );
		$pItem		= $model->getPrivacyItem( $uid, $utype, $ownerId, $command );
		// var_dump($pItem);

		$option 			= new SocialPrivacyOption();
		$option->id 	 	= $pItem->id;
		$option->default    = $pItem->default;
		$option->option     = $pItem->option;

		$option->uid     	= $pItem->uid;
		$option->type     	= $pItem->type;
		$option->user_id    = $pItem->user_id;
		$option->value      = $pItem->value;
		$option->custom 	= $pItem->custom;

		$option->pid 		= $pItem->pid;

		$option->editable 	= $pItem->editable;

		$option->override 	= false;

		return $option;
	}


	/**
	 * return html code for privacy selection.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The object unique id.
	 * @param	string	The object type.
	 * @editOverride 	array which accept only two key. 'override' and 'value'.
	 * 						'override' (boolean) to indicate if overiding is needed or not.
	 * 						'value' (boolean) to indicate the new value of editable property
	 * @return 	string 	html code
	 */
	public function form($uid, $utype, $ownerId, $command = null, $isHtml = false, $streamId = null, $editOverride = array())
	{
		// Get the current logged in user.
		$my = ES::user();

		// Get a list of privacy options
		$pItem = $this->getOption($uid, $utype, $ownerId, $command);

		//preload users
		if (count($pItem) > 0) {

			$arrUser = array();

			foreach ($pItem->custom as $item) {
				$arrUser[] = $item->user_id;
			}

			if (count($arrUser) > 0) {
				ES::user($arrUser);
			}
		}

		// TODO: should we check if the current user has the edit privacy override ability?
		if ($my->id && $editOverride && isset($editOverride['override']) && isset($editOverride['value'])) {
			if ($editOverride['override']) {
				$pItem->editable = $editOverride['value'];

				$pItem->override = true;
			}
		}

		// Set the tooltip text
		$tooltipText = FD::_('COM_EASYSOCIAL_PRIVACY_TOOLTIPS_SHARED_WITH_' . strtoupper($this->toKey($pItem->value)), true);

		// Get the icon for the rule
		$icon = $this->getIconClass($this->toKey($pItem->value));

		$theme = ES::themes();
		$theme->set('item', $pItem);
		$theme->set('utype' , $pItem->type );
		$theme->set('uid' , $pItem->uid );
		$theme->set('isHtml' , $isHtml);
		$theme->set('tooltipText', $tooltipText);
		$theme->set('streamid', $streamId);
		$theme->set('icon', $icon);

		$namespace = 'site/privacy/default.privacy.options';

		$output = $theme->output($namespace);

		return $output;
	}

	public static function getIconClass( $key = '' )
	{
		$key = ( empty( $key) ) ? 'public' : $key;
		return self::$icons[$key];
	}

	public function getValue( $key, $rule )
	{
		$data = $this->getData();

		// default to core.view
		// Test if the rule even exist first.
		if(! isset( $data[ $key ][ $rule ] ) )
		{
			$key 	= 'core';
			$rule 	= 'view';
		}

		$check = $data[ $key ][ $rule ];

		if ( empty($check) ) {
			// no privacy at all ?!
			// just return 0
			return 0;
		}

		$options 	= (array) $data[ $key ][ $rule ]->options;

		// We only want to get the items that are checked.
		// Since the options value only contains 0 or 1.
		$value 		= '';

		$firstOption = '';
		if( in_array('1', $options) )
		{
			$options 	 = array_flip( $options );
			$firstOption = $options[ 1 ];
		}
		else
		{
			$firstOption = array_shift( $options );
		}

		$selected 	= $this->toValue( $firstOption );

		$customData = $data[ $key ][ $rule ]->custom;

		if( $customData )
		{
			$value = array( $selected, $customData);
		}
		else
		{
			$value = $selected;
		}

		return $value;
	}

	/**
	 * Validates a certain action againts list of objects
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The key / group to test. E.g profile, story
	 * @param	string	The rule to test. E.g search, view
	 * @param	string	The privacy type, user or app.
	 * @param	array	SocialPrivacyOption
	 * @return 	array 	SocialPrivacyOption
	 *			boolean	false when failed.
	 */

	// public function validate( $key, $rule, $element )

	public function validate( $keys, $uid, $utype = '', $ucreatorid = '' )
	{
		$rules 	= explode( '.', $keys );
		$key  	= array_shift( $rules );
		$rule 	= implode( '.', $rules );

		// if current user is a site admin, always allow.
		$targetUser = FD::user( $this->target );
		if( $targetUser->isSiteAdmin() )
		{
			return true;
		}

		// if owner, always allow.
		if ($targetUser->id && $targetUser->id == $ucreatorid) {
			return true;
		}


		$element = $this->getOption( $uid , $utype , $ucreatorid, $keys );

		if( empty( $element ) )
		{
			return false;
		}

		if( $element->type == SOCIAL_TYPE_USER )
		{
			// this mean we check again user's privacy setting.
			$targetPrivacy = FD::privacy( $element->user_id );
			$targetValue   = $targetPrivacy->getValue( $key, $rule );

			$data          = array();

			if( is_array( $targetValue ) )
			{
				$data[0] = $targetValue[0];
				$data[1] = $targetValue[1];
			}
			else
			{
				$data[0] = $targetValue;
				$data[1] = null;
			}

			return $this->check( $this->target, $element->user_id, $data[0], $data[1] );
		}
		else
		{
			//this mean we cheak again app's object privacy
			return $this->check( $this->target, $element->user_id, $element->value, $element->custom );
		}
	}

	/**
	 * perform the actual checking
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		user_id to check
	 * @param	int		target_id to check against
	 * @param	int 	privacy value
	 * @return 	boolean	true / false
	 */

	private function check( $my_id, $target_id, $target_privacy, $target_privacy_custom = null )
	{
		$isValid 	= false;

		if ($my_id && $my_id == $target_id) {
			return true;
		}

		switch( $target_privacy ) {
			// Public privacy simply means that everything is valid :)
			case SOCIAL_PRIVACY_PUBLIC:
				$isValid	= true;

				break;

			// Member privacy simply means that the viewer needs to be a logged in user.
			case SOCIAL_PRIVACY_MEMBER:
				$isValid 	= $my_id > 0;

				break;

			// Friends of friend basically means that the user needs to be at least a 2nd level friends.
			case SOCIAL_PRIVACY_FRIENDS_OF_FRIEND:

				if ($my_id == $target_id) {
					$isValid = true;
					break;
				}

				$friendsModel 		= FD::model( 'Friends' );
				$isValid 			= $friendsModel->isFriendsOfFriends( $target_id , $my_id );

				break;

			// The viewer needs to be a friend with the target.
			case SOCIAL_PRIVACY_FRIEND:

				if ($my_id == $target_id) {
					$isValid = true;
					break;
				}

				$friendsModel 		= FD::model( 'Friends' );
				$isValid 			= $friendsModel->isFriends( $target_id , $my_id );
				break;

			// Only viewable by the target
			case SOCIAL_PRIVACY_ONLY_ME:

				$isValid	= $my_id == $target_id;

				break;

			// Custom privacy values here.
			case SOCIAL_PRIVACY_CUSTOM:

				if ($my_id == $target_id) {
					$isValid = true;
					break;
				}

				$customData = $target_privacy_custom;

				if (empty( $customData )) {
					$isValid = false;
				}

				foreach ( $customData as $item ) {
					if ($item->user_id == $my_id) {
						$isValid = true;
						break;
					}
				}

				break;

			default:
				break;
		}

		return $isValid;

	}

}
