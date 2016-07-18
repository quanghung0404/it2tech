<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewBloggers extends EasyBlogAdminView
{
	/**
	 * Displays the list of authors from the site.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		// Check for access
		$this->checkAccess('easyblog.manage.user');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Set heading
		$this->setHeading('COM_EASYBLOG_TITLE_BLOGGERS', '', 'fa-group');

		$filter_state = $this->app->getUserStateFromRequest( 'com_easyblog.users.filter_state', 		'filter_state', 	'*', 'word' );

		$search = $this->app->getUserStateFromRequest( 'com_easyblog.users.search', 			'search', 			'', 'string' );
		$search = trim(JString::strtolower( $search ) );

		$order = $this->app->getUserStateFromRequest( 'com_easyblog.users.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $this->app->getUserStateFromRequest( 'com_easyblog.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		// Get data from the model
		$isBrowse = $this->input->get('browse', 0, 'int');
		$model = EB::model('Bloggers');

		$result = $model->getUsers($isBrowse);
		$pagination = $this->get('Pagination');

		$authors = array();

		if ($result) {

			foreach ($result as $row) {
				$author = EB::user($row->id);
				$author->usergroups = $this->getGroupTitle($author->id);

				if (! $isBrowse) {
					$author->postCount = $row->totalPost;
				}

				$author->featured = $row->featured ? true : false;
				$authors[] = $author;
			}
		}

		$browse = $this->input->get('browse', 0, 'int');
		$browsefunction = $this->input->get('browsefunction', 'insertMember', 'string');

		$this->set('browse', $browse);
		$this->set('browsefunction', $browsefunction);
		$this->set('authors', $authors);
		$this->set('pagination', $pagination);
		$this->set('state', JHTML::_('grid.state', $filter_state));
		$this->set('search', $search);
		$this->set('order', $order);
		$this->set('orderDirection', $orderDirection);

		parent::display('bloggers/default');
	}

	/**
	 * Displays the author's form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function form()
	{
		// Check for access
		$this->checkAccess('easyblog.manage.user');
		
		// Get the author's id
		$id = $this->input->get('id', 0, 'int');
		$author = EB::user($id);

		// Get the session data
		$post = EB::getSession('EASYBLOG_REGISTRATION_POST');

		// Set heading
		$title = 'COM_EASYBLOG_TITLE_EDIT_AUTHOR';

		if (!$id) {
			$title = 'COM_EASYBLOG_TITLE_CREATE_AUTHOR';
		}

		JToolBarHelper::title(JText::_($title), 'users');

		$this->setHeading($title, '', 'fa-user');

		$user = JFactory::getUser($id);

		// Determines if this is a new user or not
		$isNew = $user->id == 0 ? true : false;

		if ($isNew && !empty($post)) {

			unset($post['id']);

			$pwd = $post['password'];

			unset($post['password']);
			unset($post['password2']);

			$user->bind($post);

			$post['password'] = $pwd;


			$author->bind($post);
		}

		// Load up feedburner data
		$feedburner = EB::table('Feedburner');
		$feedburner->load($author->id);

		// Load up twitter oauth client
		$twitter = EB::table('OAuth');
		$twitter->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_TWITTER, 'system' => false));

		// Load up linkedin oauth table
		$linkedin = EB::table('OAuth');
		$linkedin->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_LINKEDIN, 'system' => false));

		// Load up facebook oauth table
		$facebook = EB::table('OAuth');
		$facebook->load(array('user_id' => $user->id, 'type' => EBLOG_OAUTH_FACEBOOK, 'system' => false));

		$facebookClient = EB::oauth()->getClient(EBLOG_OAUTH_FACEBOOK);
		$twitterClient = EB::oauth()->getClient(EBLOG_OAUTH_TWITTER);
		$linkedinClient = EB::oauth()->getClient(EBLOG_OAUTH_LINKEDIN);

		// Load up adsense data
		$adsense = EB::table('Adsense');
		$adsense->load($author->id);

		// If this is a new author and the post was submitted before
		if ($isNew && $post) {
			$feedburner->url = $post['feedburner_url'];

			$twitter->message = $post['integrations_twitter_message'];
			$twitter->auto = $post['integrations_twitter_auto'];

			$linkedin->auto = $post['integrations_linkedin_auto'];
			$linkedin->private = isset( $post['integrations_linkedin_private'] ) ? $post['integrations_linkedin_private'] : false;

			$facebook->auto = $post['integrations_facebook_auto'];

			$adsense->published	= $post['adsense_published'];
			$adsense->code = $post['adsense_code'];
			$adsense->display = $post['adsense_display'];
		}

		// Get the WYSIWYG editor
		$editor = JFactory::getEditor();

		// Get the user params
		$params = $user->getParameters(true);

		// Get the params
		$bloggerParams	= $author->getParams();

		// Allow admin edit user multiple theme option from backend
		$multithemes = new stdClass();
		$multithemes->enable = $this->config->get('layout_enablebloggertheme', true);

		if( !is_array($this->config->get('layout_availablebloggertheme'))) {
			$multithemes->availableThemes = explode('|', $this->config->get('layout_availablebloggertheme'));
		}

		$multithemes->selectedTheme = $bloggerParams->get('theme', 'global');

		// Load up joomla's user forms
		require_once(JPATH_ADMINISTRATOR . '/components/com_users/models/user.php');

		$language = JFactory::getLanguage();
		$language->load('com_users', JPATH_ADMINISTRATOR);

		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users/models/fields');
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_users/model/form');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_users/model/field');

		$jUserModel = new UsersModelUser();

		$form = $jUserModel->getForm();
		$form->setValue('password', null);
		$form->setValue('password2', null);

		$this->set('linkedinClient', $linkedinClient);
		$this->set('twitterClient', $twitterClient);
		$this->set('facebookClient', $facebookClient);
		$this->set('form', $form);
		$this->set('editor', $editor);
		$this->set('bloggerParams', $bloggerParams);
		$this->set('user', $user);
		$this->set('author', $author);
		$this->set('params', $params);
		$this->set('feedburner', $feedburner);
		$this->set('adsense', $adsense);
		$this->set('twitter', $twitter);
		$this->set('facebook', $facebook);
		$this->set('linkedin', $linkedin);
		$this->set('isNew', $isNew);
		$this->set('post', $post);
		$this->set('multithemes', $multithemes);

		parent::display('bloggers/form');
	}


	public function getGroupTitle( $user_id )
	{
		$db = EasyBlogHelper::db();
		$sql = "SELECT title FROM ".$db->nameQuote('#__usergroups')." ug left join ".
				$db->nameQuote('#__user_usergroup_map')." map on (ug.id = map.group_id)".
				" WHERE map.user_id=". $db->Quote( $user_id );

		$db->setQuery($sql);
		$result = $db->loadResultArray();
		return nl2br( implode("\n", $result) );
	}

	public function getPostCount( $id )
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE `created_by`=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function registerToolbar()
	{
		if ($this->getLayout() == 'form') {
			JToolBarHelper::apply('bloggers.apply');
			JToolbarHelper::save('bloggers.save');
			JToolBarHelper::cancel('bloggers.cancel');

		}

		if ($this->getLayout() == 'default') {
			JToolBarHelper::title(JText::_('COM_EASYBLOG_BLOGGERS_TITLE'), 'users');

			JToolbarHelper::addNew('bloggers.create');
			JToolBarHelper::divider();
			JToolBarHelper::custom('bloggers.feature' , 'star' , '' , JText::_( 'COM_EASYBLOG_FEATURE_TOOLBAR' ) );
			JToolBarHelper::custom('bloggers.unfeature' , 'star-empty' , '' , JText::_( 'COM_EASYBLOG_UNFEATURE_TOOLBAR' ) );
			JToolBarHelper::divider();
			JToolbarHelper::deleteList(JText::_('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE'), 'bloggers.delete');

			return;
		}
	}
}
