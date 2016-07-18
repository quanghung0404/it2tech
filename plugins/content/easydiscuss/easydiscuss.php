<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport( 'joomla.plugin.plugin' );
jimport('joomla.filesystem.file');

class plgContentEasyDiscuss extends JPlugin
{
	var $extension	= null;
	var $view		= null;
	var $loaded		= null;

	public function __construct( &$subject , $params )
	{
		$this->extension	= JRequest::getString( 'option' );
		$this->view			= JRequest::getString( 'view' );

		// Load language file for use throughout the plugin
		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		parent::__construct( $subject, $params );
	}

	/**
	 * Tests if EasyBlog exists
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';
			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Needed to update the content of the discussion whenever the article is being edited and saved.
	 */
	public function onAfterContentSave( &$article, $isNew )
	{

		if (! $this->exists()) {
			return false;
		}

		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if( $this->extension == 'com_easydiscuss' || $this->loaded || ( isset( $article->easydiscuss ) && $article->easydiscuss == true ) || ( isset($article->state) && !$article->state && $this->extension == 'com_content') )
		{
			return;
		}

		$params = $this->getParams();

		$allowed	= $params->get( 'allowed_components' , 'com_content,com_easyblog');
		$allowed	= explode( ',' , $allowed );

		//include com_easydiscuss
		$allowed[] = 'com_easydiscuss';

		if (!in_array($this->extension, $allowed)) {
			return false;
		}

		$this->mapExisting( $article );

		return true;
	}

	/**
	 * onContentAfterSave trigger for Joomla 1.6 onwards.
	 *
	 **/
	public function onContentAfterSave($context, $article, $isNew)
	{
		return $this->onAfterContentSave( $article , $isNew );
	}

	/**
	 * onContentAfterDisplay trigger for Joomla 1.6 onwards.
	 *
	 **/
	public function onContentAfterDisplay( $context , &$article, &$params, $page = 0 )
	{
		return $this->onAfterDisplayContent( $article , $params , $page );
	}

	/**
	 * Triggers for EasyBlog.
	 */
	public function onDisplayComments( &$blog , &$params )
	{
		$blog->catid = $blog->category_id;

		return $this->onAfterDisplayContent( $blog , $params , 0 , __FUNCTION__ );
	}



	/**
	 * Triggered after the content is displayed.
	 *
	 */
	public function onAfterDisplayContent( &$article, &$articleParams, $limitstart , $trigger = '' )
	{
		if (! $this->exists()) {
			return false;
		}

		$app	= JFactory::getApplication();
		$params = $this->getParams();

		$allowed	= $params->get( 'allowed_components' , 'com_content,com_easyblog');
		$allowed	= explode( ',' , $allowed );

		if (!in_array($this->extension, $allowed) || !$article->id) {
			return '';
		}

		// If the current page is easydiscuss, we want to skip this altogether.
		// We also need to skip this when the plugins are being triggered in the discussion replies otherwise it will
		// be in an infinite loop generating all contents.
		if ($this->extension == 'com_easydiscuss' ||
			$this->loaded ||
			(isset($article->easydiscuss) && $article->easydiscuss == true) ||
			($this->extension == 'com_content' && !$article->state)) {
			return;
		}

		if ($this->extension == 'com_easyblog') {
			$inputs = ED::request();
			$view = $inputs->get('view', '', 'cmd');
			$id = $inputs->get('id', 0, 'int');

			if ($view != 'entry' || !$id) {
				return;
			}
		}

		// @rule: Test for exclusions on the categories
		$excludedCategories	= $params->get( 'exclude_category' );

		if (!is_array($excludedCategories)) {
			$excludedCategories	= explode(',' , $excludedCategories);
		}

		if (in_array($article->catid , $excludedCategories)) {
			return '';
		}

		// @rule: Test for exclusions on the article id.
		$excludedArticles	= trim($params->get( 'exclude_articles'));

		if (!empty($excludedArticles)) {
			$excludedArticles	= explode(',' , $excludedArticles);

			if (in_array($article->id , $excludedArticles)) {
				return '';
			}
		}

		// @rule: Test for inclusions on the categories
		$allowedCategories	= $params->get( 'include_category' );

		if (is_array($allowedCategories)) {
			$allowedCategories	= implode(',' , $allowedCategories);
		}

		$allowedCategories 	= trim($allowedCategories);

		if ($allowedCategories != 'all' && !empty($allowedCategories) && $this->extension == 'com_content') {
			$allowedCategories 	= explode(',' , $allowedCategories);

			if (!in_array($article->catid , $allowedCategories)) {
				return '';
			}
		}

		// Get the mapping
		$ref = ED::table('PostsReference');
		$exists = $ref->loadByExtension($article->id, $this->extension);

		if (!$exists) {
			// Map the article into EasyDiscuss
			$this->mapExisting( $article );

			$ref = ED::table('PostsReference');
			$ref->loadByExtension($article->id, $this->extension);
		}

		// Load the discussion item
		$post = ED::post($ref->post_id);

		if (!$post->published) {
			return;
		}

		// Load css file
		$this->attachHeaders();

		if ($this->isFrontpage()) {
			$this->addFrontpageTools( $article , $post );

		} else {
			$this->loaded	= true;

			// Show normal discussions data
			$html = $this->addResponses( $article , $post );

			// if ($this->extension == 'com_easyblog') {
			// 	return $html;
			// }
		}

		return '';
	}

	/**
	 * Retrieves Joomla's version
	 */
	public function getJoomlaVersion()
	{
		$jVerArr   = explode('.', JVERSION);
		$jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	/**
	 * Retrieves parameter plugins.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	JParameter		JParameter object from Joomla.
	 */
	public function getParams()
	{
		static $params 	= null;

		if( !$params )
		{
			$plugin 		= JPluginHelper::getPlugin( 'content', 'easydiscuss' );
			$params 		= DiscussHelper::getRegistry( $plugin->params );
		}

		return $params;
	}

	/**
	 * Attaches the plugin's css file.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	boolean 	True on success, false otherwise.
	 */
	private function attachHeaders()
	{
		static $loaded 	= false;

		if( !$loaded )
		{
			ED::init();

			$doc 	= JFactory::getDocument();
			$path 	= rtrim( JURI::root() , '/' ) . '/plugins/content/easydiscuss/css/styles.css';
			$doc->addStyleSheet( $path );

			$loaded 	= true;
		}
		return $loaded;
	}

	private function isFrontpage()
	{
		switch( $this->extension )
		{
			case 'com_content':
				return ( $this->view == 'frontpage' ) || $this->view == 'featured';
			break;
			case 'com_k2':
				return $this->view == 'latest' || $this->view == 'itemlist';
			break;
			case 'com_easyblog':
				return ( $this->view == 'latest' );
			break;
		}
		return false;
	}

	/**
	 * Adds some nifty contents into the frontpage listing of com_content.
	 *
	 * @access 	public
	 * @param 	stdclass $article 		The standard Joomla article object.
	 * @param 	DiscussTablePost $post 	EasyDiscuss DiscussTablePost object.
	 * @return 	stdclass 				The Joomla article object.
	 **/
	public function addFrontpageTools( &$article , &$post )
	{
		$params = $this->getParams();

		// Just return if it's not needed.
		if (!$params->get( 'frontpage_tools', true)) {
			return $article;
		}

		$total = $post->getTotalReplies();
		$url = $this->getArticleURL( $article );
		$hits = $this->getArticleHits( $article );
		$config = ED::config();
		$my = JFactory::getUser();

		ob_start();
		include($this->getTemplatePath('frontpage.php'));
		$contents 	= ob_get_contents();
		ob_end_clean();

		// EasyBlog specifically uses 'text'
		if ($this->extension == 'com_easyblog') {
			$article->text .= $contents;
			return $article;
		}


		$article->introtext .= $contents;


		return $article;
	}

	/**
	 * Returns the formatted date which is required during the output.
	 * The resultant date includes the offset.
	 *
	 * @access 	public
	 * @param 	string $format 		The date format.
	 * @param 	string $dateString 	The date result.
	 * @return 	string 				The formatted date result.
	 */
	public function formatDate( $format , $dateString )
	{
		$output = ED::date($dateString)->display($format);
		return $output;
	}

	/**
	 * Attaches the response and form in the article.
	 *
	 * @access 	public
	 * @param 	stdclass			$article 		The standard object from the article.
	 * @param 	DiscussTablePost	$post 			The post table.
	 * @return 	string 				The formatted date result.
	 */
	public function addResponses( &$article , &$post )
	{
		if (! $this->exists()) {
			return false;
		}

		$params = $this->getParams();

		$model = ED::model('Posts');
		$config = ED::config();
		$my = JFactory::getUser();
		$acl = ED::acl();

		// Get composer
		$opts = array('replying', $post);
		$composer = ED::composer($opts);

		$repliesLimit = $params->get( 'items_count' , 5 );

		$totalReplies = $post->getTotalReplies();

		$hasMoreReplies	= false;

		$limitstart		= null;
		$limit			= null;

		if ($repliesLimit) {
			$limit = $repliesLimit;
		}


		$sort = ED::request()->get('sort', ED::getDefaultRepliesSorting(), 'word');
		$limitstart = ED::request()->get('limitstart', 0);


		$replies = $post->getReplies(true, $limit, $sort, $limitstart);
		// Get the pagination for replies
		$pagination = $model->getPagination();


		$isMainLocked 	= false;
		$canDeleteReply = false;

		// Load the category.
		$category = ED::table('Category');
		$category->load( (int) $post->category_id );

		$canReply = ((($my->id != 0) || ($my->id == 0 && $config->get('main_allowguestpost' ) ) ) && $acl->allowed('add_reply', '0') ) ? true : false;

		$system = new stdClass();
		$system->config	= ED::config();
		$system->my = $my;
		$system->acl = $acl;

		ob_start();
		include( dirname(__FILE__) . '/tmpl/default.php' );
		$contents	= ob_get_contents();
		ob_end_clean();

		// add bbcode settings
        $bbcodeSettings = ED::themes()->output('admin/structure/settings');
        $scripts = ED::scripts()->getScripts();

        $htmlContent = $bbcodeSettings . $contents . $scripts;

		$article->text	.= $htmlContent;

		return $htmlContent;
	}

	/**
	 * Returns the URL to a specific article in Joomla.
	 *
	 * @access 	public
	 * @param 	stdclass 	$article 	The standard Joomla article object.
	 * @return 	string 					The formatted url to the article.
	 */
	private function getArticleURL( &$article )
	{
		$uri		= JURI::getInstance();


		switch( $this->extension )
		{
			 case 'com_content':

			 	require_once( JPATH_ROOT . '/components/com_content/helpers/route.php' );

				JTable::addIncludePath( JPATH_ROOT . '/libraries/joomla/database/table' );

				$category	= JTable::getInstance( 'Category' , 'JTable' );
				$category->load( $article->catid );

				$url = JRoute::_(ContentHelperRoute::getArticleRoute($article->id . ':' . $article->alias , $article->catid));
				$url = $url . '#discuss-' . $article->id;

				return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );

			 break;
			 case 'com_easyblog':
				require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php');
				return EBR::getRoutedURL('index.php?option=com_easyblog&view=entry&id=' . $article->id, false, true);
			 break;
			 case 'com_k2':
				require_once( JPATH_ROOT . '/components/com_k2/helpers/route.php' );

				JTable::addIncludePath( JPATH_ROOT . '/libraries/joomla/database/table' );

				$category	= JTable::getInstance( 'Category' , 'JTable' );
				$category->load( $article->catid );

				$url		= K2HelperRoute::getItemRoute( $article->id . ':' . $article->alias , $article->catid . ':' . $category->alias );

				$url = $url . '#discuss-' . $article->id;

				return $uri->toString( array('scheme', 'host', 'port')) . '/' . ltrim( $url , '/' );
			 break;
		}
	}

	/**
	 * Gets the total hit count for the specific article.
	 *
	 * @access 	private
	 * @param 	stdclass	$article 	The article object.
	 * @return 	int 					The total hits for the specific article.
	 */
	private function getArticleHits( &$article )
	{
		$db 	= ED::db();

		$query	= 'SELECT ' . $db->nameQuote( 'hits' ) . ' FROM ' . $db->nameQuote( '#__content' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $article->id );

		$db->setQuery( $query );
		$hits 	= (int) $db->loadResult();

		return $hits;
	}

	public function mapExisting( &$article )
	{
		if (! $this->exists()) {
			return false;
		}

		// @rule: If article is not published, do not try to process anything
		if( $this->extension == 'com_easydiscuss' || (!$article->state && $this->extension == 'com_content')) {
			return false;
		}

		$ref = ED::table('PostsReference');
		$exists	= $ref->loadByExtension( $article->id , $this->extension );
		$isNew	= !$exists;

		// @rule: Only append discussions that are already added into the reference table.
		$post	= $this->createDiscussion( $article , $isNew );

		if (!$exists) {
			// @rule: Store the references
			$ref->set('post_id' , $post->id);
			$ref->set('reference_id', $article->id);
			$ref->set('extension', $this->extension);
			$ref->store();
		}
	}

	public function getTemplatePath( $file )
	{
		return dirname( __FILE__ ) . '/tmpl/' . $file;
	}

	/**
	 * Creates a new discussion in EasyDiscuss so that we can link the article and the content.
	 *
	 * @access 	public
	 * @param 	stdclass 	$article 	The standard Joomla article object.
	 * @return 	DiscussTablePost 		EasyDiscuss post table.
	 */
	public function createDiscussion( &$article , $isNew = true )
	{
		if (! $this->exists()) {
			return false;
		}

        $post = ED::post();
		$params = $this->getParams();

		if (!$isNew) {
			// Get the mapping
			$ref = ED::table('PostsReference');
			$ref->loadByExtension( $article->id , $this->extension );

			// var_dump($ref->post_id);exit;

			// Load the discussion item
			$post = ED::post($ref->post_id);
		}


		$data = array();

		$data['category_id'] = $params->get('category_storage', 1);
		$data['title'] = $article->title;

		// @rule: Set the creation date
		$data['created'] = $article->created;

		// @rule: Set the publishing state
		$data['published'] = DISCUSS_ID_PUBLISHED;

		// @rule: Set the modified date
		$data['modified'] = $article->modified;

		// @rule: Set the user id
		$data['user_id'] = $article->created_by;

		// @rule: Set the user type
		$data['user_type'] = 'member';

		// @rule: Set the hits
		$data['hits'] = $article->hits;

		// @rule: We only take the introtext part.
		$text	= $article->introtext;

		$config = ED::config();
		$contentType = 'html';

		if ($config->get( 'layout_editor') == 'bbcode') {
			$text	= ED::parser()->html2bbcode($text);
			$contentType = 'bbcode';
		}

		// @rule: Add a read more text that links to the article.
		if ($params->get('readmore_in_post' , true)) {

			$url	= $this->getArticleURL( $article );

			ob_start();
			include( $this->getTemplatePath( 'readmore.' . $contentType . '.php' ) );
			$readmore = ob_get_contents();
			ob_end_clean();

			$text .= $readmore;
		}

		$data['content'] = $text;
		$data['content_type'] = $contentType;

		$post->bind($data);
		$state = $post->save();

		// if ($state) {
		// 	$id = $post->id;
		// 	$post = ED::post($id);
		// }

		return $post;
	}

	/**
	 * Get registration link based on the provider.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	string 	The URL to the responsible component.
	 */
	public function getRegistrationLink()
	{
		$params 	= $this->getParams();
		$url 		= '';

		switch( $params->get( 'login_provider' , 'joomla' ) )
		{
			case 'cb':
				$url 	= JRoute::_( 'index.php?option=com_comprofiler&task=registers');
				break;

			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url 	= CRoute::_( 'index.php?option=com_community&view=register' );
				break;

			case 'easysocial':

				if (ED::easysocial()->exists()) {
					$link 	= FRoute::registration();
				} else {
					$link 	= $default;
				}

				break;

			default:

				$url 	= JRoute::_( 'index.php?option=com_users&view=registration' );
				break;
		}

		return $url;
	}

	/**
	 * Get login link based on the provider.
	 *
	 * @access 	public
	 * @param 	null
	 * @return 	string 	The URL to the responsible component.
	 */
	public function getLoginLink()
	{
		$params 	= $this->getParams();
		$url 		= '';

		$id 		= JRequest::getInt( 'id' );

		$article 	= JTable::getInstance( 'Content' , 'JTable' );
		$article->load( $id );

		$return 	= base64_encode( $this->getArticleURL( $article ) );
		$default 	= JRoute::_( 'index.php?option=com_users&view=login&return=' . $return );

		switch( $params->get( 'login_provider' , 'joomla' ) )
		{
			case 'jomsocial':
				include_once JPATH_ROOT . '/components/com_community/libraries/core.php';
				$url 	= CRoute::_( 'index.php?option=com_community' );
				break;

			case 'easysocial':

				$easysocial 	= DiscussHelper::getHelper( 'EasySocial' );

				if( $easysocial->exists() )
				{
					$link 	= FRoute::login();
				}
				else
				{
					$link 	= $default;
				}
				break;

			case 'cb':
			default:
				$url 	= $default;
				break;
		}

		return $url;
	}
}
