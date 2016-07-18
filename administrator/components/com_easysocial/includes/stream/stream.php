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

// Include necessary libraries here.
require_once(__DIR__ . '/dependencies.php');
require_once(__DIR__ . '/template.php');
require_once(SOCIAL_LIB . '/privacy/option.php');
FD::import('admin:/includes/group/group');

class SocialStream
{
	/**
	 * Contains a list of stream data.
	 * @var	Array
	 */
	public $data = null;

	/*
	 * this nextStartDate used as pagination.
	 */
	private $nextdate = null;

	/*
	 * this nextEndDate used as pagination.
	 */
	private $enddate = null;

	private $uids = null;

	/**
	 * Stores the current context
	 *
	 * @var string
	 */
	private $currentContext	= null;

	/*
	 * this nextlimit used as actvities log pagination.
	 */
	private $nextlimit = null;

	/**
	 * Determines if the current request is for a single item output.
	 * @var boolean
	 */
	private $singleItem 	= false;

	/**
	 * Determines the current filter type.
	 * @var string
	 */
	public $filter 			= null;

	/**
	 * Determines if the current retrieval is for guest viewing or not.
	 * @var string
	 */
	public $guest 			= null;


	/**
	 * Determines if the current retrieval is for cluster or not. (groups or event).
	 * @var string
	 */
	public $isCluster       = null;


	/**
	 * options
	 * @var string
	 */
	public $options 			= null;


	/**
	 * public stream pagination
	 *
	 */
	public $limit  			= 0;
	public $startlimit  	= 0;
	public $pagination 		= null;


	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct()
	{
		$this->filter = 'all';
		$this->guest = false;
		$this->options = array();

		$app = JFactory::getApplication();
		$this->input = $app->input;
	}

	/**
	 * Delete stream items given the app type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 */
	public function delete($contextId, $contextType, $actorId = '', $verb = '')
	{
		// Load dispatcher.
		$dispatcher = ES::dispatcher();
		$args = array($contextId, $contextType, $verb);

		// Trigger onBeforeStreamDelete
		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onBeforeStreamDelete', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onBeforeStreamDelete', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onBeforeStreamDelete', $args);

		$model 	= FD::model('Stream');

		$model->delete($contextId, $contextType, $actorId, $verb);

		// Trigger onAfterStreamDelete
		$dispatcher->trigger(SOCIAL_APPS_GROUP_USER, 'onAfterStreamDelete', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_GROUP, 'onAfterStreamDelete', $args);
		$dispatcher->trigger(SOCIAL_APPS_GROUP_EVENT, 'onAfterStreamDelete', $args);
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   null
	 * @return  SocialStream	The stream object.
	 */
	public static function factory()
	{
		return new self();
	}

	/**
	 * Creates the stream template
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTemplate()
	{
		$template 	= new SocialStreamTemplate();

		return $template;
	}

	/**
	 * check if activity already exists or not.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return  boolean
	 */
	public function exists( $uid, $context, $verb, $actorId, $options = array() )
	{
		$model 		= FD::model( 'Stream' );
		$exits = $model->exists($uid, $context, $verb, $actorId, $options);
		return $exits;
	}

	/**
	 * Creates a new stream item.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * // Load up the library.
	 * $stream 		= FD::get( 'Stream' );
	 *
	 * // We need to generate the stream template.
	 * $template 	= $stream->getTemplate();
	 *
	 * // Set actors.
	 * $template->setActor( $id , $type );
	 *
	 * // Set verb
	 * $template->setVerb( 'create' );
	 *
	 * // Create the stream item.
	 * $stream->add( $template );
	 *
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	mixed						Accepts array, object or SocialStreamTemplate which represents the stream's data.
	 * @return	SocialTableStreamItem		Returns the new stream id if success, false otherwise.
	 */
	public function add(SocialStreamTemplate $data)
	{
		// Let's try to aggregate the stream item.
		// Get the stream model
		$model = FD::model('Stream');

		// Get the config obj.
		$config = FD::config();

		// The duration between activities.
		$duration = $config->get('stream.aggregation.duration');


		// check if the actor is in ESAD profile or not.
		if ($data->actor_id) {
			$actor = FD::user($data->actor_id);
			if (! $actor->hasCommunityAccess()) {
				return false;
			}
		}


		// Determine which context types should be aggregated.
		$aggregateContext = $config->get('stream.aggregation.contexts');

		if (count($data->childs) > 0) {
			// reset this flag to false whenever there are items in child property.
			$data->isAggregate = false;
		}

		//now lets bind the isPublic privacy
		$data->bindStreamAccess();

		// @trigger: onPrepareComments
		$dispatcher = FD::dispatcher();
		$args = array(&$data);

		// Determines what group of apps should we trigger
		$eventGroup = $data->cluster_type ? $data->cluster_type : SOCIAL_APPS_GROUP_USER;
		$dispatcher->trigger($eventGroup, 'onBeforeStreamSave', $args);

		// Get the unique id if necessary.
		$uid = $model->updateStream($data);

		if (count($data->childs) > 0) {
			foreach ($data->childs as $contextId) {

				// Load the stream item table
				$item = FD::table('StreamItem');
				$item->bind($data);

				//override contextId
				$item->context_id = $contextId;

				// Set the uid for the item.
				$item->uid 	= $uid;

				// Let's try to store the stream item now.
				$state 	= $item->store();

				if (!$state) {
					return false;
				}
			}
		} else {
			// Load the stream item table
			$item 	= FD::table( 'StreamItem' );
			$item->bind($data);

			// Set the uid for the item.
			$item->uid = $uid;

			// set context item's params
			$item->params = $data->item_params;

			// Let's try to store the stream item now.
			$state = $item->store();

			if (!$state) {
				return false;
			}

		}

		// Determine if there's "with" in this stream and add it in.
		if ($data->with) {
			$model->setWith( $uid , $data->with );
		}

		// Determine if there's mentions in this stream and we need to create it.
		if ($data->mentions) {
			$model->addMentions($uid , $data->mentions);
		}

		$dispatcher->trigger( $eventGroup , 'onAfterStreamSave' , $args );

		return $item;
	}

	/**
	 * Update stream.modified date.
	 *
	 * the context can be 'stream' and when the context is stream, the uid is the stream.id
	 * the context can be 'activity' and when the context is activity, the uid is the stream_item.id
	 * we need to work accordingly based on the context passed in.
	 */

	public function updateModified( $streamId, $user_id = '', $user_action = '')
	{
		$model = FD::model( 'Stream' );
		$state = $model->updateModified( $streamId, $user_id, $user_action );

		return $state;
	}


	/**
	 * Update stream.last_action and last_userid.
	 *
	 */
	public function revertLastAction($streamId, $user_id = '', $user_action = '') {
		$model = FD::model( 'Stream' );
		$state = $model->revertLastAction( $streamId, $user_id, $user_action );

		return $state;
	}


	/**
	 * stream's with tagging.
	 * return array of foundry user object.
	 */
	private function getStreamTagWith( $streamId )
	{
		$model = FD::model( 'Stream' );
		return $model->getTagging( $streamId, 'with' );
	}

	/**
	 * stream's mentions tagging.
	 * return array of objects with:
	 *        $obj->user   : FD::user(),
	 *        $obj->offset : int,
	 *        $obj->length : int
	 */
	private function getTags( $streamId )
	{
		$model 		= FD::model( 'Stream' );

		$mentions 	= $model->getTagging( $streamId, 'tags' );

		return $mentions;
	}

	/**
	 * Returns a list of hash tags from a particular stream.
	 *
	 * stream's mentions tagging.
	 * return array of objects with:
	 *        $obj->title   : "Some hash tag",
	 *        $obj->offset : int,
	 *        $obj->length : int
	 */
	private function getHashtags( $streamId )
	{
		$model 		= FD::model( 'Stream' );

		$hashtags 	= $model->getTagging( $streamId, 'hashtags' );

		return $hashtags;
	}

	public function formatItem( SocialStreamItem &$stream )
	{
		$content 	= $stream->content;
		$tags		= $stream->tags;

		if( !$tags )
		{
			return;
		}

		// @TODO: We need to merge the mentions and hashtags since we are based on the offset.
		foreach ($tags as $tag) {

			if ($tag->type == 'user' ) {
				$replace 	= '<a href="' . $tag->user->getPermalink() . '" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="' . $tag->user->id . '" class="mentions-user">@' . $tag->user->getName() . '</a>';
			}

			if ($tag->type == 'hashtag') {
				$alias = JFilterOutput::stringURLSafe($tag->title);

				$replace = '<a href="' . FRoute::dashboard( array( 'layout' => 'hashtag' , 'tag' => $alias ) ) . '" class="mentions-hashtag">#' . $tag->title . '</a>';
			}

			$content	= JString::substr_replace( $content , $replace , $tag->offset , $tag->length );
		}

		$stream->content 	= $content;
	}



	/**
	 * Formats a stream item with the necessary data.
	 *
	 * Example:
	 * <code>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 *
	 */
	public function format( $items , $context = 'all', $viewer = null, $loadCoreAction = true, $defaultEvent = 'onPrepareStream' , $options = array() )
	{
		// Get the current user
		$my = is_null($viewer) ? FD::user() : FD::user($viewer);

		// Basic display options
		$commentLink 	= isset($options['commentLink']) && $options['commentLink'] || !isset($options['commentLink']) ? true : false;
		$commentForm	= (isset($options['commentForm']) && $options['commentForm']) || !isset($options['commentForm']) ? true : false;

		// Default the event to onPrepareStream
		if (!$defaultEvent) {
			$defaultEvent	= 'onPrepareStream';
		}

		// Determines if this is a stream
		$isStream	= false;

		if ($defaultEvent == 'onPrepareStream') {
			$isStream	= true;
		}


		// If there's no items, skip formatting this because it's pointless to run after this.
		if (!$items) {
			return $items;
		}

		// Prepare default data
		$data 		= array();
		$activeUser	= FD::user();

		// Get stream model
		$model 		= FD::model('Stream');

		//current user being view.
		$targetUser = JRequest::getInt('id', '');

		if (empty($targetUser)) {
			$targetUser = $my->id;
		}

		if ($targetUser && strpos($targetUser, ':')) {
			$tmp 		= explode( ':', $targetUser);
			$targetUser = $tmp[0];
		}

		// Get template configuration
		$templateConfig 	= FD::themes()->getConfig();

		// Get the site configuration
		$config  = FD::config();

		// Link options
		// We only have to do this once instead of putting inside the loop.
		$linkOptions = array('target'=>'_blank');

		if ($config->get('stream.content.nofollow')) {
			$linkOptions['rel'] = 'nofollow';
		}


		// Format items with appropriate objects.
		foreach ($items as &$row) {

			// Get the uid
			$uid = $row->id;

			// Get the last updated time
			$lastupdate = $row->modified;

			// Determines if this is a cluster
			$isCluster      = ($row->cluster_id) ? true : false;

			// Obtain related activities for aggregation.
			$relatedActivities		= null;

			if ($isStream) {
				$relatedActivities = $model->getRelatedActivities( $uid , $row->context_type, $viewer );
			} else {
				$relatedActivities = $model->getActivityItem( $uid );
			}

			$aggregatedData = $this->buildAggregatedData($relatedActivities);

			// Get the stream item.
			$streamItem  = new SocialStreamItem();

			if (isset($row->isNew)) {
				$streamItem->isNew = $row->isNew;
			}

			// Set the state
			$streamItem->state = $row->state;

			// Set the actors (Aggregated actors )
			$streamItem->actors = $aggregatedData->actors;

			// Set the edited value
			$streamItem->edited = isset($row->edited) ? $row->edited : false;

			// Set the content
			$streamItem->content = $row->content;
			$streamItem->content_raw = $row->content;

			// Set the title of the stream item.
			$streamItem->title 	= $row->title;

			// Set the targets (Aggregated targets )
			$streamItem->targets = $aggregatedData->targets;

			// Set the aggregated items here so 3rd party can manipulate the data.
			$streamItem->aggregatedItems 	= $relatedActivities;

			$streamItem->contextIds	= $aggregatedData->contextIds;

			// Set the context params. ( Aggregated params )
			$streamItem->contextParams	= $aggregatedData->params;

			// main stream params
			$streamItem->params = $row->params;

 			// Set the stream uid / activity id.
			$streamItem->uid = $uid;

			// Set stream lapsed time
			$streamItem->lapsed	= FD::date($row->created)->toLapsed();
			$streamItem->created = FD::date($row->created);

			// Set the actor with the user object.
			$streamItem->actor 	= FD::user($row->actor_id);

			// Set the context id.
			$streamItem->contextId	= $aggregatedData->contextIds[0];

			// Set the verb for this item.
			$streamItem->verb = $aggregatedData->verbs[0];

			// Set the context type.
			$streamItem->context  = $row->context_type;

			// stream display type
			$streamItem->display = $row->stream_type;

			// stream cluster_id
			$streamItem->cluster_id = $row->cluster_id;

			// stream cluster_type
			$streamItem->cluster_type = $row->cluster_type;

			// stream cluster_type
			$streamItem->cluster_access = $row->cluster_access;

			// stream privacy access
			$streamItem->access = $row->access;

			// stream privacy access
			$streamItem->custom_access = $row->custom_access;

			// Define an empty color
			$streamItem->color = '';

			// Define an empty favicon
			$streamItem->icon = '';

			// Always enable labels
			$streamItem->label = true;

			$streamItem->custom_label = '';

			$streamItem->opengraph = FD::opengraph();

			// Determines if this stream item has been bookmarked by the viewer
			$streamItem->bookmarked = false;
			if ($row->bookmarked) {
				$streamItem->bookmarked = true;
			}

			// Determines if this stream item has been bookmarked by the viewer
			$streamItem->sticky = false;
			if ($row->sticky) {
				$streamItem->sticky = true;
			}

			// @TODO: Since our stream has a unique favi on for each item. We need to get it here.
			// Each application is responsible to override this favicon, or stream wil use the context type.
			$streamItem->favicon	= $row->context_type;
			$streamItem->type 		= $row->context_type;

			$streamDateDisplay = $templateConfig->get('stream_datestyle');

			$streamItem->friendlyDate = $streamItem->lapsed;

			if ($streamDateDisplay == 'datetime') {
				$streamItem->friendlyDate	= $streamItem->created->toFormat($templateConfig->get('stream_dateformat_format', 'Y-m-d H:i'));
			}

			// getting the the with and mention tagging for the stream, only if the item is a stream.
			$streamItem->with = array();
			$streamItem->mention = array();

			if ($isStream) {
				$streamItem->with = $this->getStreamTagWith($uid);
				$streamItem->tags = $this->getTags($uid);
			}

			// Format the mood
			if (!empty($row->mood_id)) {

				$mood = FD::table('Mood');

				$mood->id = $row->md_id;
				$mood->namespace = $row->md_namespace;
				$mood->namespace_uid = $row->md_namespace_uid;
				$mood->icon = $row->md_icon;
				$mood->verb = $row->md_verb;
				$mood->subject = $row->md_subject;
				$mood->custom = $row->md_custom;
				$mood->text = $row->md_text;
				$mood->user_id = $row->md_user_id;
				$mood->created = $row->md_created;

				$streamItem->mood = $mood;
			}

			// Format the users that are tagged in this stream.
			if( !empty( $row->location_id ) )
			{
				$location 	= FD::table( 'Location' );

				//$location->load( $row->location_id );
				// lets  assign the values into location jtable.
				$location->id              = $row->loc_id;
				$location->uid             = $row->loc_uid;
				$location->type            = $row->loc_type;
				$location->user_id         = $row->loc_user_id;
				$location->created         = $row->loc_created;
				$location->short_address   = $row->loc_short_address;
				$location->address         = $row->loc_address;
				$location->latitude        = $row->loc_latitude;
				$location->longitude       = $row->loc_longitude;
				$location->params          = $row->loc_params;

				$streamItem->location		= $location;
			}

			// target user. this target user is different from the targets. this is the user who are being viewed currently.
			$streamItem->targetUser = $targetUser;

			// privacy
			$streamItem->privacy    = null;

			// Check if the content is not empty. We need to perform some formatings
			if (isset($streamItem->content) && !empty($streamItem->content)) {
				$content = $streamItem->content;

				// Format mentions
				$content = $this->formatMentions($streamItem);

				// Apply e-mail replacements
				$content = FD::string()->replaceEmails($content);

				// Apply bbcode
				$content = FD::string()->parseBBCode($content, array('escape' => false, 'links' => true, 'code' => true));

				// Some app might want the raw contents
				$streamItem->content_raw	= $streamItem->content;
				$streamItem->content		= $content;
			}

			// Stream meta
			$streamItem->meta = '';

			if (!empty($streamItem->with) || !empty($streamItem->location) || !empty($streamItem->mood)) {
				$theme = FD::themes();
				$theme->set('stream', $streamItem);
				$streamItem->meta = $theme->output('site/stream/meta');
			}

			// Determines if the stream item is deleteable
			$streamItem->deleteable	= false;

			// Determines if the stream item is editable
			$streamItem->editable = false;

			// Group stream should allow cluster admins to delete
			if ($streamItem->cluster_id) {

				$cluster 	= FD::cluster($streamItem->cluster_type, $streamItem->cluster_id);

				if ($cluster->isAdmin() || FD::user()->isSiteAdmin() || (FD::user()->getAccess()->allowed('stream.delete', false) && FD::user()->id == $streamItem->actor->id)) {
					$streamItem->deleteable	= true;
				}

			} else {
				if (FD::user()->getAccess()->allowed('stream.delete', false) && FD::user()->id == $streamItem->actor->id) {
					$streamItem->deleteable	= true;
				}
			}

			if (FD::user()->isSiteAdmin()) {
				$streamItem->deleteable	= true;
			}

			// determine if we should show the 'last action by' text or not.
			// @TODO: rules: only users who are friend of the last_action_user_id should see.
			$streamItem->lastaction = '';
			if ($row->last_userid && $row->last_action && FD::user()->id && FD::user()->id != $row->last_userid) {
				$streamItem->lastaction = JText::sprintf('COM_EASYSOCIAL_STREAM_LASTACTION_' . strtoupper($row->last_action), FD::themes()->html( 'html.user' , $row->last_userid ));
			}


			// Only polls app can determines if the stream item can edit or not the poll item.
			$streamItem->editablepoll	= false;

			// streams actions.
			$streamItem->comments 	= ( $defaultEvent == 'onPrepareStream' ) ? true : false;
			$streamItem->likes 		= ( $defaultEvent == 'onPrepareStream' ) ? true : false;
			$streamItem->repost 	= ( $defaultEvent == 'onPrepareStream' ) ? true : false;


			// @trigger onPrepareStream / onPrepareActivity
			$includePrivacy  		= ( $isCluster ) ? false : true;
			$result					= $this->$defaultEvent( $streamItem, $includePrivacy );

			// Allow app to stop loading / generating the stream and
			// if there is still no title, we need to skip this stream altogether.
			if ($result === false || !$streamItem->title) {
				continue;
			}

			// This mean the plugin did not set any privacy. lets use the stream / activity.
			if (is_null($streamItem->privacy) && !$isCluster) {
				$privacyObj = FD::privacy( $activeUser->id );

				$privacy 	= ( isset( $row->privacy ) ) ? $row->privacy : null;
				$pUid 		= $uid;
				$tmpStreamId = ( $defaultEvent == 'onPrepareActivityLog') ? $row->uid : $row->id;

				$sModel = FD::model('Stream');
				$aItem  = $sModel->getActivityItem( $tmpStreamId, 'uid' );

				if (count($streamItem->contextIds) == 1 && is_null($privacy)) {
					if ($aItem) {
						$pUid 	= $aItem[0]->id;
					}
				}

				if(! $privacyObj->validate('core.view', $pUid, SOCIAL_TYPE_ACTIVITY, $streamItem->actor->id)) {
					continue;
				}

				$tmpStreamId = $streamItem->aggregatedItems[0]->uid;

				if ( $defaultEvent == 'onPrepareActivityLog') {
					$tmpStreamId = ( count($aItem) > 1 ) ? '' : $tmpStreamId;
				}

				$streamItem->privacy = $privacyObj->form( $pUid, SOCIAL_TYPE_ACTIVITY, $streamItem->actor->id, null, false, $tmpStreamId );
			}

			$itemGroup = ( $streamItem->cluster_id ) ? $streamItem->cluster_type : SOCIAL_APPS_GROUP_USER;

			if ($streamItem->display != SOCIAL_STREAM_DISPLAY_MINI) {

				$canComment = true;
				// comments
				if (isset( $streamItem->comments ) && $streamItem->comments) {
					if (!$streamItem->comments instanceof SocialCommentBlock) {
						$streamItem->comments	= FD::comments( $streamItem->contextId , $streamItem->context , $streamItem->verb, $itemGroup , array( 'url' => FRoute::stream( array( 'layout' => 'item', 'id' => $streamItem->uid ) ) ), $streamItem->uid );
					}

					// for comments, we need to check against the actor privacy and see if the current viewer allow to
					// post comments on their stream items or not.
					if (!$isCluster) {
						$privacyObj = FD::privacy( $activeUser->id );

						if (! $privacyObj->validate( 'story.post.comment', $streamItem->actor->id, SOCIAL_TYPE_USER)) {
							$canComment = false;
						}
					}
				}

				// Set comment option the streamid
				if ($streamItem->comments) {
					$streamItem->comments->setOption( 'streamid', $streamItem->uid );
				}

				// If comments link is meant to be disabled, hide it
				if (!$commentLink) {
					$streamItem->commentLink	= false;
				}

				// If comments is meant to be disabled, hide it.
				if ( $streamItem->comments && ( ( isset($streamItem->commentForm) && !$streamItem->commentForm) || !$commentForm || !$canComment )  ) {
					$streamItem->comments->setOption('hideForm', true);
				}


				//likes
				if (isset($streamItem->likes) && $streamItem->likes) {
					if (!$streamItem->likes instanceof SocialLikes) {
						$likes 			= FD::likes();
						$likes->get($streamItem->contextId , $streamItem->context, $streamItem->verb, $itemGroup, $streamItem->uid );

						$streamItem->likes = $likes;
					}
				}

				//set likes option the streamid
				if ($streamItem->likes) {
					$streamItem->likes->setOption( 'streamid', $streamItem->uid );
				}

				// Build repost links
				if (isset($streamItem->repost) && $streamItem->repost) {

					if(!$streamItem->repost instanceof SocialRepost) {
						$repost = FD::get('Repost', $streamItem->uid , SOCIAL_TYPE_STREAM, $itemGroup );
						$streamItem->repost = $repost;
					}
				}

				// set cluseter into repost
				if ($isCluster && $streamItem->repost) {
					$streamItem->repost->setCluster( $streamItem->cluster_id, $streamItem->cluster_type );
				}


				// Enable sharing on the stream
				if ($config->get('stream.sharing.enabled')) {

					if ( !isset($streamItem->sharing) || (isset($streamItem->sharing) && $streamItem->sharing !== false && !$streamItem->sharing instanceof SocialSharing)) {
						$sharing = FD::get( 'Sharing', array( 'url' => FRoute::stream(array('layout' => 'item', 'id' => $streamItem->uid, 'external' => true), true), 'display' => 'dialog', 'text' => JText::_( 'COM_EASYSOCIAL_STREAM_SOCIAL' ) , 'css' => 'fd-small' ) );
						$streamItem->sharing = $sharing;
					}
				}

				// Now we have all the appropriate data, populate the actions
				$streamItem->actions 	= $this->getActions($streamItem);
			} else {

				$streamItem->comments 	= false;
				$streamItem->likes 		= false;
				$streamItem->repost 	= false;

				$streamItem->actions 	= '';
			}

			// Re-assign stream item to the result list.
			$data[]	= $streamItem;
		}

		// here we know, the result from queries contain some records but it might return empty data due to privacy.
		// if that is the case, then we return TRUE so that the library will go retrieve the next set of data.
		if (count($data) <= 0) {
			return true;
		}

		return $data;
	}

	/**
	 * get hashtag title
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHashTag( $id )
	{
		$tb = FD::table('StreamTags');

		$tb->loadByTitle($id);
		return $tb;
	}

	/**
	 * Set stream access.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string		privacy rule. e.g. core.view
	 *          int			privacy value in integer
	 *          array 		user ids
	 */
	public function updateAccess($streamId, $privacy, $custom = null)
	{
		$model = FD::model('Stream');

		$customPrivacy = '';
		if ($privacy == SOCIAL_PRIVACY_CUSTOM) {
			if ($custom) {
				if(! is_array($custom) ) {
					$customPrivacy = $custom;
				} else {
					$customPrivacy = implode( ',' , $custom);
				}

				$customPrivacy = ',' . $customPrivacy . ',';
			}
		} else {
			$customPrivacy = '';
		}

		$state = $model->updateAccess( $streamId, $privacy, $customPrivacy );
		return $state;
	}


	/**
	 * Processes mentions in a stream object
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function formatMentions( SocialStreamItem &$stream )
	{
		// Get the current view
		$view = JRequest::getCmd( 'view', '' );

		// Get the stream's content
		$content = $stream->content;

		// Get tags for the stream
		$tags = isset($stream->tags) ? $stream->tags : array();

		// If there is no tags, just skip this and escape the content
		if (!$tags) {
			return FD::string()->escape($content);
		}

		// We need to store the changes in an array and replace it accordingly based on the counter.
		$items 	= array();

		// We need to merge the mentions and hashtags since we are based on the offset.
		$i 		= 0;

		foreach( $tags as $tag ) {

			if ($tag->type == 'user') {
				$replace 	= '<a href="' . $tag->user->getPermalink() . '" data-popbox="module://easysocial/profile/popbox" data-popbox-position="top-left" data-user-id="' . $tag->user->id . '" class="mentions-user">' . $tag->user->getName() . '</a>';
			}

			if ($tag->type == 'hashtag') {

				// $alias = JFilterOutput::stringURLSafe($tag->title);
				$alias = $tag->title;
				$url = '';

				if ($view == 'groups' || $view == 'events') {

					$clusterReg 	= FD::registry($stream->params);
					$object 		= $clusterReg->get($stream->cluster_type);

					switch( $stream->cluster_type )
					{
						case SOCIAL_TYPE_GROUP:
							// for now we assume all is group type.
							$group = new SocialGroup();
							$group->bind($object);

							$url = FRoute::groups(array('layout' => 'item' , 'id' => $group->getAlias(), 'tag' => $alias));
							break;

						case SOCIAL_TYPE_EVENT:
							$event = new SocialEvent();
							$event->bind($object);

							$url = FRoute::events(array('layout' => 'item', 'id' => $event->getAlias(), 'tag' => $alias));
							break;

						default:
							FRoute::dashboard(array('layout' => 'hashtag' , 'tag' => $alias));
							break;
					}

				} else {
					$url = FRoute::dashboard(array('layout' => 'hashtag' , 'tag' => $alias));
				}

				$replace 	= '<a href="' . $url . '" class="mentions-hashtag">#' . $tag->title . '</a>';
			}

			$links[$i]	= $replace;

			$replace 	= '[si:mentions]' . $i . '[/si:mentions]';
			$content	= JString::substr_replace( $content , $replace , $tag->offset , $tag->length );

			$i++;
		}

		// Once we have the content, escape it
		$content 	= FD::string()->escape($content);

		if ($links) {
			for ($x =0; $x < count($links); $x++) {
				$content 	= str_ireplace('[si:mentions]' . $x . '[/si:mentions]', $links[$x], $content);
			}
		}


		return $content;
	}

	public function getActivityNextLimit()
	{
		return $this->nextlimit;
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLogs( $max = 10 )
	{
		$model 	= FD::model( 'Activities' );
		$result	= $model->getData( $max );

		$data 	= $this->format( $result );

		return $data;
	}

	/**
	 * Retrieves a list of stream item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	 //public function getActivityLogs( $uId, $uType = SOCIAL_TYPE_USER, $context = SOCIAL_STREAM_CONTEXT_TYPE_ALL , $filter = 'all')
	public function getActivityLogs( $options = array() )
	{

		$uId 		= isset( $options['uId'] ) 		? $options['uId'] : '';
		$uType 		= isset( $options['uType'] ) 	? $options['uType'] : SOCIAL_TYPE_USER;
		$context 	= isset( $options['context'] ) 	? $options['context'] : SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		$filter 	= isset( $options['filter'] ) 	? $options['filter'] : 'all';
		$max 		= isset( $options['max'] ) 		? $options['max'] : '';
		$limitstart = isset( $options['limitstart'] ) 		? $options['limitstart'] : 0;


	 	if( empty( $uId ) )
	 	{
	 		$uId = FD::user()->id;
	 	}

		if( empty( $context ) )
		{
			$context = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		}


		$activity		 = FD::model( 'Activities' );

		if( ! $limitstart )
		{
			$activity->setState( 'limitstart', 0 );
		}

		$result 		 = 	$activity->getItems(
									array( 'uId' => $uId,
										   'uType' => $uType,
										   'context' => $context,
										   'filter' => $filter,
										   'max' 	=> $max,
										   'limitstart' => $limitstart
										)
									);

		$this->nextlimit = $activity->getNextLimit( $limitstart );

		// If there's nothing, just return a boolean value.
		if( !$result )
		{
			return false;
		}

		// register the resultset.
		$streamModel		 = FD::model( 'Stream' );
		$streamModel->setBatchActivityItems( $result );


		$data	= $this->format( $result , $context, null, false, 'onPrepareActivityLog' );

		if( is_bool( $data ) )
			return array();

		for( $i = 0; $i < count($data); $i++ )
		{
			$item =& $data[$i];

			$tbl = FD::table( 'StreamHide' );
			$tbl->load( $item->uid, $uId, SOCIAL_STREAM_HIDE_TYPE_ACTIVITY );


			$isHidden = false;
			if( $tbl->id )
				$isHidden = true;

			$item->isHidden = $isHidden;
		}


		$this->data = $data;
		return $this->data;
	 }

	/**
	 * Retrieves a single stream item.
	 *
	 * @param	int		The stream item's id.
	 * @since	1.0
	 */
	public function getItem($streamId, $clusterId = '', $clusterType = '', $loadModerated = false)
	{
		// Get the current viewer
		$viewer = FD::user()->id;

		// Load up the stream model
		$model = FD::model('Stream');

		// Default options
		$options = array(
							'streamId' => $streamId,
							'context' => 'all',
							'ignoreUser' => true,
							'viewer' => $viewer
						);

		// If configured to retrieve moderated items, we should explicitly let the model know about this
		if ($loadModerated) {
			$options['moderated'] = true;
		}

		// Retrieve data based on the type
		if ($clusterId && $clusterType) {

			$options['clusterId'] = $clusterId;
			$options['clusterType'] = $clusterType;

			$result = $model->getClusterStreamData($options);
		} else {
			$result = $model->getStreamData($options);
		}

		if (!$result) {
			return false;
		}

		$result[0]->isNew = true;

		$this->data = $this->format($result);
		$this->singleItem = true;

		return $this->data;
	}


	/**
	 * Retrieves a single stream item actor.
	 * return: SocialUser object, all false if not found.
	 * @since	1.0
	 */
	public function getStreamActor( $streamId )
	{
		$model 	= FD::model( 'Stream' );
		$actor 	= $model->getStreamActor( $streamId );
		return $actor;
	}

	public function getPublicStream( $limit = 10, $startlimit = 0, $hashtag = null )
	{
		$this->guest = true;

		$viewerId 	= FD::user()->id;
		$context	= SOCIAL_STREAM_CONTEXT_TYPE_ALL;

		$attempts = 2;
		$keepSearching = true;

		$model		= FD::model( 'Stream' );

		$this->startlimit = $startlimit;

		do
		{
			$options	= array(
							'userid' 		=> '0',
							'context' 		=> $context,
							'direction' 	=> 'older',
							'limit' 		=> $limit,
							'startlimit' 	=> $startlimit,
							'guest' 		=> true,
							'ignoreUser'	=> true,
							'viewer' 		=> $viewerId
						);

			if ($hashtag) {
				$options['tag'] = $hashtag;
			}

			$result		= $model->getStreamData( $options );

			// If there's nothing, just return a boolean value.
			if( !$result )
			{
				$this->startlimit = 0; // so that the next cycle will stop
				return $this;
			}

			$requireSearch =  $this->format( $result , $context, $viewerId );

			if( $requireSearch !== true )
			{
				$this->data = $requireSearch;
				$keepSearching = false;
			}

			$attempts--;

			$startlimit 	  = $startlimit + $limit;
			$this->startlimit = $startlimit ;

		} while( $keepSearching === true && $attempts > 0 );

	}

	public function getPagination()
	{
		$htmlContent = '';

		if ($this->pagination) {
			$page = $this->pagination;

			$previousLink = '';
			$nextLink = '';

			//build the extra params into the url
			$params = $this->buildPaginationParams();


			if (! is_null($page['previous'])) {
				$previousLink = JRoute::_($params . '&limitstart=' . $page['previous']);
			}

			if (! is_null($page['next'])) {
				$nextLink = JRoute::_($params . '&limitstart=' . $page['next']);
			}

			$theme 		= Foundry::get( 'Themes' );

			$theme->set( 'next' , $nextLink );
			$theme->set( 'previous' , $previousLink );

			$htmlContent 	= $theme->output( 'site/stream/pagination' );
		}

		return $htmlContent;
	}


	public function buildPaginationParams()
	{
		$params = '';

		$view = JRequest::getVar( 'view', 'dashboard' );

		if ($view) {
			$params .= '&view=' . $view;
		}

		if (JRequest::getVar( 'layout' )) {
			$params .= '&layout=' . JRequest::getVar( 'layout' );
		} else {
			if ($view == 'groups') {
				$params .= '&layout=item';
			}
			if ($view == 'events') {
				$params .= '&layout=item';
			}
			if ($view=='profile') {
				$params .= '&layout=timeline';
			}
		}

		$type = '';

		// Define those query strings here
		if (JRequest::getVar('type', '') != '') {
			$type = JRequest::getVar('type');

			if ($type == 'custom') {
				$type = 'filter';
			}
		}

		if (FD::config()->get('stream.pagination.style') == 'loadmore') {
			$params .= '&type=' . $type;

			if (JRequest::getVar('filterid', '')) {
				$params .= '&filterid=' . JRequest::getVar('filterid');
			}

			if (JRequest::getVar('id', '')) {

				if ($view=='profile' || $view=='profiles' || $view=='groups' || $view=='events') {
					$params .= '&id=' . JRequest::getVar('id');
				} else if ($type == 'group') {
					$params .= '&groupId=' . JRequest::getVar('id');
				} else if ($type == 'event') {
					$params .= '&eventId=' . JRequest::getVar('id');
				} else {
					$params .= '&filterid=' . JRequest::getVar('id');
				}
			}


		} else {

			// normal pagination.
			if($type == 'hashtag') {
				$params .= '&filterId=' . JRequest::getVar('id');
			} else if ( $type == 'apps' ) {
				$params .= '&app=' . JRequest::getVar('id');
			} else if ($type == 'event') {
				$params .= '&type=' . $type;
			} else if ( $type == 'group') {
				$params .= '&type=' . $type;
			} else {
				$params .= '&type=' . $type;
			}


			// if($type == 'event' && JRequest::getVar('id', '')) {
			// 	$params .= '&eventId=' . JRequest::getVar('id');
			// }

			// if($type == 'group' && JRequest::getVar('id', '')) {
			// 	$params .= '&groupId=' . JRequest::getVar('id');
			// }

			if ($view == 'dashboard') {
				if(JRequest::getVar('eventId', '')) {
					$params .= '&eventId=' . JRequest::getVar('eventId');
				}

				if(JRequest::getVar('groupId', '')) {
					$params .= '&groupId=' . JRequest::getVar('groupId');
				}
			}

			if (JRequest::getVar('id', '')) {
				if ($view=='profile' || $view=='profiles' || $view=='groups' || $view=='events') {
					if ($view == 'events' && JRequest::getVar('eventId', '')) {
						$params .= '&id=' . JRequest::getVar('eventId');

					} else {
						$params .= '&id=' . JRequest::getVar('id');
					}

				} else if ($type == 'group') {

					$params .= '&groupId=' . JRequest::getVar('id');

				} else if ($type == 'event') {

					$params .= '&eventId=' . JRequest::getVar('id');


				} else if($type == 'list') {
					$params .= '&listId=' . JRequest::getVar('id');
				} else {

					$params .= '&filterid=' . JRequest::getVar('id');
				}

			} else if(JRequest::getVar('filterid', '') && $type != 'filter') {
				$params .= '&id=' . JRequest::getVar('filterid');
			}

		}

		if (JRequest::getVar('filter', '')) {
				$params .= '&filter=' . JRequest::getVar('filter');
		}

		if (JRequest::getVar('filterId', '')) {
			$params .= '&filterId=' . JRequest::getVar('filterId');
		}


		if (JRequest::getVar('tag', '')) {
			$params .= '&tag=' . JRequest::getVar('tag');
		}

		if (JRequest::getVar('app', '')) {
			$params .= '&app=' . JRequest::getVar('app');
		}

		if (! JRequest::getInt('Itemid')) {
			$Itemid = FRoute::getItemId( $view );
			$params .= '&Itemid=' . $Itemid;
		}


		return $params;
	}

	public function getStickies($options = array())
	{
		$results     = null;

		if (! ES::config()->get('stream.pin.enabled')) {
			return array();
		}

		// lets process default values
		$type = 'sticky';
		$userId = isset( $options['userId'] ) 	? $options['userId'] : null;
		$viewerId = isset( $options['viewerId'] )   ? $options['viewerId'] : null;

		// If viewer is null, we assume the caller wants to fetch from the current user's perspective.
		if (is_null($viewerId)) {
			$viewerId 	= FD::user()->id;
		}

		// Ensure that the user id's are in an array form.
		$user = FD::user();
		$userId = ( empty( $userId ) ) ? $user->id : $userId;
		$userId	= FD::makeArray($userId);


		// Cluster stream items
		$clusterId = isset($options['clusterId']) ? $options['clusterId'] : null;
		$clusterType = isset($options['clusterType']) ? $options['clusterType'] : null;
		$clusterCategory = isset($options['clusterCategory']) ? $options['clusterCategory'] : null;
		$context = SOCIAL_STREAM_CONTEXT_TYPE_ALL;



		$limit = isset($options['limit']) ? $options['limit'] : 0;

		$configs	= array(
								'userid' 		=> $userId,
								'viewer' 		=> $viewerId,
								'context' 		=> $context,
								'issticky'		=> true,
								'clusterId' 	=> $clusterId,
								'clusterType' 	=> $clusterType,
								'clusterCategory' => $clusterCategory,
								'limit'			=> $limit
							);

		$model	= FD::model('Stream');


		//trigger onBeforeGetStream
		$this->triggerBeforeGetStream($configs);

		// var_dump($configs);exit;

		// Bind the context to the object
		$tmpContext = $configs['context'];

		if (is_array($configs['context'])) {
			$tmpContext = (count($configs['context']) > 1) ? implode( '|', $configs['context'] ) : $configs['context'][0];
		}

		$this->currentContext = $tmpContext;

		// since we allow options override, we need to perform checking only after the triggering
		$isCluster = ( $configs['clusterId'] || $configs['clusterType'] || $configs['clusterCategory'] ) ? true : false ;
		$this->isCluster = $isCluster;

		// $isCluster = ($clusterId || $clusterType || $clusterCategory) ? true : false ;

		if ($isCluster) {
			$results = $model->getClusterStreamData($configs);
		} else {
			$results = $model->getStreamData($configs);
		}

		// var_dump($results);exit;

		// If there's nothing, just return a boolean value.
		if (!$results) {
			return array();
		}

		// $this->uids = $model->getUids();

		// now we are safe to run the format function.
		$data  = $this->format($results , $context, $viewerId, true, 'onPrepareStream', array());
		return $data;
	}

	/**
	 * Retrieves a list of stream item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function get($options = array(), $displayOptions = array())
	{
		$users = array();

		// Lets process default values
		$actorId = isset( $options['actorId'] ) 	? $options['actorId'] : null;
		$userId = isset( $options['userId'] ) 	? $options['userId'] : null;
		$listId = isset( $options['listId'] ) 	? $options['listId'] : null;
		$profileId = isset($options['profileId']) ? $options['profileId'] : null;
		$context = isset( $options['context'] ) 	? $options['context'] : SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		$type = isset( $options['type'] ) 	? $options['type'] : SOCIAL_TYPE_USER;
		$limitStart = isset( $options['limitStart'] ) ? $options['limitStart'] : '';
		$limitEnd = isset( $options['limitEnd'] ) ? $options['limitEnd'] : '';
		$direction = isset( $options['direction'] )  ? $options['direction'] : 'older';
		$viewerId = isset( $options['viewerId'] )   ? $options['viewerId'] : null;
		$guest = isset( $options['guest'] )   ? $options['guest'] : false;
		$tag = isset( $options[ 'tag' ] ) ? $options[ 'tag' ] : false;
		$ignoreUser = isset( $options[ 'ignoreUser' ] ) ? $options[ 'ignoreUser' ] : false ;
		$onlyModerated = isset($options['onlyModerated']) ? $options['onlyModerated'] : false;
		$noSticky = isset( $options[ 'nosticky' ] ) ? $options[ 'nosticky' ] : false ;
		$customView = isset( $options[ 'view' ] ) ? $options[ 'view' ] : false ;

		// Cluster stream items
		$clusterId = isset($options['clusterId']) ? $options['clusterId'] : null;
		$clusterType = isset($options['clusterType']) ? $options['clusterType'] : null;
		$clusterCategory = isset($options['clusterCategory']) ? $options['clusterCategory'] : null;


		// Pagination stuffs
		$limit = isset($options['limit']) ? $options['limit'] : FD::config()->get('stream.pagination.pagelimit', 10);
		$startlimit = isset($options['startlimit']) ? $options['startlimit'] : 0;


		if (!is_array($context) && strpos( $context, '|') !== false) {
			$context = explode( '|', $context );
		}

		// If viewer is null, we assume the caller wants to fetch from the current user's perspective.
		if (is_null($viewerId)) {
			$viewerId 	= FD::user()->id;
		}

		// Ensure that the user id's are in an array form.
		$user = FD::user();
		$userId = ( empty( $userId ) ) ? $user->id : $userId;
		$userId	= FD::makeArray($userId);

		if (empty($context)) {
			$context = SOCIAL_STREAM_CONTEXT_TYPE_ALL;
		}

		$isFollow = false;

		if ($type == 'follow') {
			$this->filter 	= 'follow';

			// reset the type to user and update the isFollow flag.
			$type 		= SOCIAL_TYPE_USER;
			$isFollow 	= true;
		}

		$isBookmark = false;
		if ($type == 'bookmarks') {
			$this->filter 	= 'bookmarks';
			$isBookmark 	= true;
		}

		$isSticky = false;
		$userStickyOnly = false;
		if ($type == 'sticky') {
			$this->filter 	= 'sticky';

			// reset the type to user and update the isSticky flag.
			$type 		= SOCIAL_TYPE_USER;
			$isSticky 	= true;
			$userStickyOnly = true;
		}

		if ($listId) {
			$this->filter = 'list';
		}

		if ($guest) {
			$this->filter = 'everyone';
		}

		// Ensure that the tag is an array
		$tag = FD::makeArray($tag);

		if ($tag) {
			$this->filter = 'custom';
		}

		// Get stream model to fetch those records.
		$model = FD::model('Stream');
		$data = array();

		//$this->data		= $this->format( $result , $context, $viewerId );
		$keepSearching = true;
		$tryLimit      = 2;

		$options	= array(
								'actorid' 		=> $actorId,
								'userid' 		=> $userId,
								'list' 			=> $listId,
								'profileId' 	=> $profileId,
								'context' 		=> $context,
								'type' 			=> $type,
								'limitstart' 	=> $limitStart,
								'limitend' 		=> $limitEnd,
								'viewer' 		=> $viewerId,
								'isfollow' 		=> $isFollow,
								'isbookmark'	=> $isBookmark,
								'issticky'		=> $isSticky,
								'nosticky'		=> $noSticky,
								'userstickyonly' => $userStickyOnly,
								'direction' 	=> $direction,
								'guest' 		=> $guest,
								'tag'			=> $tag,
								'ignoreUser'	=> $ignoreUser,
								'clusterId' 	=> $clusterId,
								'clusterType' 	=> $clusterType,
								'clusterCategory' => $clusterCategory,
								'startlimit'	=> $startlimit,
								'limit'			=> $limit,
								'onlyModerated' => $onlyModerated,
								'customView' => $customView
							);

// var_dump($options);exit;

		//trigger onBeforeGetStream
		$this->triggerBeforeGetStream($options);

		// Bind the context to the object
		$tmpContext = $options['context'];

		if (is_array($options['context'])) {
			$tmpContext = (count($options['context']) > 1) ? implode( '|', $options['context'] ) : $options['context'][0];
		}

		$this->currentContext = $tmpContext;

		// since we allow options override, we need to perform checking only after the triggering
		$isCluster = ( $options['clusterId'] || $options['clusterType'] || $options['clusterCategory'] ) ? true : false ;
		$this->isCluster = $isCluster;

		$this->options = $options;

		$this->startlimit = $startlimit;

		$result = null;

		if ($isCluster) {
			$result = $model->getClusterStreamData($options);
		} else {
			$result = $model->getStreamData($options);
		}

		// If there's nothing, just return a boolean value.
		if (!$result) {
			$this->startlimit = '';
			return $this;
		}

		// we need to get the pagination and total first before u can execute the format.
		// this is because during the format, the shares context type might overwrite the total due
		// to another call to stream lib the get function.

		$this->pagination = $model->getPagination();

		//determine if loadmore show be displayed or not.
		$total = $model->getTotalCount();
		// if ($total && ($total - ($startlimit + $limit)) >= 1) {
		if ($total && ($total >= 1) ) {
			$this->startlimit 	  = $startlimit + $limit;
		} else {
			$this->startlimit = '';
		}

		if( $direction == 'later' )
		{
			$this->nextdate = $model->getCurrentStartDate();
		}

		$this->uids = $model->getUids();

		// now we are safe to run the format function.

		$requireSearch  = $this->format($result , $context, $viewerId, true, 'onPrepareStream', $displayOptions);
		$this->data = $requireSearch;

		// triggering onAfterGetStream
		$this->triggerAfterGetStream( $this->data );

		return $this;
	}

	public function getCount()
	{
		if( $this->data )
		{
			return count( $this->data );
		}
		else
		{
			return '0';
		}
	}


	/**
	 * Returns next start date used in stream pagination
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string - in mysql date format.
	 * @return
	 */
	public function getNextStartDate()
	{
		return $this->nextdate;
	}

	/**
	 * Returns next end date used in stream pagination
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string - in mysql date format.
	 * @return
	 */
	public function getNextEndDate()
	{
		return $this->enddate;
	}

	/**
	 * Returns next limit used in public stream pagination
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string - in mysql date format.
	 * @return
	 */
	public function getNextStartLimit()
	{
		return $this->startlimit;
	}

	public function getUids()
	{
		return $this->uids;
	}


	/**
	 * Returns a html formatted data for the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html($loadmore = false, $customEmptyMsg = '', $options = array())
	{
		$theme = FD::themes();
		$output = '';
		$config = FD::config();

		// Determine if we should only get the content only
		$contentOnly = isset($options['contentOnly']) ? $options['contentOnly'] : false;

		// Get the current view
		$view = JRequest::getVar('view');

		// Determines if this is a guest
		$isGuest = $this->guest;

		if (!$isGuest) {
			//let check one more time if current viewer is guest or not.
			$my = FD::user();
			$isGuest = ( $my->id == 0 && $view != 'groups') ? true : $isGuest;
		}

		if (is_array($this->currentContext)) {
			$theme->set('context', implode( '|', $this->currentContext) );
		} else {
			$theme->set('context', $this->currentContext);
		}

		$theme->set('view', $view);

		if (!empty($this->story)) {
			$theme->set('story', $this->story);
		}

		//stickies posts
		if (isset($this->stickies) && $this->stickies) {
			$theme->set('stickies', $this->stickies);
		}

		if ($config->get('stream.pagination.style') == 'page') {
			$theme->set( 'pagination', $this->getPagination() );
		}

		// Determines if we should display the translations.
		$my = ES::user();
		$language = $my->getLanguage();
		$siteLanguage = JFactory::getLanguage();
		$showTranslations = false;

		if (($language != $siteLanguage->getTag()) || $config->get('stream.translations.explicit')) {
			$showTranslations = true;
		}

		$theme->set('showTranslations', $showTranslations);

		if ($loadmore) {
			if ($this->data && is_array($this->data)) {
				foreach ($this->data as $stream) {

					if ($contentOnly) {
						$output .= $theme->loadTemplate('site/stream/default.item.content', array('stream' => $stream, 'showTranslations' => $showTranslations));
					} else {
						$output .= $theme->loadTemplate('site/stream/default.item', array('stream' => $stream, 'showTranslations' => $showTranslations));
					}

				}
			}
		} else {

			if ($this->singleItem) {

				if (empty( $this->data ) || count( $this->data ) == 0 || $this->data === true) {
					$output .= $theme->output('site/stream/default.unavailable');

					return $output;
				}


				$theme->set('stream', $this->data[0]);

				if ($contentOnly) {
					$output = $theme->output('site/stream/default.item.content');
				} else {
					$output = $theme->output('site/stream/default.item');
				}

			} else {

				// Define empty messages here
				$empty = $customEmptyMsg ? $customEmptyMsg : JText::_('COM_EASYSOCIAL_STREAM_NO_STREAM_ITEM');

				if ($this->filter == 'follow') {
					$empty = $customEmptyMsg ? $customEmptyMsg : JText::_('COM_EASYSOCIAL_STREAM_NO_STREAM_ITEM_FROM_FOLLOWING');
				}

				if ($this->filter == 'list') {
					$empty = $customEmptyMsg ? $customEmptyMsg : JText::_('COM_EASYSOCIAL_STREAM_NO_STREAM_ITEM_FROM_LIST');
				}



				$theme->set('empty', $empty);
				$theme->set('streams', $this->data);
				$theme->set('nextdate', $this->nextdate);
				$theme->set('enddate', $this->enddate);
				$theme->set('guest', $isGuest);
				$theme->set('nextlimit', $this->startlimit);
				$theme->set('iscluster', $this->isCluster);

				$output = $theme->output('site/stream/default');
			}
		}

		return $output;
	}

	public function action()
	{
		$theme 		= FD::get( 'Themes' );
		$theme->set( 'stream' , $this->data[ 0 ] );

		$output = $theme->output( 'site/stream/actions' );

		return $output;
	}

	/**
	 * Return the raw data for the stream.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function json()
	{
		//@TODO: Perhaps there's something that we need to modify here for json type?

		$json 	= FD::json();
		$output = $json->encode( $this->data );

		return $output;
	}

	/**
	 * Return the raw data for the stream.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function toArray()
	{
		return $this->data;
	}

	/**
	 * Prepares core actions
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareCoreActions( SocialStreamItem &$item )
	{
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );

		// Default value.
		$result		= array();

		// Get core actions.
		$path 		= dirname( __FILE__ ) . '/actions';
		$config 	= FD::config();
		$actions 	= $config->get( 'stream.actions' );

		foreach( $actions as $action )
		{
			// Include the file library.
			require_once( $path . '/' . $action . '.php' );

			// Replace all spaces with underscores.
			$name 		= str_ireplace( ' ' , '_' , $action );

			// Build index key here.
			$key 		= strtolower( $name );

			// Get class name.
			$className 	= 'SocialStreamAction' . ucfirst( $name );

			// Instantiate the action object.
			$actionObj	= new $className( $item );

			// Set the actions.
			$result[ $action ]	= $actionObj;
		}

		return $result;
	}

	/**
	 * Prepares stream actions.
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function onPrepareStreamActions( SocialStreamItem &$item )
	{
		// Get apps library.
		$apps 	= FD::getInstance( 'Apps' );

		// Try to load user apps
		$state 	= $apps->load( SOCIAL_APPS_GROUP_USER );

		// By default return true.
		$result 	= true;

		if (!$state) {
			return false;
		}

		// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
		$dispatcher		= FD::dispatcher();

		// Pass arguments by reference.
		$args 			= array( &$item );

		// @trigger: onPrepareStream for the specific context
		$dispatcher->trigger( SOCIAL_APPS_GROUP_USER , 'onPrepareStreamActions' , $args , $item->context );

		// @TODO: Check each actions and ensure that they are instance of ISocialStreamAction

		return true;
	}

	/**
	 * Prepares a stream item.
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function onPrepareStream( SocialStreamItem &$item, $includePrivacy = true )
	{
		// Get apps library.
		$result = $this->onPrepareEvent('onPrepareStream', $item, $includePrivacy);
		return $result;
	}

	/**
	 * Prepares a stream item for activity logs
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{
		// Get apps library.
		$result = $this->onPrepareEvent( __FUNCTION__ , $item, $includePrivacy );
		return $result;
	}

	/**
	 * Prepares the stream by rendering apps
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function onPrepareEvent($eventName, SocialStreamItem &$item, $includePrivacy = true)
	{
		$view = $this->input->get('view', '', 'cmd');

		// Get apps library.
		$apps = ES::apps();

		// Determine the app group
		$group = SOCIAL_APPS_GROUP_USER;

		// If it is in the group view we should render the apps based on the appropriate group
		if ($view && ($view == 'groups' || $view == 'events' || $view == 'stream') && $item->cluster_type) {
			$group = $item->cluster_type;
		}

		// Try to load user apps
		$state = $apps->load($group);

		// By default return true.
		$result = true;

		if (!$state) {
			return $result;
		}

		// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
		$dispatcher = ES::dispatcher();

		// Pass arguments by reference.
		$args = array(&$item, $includePrivacy);

		// @trigger: onPrepareStream for the specific context
		$result = $dispatcher->trigger($group, $eventName, $args, $item->context);

		return $result;
	}

	private function triggerAfterGetStream( &$items )
	{
		if (!$items) {
			return;
		}

		$view  = JRequest::getCmd( 'view', '' );

		// Get apps library.
		$apps 	= FD::getInstance( 'Apps' );

		// Determine the app group
		$group 	= SOCIAL_APPS_GROUP_USER;

		// Try to load user apps
		$state 	= $apps->load( $group );

		// By default return true.
		$result 	= true;

		if (!$state) {
			return $result;
		}

		// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
		$dispatcher		= FD::dispatcher();

		// Pass arguments by reference.
		$args 			= array( &$items );

		// @trigger: onPrepareStream for the specific context
		$result 		= $dispatcher->trigger( $group , 'onAfterGetStream' , $args );


		return $result;
	}


	private function triggerBeforeGetStream( &$options )
	{
		if(! $options )
		{
			return;
		}

		$view  = JRequest::getCmd( 'view', '' );

		// Get apps library.
		$apps 	= FD::getInstance( 'Apps' );

		// Determine the app group
		$group 	= SOCIAL_APPS_GROUP_USER;

		// If it is in the group view we should render the apps based on the appropriate group
		if ($view && ($view == 'groups' || $view == 'events' || $view == 'stream') && (isset($options['clusterType']) && $options['clusterType'])) {
			$group	= $options['clusterType'];
		}

		// Try to load user apps
		$state 	= $apps->load( $group );

		// By default return true.
		$result 	= true;

		if (!$state) {
			return false;
		}

		// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
		$dispatcher		= FD::dispatcher();

		// Pass arguments by reference.
		$args 			= array( &$options, $view );

		$dispatcher->trigger( $group , 'onBeforeGetStream' , $args );
	}


	/**
	 * Build the aggregated data
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function buildAggregatedData( $activities )
	{
		// If there's no activity at all, it should fail here.
		// There should be at least 1 activity.
		if( !$activities )
		{
			return false;
		}

		$data 					= new stdClass();
		$data->contextIds		= array();
		$data->actors 			= array();
		$data->targets 			= array();
		$data->verbs 			= array();
		$data->params 			= array();

		// Temporary data
		$actorIds 		= array();
		$targetIds		= array();

		foreach( $activities as $activity )
		{
			// Assign actor into temporary data only when actor id is valid.
			if( $activity->actor_id )
			{
				$actorIds[]			= $activity->actor_id;
			}

			// Assign target into temporary data only when target id is valid.
			if( $activity->target_id )
			{
				if( !( $activity->context_type == 'photos' && $activity->verb == 'add' )
					&& !( $activity->context_type == 'shares' && $activity->verb == 'add.stream' ) )
				{
					$targetIds[]		= $activity->target_id;
				}
			}

			// Assign context ids.
			$data->contextIds[]	= $activity->context_id;

			// Assign the verbs.
			$data->verbs[]		= $activity->verb;

			// Assign the params
			$data->params[ $activity->context_id ]	= isset( $activity->params ) ? $activity->params : '';
		}

		// Pre load users.
		$userIds	= array_merge( $data->actors , $data->targets );
		FD::user( $userIds );


		// Build the actor's data
		if( $actorIds )
		{
			$actorIds = array_unique( $actorIds );
			foreach( $actorIds as $actorId )
			{
				$user 			= FD::user( $actorId );

				$data->actors[]	= $user;
			}
		}

		// Build the target's data.
		if( $targetIds )
		{
			$targetIds = array_unique( $targetIds );
			foreach( $targetIds as $targetId )
			{
				$user 				= FD::user( $targetId );
				$data->targets[]	= $user;
			}
		}

		return $data;
	}

	/**
	 * Displays the actions block that is used on a stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getActions($options = array())
	{
		$theme			= FD::themes();

		// If the options is a stream item object, we need to map it accordingly.
		if( $options instanceof SocialStreamItem )
		{
			// Set the friendly date
			$friendlyDate 	= $options->friendlyDate;

			// Set the comments
			$comments 		= isset( $options->comments ) ? $options->comments : '';

			// Set the likes
			$likes 			= isset( $options->likes ) ? $options->likes : '';

			// Set the repost
			$repost 		= isset( $options->repost ) ? $options->repost : '';

			// Set the sharing
			$sharing		= isset( $options->sharing ) ? $options->sharing : '';

			// Set the privacy
			$privacy 		= isset( $options->privacy ) ? $options->privacy : '';

			// Set the location
			$location 		= isset( $options->location ) ? $options->location : '';

			// Set the stream's uid
			$uid 			= $options->uid;

			// Determine if the comments link should be visible
			$commentLink 	= isset($options->commentLink) ? $options->commentLink : true;

			$icon 			= isset( $options->icon ) ? $options->icon : '';
		}
		else
		{
			// Set the default friendly date
			$friendlyDate	= false;

			$date 			= isset( $options[ 'date' ] ) ? $options[ 'date' ] : null;
			$comments 		= isset( $options[ 'comments' ] ) ? $options[ 'comments' ] : '';
			$likes 			= isset( $options[ 'likes' ] ) ? $options[ 'likes' ] : '';
			$repost 		= isset( $options[ 'repost' ] ) ? $options[ 'repost' ] : '';
			$sharing 		= isset( $options[ 'sharing' ] ) ? $options[ 'sharing' ] : '';
			$uid 			= isset( $options[ 'uid' ] ) ? $options[ 'uid' ] : '';
			$privacy 		= isset( $options[ 'privacy' ] ) ? $options[ 'privacy' ] : '';
			$icon 			= isset( $options[ 'icon' ] ) ? $options[ 'icon' ] : '';
			$location 		= isset( $options[ 'location' ] ) ? $options[ 'location' ] : '';
			$commentLink	= isset($options['commentLink']) ? $options['commentLink'] : true;

			if (!is_null($date)) {
				$friendlyDate 	= FD::date( $date )->toLapsed();
			}
		}

		$theme->set('commentLink'	, $commentLink);
		$theme->set( 'location'		, $location );
		$theme->set( 'icon'			, $icon );
		$theme->set( 'friendlyDate' , $friendlyDate );
		$theme->set( 'privacy'	, $privacy );
		$theme->set( 'uid'		, $uid );
		$theme->set( 'comments' , $comments );
		$theme->set( 'likes' , $likes );
		$theme->set( 'repost' , $repost );
		$theme->set( 'sharing' , $sharing );

		$output 	= $theme->output( 'site/stream/actions' );

		return $output;
	}

	/**
	 * Translate stream's date time.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function translateDate( $day, $hour, $min )
	{
		$dayString  = '';
		$timeformat = '%I:%M %p';


		$day 	= ( $day < 0 ) ? '0' : $day;
		$hour 	= ( $hour < 0 ) ? '0' : $hour;
		$min 	= ( $day < 0 ) ? '0' : $min;

		// today
		if( $day == 0)
		{
			if( $min > 60 )
			{
				$dayString  = $hour . JText::_( 'COM_EASYSOCIAL_STREAM_X_HOURS_AGO');
			} else if( $min <= 0)
			{
			    $dayString  = JText::_( 'COM_EASYSOCIAL_STREAM_LESS_THAN_ONE_MIN_AGO' );
			}
			else
			{
			    $dayString  = $min . JText::_( 'COM_EASYSOCIAL_STREAM_X_MINS_AGO');
			}
		}
		elseif ( $day == 1 )
		{
			$time 	= FD::date( '-' . $min . ' mins' );

			$dayString  = JText::_( 'COM_EASYSOCIAL_STREAM_YESTERDAY_AT' ) . $time->toFormat($timeformat);
		}
		elseif( $day > 1 && $day <= 7)
		{
			$dayString		= FD::get( 'Date', '-' . $min . ' mins')->toFormat( '%A ' . JText::_( 'COM_EASYSOCIAL_STREAM_DATE_AT' ) . ' ' . $timeformat);
		}
		else
		{
			$dayString		= FD::get( 'Date', '-' . $min . ' mins')->toFormat('%b %d ' . JText::_( 'COM_EASYSOCIAL_STREAM_DATE_AT' ) . ' ' . $timeformat);
		}


		return $dayString;
	}

	public static function getContentImage( $item )
	{
		$image = '';

		$content = $item->content . $item->preview;

		$pattern = '/\"background-image:\surl\(\'(.*)\'\);\"/i';
		preg_match($pattern, $content, $matches);

		if($matches)
		{
			$imgPath   = $matches[1];
			$image     = self::rel2abs($imgPath, JURI::root() );

		} else {

			$img            = '';
			$pattern		= '#<img[^>]*>#i';
			preg_match( $pattern , $content , $matches );

			if($matches )
			{
				$img    = $matches[0];
			}

			//image found. now we process further to get the absolute image path.
			if( $img )
			{
				//get the img source
				$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
				preg_match($pattern, $img, $matches);
				if($matches)
				{
					$imgPath   = $matches[1];
					$image    = self::rel2abs($imgPath, JURI::root() );
				}
			}

		}

		return $image;
	}

	public static function rel2abs($rel, $base)
	{
		/* return if already absolute URL */
		if (parse_url($rel, PHP_URL_SCHEME) != '') return $rel;

		/* queries and anchors */
		if (@$rel[0]=='#' || @$rel[0]=='?') return $base.$rel;

		/* parse base URL and convert to local variables:
		   $scheme, $host, $path */
		extract(parse_url($base));

		/* remove non-directory element from path */
		$path = preg_replace('#/[^/]*$#', '', $path);

		/* destroy path if relative url points to root */
		if ( @$rel[0] == '/') $path = '';

		/* dirty absolute URL */
		$abs = "$host$path/$rel";
		/* replace '//' or '/./' or '/foo/../' with '/' */
		$re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
		for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

		/* absolute URL is ready! */
		return $scheme.'://'.$abs;
	}
}
