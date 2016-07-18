<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogEasySocial extends EasyBlog
{
	public static $file = null;
	private $exists	= false;

	public function __construct()
	{
		parent::__construct();

		self::$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		// Load languages
		$this->loadLanguage();
	}

	/**
	 * Determines if EasySocial is installed on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		jimport('joomla.filesystem.file');

		if (!JFile::exists(self::$file)) {
			return false;
		}

		include_once(self::$file);

		if (!class_exists('FD')) {
		    return false;
		}

		return true;
	}

	/**
	 * Renders the messaging link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMessagingHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		// Initialize easysocial's library
		$this->init();

		$user = FD::user($authorId);

		$template = EB::template();
		$template->set('user', $user);

		$output = $template->output('site/easysocial/conversation');

		return $output;
	}


	/**
	 * Renders the friend link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFriendsHtml($authorId)
	{
		if (!$this->exists()) {
			return;
		}

		$user = FD::user($authorId);

		// Check if the user is friends with the current viewer.
		if ($user->isFriends($this->my->id)) {
			return;
		}

		$this->init();

		$template = EB::template();
		$template->set('id', $authorId);

		$output = $template->output('site/easysocial/friends');

		return $output;
	}

	/**
	 * Retrieves EasySocial's toolbar
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getToolbar()
	{
		if (!$this->exists()) {
			return;
		}

		$toolbar = Foundry::get('Toolbar');
		$output = $toolbar->render();

		return $output;
	}

	/**
	 * Renders the mini header of EasySocial
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderMiniHeader($group)
	{
		if (!$this->exists()) {
			return;
		}

		// Initialize EasySocial's css files
		$this->init();

		$themes = Foundry::themes();

		$output = '';

		ob_start();
		echo '<div id="fd" class="es" style="margin-bottom: 15px;">';
		echo $themes->html('html.miniheader', $group);
		echo '</div>';
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Initializes EasySocial
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function init()
	{
		static $loaded 	= false;

		if (!$loaded) {

			require_once(self::$file);

			$document = JFactory::getDocument();

			if ($document->getType() == 'html') {
				// We also need to render the styling from EasySocial.
				$doc = Foundry::document();
				$doc->init();

				$page = Foundry::page();
				$page->processScripts();

			}

			Foundry::language()->load('com_easysocial' , JPATH_ROOT);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Retrieves a list of events joined by the user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEvents()
	{
		if (!$this->exists()) {
			return array();
		}

		$options = array();

		if (!EB::isSiteAdmin()) {
			$options['uid'] = $this->my->id;
		}

		$model = FD::model('Events');
		$result = $model->getEvents($options);
		$events = array();

		if (!$result) {
			return $events;
		}

		foreach ($result as $event) {
			$obj = new stdClass();
			$obj->title = $event->getName();
			$obj->source_id = $event->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_EASYSOCIAL_EVENT;
			$obj->type = 'event';
			$obj->avatar = $event->getAvatar();

			$groups[] = $obj;
		}

		return $groups;
	}

	/**
	 * Retrieves a list of groups joined by the user
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		if (!$this->exists()) {
			return array();
		}

		$model = FD::model('Groups');

		$options = array();

		if (!EB::isSiteAdmin()) {
			$options['uid'] = $this->my->id;
		}

		$result = $model->getGroups($options);
		$groups = array();

		if (!$result) {
			return $groups;
		}

		foreach ($result as $group) {
			$obj = new stdClass();
			$obj->title = $group->getName();
			$obj->source_id = $group->id;
			$obj->source_type = EASYBLOG_POST_SOURCE_EASYSOCIAL_GROUP;
			$obj->type = 'group';
			$obj->avatar = $group->getAvatar();

			$groups[] = $obj;
		}

		return $groups;
	}

	/**
	 * Displays the user's points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPoints($id)
	{
		$config = EasyBlogHelper::getConfig();

		if (!$this->exists()) {
			return;
		}

		if (!$config->get('integrations_easysocial_points')) {
			return;
		}

		$theme = EB::template();

		$user = Foundry::user($id);

		$theme->set('user', $user);
		$output = $theme->output( 'site/easysocial/points' );

		return $output;
	}

	/**
	 * Displays comments
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentHTML($blog)
	{
		if(!$this->exists()) {
			return;
		}

		Foundry::language()->load('com_easysocial', JPATH_ROOT);

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $blog->id);
		$comments = Foundry::comments($blog->id, 'blog', SOCIAL_APPS_GROUP_USER, $url);

		$theme = EB::template();
		$theme->set('blog', $blog);
		$theme->set('comments', $comments);
		$output = $theme->output('site/easysocial/comments');

		return $output;
	}

	/**
	 * Returns the comment counter
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentCount($blog)
	{
		if (!$this->exists()) {
			return;
		}

		Foundry::language()->load('com_easysocial', JPATH_ROOT);

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $blog->id);
		$comments = Foundry::comments($blog->id, 'blog', SOCIAL_APPS_GROUP_USER, $url);

		return $comments->getCount();
	}

	/**
	 * Assign badge
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignBadge( $rule , $message , $creatorId = null )
	{
		if( !$this->exists() )
		{
			return false;
		}

		$creator 	= Foundry::user( $creatorId );

		$badge 	= Foundry::badges();
		$state 	= $badge->log( 'com_easyblog' , $rule , $creator->id , $message );

		return $state;
	}


	/**
	 * Assign points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function assignPoints( $rule , $creatorId = null )
	{
		if (!$this->exists()) {
			return false;
		}

		$creator = FD::user( $creatorId );
		$points = FD::points();
		$state = $points->assign($rule, 'com_easyblog', $creator->id);

		return $state;
	}

	/**
	 * Creates a new stream for new blog post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createBlogStream($blog, $isNew)
	{
		if (!$this->exists()) {
			return false;
		}

		$postLib = EB::post($blog->id);
		$stream = FD::stream();
		$template = $stream->getTemplate();

		// If this is a feed source and the settings doesn't allow this, skip this
		if ($isNew && $postLib->isFromFeed() && !$this->config->get('integrations_easysocial_stream_newrss')) {
			return false;
		}

		// If this is a new post and settings is disabled
		if ($isNew && !$postLib->isFromFeed() && !$this->config->get('integrations_easysocial_stream_newpost')) {
			return false;
		}

		// If this is an edited post, ensure that settings allows this
		if (!$isNew && !$this->config->get('integrations_easysocial_stream_updatepost')) {
			return false;
		}

		// Get the stream template
		$template->setActor($blog->created_by, SOCIAL_TYPE_USER);
		$template->setContext($blog->id, 'blog');
		$template->setContent($blog->content);

		$esClusterType = array('event', 'group');

		// Determines if this post was contributed in a cluster
		$contribution = $blog->getBlogContribution();

		if ($contribution) {

			if (in_array($contribution->type, $esClusterType)) {
				$template->setCluster($contribution->id, $contribution->type);

			} else {
				// teamblog, jomosical.group, jomsocial.event

				$obj = new stdClass();
				$obj->utype = $contribution->type;
				$obj->uid = $contribution->id;

				$template->setParams($obj);
			}
		}

		$template->setVerb('create');

		if (!$isNew) {
			$template->setVerb('update');
		}

		// Determines if the blog post should be visible publicly
		$privacyVal = $blog->access ? '10': 0;
		$template->setAccess('easyblog.blog.view', $privacyVal);

		return $stream->add($template);
	}

	/**
	 * Creates a new stream for new blog post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createFeaturedBlogStream(EasyBlogPost $post)
	{
		// Check if integrations is enabled
		if (!$this->config->get('integrations_easysocial_stream_featurepost')) {
			return false;
		}

		// Check if easysocial exists on the site
		if (!$this->exists()) {
			return false;
		}

		$stream = FD::stream();
		$template = $stream->getTemplate();

		// Get the stream template
		$template->setActor($post->getAuthor()->id, SOCIAL_TYPE_USER);
		$template->setContext($post->id, 'blog');
		$template->setContent($post->getContent());
		$template->setTarget($post->getAuthor()->id);

		$template->setSiteWide();
		$template->setVerb('featured');

		// Determines if the blog post should be visible publicly
		$privacyVal = $post->access ? '10': 0;
		$template->setAccess('easyblog.blog.view', $privacyVal);

		return $stream->add($template);
	}

	/**
	 * Notify site subscribers whenever a new blog post is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifySubscribers(EasyBlogPost $blog, $action, $comment = null)
	{
		if (!$this->exists()) {
			return false;
		}

		// We don't want to notify via e-mail
		$emailOptions = false;
		$recipients = array();
		$rule = '';

		// Get the permalink of the post
		$permalink = $blog->getPermalink();

		// Get the blog image
		$image = $blog->getImage() ? $blog->getImage('frontpage') : '';

		// New post created on the site
		if ($action == 'new.post') {

			$rule = 'blog.create';
			$recipients = $blog->getRegisteredSubscribers('new', array($blog->created_by));
			$options = array(
				'uid' => $blog->id,
				'actor_id' => $blog->created_by,
				'title' => JText::sprintf('COM_EASYBLOG_EASYSOCIAL_NOTIFICATION_NEW_BLOG_POST', $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		// New comment posted on the site
		if ($action == 'new.comment') {

			if (!$this->config->get('integrations_easysocial_notifications_newcomment')) {
				return false;
			}

			$rule = 'blog.comment';

			// Get a list of recipients that we should notify
			$recipients = $comment->getSubscribers($blog, array($comment->created_by));
			$recipients = array_merge($recipients, array($blog->created_by));

			// Format the comment's content
			$content = $comment->getContent(true);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $comment->created_by,
				'title' => JText::sprintf('COM_EASYBLOG_EASYSOCIAL_NOTIFICATION_NEW_COMMENT_ON_THE_BLOG_POST', $content, $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		// New ratings added on the post
		if ($action == 'ratings.add' && $this->config->get('integrations_easysocial_notifications_ratings')) {

			$rule = 'blog.ratings';

			// @TODO: Perhaps notify everyone else that subscribed to this post?
			// Notify the blog author
			$recipients = array($blog->created_by);

			$options = array(
				'uid' => $blog->id,
				'actor_id' => $this->my->id,
				'title' => JText::sprintf('COM_EASYBLOG_EASYSOCIAL_NOTIFICATION_NEW_RATINGS_FOR_YOUR_BLOG_POST', $blog->title),
				'type' => 'blog',
				'url' => $permalink,
				'image' => $image
			);
		}

		if (!$rule) {
			return false;
		}

		// Send notifications to the receivers when they unlock the badge
		FD::notify($rule, $recipients, $emailOptions, $options);
	}


	/**
	 * Creates a new stream for new comments in EasyBlog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createCommentStream( $comment , $blog )
	{
		if( !$this->config->get( 'integrations_easysocial_stream_newcomment' )  )
		{
			return false;
		}

		if( !$this->exists() )
		{
			return false;
		}

		$stream 	= Foundry::stream();
		$template 	= $stream->getTemplate();

		// Get the stream template
		$template->setActor( $comment->created_by , SOCIAL_TYPE_USER );
		$template->setContext( $comment->id , 'blog' );
		$template->setContent( $comment->comment );

		$template->setVerb( 'create.comment' );
		$template->setAccess( 'easyblog.blog.view' );
		$state 	= $stream->add( $template );

		return $state;
	}

	/**
	 * Creates a new stream for new comments in EasyBlog
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addIndexerNewBlog( $blog )
	{
		if (!$this->exists() || !$this->config->get('integrations_easysocial_indexer_newpost')) {
			return false;
		}

		$indexer = FD::get('Indexer', 'com_easyblog');
		$template 	= $indexer->getTemplate();

		// getting the blog content
		$content 	= $blog->intro . $blog->content;


		$image 		= '';

		// @rule: Try to get the blog image.
		if( $blog->getImage() )
		{
			$image 	= $blog->getImage( 'thumbnail' );
		}

		if( empty( $image ) )
		{
			// @rule: Match images from blog post
			$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
			preg_match( $pattern , $content , $matches );

			$image		= '';

			if( $matches )
			{
				$image		= isset( $matches[1] ) ? $matches[1] : '';

				if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
				{
					$image	= rtrim(JURI::root(), '/') . '/' . ltrim( $image, '/');
				}
			}
		}

		if(! $image )
		{
			$image = rtrim( JURI::root() , '/' ) . '/components/com_easyblog/assets/images/default_facebook.png';
		}

		// @task: Strip out video tags
		$content		= EB::videos()->strip( $content );

		// @task: Strip out audio tags
		$content		= EasyBlogHelper::getHelper( 'Audio' )->strip( $content );

		// @task: Strip out gallery tags
		$content		= EasyBlogHelper::getHelper( 'Gallery' )->strip( $content );

		// @task: Strip out album tags
		$content		= EasyBlogHelper::getHelper( 'Album' )->strip( $content );

		// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
		// @2.0: <input class="easyblog-gallery"
		// @3.5: {ebgallery:'name'}
		$content		= EasyBlogHelper::removeGallery( $content );

		$content    = strip_tags( $content );

		if( JString::strlen( $content ) > $this->config->get( 'integrations_easysocial_indexer_newpost_length', 250 ) )
		{
			$content = JString::substr( $content, 0, $this->config->get( 'integrations_easysocial_indexer_newpost_length', 250 ) );
		}

		// lets include the title as the search snapshot.
		$content = $blog->title . ' ' . $content;
		$template->setContent( $blog->title, $content );

		$url	= EBR::_('index.php?option=com_easyblog&view=entry&id='.$blog->id);

		// Remove /administrator/ from the url.
		$url 	= JString::str_ireplace( 'administrator/' , '' , $url );

		$template->setSource($blog->id, 'blog', $blog->created_by, $url);

		$template->setThumbnail( $image );

		$template->setLastUpdate( $blog->modified );

		$state = $indexer->index( $template );
		return $state;
	}

	/**
	 * Removes a stream from JomSocial
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removePostStream(EasyBlogPost $post)
	{
		if (!$this->exists()) {
			return false;
		}

		return FD::stream()->delete($post->id, 'blog');
	}


	public function updateBlogPrivacy($blog)
	{
		if (!$this->exists() || !$this->config->get('integrations_easysocial_privacy')) {
			return false;
		}

		$privacyLib = Foundry::privacy( $blog->created_by, SOCIAL_PRIVACY_TYPE_USER);
		$privacyLib->add( 'easyblog.blog.view', $blog->id, 'blog', $blog->access);
	}

	/**
	 * Builds the sql query for privacy
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function buildPrivacyQuery($alias = 'a', $includeAnd = true)
	{
		if (!$this->exists()) {
			return;
		}

		if (!$this->config->get('integrations_easysocial_privacy')) {
			return;
		}

		$db = EB::db();
		$my = JFactory::getUser();
		$config	= EB::config();

		$my = JFactory::getUser();
		$esFriends = Foundry::model( 'Friends' );

		$friends = $esFriends->getFriends( $my->id, array( 'idonly' => true ) );

		if ($friends) {
			array_push($friends, $my->id);
		}

		// Set the alias for this query
		$alias = $alias . '.';

		// Determines if we should prepend the and in front of the query
		$queryWhere = '(';

		if ($includeAnd) {
			$queryWhere	= ' AND (';	
		}
		
		$queryWhere	.= ' ( ' . $alias . '`access`= 0 ) OR';
		$queryWhere	.= ' ( (' . $alias . '`access` = 10) AND (' . $db->Quote( $my->id ) . ' > 0 ) ) OR';

		if (!$friends) {
			$queryWhere	.= ' ( ( ' . $alias . '`access` = 30 ) AND ( 1 = 2 ) ) OR';
		} else {
			$queryWhere	.= ' ( ( ' . $alias . '`access` = 30) AND ( ' . $alias . $db->qn( 'created_by' ) . ' IN (' . implode( ',' , $friends ) . ') ) ) OR';
		}

		$queryWhere	.= ' ( (' . $alias . '`access` = 40) AND ( '. $alias . $db->qn( 'created_by' ) .'=' . $my->id . ') )';
		$queryWhere	.= ' )';

		return $queryWhere;
	}

	/**
	 * Prepares the stream for EasySocial
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareStream(SocialStreamItem &$item, $appParams)
	{
		if ($item->verb == 'create') {
			return $this->prepareNewBlogStream($item, $appParams);
		}
	}


	/**
	 * Displays the stream item for new blog post
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function prepareNewBlogStream(SocialStreamItem &$item, $appParams)
	{
		// Load the post
		$post = EB::post($item->contextId);

		// Format the likes for the stream
		$likes = Foundry::likes();
		$likes->get($item->contextId, 'blog', 'create');
		$item->likes = $likes;

		// Apply comments on the stream
		$url = $post->getExternalPermalink();
		$item->comments = Foundry::comments($item->contextId, 'blog', 'create', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		// We might want to use some javascript codes.
		EB::init('site');

		// Get the creation date
		$date = $post->getCreationDate();

		$config = EB::config();
		$source = $config->get('integrations_easysocial_stream_newpost_source', 'intro');

		$content = isset($post->$source) && !empty($post->$source)? $post->$source : $post->intro;
		$content = $this->truncateContent($content, $appParams);

		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$video = $this->getVideo($content);

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		// Get the permalink of the primary category
		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id, true, null, false, true );

		// Get the alignment of the image
		$alignment = 'pull-' . $appParams->get('imagealignment', 'right');

		$theme = Foundry::themes();

		$theme->set('alignment', $alignment);
		$theme->set('video', $video);
		$theme->set('audios', $audios);
		$theme->set('date', $date);
		$theme->set('permalink', $url);
		$theme->set('blog', $post);
		$theme->set('actor', $item->actor);
		$theme->set('content', $content);
		$theme->set('categorypermalink', $catUrl);

		$item->title = $theme->output('easysocial/streams/' . $item->verb . '.title');
		$item->content = $theme->output('easysocial/streams/' . $item->verb . '.content');

		// Add opengraph tags for the stream item
		$item->opengraph->addImage($post->getImage('thumbnail'));
		$item->opengraph->addDescription($content);
	}

	private function prepareFeaturedBlogStream(&$item)
	{
		$post 	= EB::post($item->contextId);

		// Format the likes for the stream
		$likes = Foundry::likes();
		$likes->get($item->contextId, 'blog', 'featured');
		$item->likes = $likes;

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id, true, null, false, true);

		$item->comments = Foundry::comments($item->contextId, 'blog', 'featured', SOCIAL_APPS_GROUP_USER , array('url' => $url));

		$date = EB::date($post->created);

		$config = EB::config();
		$source = $config->get('integrations_easysocial_stream_newpost_source', 'intro');

		$content = isset($post->$source) && !empty($post->$source) ? $post->$source : $post->intro;
		$content = $this->truncateContent($content);

		$appParams = $this->getParams();
		$alignment = 'pull-' . $appParams->get('imagealignment', 'right');
		$this->set('alignment' , $alignment);

		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$video = $this->getVideo($content);

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');

		// Add image to the og:image
		if ($post->getImage()) {
			$item->opengraph->addImage($post->getImage('frontpage'));
		}

		$item->opengraph->addDescription($content);

	}

	private function prepareUpdateBlogStream(&$item)
	{
		$post = EB::post($item->contextId);

		// Format the likes for the stream
		$likes = Foundry::likes();
		$likes->get($item->contextId, 'blog', 'update');
		$item->likes = $likes;

		$url = EBR::_( 'index.php?option=com_easyblog&view=entry&id=' . $post->id );

		// Apply comments on the stream
		$item->comments = Foundry::comments($item->contextId, 'blog', 'update', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		// We might want to use some javascript codes.
		EB::init('site');

		$date = EB::date($post->created);

		$config = EB::config();
		$source = $config->get('integrations_easysocial_stream_newpost_source', 'intro');

		$content = isset($post->$source) && !empty($post->$source)? $post->$source : $post->intro;
		$content = $this->truncateContent($content);

		$appParams = $this->getParams();
		$alignment = 'pull-' . $appParams->get('imagealignment', 'right');
		$this->set('alignment', $alignment);

		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$video = $this->getVideo($content);

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');

		// Add image to the og:image
		if ($post->getImage()) {
			$item->opengraph->addImage($post->getImage('frontpage'));
		}

		$item->opengraph->addDescription($content);
	}


	private function prepareNewCommentStream(&$item)
	{
		$comment = EB::table('Comment');
		$comment->load($item->contextId);

		// Format the likes for the stream
		$likes = Foundry::likes();
		$likes->get($comment->id, 'blog', 'comments');
		$item->likes = $likes;

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $comment->post_id, true, null, false, true );

		// Apply comments on the stream
		$item->comments = Foundry::comments($item->contextId, 'blog', 'comments', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		$post = EB::post($comment->post_id);

		$date = EB::date($post->created);

		// Parse the bbcode from EasyBlog
		$comment->comment = EB::comment()->parseBBCode($comment->comment);

		$this->set('comment', $comment);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $post);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');

		$item->opengraph->addDescription($comment->comment);
	}

	public function truncateContent($content, $appParams)
	{
		// Get the app params
		static $maxLength = null;

		if (is_null($maxLength)) {
			$maxLength = $appParams->get('maxlength', 0);
		}

		if ($maxLength) {

			$truncateType = $appParams->get('truncation', 'chars');

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = JString::str_ireplace('&nbsp;', '', $content);
			$content = strip_tags($content);

			// Remove blank spaces since the word calculation should not include new lines or blanks.
			$content = trim($content);

			// @task: Let's truncate the content now.
			switch($truncateType) {
				case 'words':

					$tag = false;
					$count = 0;
					$output = '';

					$chunks = preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

					foreach($chunks as $piece) {

						if (!$tag || stripos($piece, '>') !== false) {
							$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
						}

						if (!$tag && trim($piece) == '') {
							$count++;
						}

						if ($count > $maxLength && !$tag) {
							break;
						}

						$output .= $piece;
					}

					unset($chunks);
					$content = $output;

					break;
				case 'chars':
				default:
					$content = JString::substr($content, 0, $maxLength);
					break;
			}
		}

		return $content;
	}

	private function prepareContent(&$content)
	{
		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$videos = $this->getVideos($content);
		$video = false;

		if (isset($videos[0])) {
			$video = $videos[0];
		}

		// Remove videos from the source
		$content = EB::videos()->strip( $content );

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $blog);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $blog->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');
	}

	private function getVideo($content)
	{
		$videos = EB::videos()->getVideoObjects($content);

		if (isset($videos[0])) {
			return $videos[0];
		}

		return false;
	}
}
