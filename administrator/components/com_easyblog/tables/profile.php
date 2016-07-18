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

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableProfile extends EasyBlogTable
{
	public $id = null;
	public $title = null;
	public $nickname = null;
	public $avatar = null;
	public $description = null;
	public $biography = null;
	public $url = null;
	public $params = null;
	public $user = null;
	public $permalink = null;
	public $custom_css = null;

	static $oauthClients = array();

	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_users', 'id', $db);
	}

	/**
	 * Binds an array of data with the current profile object.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bind($data, $ignore = array())
	{
		// Load the parent bind method
		$state = parent::bind($data, $ignore);

		// Normalize the url
		$this->url = EB::string()->normalizeUrl($this->url);

		// If user's permalink is empty, we need to generate one for them.
		if (empty($this->permalink)) {
			$user = JFactory::getUser($this->id);
			$this->permalink = $user->username;
		}

		// Ensure that the permalink is valid
		$this->permalink = JFilterOutput::stringURLSafe($this->permalink);

		return $state;
	}

	/**
	 * Override the parents implementation of storing a profile
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		$isNew = empty($this->id) ? true : false;

		$invalidKeys = array('twitter', 'blogCount', 'messaging', 'featured', 'isBloggerSubscribed');

		// before we store, we need to unset unnessary attribute.
		foreach($invalidKeys as $iKey) {
			if (isset($this->$iKey)) {
				unset($this->$iKey);
			}
		}

		$state 	= parent::store($updateNulls);
		$my 	= JFactory::getUser();

		// If the user is updating their own profile
		if ($my->id == $this->id) {
			JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

			// @rule: Integrations with EasyDiscuss
			EB::easydiscuss()->log('easyblog.update.profile', $this->id, JText::_('COM_EASYBLOG_EASYDISCUSS_HISTORY_UPDATE_PROFILE'));
			EB::easydiscuss()->addPoint('easyblog.update.profile', $this->id);
			EB::easydiscuss()->addBadge('easyblog.update.profile', $this->id);
		}

		return $state;
	}

	/**
	 * Creates a new record for the user.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return
	 */
	public function createDefault($id)
	{
		$db = EB::db();
		$user = JFactory::getUser($id);

		$obj = new stdClass();
		$obj->id = $user->id;
		$obj->nickname = $user->name;
		$obj->avatar = 'default_blogger.png';
		$obj->description = '';
		$obj->url = '';
		$obj->params = '';

		// Default to username for blogger permalink
		$obj->permalink = JFilterOutput::stringURLSafe( $user->username );

		// we do not insert 0 id into users table.
		if ($user->id) {
			$db->insertObject('#__easyblog_users', $obj);
		}

		return $obj;
	}

	/**
	 * Loads the blogger record
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int
	 * @return
	 */
	public function load($id = null, $reset = true)
	{
		static $users = null;

		$id = ( $id == '0' ) ? null : $id;

		if (is_null($id)) {
			$this->bind(JFactory::getUser(0));
			return $this;
		}

		if (empty($id)) {
			// When the id is null or 0
			$this->bind( JFactory::getUser() );
			return $this;
		}

		if (!isset($users[$id])) {

			if ((! parent::load($id)) && ($id != 0)) {
				$obj	= $this->createDefault($id);
				$this->bind($obj);
			}

			$users[$id] = clone $this;
		}

		$this->user	= JFactory::getUser($id);
		$this->bind($users[$id]);

		return $users[$id];
	}

	public function setUser($my)
	{
		$this->load($my->id);
		$this->user = $my;
	}

	/**
	 * Determines if the author is associated with any teams or not
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasTeams()
	{
		static $teams = array();

		if (!isset($teams[$this->id])) {
			$model = EB::model('TeamBlogs');

			$teams[$this->id] = $model->getUserTeams();
		}

		return count($teams[$this->id]) >= 1;
	}

	/**
	 * Retrieves the blogger's name
	 *
	 * @since	4.0
	 * @access	public
	 * @return	string
	 */
	public function getName()
	{
		if($this->id == 0) {
			return JText::_('COM_EASYBLOG_GUEST');
		}

		if (!$this->user) {
			$this->user	= JFactory::getUser($this->id);
		}

		$config = EB::config();
		$type = $config->get('layout_nameformat');

		// Default to the person's name
		$name = $this->user->name;

		if ($type == 'username') {
			$name = $this->user->username;
		}

		if ($type == 'nickname' && !empty($this->nickname)) {
			$name = $this->nickname;
		}

		// Ensure that the name cannot be exploited.
		$name = EB::string()->escape($name);

		return $name;
	}

	/**
	 * Sets an author as featured author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function setFeatured()
	{
		$model = EB::model('Featured');

		$state = $model->makeFeatured(EBLOG_FEATURED_BLOGGER, $this->id);

		return $state;
	}

	/**
	 * Remove a featured status for author on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function removeFeatured()
	{
		$model = EB::model('Featured');
		$state = $model->removeFeatured(EBLOG_FEATURED_BLOGGER, $this->id);

		return $state;
	}

	/**
	 * Retrieves author's acl
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAcl()
	{
		$acl = EB::acl($this->id);

		return $acl;
	}

	/**
	 * Retrieves the user's avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvatar()
	{
		$avatar = EB::avatar()->getAvatarURL($this);

		return $avatar;
	}

	/**
	 * Retrieve the editor to use for this user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getEditor()
	{
		$config = EB::config();
		$editor = $config->get('layout_editor');

		return $editor;
		// if ($editor != 'composer' && !$config->get('layout_editor_author')) {
		// 	return $editor;
		// }

		// $params = $this->user->params;
		// $params = json_decode($params);

		// if (!isset($params->editor) || !$params->editor) {
		// 	return $editor;
		// }

		// return $params->editor;
	}

	/**
	 * Retrieves the description of the blogger
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDescription($raw = false)
	{
		$description = $raw ? $this->description : nl2br($this->description);
		return $description;
	}

	/**
	 * Retrieves author's twitter link
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTwitterLink()
	{
		static $links = array();

		if (!isset($links[$this->id])) {

			$links[$this->id] = '';

			$oauth = EB::table('OAuth');
			$oauth->loadByUser($this->id, 'twitter');

			$params = EB::registry($oauth->params);
			$screenName = $params->get('screen_name');

			if ($screenName) {
				$links[$this->id] = 'https://twitter.com/' . $screenName;
			}
		}

		return $links[$this->id];
	}

	/**
	 * Determines if the blogger is featured on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isFeatured()
	{
		$model = EB::model('Featured');

		return $model->isFeatured('blogger', $this->id);
	}

	/**
	 * Retrieves the total number of posts created by the author
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPosts()
	{
		static $total = array();

		if (!isset($total[$this->id])) {
			$model = EB::model('Blogger');

			$total[$this->id] = $model->getTotalBlogCreated($this->id);
		}

		return $total[$this->id];
	}

	/**
	 * Retrieves the biography from the specific blogger
	 *
	 * @since	4.0
	 * @access	public
	 * @param	boolean		True to retrieve raw data
	 * @return	string
	 */
	public function getBiography($raw = false, $placeholder = false)
	{
		static $items = array();

		$key = (int) $raw  . (int) $placeholder . $this->id;

		if (!isset($items[$key])) {

			EB::loadLanguages();

			$biography = $this->biography;

			// We should only add newlines if the editor is wysiwyg editor
			$config = EB::config();

			if (!$config->get('layout_dashboard_biography_editor') && !$raw) {
				$biography = nl2br($biography);
			}

			if (!$biography && $placeholder) {
				$biography = JText::sprintf('COM_EASYBLOG_BIOGRAPHY_NOT_SET', $this->getName());
			}

			$items[$key] = $biography;
		}

		return $items[$key];
	}

	/**
	 * Retrieves the website for the author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getWebsite()
	{
		$url = $this->url == 'http://' ? '' : $this->url;

		return $url;
	}

	/**
	 * Generates the profile link for an author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getProfileLink()
	{
		$profile = EB::profile($this);

		return $profile->getLink();
	}

	/**
	 * Retrieves the alias for this author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		static $permalinks = array();

		if (!isset($permalinks[$this->id])) {
			$config = EB::config();

			if (!$this->user && $this->id) {
				$this->user = JFactory::getuser($this->id);
			}

			// If the username is invalid
			if (!isset($this->user->username) || !$this->user->username) {
				return JText::_('COM_EASYBLOG_INVALID_PERMALINK_BLOGGER');
			}

			// If user doesn't have a permalink, generate it for them
			if (!$this->permalink) {
				$this->permalink = EBR::normalizePermalink($this->user->username);

				// we canot do store here or else any 'extra' attribute that get set before this function will be wide put.!
				// $this->store();
				$this->savePermalink($this->permalink);
			}

			$permalink = $this->permalink;

			if ($config->get('main_sef_unicode') || !EBR::isSefEnabled()) {
				$permalink = $this->id . '-' . $this->permalink;
			}

			$permalinks[$this->id] = $permalink;
		}

		return $permalinks[$this->id];
	}

	private function savePermalink($permalink)
	{
		$db = EB::db();

		$query = "update `#__easyblog_users` set `permalink` = " . $db->Quote($permalink);
		$query .= " where id = " . $db->Quote($this->id);

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Retrieves the external permalink for this author
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink()
	{
		$link = EBR::getRoutedURL('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id, false, true, true);

		return $link;
	}

	/**
	 * Retrieves the permalink for this author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		$url = EB::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $this->id, $xhtml);

		return $url;
	}

	/**
	 * Retrieves the user's parameters
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	JRegistry
	 */
	public function getParam()
	{
		$registry	 = new JRegistry($this->params);

		return $registry;
	}

	/**
	 * Retrieves the user type
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUserType()
	{
		return $this->user->usertype;
	}

	/**
	 * Retrieves rss link for the blogger
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRssLink()
	{
		$config = EB::config();

		if ($config->get('main_feedburnerblogger')) {

			$feedburner	= EB::table('Feedburner');
			$feedburner->load($this->id);

			if (!empty($feedburner->url)) {

				$rssLink    = $feedburner->url;
				return $rssLink;
			}
		}

		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=blogger&id=' . $this->id, false, 'author');
	}

	/**
	 * Retrieves bloggers rss link
	 *
	 * @deprecated	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRSS()
	{
		return $this->getRssLink();
	}

	function getAtom()
	{
		return EasyBlogHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easyblog&view=blogger&id=' . $this->id, true );
	}

	/**
	 * Binds avatar
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindAvatar($file, $acl)
	{
		if (!$acl->get('upload_avatar') ) {
			return false;
		}

		// Try to upload the avatar
		$avatar = EB::avatar();

		// Get the avatar path
		$this->avatar = $avatar->upload($file, $this->user->id);

		// Assign points for aup
		EB::aup()->assign('plgaup_easyblog_upload_avatar', '', 'easyblog_upload_avatar_' . $this->user->id, JText::_('COM_EASYBLOG_AUP_UPLOADED_AVATAR'));
	}

	/**
	 * Binds users oauth settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindOauth($post, $acl)
	{
		// Store twitter settings
		if ($acl->get('update_twitter') ) {
			$twitter	= EB::table('Oauth');
			$twitter->loadByUser($this->user->id, EBLOG_OAUTH_TWITTER);

			$twitter->auto		= isset($post['integrations_twitter_auto']) ? $post['integrations_twitter_auto'] : false;
			$twitter->message	= isset($post['integrations_twitter_message']) ? $post['integrations_twitter_message'] : '';

			$twitter->store();
		}

		// Store linkedin settings
		if ($acl->get('update_linkedin')) {
			$linkedin	= EB::table('Oauth');
			$linkedin->loadByUser($this->user->id, EBLOG_OAUTH_LINKEDIN);

			$linkedin->auto		= isset($post['integrations_linkedin_auto']) ? $post['integrations_linkedin_auto'] : false;
			$linkedin->message	= isset($post['integrations_linkedin_message']) ? $post['integrations_linkedin_message'] : '';
			$linkedin->private	= isset($post['integrations_linkedin_private']) ? $post['integrations_linkedin_private'] : true;

			$linkedin->store();
		}

		// Store fb settings
		if ($acl->get('update_linkedin')) {
			$facebook	= EB::table('Oauth');
			$facebook->loadByUser($this->user->id, EBLOG_OAUTH_FACEBOOK);

			$facebook->auto		= isset($post['integrations_facebook_auto']) ? $post['integrations_facebook_auto'] : false;
			$facebook->message	= '';

			$facebook->store();
		}
	}

	/**
	 * Binds feedburner settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindFeedburner($post, $acl)
	{
		$config 	= EB::config();

		if (!$config->get('main_feedburner') || !$config->get('main_feedburnerblogger')) {
			return false;
		}

		if (!$acl->get('allow_feedburner')) {
			return false;
		}

		$feedburner	= EB::table('Feedburner');
		$feedburner->load($this->user->id);
		$feedburner->url	= $post['feedburner_url'];

		$state = $feedburner->store();

		return $state;
	}

	/**
	 * Binds users oauth settings
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function bindAdsense($post, $acl)
	{
		$config 	= EB::config();

		if (!$config->get( 'integration_google_adsense_enable')) {
			return false;
		}

		if (!$acl->get('add_adsense')) {
			return false;
		}

		$adsense = EB::table('Adsense');
		$adsense->load($this->user->id);

		// Prevent Joomla from acting funny as on some site's it automatically adds the quote character at the end.
		$adsense->code 		= rtrim( $post['adsense_code'] , '"' );
		$adsense->display 	= $post['adsense_display'];
		$adsense->published = $post['adsense_published'];

		$state 	= $adsense->store();

		if (!$state) {
			$this->setError($adsense->getError());

			return false;
		}

		return true;
	}


	/**
	 * Determines if the user is currently online
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function isOnline()
	{
		static	$loaded	= array();

		if(!isset($loaded[$this->id])) {
			$db		= EB::db();

			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__session' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'client_id') . '<>' . $db->Quote( 1 );
			$db->setQuery($query);

			$loaded[$this->id]	= $db->loadResult() > 0 ? true : false;
		}

		return $loaded[$this->id];
	}

	/**
	 * Retrieve a list of tags created by this user
	 **/
	public function getTags()
	{
		$db		= EasyBlogHelper::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		$tags	= array();

		foreach( $rows as $row )
		{
			$tag	= EB::table('Tag');
			$tag->bind( $row );
			$tags[]	= $tag;
		}

		return $tags;
	}

	/**
	 * Retrieves a list of oauth clients
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOauthClients()
	{
		// Load this only once.
		if (!self::$oauthClients) {
			$model = EB::model('Oauth');
			$result = $model->getUserClients($this->id);

			if ($result) {
				foreach ($result as $row) {

					$client = EB::table('Oauth');
					$client->bind($row);

					self::$oauthClients[$row->type] = $client;
				}
			}
		}

		return self::$oauthClients;
	}

	/**
	 * Determines if the user has oauth setup for specific site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function hasOauth($site)
	{
		$clients = $this->getOauthClients();

		if (!isset($clients[$site])) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the user has oauth setup for specific site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getOauth($site)
	{
		$clients = $this->getOauthClients();

		if (!isset($clients[$site])) {
			return false;
		}

		return $clients[$site];
	}
	/**
	 * Retrieves total number of comments the author made on the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCommentsCount()
	{
		if (!EB::comment()->isBuiltin()) {
			return 0;
		}

		$db = EB::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_comment' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'created_by' ) .'=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		return $db->loadResult();
	}
}
