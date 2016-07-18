<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

class plgFinderEasyBlog extends FinderIndexerAdapter
{
	protected $context = 'EasyBlog';
	protected $extension = 'com_easyblog';
	protected $layout = 'entry';
	protected $type_title = 'EasyBlog';
	protected $table = '#__easyblog_post';

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Ensure that EasyBlog really exists on the site first
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists()
	{
		// First we check if the extension is enabled.
		if (JComponentHelper::isEnabled($this->extension) == false) {
			return;
		}

		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Delete a url from the cache
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFromCache($id)
	{
		if (!$this->exists()) {
			return;
		}

		$db = EB::db();
		$sql = $db->sql();

		$query = array();
		$query[] = 'SELECT ' . $db->qn('link_id') . ' FROM ' . $db->qn('#__finder_links');
		$query[] = 'WHERE ' . $db->qn('url') . ' LIKE ' . $db->Quote('%option=com_easyblog&view=entry&id=' . $id . '%');

		$query = implode(' ', $query);
		$db->setQuery($query);

		$item = $db->loadResult();

		if (EB::isJoomla30()) {
			$state = $this->indexer->remove($item);
		} else {
			$state = FinderIndexer::remove($item);
		}

		return $state;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ($context == 'easyblog.blog') {
			$id = $this->deleteFromCache($table->id);

		} elseif ($context == 'com_finder.index') {
			$id = $table->link_id;

		} else {
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}

	/**
	 * Method to determine if the access level of an item changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $post, $isNew)
	{
		if (!$this->exists()) {
			return;
		}

		// Only process easyblog items here
		if ($context == 'easyblog.blog' && !$post->isBlank() && !$post->isDraft() && !$post->isPending()) {
			$this->reindex($post->id);
		}

		return true;
	}

	/**
	 * Indexes a post on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	FinderIndexerResult		The item to index
	 * @param	string	The item's format
	 * @return	void
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (!$this->exists()) {
			return;
		}

		// Build the necessary route and path information.
		$item->url = 'index.php?option=com_easyblog&view=entry&id='. $item->id;
		$item->route = EBR::getRoutedURL($item->url, false, true);

		// Remove any /administrator/ segments from the url since the indexer could be executed from the back end
		$item->route = $this->removeAdminSegment($item->route);

		//remove domain here as the route that stored in finder_links shouldnt contain the domain.
		$item->route = str_replace(JURI::root(), '/', $item->route);

		// Get the content path
		$item->path = FinderIndexerHelper::getContentPath($item->route);

		// If there is access defined, just set it to 2 which is special privileges.
		if (! $item->access || $item->access == 0) {
			$item->access = 1;
		} else if ($item->access > 0) {
			$item->access = 2;
		}

		// Load up the post item
		$post = EB::post();
		$post->load($item->id);

		// Get the intro text of the content
		$item->summary = $post->getIntro();

		// Get the contents
		$item->body = $post->getContent('entry', false);

		// If the post is password protected, we do not want to display the contents
		if ($post->isPasswordProtected()) {
			$item->summary = JText::_('PLG_FINDER_EASYBLOG_PASSWORD_PROTECTED');
		} else {

			// we want to get custom fields values.
			$fields = $post->getCustomFields();

			$fieldlib = EB::fields();

			$customfields = array();
			if ($fields) {
				foreach($fields as $field) {
					if ($field->group->hasValues($post)) {
						foreach ($field->fields as $customField) {
							$eachField = $fieldlib->get($customField->type);
							$customfields[] = $eachField->text($customField, $post);
						}
					}
				}

				$customfieldvalues = implode(' ', $customfields);
				$item->body = $item->body . ' ' . $customfieldvalues;
			}
		}

		// Add the author's meta data
		$item->metaauthor = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;
		$item->author = !empty($item->created_by_alias) ? $item->created_by_alias : $item->author;

		// If the post has an image, use it
		$image = $post->getImage('thumbnail', false, true);

		// If there's no image, try to scan the contents for an image to be used
		if (!$image && $post->isLegacy()) {
			$image = EB::string()->getImage($item->body);
		}

		// If we still can't locate any images, use the placeholder image
		if (!$image) {
			$image = EB::getPlaceholderImage();
		}

		$registry = new JRegistry();
		$registry->set('image', $image);

		$item->params = $registry;

		// Add the meta-data processing instructions.
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metakey');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metadesc');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'metaauthor');
		$item->addInstruction(FinderIndexer::META_CONTEXT, 'author');

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'EasyBlog');

		// Add the author taxonomy data.
		if (!empty($item->author) || !empty($item->created_by_alias)) {
			$item->addTaxonomy('Author', !empty($item->created_by_alias) ? $item->created_by_alias : $item->author);
		}

		// Add the category taxonomy data.
		$item->addTaxonomy('Category', $item->category, $item->cat_state, $item->cat_access);

		// Add the language taxonomy data.
		if( empty( $item->language ) )
			$item->language = '*';

		$item->addTaxonomy('Language', $item->language);

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);

		// For Joomla 3.0, the indexer is assigned to the property
		// Index the item.
		if (EB::isJoomla30()) {
			return $this->indexer->index($item);
		}

		return FinderIndexer::index($item);
	}

	private function removeAdminSegment( $url = '' )
	{
		if( $url )
		{
			$url 	= ltrim( $url , '/' );
			$url 	= str_replace('administrator/index.php', 'index.php', $url );
		}

		return $url;
	}

	/**
	 * This method would be invoked by Joomla's indexer
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function setup()
	{
		if (!$this->exists()) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves the sql query used to retrieve blog posts on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	protected function getListQuery($sql = null)
	{
		$db = JFactory::getDbo();

		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);
		$sql->select( 'a.*, b.title AS category, u.name AS author, eu.nickname AS created_by_alias');

        $sql->select('a.published AS state,a.id AS ordering');
		$sql->select('b.published AS cat_state, 1 AS cat_access');
		$sql->select('m.keywords AS metakay, m.description AS metadesc');
 		$sql->from('#__easyblog_post AS a');

 		// we only fetch the primary category.
 		$sql->join('LEFT', '#__easyblog_post_category AS pc ON pc.post_id = a.id and pc.primary = 1');
		$sql->join('INNER', '#__easyblog_category AS b ON b.id = pc.category_id');
		$sql->join('LEFT', '#__users AS u ON u.id = a.created_by');
		$sql->join('LEFT', '#__easyblog_users AS eu ON eu.id = a.created_by');
		$sql->join('LEFT', '#__easyblog_meta AS m ON m.content_id = a.id and m.type = ' . $db->Quote('post'));

		return $sql;
	}
}
