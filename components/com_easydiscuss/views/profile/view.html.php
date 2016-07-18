<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewProfile extends EasyDiscussView
{
	public function display($tmpl = null)
	{
		// Custom parameters.
		$sort = $this->input->get('sort', 'latest', 'string');
		$filter = $this->input->get('filter', 'allposts', 'word');
		$viewType = $this->input->get('viewtype', 'questions', 'word');

		// Get the current user that should be displayed
		$id = $this->input->get('id', null, 'int');
		$user = JFactory::getUser($id);

		// Check if the user is allowed to view
		if (!$this->config->get('main_profile_public') && !$this->my->id) {
			ED::setMessage('COM_EASYDISCUSS_LOGIN_TO_VIEW_PROFILE');
			$redirect = EDR::_('view=index', false);
			return $this->app->redirect($redirect);
		}

		// Load the user's profile
		$profile = ED::user($user->id);

		// If profile is invalid, throw an error.
		if (!$profile->id) {
			return JError::raiseError(404, JText::_('COM_EASYDISCUSS_USER_ACCOUNT_NOT_FOUND'));
		}

		$socialUrls = array();

		if (!empty($profile->params)) {

			$socialParams = json_decode($profile->params);

			$fields = array('facebook', 'linkedin', 'twitter', 'website', 'skype');

			foreach ($fields as $field) {
				if ($socialParams->$field) {

					$value = $socialParams->$field;
					$showProfile = 'show_' . $field;

					// Determine if the field should appear on profile page
					if (isset($socialParams->$showProfile) && $socialParams->$showProfile) {

						if ($field == 'facebook' || $field == 'linkedin' || $field == 'twitter') {

							$value = ED::url()->clean($value);
						}

						// To fix missing website icon in profile page.
						if ($field == 'website') {
							$field = 'link';
						}

						$socialUrls[$field] = $value;
					}
				}
			}
		}

		// Set the page properties
		ED::setPageTitle(JText::sprintf('COM_EASYDISCUSS_PROFILE_PAGE_TITLE', $profile->getName()));
		$this->setPathway(JText::_($profile->getName()));

		// Attach gmaps api
		if ($this->config->get('layout_profile_showlocation')) {
			$this->doc->addScript('//maps.googleapis.com/maps/api/js?sensor=true');
		}

		// Load up the models and get data
		$postsModel = ED::model('Posts');

		$badges = $profile->getBadges();

		if ($viewType == 'replies') {
			$posts	= $postsModel->getRepliesFromUser($profile->id);
		}

		if ($viewType == 'unresolved') {
			$posts	= $postsModel->getUnresolvedFromUser($profile->id);
			$pagination = $postsModel->getPagination();
		}

		if ($viewType == 'questions') {
			$options = array('filter' => $viewType, 'userId' => $profile->id, 'includeAnonymous' => false);

			// If the post is anonymous we shouldn't show to public.
			if (ED::user()->id == $profile->id) {
				$options['includeAnonymous'] = true;
				$options['private'] = true;
			}

			$posts = $postsModel->getDiscussions($options);
			$pagination = $postsModel->getPagination();
		}

		if ($viewType == 'assigned') {
			$assignedModel = ED::model('Assigned');
			$posts = $assignedModel->getPosts($profile->id);
 		}

 		if ($viewType == 'favourites') {
			$posts = $postsModel->getData(true, 'latest', null, 'favourites', '', null, 'all', $profileId);
		}

		$posts = ED::formatPost($posts);

		$filterArr = array('viewtype' => $viewType, "id" => $profile->id);
		$pagination	= $postsModel->getPagination();
		$pagination	= $pagination->getPagesLinks('profile', $filterArr, true);

		// Check for integrations
		// EasyBlog
		$easyblogExists	= $this->easyblogExists();
		$blogCount = 0;

		if ($easyblogExists && $this->config->get('integrations_easyblog_profile')) {
			$bloggerModel = EB::model('Blogger');
			$blogCount = $bloggerModel->getTotalBlogCreated($profile->id);
		}

		// Komento
		$komentoExists = $this->komentoExists();
		$commentCount = 0;

		if ($komentoExists && $this->config->get('integrations_komento_profile')) {
			$commentsModel = Komento::getModel('comments');
			$commentCount = $commentsModel->getTotalComment($profile->id);
		}

		// Clear up any notifications that are visible for the user.
		$notifications = ED::model('Notification');
		$notifications->markRead($profile->id, false, array(DISCUSS_NOTIFICATIONS_PROFILE, DISCUSS_NOTIFICATIONS_BADGE));

		// Get the content title for current view type.
		$tabsText = $this->getTabsTitle($viewType);

		// Get the post count based on the current view type.
		$tabsPostsCount = $profile->getPostsNumCount($viewType);

		// Combine the text to form a title.
		$tabsTitle = $tabsText . ' (' . $tabsPostsCount . ')';

		$posts = ED::getPostStatusAndTypes($posts);

		$favPosts = $postsModel->getData('true', 'latest', 'null', 'favourites');
		$favPosts = ED::formatPost($favPosts);

		$this->set('sort', $sort);
		$this->set('pagination', $pagination);
		$this->set('posts', $posts);
		$this->set('badges', $badges);
		$this->set('favPosts', $favPosts);
		$this->set('profile', $profile);
		$this->set('socialUrls', $socialUrls);
		$this->set('viewType', $viewType);
		$this->set('easyblogExists', $easyblogExists);
		$this->set('komentoExists', $komentoExists );
		$this->set('blogCount', $blogCount );
		$this->set('commentCount', $commentCount);
		$this->set('tabsTitle', $tabsTitle);

		parent::display('profile/default');
	}

	public function easyblogExists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
		$exists = JFile::exists($file);

		if (!$exists) {
			return false;
		}

		require_once($file);
		return true;
	}


	public function komentoExists()
	{
		$helperFile = JPATH_ROOT . '/components/com_komento/helpers/helper.php';
		$exists = JFile::exists( $helperFile );

		if (!$exists) {
		 return false;
		}

		require_once($helperFile);
		return true;
	}

	/**
	 * Displays the user editing form
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function edit($tmpl = null)
	{
		if ($this->my->guest) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_YOU_MUST_LOGIN_FIRST'), 'error');
			$this->app->redirect(EDR::_('index.php?option=com_easydiscuss&view=index'));
			return $this->app->close();
		}

		// Set page properties
		$this->setPathway(JText::_('COM_EASYDISCUSS_PROFILE'), EDR::_('index.php?option=com_easydiscuss&view=profile&id=' . $this->my->id));
		$this->setPathway(JText::_('COM_EASYDISCUSS_EDIT_PROFILE'));

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$userparams = new JRegistry($this->profile->get('params'));
		$siteDetails = new JRegistry($this->profile->get('site'));

		// Get configured max size
		$configMaxSize = $this->config->get('main_upload_maxsize', 0);

		if ($configMaxSize > 0) {

			// Convert MB size to Bytes
			// The magic number is 1048576, http://digital.ni.com/public.nsf/allkb/0F8C8B70234EBE308625708B00424DAD
			$configMaxSize = $configMaxSize * 1048576;

			// We convert to bytes because the function is accepting bytes
			$configMaxSize  = ED::string()->bytesToSize($configMaxSize);
		}

		$avatar_config_path = $this->config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = JString::str_ireplace('/', DIRECTORY_SEPARATOR, $avatar_config_path);

		$croppable = false;
		$allowJFBCAvatarEdit = false;

		if ($this->config->get('layout_avatarIntegration') == 'default') {
			$original 	= JPATH_ROOT . '/' . rtrim($this->config->get( 'main_avatarpath' ) , '/' ) . '/' . 'original_' . $this->profile->avatar;

			if (JFile::exists($original)) {
				$size = getimagesize( $original );

				$width = $size[0];
				$height = $size[1];

				// image ratio always 1:1
				$configAvatarWidth = $this->config->get('layout_avatarwidth', 160);
				$configAvatarHeight = $configAvatarWidth;

				if ($width >= $configAvatarWidth && $height >= $configAvatarHeight) {
					$croppable = true;
				}
			}
		}

		// Check if user are allowed to change username
		$changeUsername = JComponentHelper::getParams('com_users')->get('change_login_name') ? '' : 'disabled';

		if ($this->config->get('layout_avatarIntegration') == 'jfbconnect') {
			$hasAvatar = ED::integrate()->jfbconnect($this->profile);

			if (!$hasAvatar) {
				$croppable = true;
				$allowJFBCAvatarEdit = true;
			}
		}

		$avatar = $this->profile->avatar;

		if (!$avatar || $avatar == 'default.png') {
			$avatar = false;
		}

		// Get editor for signature.
		$opt = array('defaults', '');
		$composer = ED::composer($opt);

		$this->set('avatar', $avatar);
		$this->set('croppable', $croppable);
		$this->set('allowJFBCAvatarEdit', $allowJFBCAvatarEdit);
		$this->set('user', $this->my->id);
		$this->set('profile', $this->profile);
		$this->set('configMaxSize', $configMaxSize );
		$this->set('userparams', $userparams);
		$this->set('siteDetails', $siteDetails);
		$this->set('composer', $composer);
		$this->set('changeUsername', $changeUsername);

		parent::display('user/edit');
	}

	public function getTabsTitle($viewType)
	{
		if (!$viewType) {
			$viewType = 'questions';
		}

		$text1 = JText::_('COM_EASYDISCUSS_MY_POSTS');
		$text2 = JText::_('COM_EASYDISCUSS_PROFILE_TAB_' . strtoupper($viewType));

		$text = $text1 . ' - ' . $text2;

		return $text;
	}
}
