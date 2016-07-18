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

// Include necessary libraries here.
require_once( dirname( __FILE__ ) . '/plugin.php' );
require_once( dirname( __FILE__ ) . '/panel.php' );
require_once( dirname( __FILE__ ) . '/attachment.php' );

/**
 * A story class.
 *
 * @since	1.0
 */
class SocialStory
{
	private $story 	 	= null;
	public $id       	= null;
	public $moduleId 	= null;
	public $content  	= '';
	public $overlay  	= '';
	public $hashtags 	= array();
	public $mentions 	= array();

	public $cluster 	= null;
	public $clusterType = null;

	public $requirePrivacy = null;


	/**
	 * The unique target id.
	 * @var int
	 */
	public $target   = null;

	/**
	 * The unique target type.
	 * @var string
	 */
	public $targetType 	= null;

	/**
	 * Determines the type of the story.
	 * @var string
	 */
	public $type 	= null;

	public $attachments = array();
	public $panels = array();
	public $plugins = array();

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function __construct( $type )
	{
		$this->type 			= $type;
		$this->requirePrivacy 	= true;

		// Generate a unique id for the current stream object.
		$this->id = uniqid();
		$this->moduleId = 'story-' . $this->id;

		$this->app = JFactory::getApplication();
	}

	public function setMentions($mentions)
	{
		$this->mentions	= $mentions;
	}

	/**
	 * Allows caller to specify the cluster this story belong.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setCluster( $clusterId , $clusterType )
	{
		$this->cluster		= $clusterId;
		$this->clusterType	= $clusterType;
	}

	public function isCluster()
	{
		return ( $this->cluster ) ? true : false;
	}

	public function getClusterId()
	{
		return $this->cluster;
	}

	public function getClusterType()
	{
		return $this->clusterType;
	}

	public function showPrivacy( $require = true )
	{
		$this->requirePrivacy = $require;
	}

	public function requirePrivacy()
	{
		return $this->requirePrivacy;
	}


	/**
	 * Allows caller to specify the target id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setTarget( $targetId , $targetType = SOCIAL_TYPE_USER )
	{
		$this->target	= $targetId;
	}

	/**
	 * Allows caller to specify the initial content
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setContent($content='')
	{
		$this->content = $content;
	}

	/**
	 * Returns the mention form
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMentionsForm($resetToDefault=false)
	{
		$theme 	= FD::themes();

		$tmp	= array();

		$this->overlay 	= $this->content;

		if ($this->mentions) {

			// Store mentions temporarily to avoid escaping
			$i = 0;

			foreach ($this->mentions as $mention) {

				if ($mention->utype == 'user') {
					$user 		= FD::user($mention->uid);
					$replace 	= '<span>' . $user->getName() . '</span>';
				}

				if ($mention->utype == 'hashtag') {
					$replace 	= '<span>' . "#" . $mention->title . '</span>';
				}

				$tmp[$i]		= $replace;

				$replace 		= '[si:mentions]' . $i . '[/si:mentions]';
				$this->overlay 	= JString::substr_replace($this->overlay, $replace, $mention->offset, $mention->length);

				$i++;
			}
		}

		$this->overlay 	= FD::string()->escape($this->overlay);

		for ($x = 0; $x < count($tmp); $x++) {
			$this->overlay 	= str_ireplace('[si:mentions]' . $x . '[/si:mentions]', $tmp[$x], $this->overlay);
		}

		$theme->set('story', $this);
		$theme->set('defaultOverlay', $resetToDefault ? $story->overlay : '');
		$theme->set('defaultContent', $resetToDefault ? $story->content : '');

		$contents 	= $theme->output('site/mentions/form');

		return $contents;
	}

	public function setHashtags($tags=array())
	{
		if (count($tags) < 1) return;

		$content = '#' . implode(' #', $tags);
		$overlay = '<span>#' . implode('</span> <span>#', $tags) . '</span>';

		$this->content = ' ' . $content;
		$this->overlay = ' ' . $overlay;
		$this->hashtags = $tags;
	}

	/**
	 * Returns the target id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * Creates a new stream item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create($args = array())
	{
		// The content of the story
		$content = isset($args['content']) ? $args['content'] : '';

		// Context ids, and type that are related to the story
		$contextIds = isset($args['contextIds']) ? $args['contextIds'] : '';
		$contextType = isset($args['contextType']) ? $args['contextType'] : '';

		// The person that created this new story
		$actorId = isset($args['actorId']) ? $args['actorId'] : '';

		// If the object is posting on another object, the target object id should be passed in here.
		$targetId = isset($args['targetId']) ? $args['targetId'] : null;

		// If the story is associated with a location, it should be processed.
		$location = isset($args['location']) ? $args['location'] : null;

		// If the story is being tagged with other users.
		$with = isset($args['with']) ? $args['with'] : null;

		// If the content of the story contains mentions using @ and # tags.
		$mentions = isset($args['mentions']) ? $args['mentions'] : array();

		// If the story belongs in a cluster
		$cluster = isset($args['cluster']) ? $args['cluster'] : '';
		$clusterType = isset($args['clusterType']) ? $args['clusterType'] : SOCIAL_TYPE_GROUP;

		// If the story contains a mood
		$mood = isset($args['mood']) ? $args['mood'] : null;

		// Store this into the stream now.
		$stream = FD::stream();

		// Ensure that context ids are always array
		$contextIds = FD::makeArray($contextIds);

		// Determines which trigger group to call
		$group = $cluster ? $clusterType : SOCIAL_TYPE_USER;

		// Load apps
		FD::apps()->load($group);

		// Load up the dispatcher so that we can trigger this.
		$dispatcher = FD::dispatcher();

		// This is to satisfy the setContext method.
		$contextId = isset($contextIds[0]) ? $contextIds[0] : 0;

		// Get the stream template
		$template = $stream->getTemplate();

		$template->setActor($actorId, $this->type);
		$template->setContext($contextId, $contextType);
		$template->setContent($content);

		$verb = ( $contextType == 'photos' ) ? 'share' : 'create';
		$template->setVerb($verb);

		$privacyRule = isset($args['privacyRule']) ? $args['privacyRule'] : null;
		$privacyValue = isset($args['privacyValue']) ? $args['privacyValue'] : null;
		$privacyCustom = isset($args['privacyCustom']) ? $args['privacyCustom'] : null;

		if (!$privacyRule) {
			$privacyRule	= 'story.view';
			if ($contextType == 'photos') {
				$privacyRule = 'photos.view';
			} else if ($contextType == 'polls') {
				$privacyRule = 'polls.view';
			} else if ($contextType == 'videos') {
				$privacyRule = 'videos.view';
			}
		}

		if ($privacyValue && is_string($privacyValue) ) {
			$privacyValue = FD::privacy()->toValue($privacyValue);
		}

		if($privacyCustom) {
			$privacyCustom = explode( ',', $privacyCustom );
		}

		// Set this stream to be public
		$template->setAccess( $privacyRule, $privacyValue, $privacyCustom );

		// Set mentions
		$template->setMentions($mentions);

		// Set the users tagged in the  stream.
		$template->setWith($with);

		// Set the location of the stream
		$template->setLocation($location);

		// Set the mood
		if (!is_null($mood)) {
			$template->setMood($mood);
		}

		// If there's a target, we want it to appear on their stream too
		if ($targetId) {
			$template->setTarget($targetId);
		}

		if ($contextType == 'photos') {

			if (count( $contextIds ) > 0) {
				foreach ($contextIds as $photoId) {
					$template->setChild($photoId);
				}
			}
		}

		if ($cluster) {

			$clusterObj = FD::cluster($clusterType, $cluster);

			if ($clusterObj) {

				// Set the params to cache the group data
				$registry = FD::registry();
				$registry->set($clusterType, $clusterObj);

				// Set the params to cache the group data
				$template->setParams($registry);

				$template->setCluster($cluster, $clusterType, $clusterObj->type);
			} else {
				$template->setCluster($cluster, $clusterType, 1);
			}
		}

		// Build the arguments for the trigger
		$args = array(&$template, &$stream, &$content);

		// @trigger onBeforeStorySave
		$dispatcher->trigger($group, 'onBeforeStorySave' , $args);

		// Create the new stream item.
		$streamItem = $stream->add($template);

		// Store link items
		$this->storeLinks($stream, $streamItem, $template);

		// Set the notification type
		$notificationType = SOCIAL_TYPE_STORY;

		// Construct our new arguments
		$args = array(&$stream, &$streamItem, &$template);

		// @trigger onAfterStorySave
		$dispatcher->trigger($group, 'onAfterStorySave', $args);

		// Send a notification to the recipient if needed.
		if ($targetId && $actorId != $targetId) {
			$this->notify($targetId, $streamItem, $template->content, $contextIds, $contextType , $notificationType);
		}

		// Send a notification alert if there are mentions
		if ($mentions && !empty($mentions)) {
			$this->notifyMentions($streamItem, $mentions, $contextType, $contextIds , $template->content , $targetId);
		}

		return $streamItem;
	}

	/**
	 * Stores any link assets
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function storeLinks( $stream , $streamItem , $template )
	{
		// Get the link information from the request
		$link = $this->app->input->get('links_url', '', 'default');
		$title = $this->app->input->get('links_title', '', 'default');
		$content = $this->app->input->get('links_description', '', 'default');
		$image = $this->app->input->get('links_image', '', 'default');

		// If there's no data, we don't need to store in the assets table.
		if (empty($title) && empty($content) && empty($image)) {
			return false;
		}

		// Cache the image if necessary
		$links = FD::links();
		$fileName = $links->cache($image);

		$registry = FD::registry();
		$registry->set('title', $title);
		$registry->set('content', $content);
		$registry->set('image', $image);
		$registry->set('link', $link);
		$registry->set('cached', false);

		// Image link should only be modified when the file exists
		if ($fileName !== false) {
			$registry->set('cached', true);
			$registry->set('image', $fileName);
		}

		// Store the link object into the assets table
		$assets = FD::table('StreamAsset');
		$assets->stream_id = $streamItem->uid;
		$assets->type = 'links';
		$assets->data = $registry->toString();

		// Store the assets
		$state = $assets->store();

		return $state;
	}

	/**
	 * Notify users that is mentioned in the story
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyMentions( $stream , $mentions , $contextType , $contextIds , $content , $targetId )
	{
		$recipients 	= array();

		if (!$mentions) {
			return;
		}

		foreach ($mentions as $mention) {

			// Only process items with users tagging since we only want to notify users.
			if ($mention->type != 'entity'){
				continue;
			}

            $parts = explode(':', $mention->value);

            if (count($parts) != 2) {
                continue;
            }

            $type = $parts[0];
            $id = $parts[1];

			$recipients[]	= FD::user($id);
		}

		$actor 		= FD::user($stream->actor_id);

		// Add notification to the requester that the user accepted his friend request.
		$state = null;

		foreach ($recipients as $recipient) {

			// If the recipient is being mentioned in a post that is posted on their own stream, we shouldn't need to notify them again.
			if ($recipient->id == $targetId) {
				continue;
			}

	        // Set the email options
	        $emailOptions   = array(
	            'title'     	=> 'COM_EASYSOCIAL_EMAILS_USER_MENTIONED_YOU_IN_A_POST_SUBJECT',
	            'template'  	=> 'site/profile/post.mentions',
	            'permalink' 	=> $stream->getPermalink(false, true),
	            'actor'     	=> $actor->getName(),
	            'actorAvatar'   => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink'     => $actor->getPermalink(true, true),
	            'message'		=> $content
	        );

	        $systemOptions  = array(
	        	'uid'           => $stream->id,
	            'context_type'  => $contextType,
	            'context_ids'	=> FD::json()->encode($contextIds),
	            'type'			=> SOCIAL_TYPE_STORY,
	            'url'           => $stream->getPermalink(false, false, false),
	            'actor_id'      => $actor->id,
	            'target_id'		=> $recipient->id,
	            'aggregate'     => true,
	            'content'		=> $content
	        );

			// Send notification to the target
			$state 		= FD::notify('stream.tagged', array($recipient->id), $emailOptions, $systemOptions);
		}

		return $state;
	}

	/**
	 * Notifies a user when someone posted something on their timeline
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int				The target user id.
	 * @param	SocialStream	The stream table
	 * @return
	 */
	public function notify($id, $stream, $content, $contextIds, $contextType, $notificationType)
	{
		$recipient 	= FD::user($id);
		$actor 		= FD::user($stream->actor_id);

		$systemOptions	= array(
			// The unique node id here is the #__social_friend id.
			'uid'			=> $stream->id,
			'content'		=> $content,
			'actor_id'		=> $actor->id,
			'target_id'		=> $recipient->id,
			'context_ids'	=> FD::json()->encode( $contextIds ),
			'context_type'	=> 'post.user.timeline',
			'type'			=> $notificationType,
			'url'			=> $stream->getPermalink(false, false, false)
		);

		$emailOptions = array(
			'title'			=> 'COM_EASYSOCIAL_EMAILS_USER_POSTED_ON_YOUR_TIMELINE_SUBJECT',
			'template'		=> 'site/profile/post.story',
			'params' 		=> array(
								'actor' 		=> $actor->getName(),
								'actorAvatar' 	=> $actor->getAvatar(),
								'actorLink' 	=> $actor->getPermalink(true, true),
								'permalink' 	=> $stream->getPermalink(false, true),
								'content' 		=> $content
								)
		);

		$state 	= FD::notify('profile.story', array($recipient->id), $emailOptions, $systemOptions);

		return $state;
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
	public static function factory( $type )
	{
		return new self( $type );
	}

	/**
	 * Get's a template object for story.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createPlugin($name, $type='plugin')
	{
		$pluginClass = 'SocialStory' . ucfirst($type);

		$plugin = new $pluginClass($name, $this);

		return $plugin;
	}

	/**
	 * Trigger to prepare the story item before being output.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function prepare()
	{
		// Load up the necessary apps
		FD::apps()->load( $this->type );

		// Pass arguments by reference.
		$args		= array( &$this );

		// Only go through dispatcher when there is some apps loaded, otherwise it's pointless.
		$dispatcher = FD::dispatcher();

		// StoryAttachment service
		$panels		= $dispatcher->trigger( $this->type , 'onPrepareStoryPanel' , $args );

		if( $panels )
		{
			foreach( $panels as $panel )
			{
				if ( $panel instanceof SocialStoryPanel )
				{
					$this->panels[]		= $panel;
					$this->plugins[]	= $panel;
				}
			}
		}

		return true;
	}

	/**
	 * Get's the content in html form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determine whether or not to show the current status.
	 * @return
	 */
	public function html( $showCurrentStory = true )
	{
		$my = FD::user();

		// Let's test if the current viewer is allowed to view this profile.
		if( $this->requirePrivacy() )
		{
			if( $this->target && $my->id != $this->target )
			{
				$privacy 	= FD::privacy( $my->id );
				$state = $privacy->validate( 'profiles.post.status' , $this->target , SOCIAL_TYPE_USER );

				if( ! $state )
				{
					return '';
				}
			}
		}

		// Prepare the story.
		$this->prepare();

		// Determines if the story form should be expanded by default.
		$expanded = false;

		if (!empty($this->content)) {
			$expanded = true;
		}

		// Get moods
		$gender = $my->getGenderLang();
		$moods = $this->getMoods($gender);

		$theme = FD::get('Themes');
		$theme->set('moods', $moods);
		$theme->set('expanded', $expanded);
		$theme->set('story', $this);

		$output = $theme->output( 'site/story/default' );

		return $output;
	}

	/**
	 * Get a list of preset moods
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMoods($gender="_NOGENDER")
	{
		$moods = array();
		// @TODO: In the future, we could scan for moods
		$verbs = array('feeling');

		foreach ($verbs as $verb) {

			$file = dirname(__FILE__) . '/moods/' . $verb . '.mood';
			$verb = FD::makeObject($file);

			// Apppend gender suffix to language keys
			foreach ($verb->moods as $mood) {
				$mood->text    .= $gender;
				$mood->subject .= $gender;
			}

			$moods[$verb->key] = $verb;
		}

		return $moods;
	}

	/**
	 * Get's the content in json form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	bool	Determine whether or not to show the current status.
	 * @return
	 */
	public function json()
	{
		$json 	= FD::json();

		$obj 	= (object) $this->story;

		$output = $json->encode( $obj );

		return $output;
	}

}

