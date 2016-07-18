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

require_once(dirname(__FILE__) . '/table.php');

class EasyBlogTableField extends EasyBlogTable
{
	public $id = null;
	public $group_id = null;
	public $title = null;
	public $help = null;
	public $state = null;
	public $required = null;
	public $type = null;
	public $params = null;
	public $options = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__easyblog_fields', 'id', $db);
	}

	/**
	 * Ensure that the field has proper values
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function check()
	{
		if (!$this->title) {
			$this->setError(JText::_('COM_EASYBLOG_FIELDS_EMPTY_TITLE_ERROR'));

			return false;
		}

		return true;
	}
	/**
	 * Retrieve the custom field title
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
	 * Retrieve the custom field's help
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getHelp()
	{
		return JText::_($this->help);
	}

	/**
	 * Override parent's logics
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete($pk = null)
	{
		// Request parent to delete items first
		$state = parent::delete($pk);

		// When a custom field is deleted from the site, delete the values too
		$model = EB::model('Fields');
		$model->deleteFieldValue($this->id);

		return $state;
	}

	/**
	 * Publishes a field group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$this->state = true;

		return $this->store();
	}

	public function setRequired()
	{
		$this->required = 1;

		return $this->store();
	}

	public function removeRequired()
	{
		$this->required = 0;

		return $this->store();
	}



	/**
	 * Unpublishes a field group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function unpublish()
	{
		$this->state = false;

		return $this->store();
	}

	/**
	 * Retrieves the title of the group
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGroupTitle()
	{
		$group 	= EB::table('FieldGroup');
		$group->load($this->group_id);

		return JText::_($group->title);
	}

	/**
	 * Retrieve the display value
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDisplay(EasyBlogPost &$post)
	{
		$fields = EB::fields();

		$field = $fields->get($this->type);

		return $field->display($this, $post);
	}

	/**
	 * Retrieves the form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getForm(EasyBlogPost $post, $elementName = 'fields')
	{
		$fields = EB::fields();

		$field = $fields->get($this->type);
		$field->setFormElement($elementName);

		return $field->form($post, $this);
	}
}
