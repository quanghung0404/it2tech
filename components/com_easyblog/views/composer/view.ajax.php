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

require_once(EBLOG_ROOT . '/views/views.php');

class EasyBlogViewComposer extends EasyBlogView
{
	/**
	 * Renders the blog template
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderTemplate()
	{
		$uid = $this->input->get('uid', 0, 'int');

		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($uid);

		if (!$postTemplate->data) {
			return $this->ajax->resolve();
		}

		$document = $postTemplate->getDocument();
		$content = $document->getEditableContent();

		return $this->ajax->resolve(JText::_($document->title), $document->permalink, $content);
	}

	/**
	 * Given a value, normalize the permalink and ensure that it's valid
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function normalizePermalink()
	{
		$original = $this->input->get('permalink', '', 'default');

		// Check if the user really has access to write new post
		$post = EB::post();

		if (!$post->canCreate()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'));
		}

		$model = EB::model('Blog');
		$permalink = $model->normalizePermalink($original);

		return $this->ajax->resolve($permalink);
	}

	/**
	 * Confirmation to delete post
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$uid = $this->input->get('uid', '', 'default');
		$post = EB::post($uid);

		if (!$post->canDelete()) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_POST'));
		}

		$theme = EB::template();
		$theme->set('uid', $uid);
		$contents = $theme->output('site/composer/dialogs/delete.post');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Deletes blog posts from composer view
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete()
	{
		// Check for tokens
		EB::checkToken();


		// Get the list of blog id's
		$ids = $this->input->get('ids', '', 'array');

		// Get any return url
		$return = EB::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false);

		foreach ($ids as $id) {

			$post = EB::post($id);

			if (!$post->canDelete()) {
				$this->info->set(JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG'), 'error');
				return $this->app->redirect($return);
			}

			$post->delete();
		}

		EB::info()->set(JText::_('COM_EASYBLOG_DASHBOARD_DELETE_SUCCESS'), 'success');

		return $this->ajax->resolve($return);
	}	

	/**
	 * Confirmation to close composer launcher
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmClose()
	{
		$theme = EB::template();
		$contents = $theme->output('site/composer/dialogs/close.composer');
		return $this->ajax->resolve($contents);
	}

	/**
	 * Retrieves suggestions for keywords based on the content
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function suggestKeywords()
	{

		// ini_set('xdebug.var_display_max_depth', 5);
		// ini_set('xdebug.var_display_max_children', 256);
		// ini_set('xdebug.var_display_max_data', (1024 * 4));

		// @TODO: Check if the author has access to write and publish post
		$content = $this->input->get('data', '', 'default');

		$url = 'http://lang.stackideas.com/index.php?option=com_lang&view=keywords&layout=compute';

		// Load up the connector first.
		$connector = EB::connector();
		$connector->addUrl($url);
		$connector->addQuery('text', $content);
		$connector->setMethod('POST');
		$connector->execute();

		$result = $connector->getResult($url, false, true);
		$keywords = json_decode($result);

		$this->ajax->resolve($keywords->result);
	}

	/**
	 * Lists down recent articles created by the author
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listArticles()
	{
		// Ensure that the user is logged in
		EB::requireLogin();

		$exclude = $this->input->get('exclude', '', 'default');

		$model = EB::model('Blog');
		$items = $model->getUserPosts($this->my->id, array('exclude' => $exclude));

        if (!$items) {
            return $this->ajax->resolve($items);
        }

		$posts = array();

		foreach ($items as $item) {
			$post = EB::post();
			$post->bind($item, array('force' => true));

			// Set a formatted date
			$post->formattedDate = EB::date($post->created)->format(JText::_('DATE_FORMAT_LC2'));
			$post->intro = $post->getIntro(true);

			$post->permalink = $post->getExternalPermalink();

			$posts[] = $post;
		}

		$theme = EB::template();
		$theme->set('posts', $posts);

		$output = $theme->output('site/composer/posts/post');

		return $this->ajax->resolve($output);

	}

	/**
	 * This does nothing apart from keeping the user's connection active
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function keepAlive()
	{
		return $this->ajax->resolve();
	}

	/**
	 * Renders a list of authors available on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listAuthors()
	{
		$my = JFactory::getUser();

		// Anyone with moderate_entry acl is also allowed to change author.
		if (!EB::isSiteAdmin() && !$this->acl->get('moderate_entry')) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		$authors = array();
		$pagination = null;

		if (!EB::isSiteAdmin() && !$this->acl->get('moderate_entry')) {
			// always return the current user.
			$user = EB::user($my->id);
			$authors[] = $user;
		} else {

			$model = EB::model('Users');
			$result = $model->getUsers(true, true);
			$pagination = $model->getPagination(true);

			if (!$result) {
				// always return the current user.
				$user = EB::user($my->id);
				$authors[] = $user;
			} else {

				//preload users
				$ids = array();
				foreach ($result as $row) {
					$ids[] = $row->id;
				}

				EB::user($ids);

				foreach ($result as $row) {
					$user = EB::user($row->id);
					$authors[] = $user;
				}

			}
		}

		// Get the selected author
		$selected = $this->input->get('selected', 0, 'int');

		$template = EB::template();
		$template->set('selected', $selected);
		$template->set('authors', $authors);
		$template->set('pagination', $pagination);

		$output = $template->output('site/composer/form/author/author');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders a list of associates the author has to the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function listAssociates()
	{
		// Associates may compromise of teams, groups, events etc.
		$associates = array('teams' => array(), 'events' => array(), 'groups' => array());

		// Check if the user is really allowed to use this
		if (!$this->my->id) {
			return $this->ajax->reject(JText::_('COM_EASYBLOG_NOT_ALLOWED'));
		}

		// Get a list of selected items
		$source_id = $this->input->get('source_id', 0, 'int');
		$source_type = $this->input->get('source_type', '', 'default');

		// List teams the user joined on the site
		$model = EB::model('TeamBlogs');
		$teams = $model->getTeamJoined($this->my->id);

		if ($teams) {
			foreach ($teams as $team) {
				$obj = new stdClass();
				$obj->title = $team->title;
				$obj->source_id = $team->id;
				$obj->source_type = EASYBLOG_POST_SOURCE_TEAM;
				$obj->type = 'team';
				$obj->avatar = $team->getAvatar();
				$associates['teams'][] = $obj;
			}
		}

		// EasySocial groups
		$groups = EB::easysocial()->getGroups();
		$events = EB::easysocial()->getEvents();


		// List groups the user joined on the site
		$groups = array_merge($groups, EB::jomsocial()->getGroups());
		$events = array_merge($events, EB::jomsocial()->getEvents());

		// Assign them into the main object.
		$associates['groups'] = $groups;
		$associates['events'] = $events;


		$template = EB::template();
		$template->set('source_id', $source_id);
		$template->set('source_type', $source_type);
		$template->set('associates', $associates);

		$output = $template->output('site/composer/form/associates');

		return $this->ajax->resolve($output);
	}

	/**
	 * Renders the composer
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function manager()
	{
		$this->ajax->verifyAccess();

		// Ensure user has permission to create entry.
		if (!$this->acl->get('add_entry')) {

			$exception = EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG');
			$excepton->html = EB::template()->output('site/composer/blocked');

			return $this->ajax->reject($exception);
		}

		// null = new post
		// 63   = post 63 from post table
		// 63.2 = post 63, revision 2 from history table
		$uid = $this->input->getVar('uid', null);

		// If no id given, create a new post.
		$post = EB::post($uid);

		if (!$uid) {
			$post->create();
			$uid = $post->uid;
		}

		// Create manager manifest object
		$manifest = new stdClass();
		$manifest->uid = $uid;
		$manifest->doctype = $post->doctype;
		$manifest->title = $post->title;

		// If this is an EasyBlog document,
		// return the inline html of the manager.
		if ($manifest->doctype == 'ebd') {
			$manifest->html = EB::composer()->renderManager($uid);
		} else {
			// If this is a Legacy Document
			// return the iframe url to the manager.
			$manifest->url = EASYBLOG_JOOMLA_URI . '/index.php?option=com_easyblog&view=dashboard&layout=composer&tmpl=component' . ($uid ? "&uid=$uid" : '');
		}

		return $this->ajax->resolve($manifest);
	}

	/**
	 * Allows uploading of an audio file to the server temporarily.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function uploadAudio()
	{
		// Check for request forgeries
		// EB::checkToken();

		// Ensure that the user is logged in
		EB::requireLogin();

		// Ensure that the user really has permissions to create blog posts on the site
		if (!$this->acl->get('add_entry')) {

			EB::exception('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG', EASYBLOG_MSG_ERROR)->setGlobal();

			return $this->ajax->reject();
		}

		$file = $this->input->files->get('file');

		if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
			echo JText::_("COM_EASYBLOG_COMPOSER_UNABLE_TO_LOCATE_TEMPORARY_FILE");
			exit;
		}

		// Upload this file into their respective images folder.
        $mm = EB::mediamanager();
        $path = $mm->getAbsolutePath('/', 'user:' . $this->my->id);
        $uri = $mm->getAbsoluteURI('/', 'user:' . $this->my->id);

        $result = $mm->upload($file, 'user:' . $this->my->id);

		// Get the audio player which needs to be embedded on the composer.
		$player = EB::audio()->getPlayer($result->url);

		$obj = new stdClass();
		$obj->title = $result->title;
		$obj->player = $player;
		$obj->file = $result->url;
		$obj->path = $result->path;

		echo json_encode($obj);
		exit;
	}

	/**
	 * Location suggestions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLocations()
	{
		// Require user to be logged in
		EB::requireLogin();

		$lat = $this->input->get('latitude', '', 'string');
		$lng = $this->input->get('longitude', '', 'string');
		$query = $this->input->get('query', '', 'string');

		// Get the configured service provider for location
		$provider = $this->config->get('location_service_provider');

		$service = EB::location($provider);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		if ($lat && $lng) {
			$service->setCoordinates($lat, $lng);
		}

		if ($query) {
			$service->setSearch($query);
		}

		$venues = $service->getResult($query);

		if ($service->hasErrors()) {
			return $this->ajax->reject($service->getError());
		}

		return $this->ajax->resolve($venues);
	}

	/**
	 * Renders the embed video dialog for legacy posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function embedVideoDialog()
	{
		EB::requireLogin();

		$theme = EB::template();
		$output = $theme->output('site/mediamanager/dialog.embed');

		return $this->ajax->resolve($output);
	}

	/**
	 * Cancel file size warning
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function cancelFileSizeWarning()
	{
		$theme = EB::template();
		$contents = $theme->output('site/composer/dialogs/cancel.warning');
		return $this->ajax->resolve($contents);
	}
}
