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

class EasyBlogTableTag extends EasyBlogTable
{
	public $id = null;
	public $created_by = null;
	public $title = null;
	public $alias = null;
	public $created = null;
	public $status = null;
	public $published = null;
	public $default = null;
	public $ordering = null;
	public $params = null;

	public function __construct(& $db )
	{
		parent::__construct( '#__easyblog_tag' , 'id' , $db );
	}

	/**
	 * Loads a tag
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function load($id = null, $loadByTitle = false, $debug = false)
	{
		if (!$loadByTitle) {
			static $items = null;

			if (!isset($items[$id])) {
				$items[$id] = parent::load($id);
			}

			return $items[$id];
		}

		static $tags = array();

		$index = $id . $loadByTitle;

		if (!isset($tags[$index])) {
			$db = EB::db();

			$query = array();
			$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_tag');
			$query[] = 'WHERE (';
			$query[] = $db->quoteName('title') . '=' . $db->Quote(JString::str_ireplace('-', ' ', $id));
			$query[] = 'OR ' . $db->quoteName('alias') . '=' . $db->Quote(JString::str_ireplace(':', '-', $id));
			$query[] = ')';

			$query = implode(' ', $query);

			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result) {
				parent::bind($result);

				$tags[$index] = clone $this;
			} else {
				$tags[$index] = false;
			}
		}

		parent::bind($tags[$index]);
	}

	/**
	 * Determines if the alias exists
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function aliasExists()
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_tag' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $this->alias );

		if ($this->id != 0){
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	/**
	 * Determines if the tag already exists
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function exists($title, $isNew = true)
	{
		$db	= EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__easyblog_tag');
		$query[] = 'WHERE ' . $db->quoteName('title') . '=' . $db->Quote($title);

		if (!$isNew){
			$query[] = 'AND ' . $db->quoteName('id') . '!=' . $db->Quote($this->id);
		}

		$query[] = 'LIMIT 1';

		$query = implode(' ', $query);

		$db->setQuery($query);

		$result	= $db->loadResult() > 0 ? true : false;

		return $result;
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	public function bind($data, $ignore = array())
	{
		parent::bind($data, $ignore);

		if (empty($this->created)) {
			$date = EB::date();
			$this->created = $date->toMySQL();
		}

		if (!$this->title) {
			return;
		}

		// we only do this when this is a new tag or tag with empty alias
		if (empty($this->id) || empty($this->alias)) {
			jimport('joomla.filesystem.filter.filteroutput');

			$i = 1;
			while($this->aliasExists() || empty($this->alias)) {

				$this->alias = empty($this->alias) ? $this->title : $this->alias . '-' . $i;
				$i++;
			}
		}

		$this->alias = EBR::normalizePermalink($this->alias);

	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @since 4.0
	 * @return boolean
	 * @param object $db
	 */
	public function delete($pk = null)
	{
		$db = EB::db();

		// Ensure that tag associations are removed
		$this->deletePostTag();

		if ($this->created_by != 0) {

			// Load site language files
			EB::loadLanguages();

			$config = EB::config();

			// Integrations with EasyDiscuss
			$easydiscuss = EB::helper('EasyDiscuss');
			$easydiscuss->log( 'easyblog.delete.tag' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG' , $this->title ) );
			$easydiscuss->addPoint( 'easyblog.delete.tag' , $this->created_by );
			$easydiscuss->addBadge( 'easyblog.delete.tag' , $this->created_by );

			// Assign EasySocial points
			$easysocial 	= EB::helper( 'EasySocial' );
			$easysocial->assignPoints( 'tag.remove' , $this->created_by );

			// Assign jomsocial points
			EB::jomsocial()->assignPoints('com_easyblog.tag.remove', $this->created_by);

			// Assign AUP points
			EB::aup()->assignPoints('plgaup_easyblog_delete_tag', JText::sprintf('COM_EASYBLOG_AUP_TAG_DELETED', $this->title));
		}

		return parent::delete();
	}

	/**
	 * Delete all associated blog posts with this tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deletePostTag()
	{
		$model = EB::model('Tags');

		return $model->deleteAssociation($this->post_id);
	}

	/**
	 * Retrieves the number of posts that are associated with this tag.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostCount()
	{
		$db = EB::db();

		$query = array();
		$query[] = 'SELECT COUNT(1) FROM ' . $db->qn('#__easyblog_post_tag');
		$query[] = 'WHERE ' . $db->qn('tag_id') . '=' . $db->Quote($this->id);

		$query = implode(' ', $query);
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}


	/**
	 * Retrieves rss link for the tag
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getRssLink()
	{
		return EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->id, false, 'tag');
	}

	/**
	 * Gets the translated tag title
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTitle()
	{
		return JText::_($this->title);
	}

	/**
	 * Retrieves the external permalink for this blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getExternalPermalink($format = null)
	{
		$format = !is_null($format) ? '&format=' . $format : '';
		$link = EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->id . $format, false, true, true);

		return $link;
	}

	/**
	 * Retrieves the alias of a tag
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAlias()
	{
		$config = EB::config();
		$alias = $this->alias;

		if ($config->get('main_sef_unicode') || !EBR::isSefEnabled()) {
			$alias = $this->id . '-' . $this->alias;
		}

		return $alias;
	}

	/**
	 * Gets the tag permalink
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink($xhtml = true)
	{
		$link = 'index.php?option=com_easyblog&view=tags&layout=tag&id=' . $this->getAlias();
		$link = EBR::_($link, $xhtml);

		return $link;
	}

	/**
	 * Saves a tag on the site
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function store($updateNulls = false)
	{
		// Check for empty title
		if (empty($this->title)){
			$this->setError(JText::_('COM_EASYBLOG_INVALID_TAG'));
			return false;
		}

		// @rule: Check if such tag exists.
		if ($this->exists($this->title, !$this->id)){
			$this->setError(JText::_('COM_EASYBLOG_TAG_ALREADY_EXISTS'));

			return false;
		}

		// @task: If alias is null, we need to generate them here.
		jimport('joomla.filesystem.filter.filteroutput');

		$i = 1;

		while($this->aliasExists() || empty($this->alias)) {
			$this->alias = empty($this->alias) ? $this->title : $this->alias . '-' . $i;
			$i++;
		}

		$this->alias = EBR::normalizePermalink($this->alias);

		$date = EB::date($this->created);
		$this->created = $date->toSql();

	    $isNew	= !$this->id;
	    $state	= parent::store();
	    $my		= JFactory::getUser();

	    // Integrate with 3rd party extension
	    if ($isNew && $my->id != 0) {

	    	EB::loadLanguages();
	    	$config = EB::getConfig();

			// @rule: Integrations with EasyDiscuss
			EB::easydiscuss()->log('easyblog.new.tag', $my->id, JText::sprintf('COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_TAG', $this->title));
			EB::easydiscuss()->addPoint('easyblog.new.tag', $my->id);
			EB::easydiscuss()->addBadge('easyblog.new.tag', $my->id);

			// Assign EasySocial points
			EB::easysocial()->assignPoints('tag.create', $my->id);

			// Assign jomsocial points
			EB::jomsocial()->assignPoints('com_easyblog.tag.add', $my->id);

			// AUP
			EB::aup()->assignPoints('plgaup_easyblog_add_tag', $my->id, JText::sprintf('COM_EASYBLOG_AUP_TAG_ADDED', $this->title));
		}

	    return $state;
	}

	/**
	 * Retrieve a list of tags that is associated with this tag
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultParams()
	{

		static $_cache = null;

		if (! $_cache) {

			$manifest = JPATH_ROOT . '/components/com_easyblog/views/tags/tmpl/tag.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$_cache = new JRegistry($obj);
		}

		return $_cache;
	}
}
