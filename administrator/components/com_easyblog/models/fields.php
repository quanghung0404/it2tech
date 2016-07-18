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

require_once(dirname(__FILE__) . '/model.php');

class EasyBlogModelFields extends EasyBlogAdminModel
{
	public $total = null;
	public $pagination = null;

	public function __construct()
	{
		$this->app = JFactory::getApplication();

		parent::__construct();

		$limit			= $this->app->getUserStateFromRequest( 'com_easyblog.fields.limit', 'limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (is_null($this->total)) {
			$db = EB::db();

			$query		= array();
			$query[] 	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields');

			$query 		= implode(' ', $query);

			$db->setQuery($query);

			$total = $db->loadResult();

			$this->total 	= $total;
		}

		return $this->total;
	}

	public function getPagination()
	{
		if (is_null($this->pagination)) {
			jimport('joomla.html.pagination');

			$this->pagination 	= new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->pagination;
	}

	public function getItems()
	{
		$mainframe = JFactory::getApplication();
		$db	= EasyBlogHelper::db();

		$filter_groups = $mainframe->getUserStateFromRequest('com_easyblog.fields.filter_groups', 'filter_groups', '', 'string');
		$search = $mainframe->getUserStateFromRequest('com_easyblog.fields.search', 'search', '', 'string');
		$search = $db->getEscaped(trim(JString::strtolower($search)));

		$query = array();

		$pagination = $this->getPagination();

		$query[] = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields');

		$where = array();

		if ($filter_groups) {
			$where[] = $db->quoteName('id') . '=' . $db->Quote($filter_groups);
		}

		if ($search) {
			$where[] = 'LOWER(' . $db->quoteName('title') . ') LIKE \'%' . $search . '%\' ';
		}

		$where = (count($where)? ' WHERE ' . implode(' AND ', $where) : '');

		$query[] = $where;
		$query[] = 'LIMIT ' . $pagination->limitstart . ',' . $pagination->limit;

		$query = implode(' ', $query);

		$db->setQuery($query);
		$data = $db->loadObjectList();

		return $data;
	}

	/**
	 * Removes association between a custom field group and the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteGroupAssociation($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_category_fields_groups');
		$query .= ' WHERE ' . $db->quoteName('group_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Deletes custom fields values given the field id
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteFieldValue($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_fields_values');
		$query .= ' WHERE ' . $db->quoteName('field_id') . '=' . $db->Quote($id);

		$db->setQuery($query);

		return $db->Query();
	}

	/**
	 * Deletes associated field values for a particular blog post
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function deleteBlogFields($id)
	{
		$db = EB::db();

		$query = 'DELETE FROM ' . $db->quoteName('#__easyblog_fields_values');
		$query .= ' WHERE ' . $db->quoteName('post_id') . '=' . $db->Quote($id);

		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Retrieves the custom field value
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFieldValues($fieldId, $blogId)
	{
		$db = EB::db();

		$values = array();

		if (EB::cache()->exists($blogId, 'posts')) {

			$data = EB::cache()->get($blogId, 'posts');

			if (isset($data['customfields']) && isset($data['customfields'][$fieldId])) {

				foreach($data['customfields'][$fieldId] as $item) {
					$values[] = $item;
				}

				return $values;
			}

			// no customfield values for this post.
			return array();

		} else {

			$query  = 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_values');
			$query .= ' WHERE ' . $db->quoteName('field_id') . '=' . $db->Quote($fieldId);
			$query .= ' AND ' . $db->quoteName('post_id') . '=' . $db->Quote($blogId);

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if (!$result) {
				return $result;
			}

			foreach ($result as $row) {
				$value = EB::table('FieldValue');
				$value->bind($row);

				$values[] = $value;
			}

		}

		return $values;
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlogProperties($categoryId)
	{
		$db 	= EB::db();

		$query	= 'SELECT c.* FROM ' . $db->quoteName('#__easyblog_category_fields_groups') . ' AS a';

		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields_groups') . ' AS b';
		$query .= ' on a.' . $db->quoteName('group_id') . ' = b.' . $db->quoteName('id');
		$query .= ' INNER JOIN ' . $db->quoteName('#__easyblog_fields') . ' AS c';
		$query .= ' on c.' . $db->quoteName('group_id') . ' = b.' . $db->quoteName('id');
		$query .= ' WHERE a.' . $db->quoteName('category_id') . '=' . $db->Quote($categoryId);

		$db->setQuery($query);

		$fields	= $db->loadObjectList();

		// dump($fields);
	}

	/**
	 * Retrieve a list of custom field groups on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroups()
	{
		$db 	= EB::db();

		$query	= 'SELECT * FROM ' . $db->quoteName('#__easyblog_fields_groups');

		$db->setQuery($query);

		$groups	= $db->loadObjectList();

		return $groups;
	}


	/**
	 * Preload a list of custom fields for each posts
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preloadFields($postIds = array())
	{
		$db = EB::db();

		if (!$postIds) {
			return array();
		}

		$query = 'select '.$db->qn('a.id').' as '.$db->qn('cat_fg_id').', '.$db->qn('a.category_id').' as '.$db->qn('cat_fg_category_id').', '.$db->qn('a.group_id').' as '.$db->qn('cat_fg_group_id').', ';
		$query .= $db->qn('fg.id').' as '.$db->qn('fg_id').', '.$db->qn('fg.title').' as '.$db->qn('fg_title').', '.$db->qn('fg.description').' as '.$db->qn('fg_description').', '.$db->qn('fg.created').' as '.$db->qn('fg_created').', '.$db->qn('fg.state').' as '.$db->qn('fg_state').', '.$db->qn('fg.read').' as '.$db->qn('fg_read').', '.$db->qn('fg.write').' as '.$db->qn('fg_write').', '.$db->qn('fg.params').' as '.$db->qn('fg_params').', ';
		$query .= $db->qn('f.id').' as '.$db->qn('f_id').', '.$db->qn('f.group_id').' as '.$db->qn('f_group_id').', '.$db->qn('f.title').' as '.$db->qn('f_title').', '.$db->qn('f.help').' as '.$db->qn('f_help').', '.$db->qn('f.state').' as '.$db->qn('f_state').', '.$db->qn('f.required').' as '.$db->qn('f_required').', '.$db->qn('f.type').' as '.$db->qn('f_type').', '.$db->qn('f.params').' as '.$db->qn('f_params').', '.$db->qn('f.created').' as '.$db->qn('f_created').', '.$db->qn('f.options').' as '.$db->qn('f_options').', ';
		$query .= $db->qn('fv.id').' as '.$db->qn('fv_id').', '.$db->qn('fv.field_id').' as '.$db->qn('fv_field_id').', '.$db->qn('fv.post_id').' as '.$db->qn('fv_post_id').', '.$db->qn('fv.value').' as '.$db->qn('fv_value');
		$query .= ' from '.$db->qn('#__easyblog_category_fields_groups').' as a';
		$query .= ' inner join '.$db->qn('#__easyblog_post_category').' as p on '.$db->qn('a.category_id').' = ' . $db->qn('p.category_id');
		$query .= '	inner join '.$db->qn('#__easyblog_fields_groups').' as fg on '.$db->qn('a.group_id').' = '.$db->qn('fg.id');
		$query .= '	inner join '.$db->qn('#__easyblog_fields').' as f on '.$db->qn('fg.id').' = '.$db->qn('f.group_id');
		$query .= '	left join '.$db->qn('#__easyblog_fields_values').' as fv on '.$db->qn('fv.field_id').' = '.$db->qn('f.id').' and '.$db->qn('fv.post_id').' = '.$db->qn('p.post_id');
		if (count($postIds) == 1) {
			$query .= ' where '.$db->qn('p.post_id').' = '.$db->Quote($postIds[0]);
		} else {
			$query .= ' where p.post_id IN ('.implode(',',$postIds).')';
		}

		// echo $query . '<br/><br/>';

		$db->setQuery($query);

		$results = $db->loadObjectList();

		return $results;
	}

}
