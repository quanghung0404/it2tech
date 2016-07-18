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

jimport('joomla.filesystem.file');

class PlgContentEasySocial extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	public function exists()
	{
		$file 	= JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php';

		if (!JFile::exists($file)) {
			return false;
		}

		include_once($file);

		// joomla content helper
		require_once (JPATH_ROOT.'/components/com_content/helpers/route.php');


		return true;
	}

	/**
	 * Retrieves the plugin params
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPluginParams()
	{
		static $params = false;

		if (!$params && $this->exists()) {
			$plugin	= JPluginHelper::getPlugin('content', 'easysocial');
			$params = ES::registry($plugin->params);
		}

		return $params;
	}

	/**
	 * Retrieves EasySocial app params
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAppParams()
	{
		static $params = null;

		if (is_null($params)) {
			$app = ES::table('App');
			$app->load(array('type' => SOCIAL_TYPE_APPS, 'group' => SOCIAL_TYPE_USER, 'element' => 'article'));

			if (!$app->id) {
				$params = false;
			} else {
				$params	= $app->getParams();
			}
		}

		return $params;
	}

	/**
	 * Check the user session for the award points.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function sessionExists()
	{
		// Get the IP address from the current user
		$ip	= $_SERVER['REMOTE_ADDR'];

		// Check the article item view
		$this->app = JFactory::getApplication();
		$view = $this->app->input->get('view');

		// Get the current article item id
		$itemId = $this->app->input->get('id', 0, 'int');

		if (!empty($ip) && !empty($itemId) && $view == 'article') {

			$token = md5($ip . $itemId);
			$session = JFactory::getSession();
			$exists	= $session->get($token , false);

			// If the session existed return true
			if ($exists) {
				return true;
			}

			// Set the token so that the next time the same visitor visits the page, it wouldn't get executed again.
			$session->set($token , 1);
		}

		return false;
	}

	/**
	 * Triggered when preparing an article for display
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onContentPrepare($context, &$article, &$params)
	{
		$pluginParams = $this->getPluginParams();

		if ($pluginParams->get('placement', 1) != 1) {
			return;
		}

		$contents = $this->getAttachData($context, $article, $params);

		$article->text .= $contents;
	}

	/**
	 * Places the attached data on the event afterDisplayTitle
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onContentAfterTitle($context, &$article, &$params)
	{
		$pluginParams = $this->getPluginParams();

		if ($pluginParams->get('placement', 1) != 2) {
			return;
		}

		$contents = $this->getAttachData($context, $article, $params);

		return $contents;
	}

	/**
	 * Places the attached data on the event beforeDisplayContent
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onContentBeforeDisplay($context, &$article, &$params)
	{
		$pluginParams = $this->getPluginParams();

		if ($pluginParams->get('placement', 1) != 3) {
			return;
		}

		$contents = $this->getAttachData($context, $article, $params);

		return $contents;
	}

	/**
	 * Places the attached data on the event afterDisplayContent
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	/**
	 * Renders the author's box at the end of the article
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onContentAfterDisplay($context, &$article, &$params)
	{
		if ($context != 'com_content.article') {
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Get app and plugin params
		$pluginParams 	= $this->getPluginParams();
		$appParams 		= $this->getAppParams();

		if ($pluginParams->get('modify_contact_link', true)) {
			$author 	= FD::user($article->created_by);

			// Update the author link
			$article->contact_link	= $author->getPermalink();
		}

		$pluginParams = $this->getPluginParams();

		if ($pluginParams->get('placement', 1) != 4) {
			return;
		}

		$contents = $this->getAttachData($context, $article, $params);

		return $contents;
	}

	public function getAttachData($context, &$article, &$params)
	{
		if ($context != 'com_content.article') {
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Get the current viewer
		$my = FD::user();

		// Get app and plugin params
		$pluginParams = $this->getPluginParams();
		$appParams = $this->getAppParams();

		// Get the current view
		$view = JRequest::getVar('view');

		// Only assign points to viewer when they are not a guest and not the owner of the article
		if ($my->id && $my->id != $article->created_by && $view == 'article' && !$this->sessionExists()) {

			// Assign points to viewer
			$this->assignPoints('read.article', $my->id);

			// Assign badge to the viewer
			$this->assignBadge('read.article', JText::_('PLG_CONTENT_EASYSOCIAL_UPDATED_EXISTING_ARTICLE' ) );

			// Assign points to author when their article is being read
			$this->assignPoints('author.read.article' , $article->created_by );

			// Create a new stream item when an article is being read
			if ($appParams->get('stream_read', false)) {
				$this->createStream($article, 'read', $my->id);
			}
		}

		$comments = '';

		// Load css files
		$this->loadAssets();

		FD::language()->loadSite();

		// If configured to display comemnts
		if ($pluginParams->get('load_comments', false)) {
			if ($my->id || (!$my->id && $pluginParams->get('guest_viewcomments', true))) {
				$url = ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias, $article->catid);

				$comments = FD::comments($article->id, 'article', 'create', SOCIAL_APPS_GROUP_USER, array('url' => $url));
				$comments = $comments->getHtml();
			}
		}

		// Get the author of the article
		if (isset($article->created_by)) {

			$author = FD::user($article->created_by);

			$displayInfo = $pluginParams->get('display_info', false);

			// Get a list of badges the author has
			$badges = $author->getBadges();

			ob_start();
			require_once(dirname(__FILE__) . '/tmpl/article.php');
			$contents = ob_get_contents();
			ob_end_clean();

			return $contents;
		}
	}

	/**
	 * Loads required assets
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function loadAssets()
	{
		if( !$this->exists() )
		{
			return false;
		}

		$document 	= JFactory::getDocument();

		if( $document->getType() == 'html' )
		{
			// We also need to render the styling from EasySocial.
			FD::document()->init();

			$page 		= FD::page();
			$page->processScripts();

			$css 		= rtrim( JURI::root() , '/' ) . '/plugins/content/easysocial/assets/style.css';
			$document->addStylesheet( $css );
		}
	}

	/**
	 * Triggered when an article is stored.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onContentAfterSave( $context , $article , $isNew )
	{
		if( !$this->exists() )
		{
			return;
		}

		if ($context != 'com_content.article' && $context != 'com_content.form') {
			return;
		}

		// Set the verb according to the state of the article
		$verb 	= $isNew ? 'create' : 'update';

		// Get application params
		$appParams 		= $this->getAppParams();

		// If app does not exist, skip this altogether.
		if( !$appParams )
		{
			return;
		}

		// If plugin is disabled to create new stream, skip this
		if( $isNew && !$appParams->get( 'stream_create' , true ) )
		{
			return;
		}

		// If plugin is disabled to create update stream, skip this.
		if( !$isNew && !$appParams->get( 'stream_update' , true ) )
		{
			return;
		}

		// Create stream record.
		$this->createStream( $article , $verb );

		// Assign points
		$command 	= $isNew ? 'create.article' : 'update.article';

		$this->assignPoints( $command , $article->created_by );

		// Assign badge for the user
		if( $isNew )
		{
			$this->assignBadge( 'create.article' , JText::_( 'PLG_CONTENT_EASYSOCIAL_CREATED_NEW_ARTICLE' ) );
		}
		else
		{
			$this->assignBadge( 'update.article' , JText::_( 'PLG_CONTENT_EASYSOCIAL_UPDATED_EXISTING_ARTICLE' ) );
		}
	}

	/**
	 * Assign points
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function assignPoints( $command , $userId = null )
	{
		if( is_null( $userId ) )
		{
			$userId 	= FD::user()->id;
		}

		return FD::points()->assign( $command , 'com_content' , $userId );
	}

	/**
	 * Assign badges
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function assignBadge( $rule , $message , $creatorId = null )
	{
		$creator 	= FD::user( $creatorId );

		$badge 	= FD::badges();
		$state 	= $badge->log( 'com_content' , $rule , $creator->id , $message );

		return $state;
	}

	/**
	 * Perform cleanup when an article is being deleted
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onContentBeforeDelete( $context , $data )
	{
		if( $context != 'com_content.article' )
		{
			return;
		}

		if (!$this->exists()) {
			return;
		}

		// Delete the items from the stream.
		$stream 	= FD::stream();
		$stream->delete( $data->id , 'article' );
	}

	/**
	 * Generate new stream activity.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function createStream( $article , $verb , $actor = null )
	{
		$tmpl	 = FD::stream()->getTemplate();

		if( is_null( $actor ) )
		{
			$actor 	= $article->created_by;
		}

		// Set the creator of this article.
		$tmpl->setActor( $actor , SOCIAL_TYPE_USER );

		// Set the context of the stream item.
		$tmpl->setContext( $article->id , 'article' );

		// Set the verb
		$tmpl->setVerb( $verb );

		// Load up the category dataset
		$category	= JTable::getInstance( 'Category' );
		$category->load( $article->catid );

		// Get the permalink
		$permalink	= ContentHelperRoute::getArticleRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

		// Get the category permalink
		$categoryPermalink 	= ContentHelperRoute::getCategoryRoute( $category->id . ':' . $category->alias );

		// Store the article in the params
		$registry 	= FD::registry();
		$registry->set( 'article' , $article );
		$registry->set( 'category' , $category );
		$registry->set( 'permalink', $permalink );
		$registry->set( 'categoryPermalink' , $categoryPermalink );

		// We need to tell the stream that this uses the core.view privacy.
		$tmpl->setAccess( 'core.view' );

		// Set the template params
		$tmpl->setParams( $registry );

		FD::stream()->add( $tmpl );
	}
}
