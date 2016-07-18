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

require_once(JPATH_COMPONENT . '/views/views.php');

class EasyBlogViewCategories extends EasyBlogView
{
	/**
	 * Retrieve custom fields based on the category
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomFields()
	{
		// Get the category id
		$id = $this->input->get('id', 0, 'int');
		$postId = $this->input->get('postId', 0, 'int');

		$post = EB::post($postId);

		// @TODO: Check if the user can render the forms

		// Load up the model
		$model = EB::model('Categories');

		// Retrieve the custom field group since each category can only have 1 group
		$group = $model->getCustomFieldGroup($id);

		// Retrieve the custom fields forms
		$fields = $model->getCustomFields($id);

		$theme = EB::template();
		$theme->set('post', $post);
		$theme->set('id', $id);
		$theme->set('group', $group);
		$theme->set('fields', $fields);

		$output = $theme->output('site/composer/form/fields');

		return $this->ajax->resolve($output);
	}

	/**
	 * Retrieve a list of categories a user is allowed to post into
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategories()
	{
		// Get the id to lookup for
		$id = $this->input->get('id', 0, 'int');

		$model  = EB::model('Categories');
		$result = $model->getCategoriesHierarchy();
		$default = '';

		if ($result) {
			foreach ($result as $row) {

				$category = new stdClass();
				$category->id = (int) $row->id;
				$category->title = $row->title;
				$category->parent_id = (int) $row->parent_id;

				$params = new JRegistry($row->params);

				$category->tags = $params->get('tags');

				if ($row->default) {
					$default = $row->id;
				}

				$categories[] = $category;
			}
		}


		return $this->ajax->resolve($categories, $default);
	}
}
