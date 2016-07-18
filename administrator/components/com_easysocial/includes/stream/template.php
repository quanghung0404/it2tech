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

class SocialStreamTemplate extends JObject
{
	/**
	 * The actor's id. (Example 42 )
	 * @var int
	 * mantadory field
	 */
	public $actor_id		= null;

	/**
	 * The unique actor type (Example: user, group , photos)
	 * @var int
	 * optional - default to user
	 */
	public $actor_type		= '';

	/**
	 * the content type id of this item. ( Example, album, status, photos, group and etc)
	 * @var int
	 * mantadory field
	 */
	public $context_id	= null;

	/**
	 * the content type of this item. ( Example, album, status, photos, group and etc)
	 * @var string
	 * mantadory field
	 */
	public $context_type	= null;

	/**
	 * the stream type. ( full or mini )
	 * @var string
	 * opstional - default to full
	 */
	public $stream_type	= null;

	/**
	 * the action for the context ( example, add, update and etc )
	 * @var string
	 * mantadory field
	 */
	public $verb 			= null;

	/**
	 * the id of which the context object associate with. ( E.g album id, when the context is a photo type. Add photo in album xxx )
	 * @var int
	 * optional - default to 0
	 */
	public $target_id 		= 0;

	/**
	 * Stream title which is optional.
	 * @var string
	 * optional
	 */
	public $title 			= null;

	/**
	 * Stream content which is option. (Example: $1 something)
	 * @var string
	 * optional
	 */
	public $content			= null;

	/**
	 * @var int
	 * system uses
	 */
	public $uid 			= 0;

	/**
	 * creation date
	 * @var mysql date
     * system use
	 */
	public $created 		= null;

	/*
	 * to determine if the stream is a sitewide
	 * @var boolean
	 * system use
	 */

	public $sitewide		= null;


	/**
	 * If this stream is posted with a location, store the location id.
	 * @var int
	 */
	public $location_id = null;

	/**
	 * If this stream is posted with their friends store in json string.
	 * @var string
	 */
	public $with 		= null;


	/**
	 * to indicate this stream item should aggregate or not.
	 */
	public $isAggregate  = null;

	/**
	 * to indicate this stream item should aggregate against the target_id or not.
	 */
	public $aggregateWithTarget  = null;


	/**
	 * to indicate this stream should be a public stream or not.
	 */
	public $isPublic 	 = null;


	/**
	 * the privacy rule that this stream should be associated with.
	 */
	public $privacy_id = null;

	/**
	 * the privacy access for this stream
	 */
	public $access = null;


	/**
	 * the custom privcy access for this stream. applicable only if the access == 100
	 */
	public $custom_access = null;


	/**
	 * if this stream is posted with params
	 */
	public $params 	 = null;


	/**
	 * if context item is posted with params
	 */
	public $item_params 	 = null;

	/**
	 * this childs is to aggreate items of same type ONLY in within one stream.
	 * the requirement is to off isAggregate flag. else it will ignore this property.
	 */
	public $childs = null;

	public $_public_rule	= null;

	/**
	 * Stores the mentions for this stream item.
	 */
	public $mentions		= null;

	/**
	 * Stores the cluster for the stream item
	 */
	public $cluster_id		= null;
	public $cluster_type	= null;
	public $cluster_access 	= null;

	/**
	 * Stores the state of the stream
	 */
	public $state = SOCIAL_STREAM_STATE_PUBLISHED;

	/**
	 * Class Constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct()
	{
		// Set the creation date to the current time.
		$date = FD::date();
		$this->created = $date->toMySQL();
		$this->sitewide = '0';
		$this->isAggregate = false;
		$this->aggregateWithTarget	= false;
		$this->isPublic = 0;
		$this->childs = array();
		$this->cluster_access = 0;

		//reset the _public_rule holder;
		$this->_public_rule = null;
	}

	/**
	 *
	 * @since	1.2
	 * @access	public
	 * @param   cluster id
	 * @param   cluster type
	 * @param   cluster access : 0 means this stream is for cluster only. 1 mean this stream will appear in cluster page and user page.
	 * @return  null
	 */
	public function setCluster( $c_id, $c_type, $c_access = 1 )
	{
		$this->cluster_id 		= $c_id;
		$this->cluster_type 	= $c_type;
		$this->cluster_access 	= $c_access;
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param   title string ( optional )
	 * @return  null
	 */
	public function setTitle( $title )
	{
		$this->title 	= $title;
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param   content string ( optional )
	 * @return  null
	 */
	public function setContent( $content )
	{
		$this->content 	= $content;
	}

	/**
	 * Sets the actor object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The actor's id.
	 * @param	string	The actor's type.
	 */
	public function setActor( $id , $type )
	{
		// Set actors id
		$this->actor_id 	= $id;

		// Set actors type
		$this->actor_type	= $type;
	}

	/**
	 * Sets the mood in the stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	int		The actor's id.
	 * @param	string	The actor's type.
	 */
	public function setMood(SocialTableMood &$mood)
	{
		if (!$mood->id) {
			return;
		}

		$this->mood_id	= $mood->id;
	}

	/**
	 * Sets the context of this stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The context id.
	 * @param	string	The context type.
	 */
	public function setContext($id, $type, $params = null)
	{
		// Set the context id.
		$this->context_id = $id;

		// Set the context type.
		$this->context_type = $type;

		if ($params) {

			if (is_string($params)) {
				$this->item_params = $params;
			}

			if ($params instanceof SocialRegistry) {
				$this->item_params = $params->toString();
			}

			// If the params is still empty, we just treat it as an object or string
			if (!$this->item_params && (is_array($params) || is_object($params))) {
				$this->item_params = FD::json()->encode($params);
			}
		}
	}

	/**
	 * Sets the verb of the stream item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb
	 */
	public function setVerb( $verb )
	{
		// Set the verb property.
		$this->verb = $verb;
	}

	/**
	 * Sets the target id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target id
	 */
	public function setTarget( $id )
	{
		$this->target_id 	= $id;
	}

	/**
	 * Sets the stream location
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setLocation( $location = null )
	{
		if( !is_null( $location ) && is_object( $location ) )
		{
			$this->location_id 	= $location->id;
		}
	}


	/**
	 * Sets the users in the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setWith( $ids = '' )
	{
		$this->with 	= $ids;
	}

	/**
	 * Sets mentions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setMentions( $mentions )
	{
		$this->mentions 	= $mentions;
	}

	/**
	 * Sets the state of the stream
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function setState($state)
	{
		$this->state = $state;
	}

	/**
	 * Sets the stream type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The target id
	 */
	public function setType( $type = 'full' )
	{
		$this->stream_type 	= $type;
	}

	public function setSiteWide( $isSideWide = true )
	{
		$this->sitewide = $isSideWide;
	}

	public function setAggregate( $aggregate = true, $aggregateWithTarget = false )
	{
		// when this is true, it will aggregate based on current context and verb.
		$this->isAggregate = $aggregate;

		// when this is true, it will aggregate based on the target_id as well.
		$this->aggregateWithTarget = $aggregateWithTarget;
	}

	public function setDate( $mySQLdate )
	{
		$this->created = $mySQLdate;
	}

	/*
	 * deprecated since 1.2.16
	 */
	public function setPublicStream( $keys, $privacy = null )
	{
		$holder = array( 'key' 		=> $keys,
						 'value' 	=> $privacy );

		$this->_public_rule = $holder;
	}

	/**
	 * Set stream access.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string		privacy rule. e.g. core.view
	 *          int			privacy value in integer
	 *          array 		user ids
	 */
	public function setAccess($keys, $privacy = null, $custom = null)
	{
		$holder = array( 'key' 		=> $keys,
						 'value' 	=> $privacy,
						 'custom' => $custom );

		$this->_public_rule = $holder;
	}

	public function bindStreamAccess()
	{
		if (! $this->actor_id) { // this is a guest.
			// let get the privacy id for the keys that passed in.
			$keys = $this->_public_rule['key'];

			$rules 	= explode( '.', $keys );
			$key  	= array_shift( $rules );
			$rule 	= implode( '.', $rules );

			$currentRule = FD::table('Privacy');
			$currentRule->load(array('type'=>$key, 'rule' => $rule));

			if (! $currentRule->id) {
				// lets load the core.view privacy.
				$currentRule->load(array('type'=> 'core', 'rule' => 'view'));
			}

			$this->privacy_id = $currentRule->id;
			$this->access = 0; // always default to public
			$this->custom_access = '';

		} else {

			$privacyLib 	= Foundry::privacy( $this->actor_id );

			$privacyData = $privacyLib->getData();

			$core = $privacyData['core']['view'];

			if( $this->_public_rule )
			{
				$keys = $this->_public_rule['key'];
				$access = $this->_public_rule['value'];
				$custom = isset($this->_public_rule['custom']) ? $this->_public_rule['custom'] : '';

				if( $this->actor_type == SOCIAL_STREAM_ACTOR_TYPE_USER )
				{
					// we need to test the user privacy for this rule.
					$rules 	= explode( '.', $keys );
					$key  	= array_shift( $rules );
					$rule 	= implode( '.', $rules );

					// if current passed in rule not found, we will use the core.view instead.
					$currentRule = $core;
					if( isset($privacyData[$key]) && isset($privacyData[$key][$rule])) {
						$currentRule = $privacyData[$key][$rule];
					}

					$this->privacy_id = $currentRule->id;
					$this->access = (! is_null($access)) ? $access : $currentRule->default;
					$this->custom_access = '';

					if($this->access == SOCIAL_PRIVACY_CUSTOM) {
						$tmp = array();

						if ($custom) {
							$tmp = $custom;
						} else if($currentRule->custom) {
							foreach( $currentRule->custom as $cc) {
								$tmp[] = $cc->user_id;
							}
						}

						if ($tmp) {
							$this->custom_access = ',' . implode(',', $tmp) . ',';
						}
					}
				}

			} else {

				$this->privacy_id = $core->id;
				$this->access = !empty($access) ? $access : $core->default;
				$this->custom_access = '';

				if($this->access == SOCIAL_PRIVACY_CUSTOM) {
					$tmp = array();

					if ($custom) {
						$tmp = $custom;
					} else if($core->custom) {
						foreach( $core->custom as $cc) {
							$tmp[] = $cc->user_id;
						}
					}

					if ($tmp) {
						$this->custom_access = ',' . implode(',', $tmp) . ',';
					}
				}
			}
		}
	}

	/**
	 * Sets the stream params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	json string only!
	 * @return
	 */
	public function setParams( $params )
	{
		if( !$params )
		{
			return;
		}

		if( !is_string( $params ) )
		{
			if( $params instanceof SocialRegistry )
			{
				$this->params 	= $params->toString();
			}
			else
			{
				$this->params = FD::json()->encode( $params );
			}
		}
		else
		{
			$this->params = $params;
		}
	}

	/*
	 * This functin allow user to aggreate items of same type ONLY in within one stream.
	 * when there are child items, the isAggreate will be off by default when processing streams aggregation.
	 * E.g. of when this function take action:
	 *		Imagine if you wanna agreate photos activity logs for one single stream but DO NOT wish to aggregate with other photos stream.
	 *      If that is the case, then you will need to use this function so that stream lib will only aggreate the photos items in this single stream.
	 */
	public function setChild( $contextId )
	{
		if( $contextId )
		{
			$this->childs[] = $contextId;
		}
	}

}
