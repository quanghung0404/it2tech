<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include main controller.
require_once(__DIR__ . '/controller.php');

class EasySocialControllerFields extends EasySocialController
{
	/**
	 * Retrieves a list of custom fields on the site.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getFields()
	{
		$lib 		= FD::fields();

		// TODO: Enforce that group be a type of user , groups only.
		$group 		= JRequest::getWord( 'group' , SOCIAL_FIELDS_GROUP_USER );

		// Get a list of fields
		$model 		= FD::model( 'Apps' );
		$fields 	= $model->getApps( array( 'type' => SOCIAL_APPS_TYPE_FIELDS ) );

		// We might need this? Not sure.
		$data 		= array();

		// Trigger: onSample
		$lib->trigger( 'onSample' , $group , $fields , $data );

		// Once done, pass this back to the view.
		$view 		= FD::getInstance( 'View' , 'Fields' );
		$view->call( __FUNCTION__ , $fields );
	}

	/**
	 * Renders a sample data given the application id.
	 *
	 * @since	1.0
	 * @access	public
	 * @return
	 */
	public function renderSample()
	{
		// Check for request forgeries.
		FD::checkToken();

		// Load the view
		$view	= FD::view( 'Fields' );

		// Get fields library.
		$lib 	= FD::fields();

		// Get the group from the query.
		$group 	= JRequest::getWord( 'group' , SOCIAL_FIELDS_GROUP_USER );

		// Get the application id from the query.
		$id 	= JRequest::getInt( 'appid' );

		// Get the profile id
		$profileId = JRequest::getInt( 'profileid' );

		// If id is not passed in, we need to throw an error.
		if( !$id )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_APPLICATION' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		$field 			= FD::table( 'Field' );
		$field->app_id 	= $id;
		$app = $field->getApp();

		if( !$app )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_APPLICATION' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__, false );
		}

		// Manually push in the profile id
		$field->profile_id = $profileId;

		$field->element = $app->element;

		// Need to be placed in an array as it is being passed as reference.
		$fields	= array( &$field );

		// Prepare the data to be passed to the application
		$data	= array();

		// Load language string.
		FD::language()->loadSite();

		// Process onSample trigger
		$lib->trigger( 'onSample' , $group , $fields , $data );

		$field = $fields[0];

		// Call the view.
		return $view->call( __FUNCTION__ , $field );
	}


	/**
	 * Retrieves the profile page configuration
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderPageConfig()
	{
		// Check for request forgeries.
		FD::checkToken();

		$path = SOCIAL_CONFIG_DEFAULTS . '/fields.header.json';
		$raw = JFile::read($path);

		$params = json_decode($raw);

		foreach ($params as $name => &$field) {

			// Only try to JText the label field if it exists.
			if (isset($field->label)) {
				$field->label = JText::_($field->label);
			}

			// Only try to JText the tooltip field if it exists.
			if (isset($field->tooltip)) {
				$field->tooltip	= JText::_($field->tooltip);
			}

			// If there are options set, we need to jtext them as well.
			if (isset($field->option)) {
				$field->option = FD::makeArray($field->option);

				foreach ($field->option as &$option) {
					$option->label = JText::_($option->label);
				}
			}
		}

		// Get any page id
		$pageId = $this->input->get('pageid', 0, 'int');

		// Load the field step
		$table = FD::table('FieldStep');

		if (!empty($pageId)) {
			$table->load($pageId);
		} else {
			foreach ($params as $name => &$field) {
				$table->$name = $field->default;
			}
		}

		// Convert table into registry format
		$values = FD::registry($table);

		return $this->view->call(__FUNCTION__, $params, $table);
	}

	/**
	 * Render's field configuration.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function renderConfiguration()
	{
		// Check for request forgeries
		ES::checkToken();

		// Get the application id.
		$appId = $this->input->get('appid', 0, 'int');

		// Get the field id. If this is empty, it is a new field item that's being added to the form.
		$fieldId = $this->input->get('fieldid', 0, 'int');

		// Application id should never be empty.
		if (!$appId) {
			$this->view->setMessage('COM_EASYSOCIAL_PROFILES_FORM_FIELDS_INVALID_APP_ID_PROVIDED', SOCIAL_MSG_ERROR);

			return $this->view->call(__FUNCTION__);
		}

		// Load frontend's language file
		ES::language()->loadSite();

		$fields = ES::fields();

		// getFieldConfigParameters is returning a stdClass object due to deep level data
		$config = $fields->getFieldConfigParameters($appId, true);

		// getFieldConfigValues is returning a JRegistry object
		$params = $fields->getFieldConfigValues($appId, $fieldId);

		// Get the html content
		$html = $fields->getConfigHtml($appId, $fieldId);

		return $this->view->call(__FUNCTION__, $config, $params, $html);
	}
}
