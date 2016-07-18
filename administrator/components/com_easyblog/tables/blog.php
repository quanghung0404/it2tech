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

require_once(__DIR__ . '/table.php');

class EasyBlogTableBlog extends EasyBlogTable
{
	public $id = null;
	public $created_by = null;
	public $modified = null;
	public $created = null;
	public $publish_up = null;
	public $publish_down = null;
	public $title = null;
	public $permalink = null;
	public $intro = null;
	public $content = null;
	public $document = null;
	public $category_id = null;
	public $published = null;
	public $state = null;
	public $ordering = null;
	public $vote = null;
	public $hits = null;
	public $access = null;
	public $allowcomment = null;
	public $subscription = null;
	public $frontpage = null;
	public $isnew = null;
	public $blogpassword = null;
	public $latitude = null;
	public $longitude = null;
	public $address = null;
	public $posttype = null;
	public $robots = null;
	public $copyrights = null;
	public $image = null;
	public $language = null;
	public $send_notification_emails = null;
	public $locked = false;
	public $ip = null;
	public $doctype = null;
	public $_checkLength = true;
	public $revision_id = null;

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_post', 'id', $db);
	}

	/**
	 * Loads a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load($id=null, $reset=true)
	{
		// Remove this once we've identified who is calling this.
		if (func_num_args() > 1) {
			var_dump("Deprecated: Use loadByPermalink() instead.");
			dump(debug_backtrace());
			exit;
		}

		// Load post from post table
		$state = parent::load($id);

		// Posts without doctypes are legacy posts.
		if (is_null($this->doctype)) {
			$this->doctype = 'legacy';
		}

		return $state;
	}

	/**
	 * Given a permalink, find the post id and load the post.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadByPermalink($permalink)
	{
		$db = EB::db();

		// Try to look for the permalink
		$query = 'SELECT a.`id` FROM ' . $this->_tbl . ' as a '
		       . 'WHERE a.`permalink` = ' . $db->Quote($permalink);
		$db->setQuery($query);
		$id = $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if (!$id) {
			$permalink = JString::str_ireplace(':', '-', $permalink);
			$query = 'SELECT a.`id` FROM ' . $this->_tbl . ' as a '
				   . 'WHERE a.`permalink` = ' . $db->Quote($permalink);
			$db->setQuery($query);
			$id = $db->loadResult();
		}

		return parent::load($id);
	}


	public function getMetaId()
	{
		$db = $this->_db;

		$query  = 'SELECT a.`id` FROM `#__easyblog_meta` AS a';
		$query  .= ' WHERE a.`content_id` = ' . $db->Quote($this->id);
		$query  .= ' AND a.`type` = ' . $db->Quote( 'post' );

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Stores the blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($log = true)
	{
		// Load language file from the front end.
		EB::loadLanguages();

		// Whenever the blog post is stored, we need to clear the cache.
		$cache = EB::getCache();
		$cache->clean('com_easyblog');
		$cache->clean('_system');
		$cache->clean('page');

		// Get easyblog's config
		$config = EB::config();

		// Get the current logged in user.
		$my = JFactory::getUser();

		// @rule: no guest allowed to create blog post.
		if( JRequest::getVar('task', '') != 'cron' && JRequest::getVar('task', '') != 'cronfeed' && empty( $my->id ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_YOU_ARE_NOT_LOGIN' ) );
			return false;
		}

		$under_approval = false;
		if( isset( $this->under_approval ) )
		{
			$under_approval = true;

			// now we need to reset this variable from the blog object.
			unset($this->under_approval);
		}

		// @trigger: onBeforeSave
		$this->triggerBeforeSave();

		// @rule: Determine if this record is new or not.
		if( empty( $this->isnew ) )
			$isNew  		= ( empty( $this->id) ) ? true : false;
		else
			$isNew          = true;

		// @rule: Get the rulesets for this user.
		$acl 	= EB::acl();

		// @rule: Process badword filters for title here.
		$blockedWord 	= EasyBlogHelper::getHelper( 'String' )->hasBlockedWords( $this->title );
		if( $blockedWord !== false )
		{
			$this->setError( JText::sprintf( 'COM_EASYBLOG_BLOG_TITLE_CONTAIN_BLOCKED_WORDS' , $blockedWord ) );
			return false;
		}

		// @rule: Check for minimum words in the content if required.
		if( $config->get( 'main_post_min' ) && $this->_checkLength )
		{
			$minimum	= $config->get( 'main_post_length' );
			$total 		= JString::strlen( strip_tags( $this->intro . $this->content ) );

			if( $total < $minimum )
			{
				$this->setError( JText::sprintf( 'COM_EASYBLOG_CONTENT_LESS_THAN_MIN_LENGTH' , $minimum) );
				return false;
			}
		}

		// @rule: Check for invalid title
		if( empty($this->title) || $this->title == JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE' ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR' ) );
			return false;
		}

		// @rule: For edited blogs, ensure that they have permissions to edit it.
		if (!$isNew && $this->created_by != JFactory::getUser()->id && !EasyBlogHelper::isSiteAdmin() && !$acl->get('moderate_entry')) {

			// @task: Only throw error when this blog post is not a team blog post and it's not owned by the current logged in user.
			$model 			= EB::model( 'TeamBlogs' );
			$contribution	= $model->getBlogContributed( $this->id );

			if( !$contribution || !$model->checkIsTeamAdmin( JFactory::getUser()->id , $contribution->team_id ) )
			{
				$this->setError( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_EDIT_BLOG' ) );
				return false;
			}
		}

		// Filter / strip contents that are not allowed
		$filterTags 		= EasyBlogHelper::getHelper( 'Acl' )->getFilterTags();
		$filterAttributes	= EasyBlogHelper::getHelper( 'Acl' )->getFilterAttributes();

		// @rule: Apply filtering on contents
		jimport('joomla.filter.filterinput');
		$inputFilter 					= JFilterInput::getInstance( $filterTags , $filterAttributes , 1 , 1 , 0 );
		$inputFilter->tagBlacklist		= $filterTags;
		$inputFilter->attrBlacklist		= $filterAttributes;
		$filterTpe                      = ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? 'html' : 'string';

		if( ( count($filterTags) > 0 && !empty($filterTags[0]) ) || ( count($filterAttributes) > 0 && !empty($filterAttributes[0]) ) )
		{
			$this->intro 					= $inputFilter->clean( $this->intro, $filterTpe );
			$this->content 					= $inputFilter->clean( $this->content, $filterTpe );
		}

		// @rule: Process badword filters for content here.
		$blockedWord 	= EasyBlogHelper::getHelper( 'String' )->hasBlockedWords( $this->intro . $this->content );
		if( $blockedWord !== false )
		{
			$this->setError( JText::sprintf( 'COM_EASYBLOG_BLOG_POST_CONTAIN_BLOCKED_WORDS' , $blockedWord ) );
			return false;
		}

		// @rule: Test for the empty-ness
		if( empty( $this->intro ) && empty( $this->content ) )
		{
			$this->setError( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_CONTENT_ERROR' ) );
		}

		$state  	= parent::store();
		$source		= JRequest::getVar( 'blog_contribute_source' , 'easyblog' );

		// if this is blog edit, then we should see the column isnew to determine
		// whether the post is really new or not.
		if( !$isNew )
		{
			$isNew 	= $this->isnew;
		}

		// this one is needed for the trigger to work properly.
		$this->isnew    = $isNew;

		// @trigger: onAfterSave
		$this->triggerAfterSave();

		// @task: If auto featured is enabled, we need to feature the blog post automatically since the blogger is featured.
		if( $config->get('main_autofeatured', 0) && EB::isFeatured( 'blogger' , $this->created_by) && !EB::isFeatured( 'post' , $this->id) )
		{
			// just call the model file will do as we do not want to create stream on featured action at this migration.
			$modelF = EB::model('Featured');
			$modelF->makeFeatured('post', $this->id);
		}

		// @task: This is when the blog is either created or updated.
		if( $source == 'easyblog' && $state && $this->published == EASYBLOG_POST_PUBLISHED && $log )
		{
			$category = EB::table('Category');
			$category->load( $this->category_id );

			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

			if( $category->private == 0 )
			{
				// @rule: Add new stream item in jomsocial
				EB::jomsocial()->addBlogActivity($this, $isNew);
			}

			// @rule: Add stream for easysocial
			if( $config->get( 'integrations_easysocial_stream_newpost' ) && $easysocial->exists() && $isNew )
			{
				$easysocial->createBlogStream( $this , $isNew );
			}

			// update privacy in easysocial.
			if( $config->get( 'integrations_easysocial_privacy' ) && $easysocial->exists() )
			{
				$easysocial->updateBlogPrivacy( $this );
			}
		}



		if( $source == 'easyblog' && $state && $this->published == EASYBLOG_POST_PUBLISHED && $isNew && $log )
		{
			// @rule: Send email notifications out to subscribers.
			$author = EB::user($this->created_by);

			// Ping pingomatic
			EB::pingomatic()->ping($this);

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

			if ($easysocial->exists())  {
				$easysocial->assignPoints( 'blog.create' , $this->created_by );
			}

			// @rule: Add userpoints for jomsocial
			if( $config->get( 'main_jomsocial_userpoint' ) )
			{
				$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';
				if( JFile::exists( $path ) )
				{
					require_once( $path );
					CUserPoints::assignPoint( 'com_easyblog.blog.add' , $this->created_by );
				}
			}

			$link	= $this->getExternalBlogLink( 'index.php?option=com_easyblog&view=entry&id='. $this->id );

			// @rule: Add notifications for jomsocial 2.6
			if( $config->get( 'integrations_jomsocial_notification_blog' ) )
			{
				// Get list of users who subscribed to this blog.
				$target	= $this->getRegisteredSubscribers( 'new' , array( $this->created_by ) );
				EasyBlogHelper::getHelper( 'JomSocial' )->addNotification( JText::sprintf( 'COM_EASYBLOG_JOMSOCIAL_NOTIFICATIONS_NEW_BLOG' , str_replace("administrator/","", $author->getProfileLink()) , $author->getName() , $link, $this->title ) , 'easyblog_new_blog' , $target , $author , $link );
			}

			// Get list of users who subscribed to this blog.

			// @rule: Add notifications for easysocial
			if( $config->get( 'integrations_easysocial_notifications_newpost' ) && $easysocial->exists() )
			{
				$easysocial->notifySubscribers( $this , 'new.post' );
			}



			// @rule: Add indexer for easysocial
			if( $config->get( 'integrations_easysocial_indexer_newpost' ) && $easysocial->exists() )
			{
				$easysocial->addIndexerNewBlog( $this );
			}

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.blog' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_BLOG' , $this->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.blog' , $this->created_by );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.blog' , $this->created_by );

			// Assign badge for users that report blog post.
			// Only give points if the viewer is viewing another person's blog post.
			EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'blog.create' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_BLOG_POST' ) );

			if( $config->get( 'integrations_easydiscuss_notification_blog' ) )
			{
				// Get list of users who subscribed to this blog.
				$target	= $this->getRegisteredSubscribers( 'new' , array( $this->created_by ) );

				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addNotification( $this ,
									JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_BLOG' , $author->getName() , $this->title) ,
									EBLOG_NOTIFICATIONS_TYPE_BLOG ,
									$target ,
									$this->created_by,
									$link );
			}

			// AUP
			EB::aup()->assignPoints('plgaup_easyblog_add_blog', $this->created_by, JText::sprintf('COM_EASYBLOG_AUP_NEW_BLOG_CREATED', $this->getPermalink(), $this->title));

			// Update the isnew column so that if user edits this entry again, it doesn't send any notifications the second time.
			$this->isnew 	= ( $this->published && $this->isnew ) ? 0 : 1;
			$this->store( false );

		}

		return $state;
	}

	public function createMeta( $key , $desc )
	{
		$id		= $this->getMetaId();

		// @rule: Save meta tags for this entry.
		$meta		= EB::table('Meta');
		$meta->load( $id );

		$meta->set( 'keywords'		, $key );
		$meta->set( 'description'	, $desc );
		$meta->set( 'content_id'	, $this->id );
		$meta->set( 'type'			, META_TYPE_POST );
		$meta->store();
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink()
	{
		static $link 	= null;

		if (is_null($link)) {

			// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
			$sh404 = EasyBlogRouter::isSh404Enabled();

			$link = EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $this->id, true, true);

			$app = JFactory::getApplication();

			// If this is being submitted from the back end we do not want to use the sef links because the URL will be invalid
			if ($app->isAdmin() && $sh404) {
				$link = rtrim( JURI::root() , '/' ) . $link;
			}

		}

		return $link;
	}

	public function getExternalBlogLink( $url )
	{
		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		$link			= EasyBlogRouter::getRoutedURL( $url , false, true );

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$link	= rtrim( JURI::root() , '/' ) . $url;
		}

		return $link;
	}

	/**
	 * Retrieves asset associated with the post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAsset()
	{
		$asset 	= EB::table('BlogAsset');
		$asset->loadByPost( $this->id );

		return $asset;
	}

	public function getQuote()
	{
		// Quotes are stored as the title.
		return $this->title;
	}

	public function getVideo()
	{
		$asset	= $this->getAsset();
		$video	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $video;
	}

	public function getPhoto()
	{
		$asset	= $this->getAsset();
		$photo	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $photo;
	}

	public function getLink()
	{
		$asset	= $this->getAsset();
		$url	= $asset->get( 'value' );

		// @TODO: Video manipulation

		return $url;
	}

	public function bindText()
	{

	}

	/**
	 * Associates this blog post with a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processCategories($categories, $primaryCategory)
	{
		// @rule: If this item is still dirty (uncleaned), we do not want to allow anything to pass through.
		if (!$this->id || is_null($this->id)) {
			return false;
		}

		// Ensure that the caller is passing us a list of categories
		if (empty($categories)) {
			return false;
		}

		// Get the acl for this current user.
		$acl = EB::acl();

		// Delete any categories associated with this blog post
		$model = EB::model('Categories');
		$model->deleteAssociation($this->id);

		foreach ($categories as $categoryId) {
			$categoryId = (int) $categoryId;

			$table = EB::table('PostCategory');
			$table->post_id = $this->id;
			$table->category_id = $categoryId;
			$table->primary = $categoryId == $primaryCategory;

			$table->store();
		}
	}

	/**
	 * Processes a list of tags to be associated with the post.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function processTags($tags, $bindDefaultTags = true)
	{
		// If this item is still dirty (uncleaned), we do not want to allow anything to pass through.
		if (!$this->id || is_null($this->id)) {
			return false;
		}

		// If there's no tags, skip this
		if (!$tags) {
			return false;
		}

		// Get the rulesets for this user.
		$acl = EB::acl();

		// Delete existing associated tags.
		$tagModel = EB::model('Tags');
		$postTagModel = EB::model('PostTag');

		// Delete related tags with this post first.
		$postTagModel->deletePostTag($this->id);

		// Cleanup whitespaces from the tags
		foreach ($tags as &$tag) {
			$tag = trim($tag);
		}

		// These are the tags that is configured to be included in the posts.
		if ($bindDefaultTags) {

			$defaultTags = $tagModel->getDefaultTagsTitle();

			if (!empty($defaultTags)) {

				$date = EB::date();

				// Associate the default tags with this blog item.
				foreach ($defaultTags as $title) {

					$tags[] = $title;
				}
			}
		}

		// Cleanup the unique tags
		$tags = array_unique($tags);

		// @rule: Process user defined tags now
		if (!empty($tags)) {

			// What if the default tag already included this?
			foreach ($tags as $title) {

				if (!$title) {
					continue;
				}

				// Load the tag table
				$table = EB::table('Tag');
				$exists = $table->load($title, true);

				// Skip this tag if the user is not allowed to create tags and tag does not exist on site.
				if (!$exists && !$acl->get('create_tag')) {
					continue;
				}

				if (!$exists) {
					$table->created_by = $this->created_by;
					$table->title = $title;
					$table->created = EB::date()->toSql();
					$table->published = true;
					$table->status = '';
					$table->store();
				}

				// Add the association of tags here.
				$postTagModel->add($table->id, $this->id, EB::date()->toSql());
			}
		}
	}

	/**
	 * Sets the primary category for the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setPrimaryCategory($id)
	{
		return $this->processCategories(array($id), $id);
	}

	/**
	 * Method to store tags for a blog post.
	 *
	 * @access	private
	 * @param	TableBlog	$blog 	The blog's database row.
	 */
	public function saveTags($tags)
	{
		// If there's no tags, just skip the whole block
		if( !$tags )
		{
			return false;
		}

		$config 	= EasyBlogHelper::getConfig();
		$acl 		= EB::acl();

		// @rule: Needed to add points for each tag creation
		if( $config->get( 'main_jomsocial_userpoint' ) )
		{
			$path	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'userpoints.php';

			if( JFile::exists( $path ) )
			{
				require_once( $path );
			}
		}

		if( !is_array( $tags ) )
		{
			$tags 	= array( $tags );
		}

		$model 		= EB::model( 'PostTag' );

		foreach( $tags as $title )
		{
			// Skip this if the tag is invalid.
			if( empty( $title ) )
			{
				continue;
			}

			$tag	= EB::table('Tag');
			$tag->load( $title , true );

			if (!$tag->exists( $title ) && $acl->get('create_tag')) {
				$tag->created_by 	= JFactory::getUser()->id;
				$tag->title 		= $title;
				$tag->created 		= EB::date()->toMySQL();
				$tag->store();
			}

			// Add the association for the tag.
			$model->add( $tag->id , $blog->id , EB::date()->toMySQL() );
		}

		return true;
	}

	/**
	 * Allows caller to explicitly auto post blog posts into social sites
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function autopost($sites = array())
	{
		// Ensure that the sites are not empty
		if (!$sites) {
			return false;
		}

		// Get EasyBlog's configuration
		$config = EB::config();

		// should add chechkfunction for multiple category here
		$category = EB::table('Category');
		$category->load($this->getPrimaryCategory());

		if (!$category->autopost) {
			return;
		}

		// These are the allowed auto posting sites
		$allowed = array(EBLOG_OAUTH_LINKEDIN, EBLOG_OAUTH_FACEBOOK, EBLOG_OAUTH_TWITTER);

		foreach ($sites as $site) {

			// Skip this if the site is not known
			if (!in_array($site, $allowed)) {
				continue;
			}

			// Process the centralize site options first
			// EB::oauth()->share($this, $site, true);

			// Process the user's options since users can also setup their own auto posting
			EB::oauth()->share($this, $site, false);
		}
	}

	/**
	 * This method invokes the trigger for events before save
	 *
	 */
	public function triggerBeforeSave()
	{
		JPluginHelper::importPlugin( 'content');
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		// @task: Try to mimic Joomla's com_content behavior.
		require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_content' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'route.php' );

		// @trigger: onBeforeEasyBlogSave trigger
		$this->introtext	= '';
		$this->text			= '';
		$dispatcher->trigger( 'onBeforeEasyBlogSave' , array( &$this , $this->isNew() ) );

		// @trigger: onBeforeContentSave trigger
		// @rule: Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext	= $this->intro;
		$this->text			= $this->content;

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$dispatcher->trigger('onContentBeforeSave', array('easyblog.blog', &$this, $this->isNew() ));
		}
		else
		{
			$dispatcher->trigger('onBeforeContentSave', array(&$this, $this->isNew() ));
		}

		// @rule: Since content plugins uses introtext and text columns, we'll just need to retrieve the values after it has been modified.
		$this->intro		= $this->introtext;
		$this->content		= $this->text;

		// @rule: Remove these properties after all process.
		unset( $this->introtext );
		unset( $this->text );
	}

	/**
	 * Retrieves a list of custom fields associated to this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFields()
	{
		$categories = $this->getCategories();

		if (!$categories) {
			return false;
		}

		$fields = array();
		$hasFields = false;

		foreach ($categories as $category) {
			$categoryFields = $category->getCustomFields();

			if ($categoryFields !== false) {
				$fields[] = $categoryFields;
				$hasFields = true;
			}
		}

		if (!$hasFields) {
			return false;
		}

		return $fields;
	}

	/**
	 * This method invokes the trigger for events after the blog is saved
	 *
	 */
	public function triggerAfterSave()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		// @trigger: onAfterEasyBlogSave
		$this->introtext	= '';
		$this->text			= '';
		$dispatcher->trigger( 'onAfterEasyBlogSave' , array(&$this, $this->isNew() ) );

		// @trigger: onAfterContentSave
		// @rule: Since content plugins uses introtext and text columns, we'll just need to mimic the introtext and text columns.
		$this->introtext	= $this->intro;
		$this->text			= $this->content;

		if( EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$dispatcher->trigger( 'onContentAfterSave', array( 'easyblog.blog', &$this, $this->isNew() ) );
		}
		else
		{
			$dispatcher->trigger( 'onAfterContentSave', array( &$this , $this->isNew() ) );
		}

		// @rule: Since content plugins uses introtext and text columns, we'll just need to retrieve the values after it has been modified.
		$this->intro		= $this->introtext;
		$this->content		= $this->text;

		// @rule: Remove these properties after all process.
		unset( $this->introtext );
		unset( $this->text );
	}

	/**
	 * Retrieves the blog image for this blog post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getImage($size = 'original')
	{
		static $cache	= array();

		$index = $this->id . '-' . $size;

		// Default blog image
		$default = rtrim(JURI::root(), '/') . '/components/com_easyblog/themes/wireframe/images/placeholder-image.png';

		if (!isset($cache[$index])) {

			// If there's no image data for this post, skip this altogether
			if (!$this->image) {
				$cache[$index] = $default;

				return $cache[$index];
			}

			// Get the json object from the image property
			$data = json_decode($this->image);

			// If the json data is corrupted, just load the default
			if (!$data) {
				$cache[$index] = $default;

				return $cache[$index];
			}

			// Get the configuration object.
			$config	= EB::config();

			// Let's see where should we find for this.
			$path = '';
			$url = '';

			// If the place is on shared, we need to get the appropriate paths
			if (isset($data->place) && $data->place == 'shared') {
				$shared	= trim(str_ireplace('\\', '/', $config->get('main_shared_path')), '/\\');
				$path = JPATH_ROOT . '/' . $shared;
				$url = rtrim(JURI::root(), '/') . '/' . $shared;
			}

			// If the place is users folder, get the appropraite paths
			if ($data->place != 'shared') {
				$place = explode(':', $data->place);
				$img = trim($config->get('main_image_path'), '/\\');

				$path = JPATH_ROOT . '/' . $img . '/' . $place[1];
				$url = rtrim(JURI::root(), '/') . '/' . $img . '/' . $place[1];
			}

			// Ensure that the item really exist before even going to do anything on the original image.
			// If the image was manually removed from FTP or any file explorer, this shouldn't yield any errors.
			$exists = JFile::exists($path . '/' . $data->path);

			// If the blog image file doesn't exist, we use the default
			if (!$exists) {
				$cache[$index] = $default;

				return $cache[$index];
			}

			$image = EB::blogimage($data->path, $path, $url);

			$cache[$index] = $image->getSource($size);
		}

		return $cache[$index];
	}

	/**
	 * Get total number of comments for this blog post
	 *
	 * @access	public
	 * @param	null
	 * @return	int
	 */
	public function getTotalComments()
	{
		static $comments = array();

		if (!isset($comments[$this->id])) {

			$count 	= EB::comment()->getCommentCount($this);

			$comments[$this->id]	= $count;
		}

		return $comments[$this->id];
	}

	/**
	 * Get a list of tag objects that are associated with this blog post.
	 *
	 * @access	public
	 * @param	null
	 * @return	Array	An Array of TableTag objects.
	 */
	public function getTags()
	{
		static $instances = array();

		if (!isset($instances[$this->id])) {

			$model 	= EB::model('PostTag');
			$tags = $model->getBlogTags($this->id);

			$instances[$this->id]	= $tags;
		}

		return $instances[$this->id];
	}

	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function unlock()
	{
		$this->locked 	= false;

		$this->store(false);
	}

	/**
	 * Allows caller to lock a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function lock()
	{
		$this->locked 	= true;

		$this->store(false);
	}

	/**
	 * Initializes the header of the html page
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderHeaders()
	{
		$doc = JFactory::getDocument();

		// Set meta tags for post
		EB::setMeta($this->id, META_TYPE_POST);

		// If there's robots set on the page, initialize it
		if ($this->robots) {
			$doc->setMetaData('robots', $this->robots);
		}

		// If there's a copyright notice, add it into the header
		if ($this->copyrights) {
			$doc->setMetaData('rights', $this->copyrights);
		}

		// Determines if the user wants to print this page
		$print = JFactory::getApplication()->input->get('print', 0, 'int');

		// Add noindex for print view by default.
		if ($print) {
			$doc->setMetadata('robots', 'noindex,follow');
		}

		$config = EB::config();
		$title 	= EB::getPageTitle($config->get('main_title'));

		if (empty($title)) {
			$doc->setTitle($this->title);
		} else {
			$doc->setTitle($this->title . ' - ' . $title );
		}
	}

	/**
	 * Prepares the content before displaying it out.
	 *
	 * @since	4.0
	 * @access	public
	 * @return
	 */
	public function prepareContent()
	{
		// Get the application
		$app = JFactory::getApplication();

		// Load up Joomla's dispatcher
		$dispatcher	= JDispatcher::getInstance();

		// @trigger: onEasyBlogPrepareContent
		EB::triggers()->trigger('easyblog.prepareContent', $this);

		// Load our own content
		$config = EB::config();

		// Because joomla plugins are looking at the following properties, we need to mimic this
		// ->introtext
		// ->text
		$this->introtext = $this->intro;
		$this->text = $this->intro . $this->content;

		// @trigger: onEasyBlogPrepareContent
		EB::triggers()->trigger('prepareContent', $this);

		// For additional joomla content triggers, we need to store the output in various "sections"
		//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
		$this->event		= new stdClass();

		// @trigger: onAfterDisplayTitle / onContentAfterTitle
		$results	= EB::triggers()->trigger('afterDisplayTitle', $this);
		$this->event->afterDisplayTitle = JString::trim(implode("\n", $results));

		// @trigger: onBeforeDisplayContent / onContentBeforeDisplay
		$results	= EB::triggers()->trigger('beforeDisplayContent', $this);
		$this->event->beforeDisplayContent = JString::trim(implode("\n", $results));

		// @trigger: onAfterDisplayContent / onContentAfterDisplay
		$results	= EB::triggers()->trigger('afterDisplayContent', $this);
		$this->event->afterDisplayContent	= JString::trim(implode("\n", $results));

		// Once the trigger is completed, we will need to re-assign the content back
		$this->intro		= $this->introtext;
		$this->content		= $this->text;

		// If necessary, add nofollow to the anchor links of the blog post
		if ($config->get('main_anchor_nofollow')) {
			$this->content 	= EasyBlogHelper::addNoFollow($this->content);
		}


		// Once the whole fiasco of setting the attributes back and forth is done, unset unecessary attributes.
		// unset($this->introtext);
		// unset($this->text);
	}

	/**
	 * Verifies the password of the blog
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function verifyPassword()
	{
		$session 	= JFactory::getSession();
		$password 	= $session->get('PROTECTEDBLOG_' . $this->id, '', 'EASYBLOG');

		if ($password == $this->blogpassword) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves assets associated with the blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAssets()
	{
		$model = EB::model('Assets', true);
		$assets = $model->getPostAssets($this->id);

		return $assets;
	}

	/**
	 * Retrieves the category name for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategory()
	{
		static $loaded	= array();

		if (!isset($loaded[$this->category_id])) {
			$category 	= EB::table('Category');
			$category->load($this->category_id);

			$loaded[$this->category_id]	= $category;
		}

		return $loaded[$this->category_id];
	}

	/**
	 * Determines if a blog post has a location value
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasLocation()
	{
		if ($this->address && $this->latitude && $this->longitude) {
			return true;
		}

		return false;
	}

	public function isPublished()
	{
		// If the blog states falls under either of this, skip this
		$disallowed 	= array(EASYBLOG_POST_UNPUBLISHED, EASYBLOG_POST_SCHEDULED, EASYBLOG_POST_DRAFT, EASYBLOG_POST_TRASHED);

		if (!in_array($this->published, $disallowed)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines whether the current blog is accessible to
	 * the current browser.
	 *
	 * @param	JUser	$my		Optional user object.
	 * @return	boolean		True if accessible and false otherwise.
	 **/
	public function isAccessible()
	{
		$allowed = EB::privacy()->checkPrivacy($this);

		if (!$allowed->allowed) {
			return $allowed;
		}


		// Check against the primary category permissions
		$category = $this->getPrimaryCategory();

		if ($category->private != 0) {
			$allowed = $category->checkPrivacy();
		}

		return $allowed;
	}

	/**
	 * Determines whether the current blog is featured or not.
	 *
	 * @since	4.0
	 * @return	boolean		True if featured false otherwise
	 **/
	public function isFeatured()
	{
		if (!$this->id) {
			return false;
		}

		static $featured	= array();

		if (!isset($featured[$this->id])) {

			$model 		= EB::model('Blog');
			$isFeatured	= $model->isFeatured($this->id);

			$featured[$this->id]	= $isFeatured;
		}

		return $featured[$this->id];
	}

	/**
	 * Determines if the blog post is password protected to the current viewer
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isPasswordProtected()
	{
		$config = EB::config();


		if($config->get('main_password_protect', true) && !empty($this->blogpassword)) {

			if (!EB::verifyBlogPassword($this->blogpassword, $this->id)) {
				return true;
			}

		}
		return false;
	}

	/**
	 * Determines if the blog post is a new blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isNew()
	{
		$isNew = !$this->id ? true : false;

		// If this post is edited, we should then check the `isnew` column to determine if this blog post is really new or now.
		// Because the blog post may be stored but it might not be processed as "new" yet.
		if (!$isNew) {
			$isNew = $this->isnew;
		}

		return $isNew;
	}

	/**
	 * Determines if the blog post is imported from the feed
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFromFeed()
	{
		$db 	= EB::db();

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_feeds_history');
		$query[]	= 'WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($this->id);

		$query 	= implode(' ', $query);

		$db->setQuery($query);

		$imported 	= $db->loadResult();

		return $imported;
	}
}
