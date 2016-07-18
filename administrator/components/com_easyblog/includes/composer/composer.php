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

class EasyBlogComposer
{
	public function __construct()
	{
		$this->my = JFactory::getUser();
		$this->app = JFactory::getApplication();
		$this->input = EB::request();
		$this->acl = EB::acl();
		$this->config = EB::config();

		// Set the user project
		$this->user = EB::user($this->my->id);
	}

	/**
	 * Retrieves the dropbox data for the current user
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getFlickrData()
	{
		// Test if the user is already associated with dropbox
		$oauth  = EB::table('OAuth');

		// Test if the user is associated with flickr
		$state	= $oauth->loadByUser($this->my->id, EBLOG_OAUTH_FLICKR);

		$data   = new stdClass();
		$data->associated	= $state;
		$data->callback  = 'flickr' . rand();
		$data->redirect  = base64_encode(rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&view=media&layout=flickrLogin&tmpl=component&callback=' . $data->callback);

		// Default login to the site
		$data->login = rtrim(JURI::root(), '/') . '/index.php?option=com_easyblog&controller=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect;


		if ($this->app->isAdmin()) {
			$data->login = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_easyblog&c=oauth&task=request&type=' . EBLOG_OAUTH_FLICKR . '&tmpl=component&redirect=' . $data->redirect . '&id=' . $this->my->id;
		}

		return $data;
	}

	/**
	 * Retrieves a list of categories
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getParentCategories()
	{
		// Load up categories available on the site
		$model = EB::model('Categories');
		$result = $model->getParentCategories('', 'all', true, true);

		$categories = new stdClass();
		$categories->primary = null;
		$categories->items = array();

		foreach ($result as $row) {
			$category = EB::table('Category');
			$category->bind($row);

			if ($category->default) {
				$categories->primary = $category;
			}

			$categories->items[] = $category;
		}

		// if there is no primary category,
		// let select the 1st category as the default one.
		if(! $categories->primary) {
			$categories->primary = $categories->items[0];
		}

		return $categories;
	}

	/**
	 * Retrieves the meta for a post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostMeta($post)
	{
		$model = EB::model('Metas');
		$meta = $model->getPostMeta($post->id);

		return $meta;
	}

	/**
	 * Retrieves a list of tags associated with a blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getPostTags($post)
	{
		$model = EB::model('PostTag');
		$tags = $model->getBlogTags($post->id);

		return $tags;
	}

	/**
	 * Retrieves a list of teams from the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTeams($authorId = null)
	{
		$model = EB::model('TeamBlogs');

		if ($authorId) {
			$teams = $model->getTeamJoined($authorId);
		}

		return $teams;
	}

	/**
	 * Retrieves the html codes for composer
	 *
	 * @since	4.0
	 * @access	public
	 * @param	int		The unique item id.
	 * @param	string	The type of the post, whether this is a post or a draft
	 * @return
	 */
	public function renderManager($uid = null)
	{
		// Get the current post library
		$post = EB::post($uid);

		// Check if user has permissions to write new entry
		if (!$post->canCreate()) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG'));
		}

		// If the blog post is edited, ensure that the user has access to edit this entry
		if (!$post->canEdit()) {
			return JError::raiseError(500, JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_BLOG'));
		}

		// Get the editor to use
		$editorSetting = $this->user->getEditor();
		$editorSetting = $editorSetting == 'composer' ? JFactory::getConfig()->get('editor') : $editorSetting;
		$editor = JFactory::getEditor($editorSetting);

		// Get a list of parent categories
		$parentCategories = $this->getParentCategories();

		// Get the default category.
		$defaultCategoryId = EB::model('Category')->getDefaultCategoryId();

		// Allow caller to alter default category
		if ($post->isBlank()) {
			$defaultCategoryId = $this->input->get('category', $defaultCategoryId, 'int');
		}

		$primaryCategory = $post->getPrimaryCategory();

		// If the menu has a default category, the primary category should be the pre-selected one.
		// And this shouldn't happening on draft post because user no need to re-configure which category should set as primary again
		if (!$post->isFromFeed() && $post->isNew() && !$post->isDraft() && $defaultCategoryId) {
			$primaryCategory = EB::table('Category');
			$primaryCategory->load($defaultCategoryId);
		}

		// Get a list of categories
		// Prepare selected category
		$selectedCategories = array();

		foreach ($post->getCategories() as $row) {
			$selectedCategories[] = (int) $row->id;
		}

		// if there is no category selected, or this is a new blog post, lets use the default category id.
		if (!$selectedCategories && $defaultCategoryId) {
			$selectedCategories[] = $defaultCategoryId;
		}

		// Prepare categories object
		$categories = array();

		$cats = EB::model('Categories')->getCategoriesHierarchy(false);

		foreach ($cats as $row) {

			$category = new stdClass();
			$category->id = (int) $row->id;
			$category->title = $row->title;
			$category->parent_id = (int) $row->parent_id;

			$params = new JRegistry($row->params);

			$category->tags = $params->get('tags');


			if (!$category->tags) {
				$category->tags = array();
			} else {
				$tags = explode(',', $category->tags);
				for($i = 0; $i < count($tags); $i++) {
					$tags[$i] = JString::trim($tags[$i]);
				}
				$category->tags = implode(',', $tags);
			}

			// Cross check if this category is selected
			$category->selected = in_array($category->id, $selectedCategories);

			// check if this is a primary category or not
			$category->isprimary = $category->id == $primaryCategory->id;

			$categories[] = $category;
		}

		// Prepare tags
		$tags = array();
		foreach ($post->getTags() as $row) {
			$tag = new stdClass();
			$tag->id = (int) $row->id;
			$tag->title = $row->title;

			$tags[] = $tag;
		}

		// Render default post templates
		$postTemplatesModel = EB::model('Templates');
		$postTemplates = $postTemplatesModel->getPostTemplates($this->my->id);

		// Get the post's author
		$author = $post->getAuthor();

		// Get a list of revisions for this post
		$revisions = $post->getRevisions();

		// Get the current revision for the post
		$workingRevision = $post->getWorkingRevision();

		// Determines if the current page load should be loading from block templates
		$postTemplate = EB::table('PostTemplate');
		$postTemplate->load($this->input->get('block_template', 0, 'int'));

		if (!$postTemplate->id || $postTemplate->id == 1) {
			$postTemplate = false;
		}

        // Get available blocks on the site
        $blocks = EB::blocks()->getAvailableBlocks();

        // Determines if we should display the custom fields tab by default
        $displayFieldsTab = false;

        // Get a list of selected categories
        $selectedCategories = $post->getCategories();

        // If there's no selected categories, we assume that the primary category
        if (!$selectedCategories) {
        	$selectedCategories = array($primaryCategory);
        }

        // If explicitly configured to be hidden, skip the checks altogether
        if ($this->config->get('layout_composer_fields')) {
	        foreach ($selectedCategories as $category) {
	        	if ($category->hasCustomFields()) {
	        		$displayFieldsTab = true;
	        		break;
	        	}
	        }
        }

        $user = EB::table('Profile');
        $user = $user->load($this->my->id);

        //available languages
        $languages = JLanguageHelper::getLanguages('lang_code');

        //post association
        $associations = $post->getAssociation();

        // Short language tag
		$momentLanguage = EB::getMomentLanguage();

		$theme = EB::template();
		$theme->set('user', $user);
		$theme->set('displayFieldsTab', $displayFieldsTab);
		$theme->set('postTemplate', $postTemplate);
		$theme->set('postTemplates', $postTemplates);
		$theme->set('workingRevision', $workingRevision);
		$theme->set('revisions', $revisions);
		$theme->set('editor', $editor);
		$theme->set('primaryCategory', $primaryCategory);
		$theme->set('categories', $categories);
		$theme->set('tags', $tags);
		$theme->set('post', $post);
		$theme->set('author', $author);
		$theme->set('uuid', uniqid());
		$theme->set('blocks', $blocks);
		$theme->set('languages', $languages);
		$theme->set('associations', $associations);
		$theme->set('momentLanguage', $momentLanguage);

		// Determines if the source id and source type is provided
		$sourceId = $this->input->get('source_id', 0, 'int');
		$sourceType = $this->input->get('source_type', '', 'default');
		$contribution = '';

		if ($sourceId && $sourceType) {
			$contribution = EB::contributor()->load($sourceId, $sourceType);
			$post->source_id = $sourceId;
			$post->source_type = $sourceType;
		}

		$theme->set('contribution', $contribution);
		$theme->set('sourceId', $sourceId);
		$theme->set('sourceType', $sourceType);

		$output = $theme->output('site/composer/manager');

		return $output;
	}
}
