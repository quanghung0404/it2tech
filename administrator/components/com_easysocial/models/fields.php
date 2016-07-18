<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Include parent model.
FD::import( 'admin:/includes/model' );

class EasySocialModelFields extends EasySocialModel
{
	private $data			= null;
	protected $total			= null;

	public function __construct()
	{
		parent::__construct( 'fields' );
	}

	/**
	 * Retrieves a single custom field data
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCustomField( $profileId , $element )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->select( '#__social_fields' , 'a' );
		$sql->column( 'a.*' );
		$sql->join( '#__social_fields_steps' , 'b' );
		$sql->on( 'b.id' , 'a.step_id' );
		$sql->join( '#__social_apps' , 'c' );
		$sql->on( 'c.id' , 'a.app_id' );
		$sql->where( 'b.uid' , $profileId );
		$sql->where( 'b.type' , 'profiles' );
		$sql->where( 'c.element' , $element );
		$sql->where( 'c.state' , SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );

		$row 	= $db->loadObject();

		if( !$row )
		{
			return $row;
		}

		$field	= FD::table( 'Field' );
		$field->bind( $row );

		return $field;
	}

	/**
	 * Responsible to create the default custom fields for a profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createDefaultItems($categoryId, $categoryType, $nodeType)
	{
		// $categoryId is the id on the category
		// $categoryType is the type of the category, eg: profiles, clusters
		// $nodeType is the unit within the category instead of the category representation, eg: user, group, event
		// This is because while events and groups is considered clusters, they have different sets of "default fields" to init

		$path = SOCIAL_ADMIN_DEFAULTS . '/fields/' . $nodeType . '.json';

		if (!JFile::exists($path)) {
			return false;
		}

		$defaults = FD::makeObject($path);

		// If there's a problem decoding the file, log some errors here.
		if (!$defaults) {
			$this->setError('Empty default object');

			return false;
		}

		// Init sequence
		$sequence = 1;

		// Init uniquekeys
		$uniqueKeys = array();

		// Let's go through each of the default items.
		foreach ($defaults as $step) {
			// Create default step for this profile.
			$stepTable = FD::table('FieldStep');
			$stepTable->bind($step);

			// Set the sequence
			$stepTable->sequence = $sequence++;

			// Map the correct uid and type.
			$stepTable->uid = $categoryId;
			$stepTable->type = $categoryType;

			// Set the state
			$stepTable->state = isset($step->state) ? $step->state : SOCIAL_STATE_PUBLISHED;

			// Set this to show in registration by default
			$stepTable->visible_registration = isset($step->visible_registration) ? $step->visible_registration : SOCIAL_STATE_PUBLISHED;

			// Set this to show in edit by default
			$stepTable->visible_edit = isset($step->visible_edit) ? $step->visible_edit : SOCIAL_STATE_PUBLISHED;

			// Set this to show in display by default
			$stepTable->visible_display = isset($step->visible_display) ? $step->visible_display : SOCIAL_STATE_PUBLISHED;

			// Try to store the default steps.
			$state = $stepTable->store();

			if (!$state || !$step->fields) {
				continue;
			}

			// Now we need to create all the fields that are in the current step
			// Init ordering
			$ordering = 0;

			foreach ($step->fields as $field) {
				$appTable = FD::table('App');
				$state = $appTable->load(array('element' => $field->element, 'group' => $nodeType, 'type' => SOCIAL_APPS_TYPE_FIELDS));

				// If the app doesn't exist, we shouldn't add it.
				if ($state && ($appTable->state == SOCIAL_STATE_PUBLISHED || $appTable->core == SOCIAL_STATE_PUBLISHED)) {
					$fieldTable = FD::table('Field');
					$fieldTable->bind($field);

					// Set the ordering
					$fieldTable->ordering				= $ordering++;

					// Ensure that the main items are being JText correctly.
					$fieldTable->title					= $field->title;
					$fieldTable->description			= $field->description;
					$fieldTable->default				= isset($field->default) ? $field->default : '';

					// Set the app id.
					$fieldTable->app_id					= $appTable->id;

					// Set the step.
					$fieldTable->step_id				= $stepTable->id;

					// Set this to show title by default
					$fieldTable->display_title			= isset($field->display_title) ? $field->display_title : SOCIAL_STATE_PUBLISHED;

					// Set this to show description by default
					$fieldTable->display_description	= isset($field->display_description) ? $field->display_description : SOCIAL_STATE_PUBLISHED;

					// Set this to be published by default.
					$fieldTable->state					= isset($field->state) ? $field->state : SOCIAL_STATE_PUBLISHED;

					// Set this to be searchable by default.
					$fieldTable->searchable				= isset($field->searchable) ? $field->searchable : SOCIAL_STATE_PUBLISHED;

					// Set this to be required by default.
					$fieldTable->required				= isset($field->required) ? $field->required : SOCIAL_STATE_PUBLISHED;

					// Set this to show in registration by default
					$fieldTable->visible_registration	= isset($field->visible_registration) ? $field->visible_registration : SOCIAL_STATE_PUBLISHED;

					// Set this to show in edit by default
					$fieldTable->visible_edit			= isset($field->visible_edit) ? $field->visible_edit : SOCIAL_STATE_PUBLISHED;

					// Set this to show in display by default
					$fieldTable->visible_display		= isset($field->visible_display) ? $field->visible_display : SOCIAL_STATE_PUBLISHED;

					// Check if the default items has a params.
					if (isset($field->params)) {
						$fieldTable->params				= FD::json()->encode($field->params);
					}

					// Store the field item.
					$fieldTable->store();

					// Generate unique key for this field after store (this is so that we have the field id)
					$keys = !empty($uniqueKeys[$stepTable->id][$fieldTable->id]) ? $uniqueKeys[$stepTable->id][$fieldTable->id] : null;
					$fieldTable->generateUniqueKey($keys);

					// Store the unique key into list of unique keys to prevent querying for keys unnecessarily
					$uniqueKeys[$stepTable->id][$fieldTable->id][] = $fieldTable->unique_key;

					// We store again to save the unique key
					$fieldTable->store();
				}
			}
		}

		return true;
	}

	/**
	 * Adds a new child item into `#__social_fields_options`.
	 * This uses the first in first out method. Before a new set of items are being inserted,
	 * the previous set would be deleted first.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The unique id for the field
	 * @param	string		The title for the child option.
	 * @return	boolean		True if success false otherwise.
	 */
	public function addChilds( $fieldId , $titles = array() )
	{
		if( !$titles )
		{
			return false;
		}

		$db 		= FD::db();
		$query 		= array();

		$query[]	= 'DELETE FROM ' . $db->nameQuote( '#__social_fields_options' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $fieldId );

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );
		$db->Query();

		// Now let's loop through all the titles.
		$query		= array();
		$query[]	= 'INSERT INTO ' . $db->nameQuote( '#__social_fields_options' );
		$query[]	= '(' . $db->nameQuote( 'parent_id' ) . ',' . $db->nameQuote( 'title' ) . ')';

		$query[]	= 'VALUES';

		foreach( $titles as $title )
		{
			$query[]	= '(' . $db->Quote( $fieldId ) . ',' . $db->Quote( $title ) . ')';

			if( next( $titles ) !== false )
			{
				$query[]	= ',';
			}
		}

		// Glue the query back.
		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		$db->Query();

		return true;
	}

	/**
	 * Retrieves the maximum sequence for a specific profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Registration' );
	 * $model->getMaxSequence( $profileId )
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique profile id.
	 * @return	int		The last sequence for the profile type.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getMaxSequence( $uid , $type = SOCIAL_TYPE_USER, $mode = null )
	{
		$db         = FD::db();
		$query 		= array();
		$query[]	= 'SELECT MAX(' . $db->nameQuote( 'sequence' ) . ')';
		$query[]	= 'FROM ' . $db->nameQuote( '#__social_fields_steps' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid );
		$query[]	= 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		if( !empty( $mode ) )
		{
			$query[] = 'AND ' . $db->nameQuote( 'visible_' . $mode ) . '=' . $db->Quote( '1' );
		}

		$query 		= implode( ' ' , $query );
		$db->setQuery( $query );

		$max		= (int) $db->loadResult();

		return $max;
	}


	/**
	 * Retrieve a list of field id's for this specific profile type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique id.
	 * @param	string	The unique item identifier.
	 * @return	Array	An array of field id's.
	 */
	public function getStorableFields( $uid , $type )
	{
		$db			= FD::db();
		$sql		= $db->sql();

		$sql->select('#__social_fields', 'a');
		$sql->column('a.id');
		$sql->innerjoin('#__social_fields_steps', 'b');
		$sql->on('a.step_id', 'b.id');
		$sql->where('b.uid', $uid);
		$sql->where('b.type', $type);

		$db->setQuery( $sql );

		$ids	= $db->loadColumn();

		return $ids;
	}

	/**
	 * Retrieves the total number of steps for the particular profile type.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $model 	= FD::model( 'Profiles' );
	 *
	 * // Returns the count in integer.
	 * $model->getTotalSteps( JRequest::getInt( 'id' ) );
	 *
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param   int 	The profile id.
	 * @return  int		The number of steps involved for this profile type.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getTotalSteps( $uid , $type = SOCIAL_TYPE_USER, $mode = null )
	{
		$db			= FD::db();

		$sql		= $db->sql();

		$sql->select( '#__social_fields_steps' )
			->where( 'uid', $uid )
			->where( 'type', $type )
			->where( 'state', SOCIAL_STATE_PUBLISHED );

		if( !empty( $mode ) )
		{
			$sql->where( 'visible_' . strtolower( $mode ), SOCIAL_STATE_PUBLISHED );
		}

		$db->setQuery( $sql->getTotalSql() );

		$result	= (int) $db->loadResult();

		return $result;
	}

	public function getItems( $options = array() )
	{
		$db		= FD::db();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__social_apps' )
				. ' WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( SOCIAL_APPS_TYPE_FIELDS )
				. ' AND ' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_STATE_PUBLISHED );

		if( is_array( $options ) )
		{
			foreach( $options as $key => $value )
			{
				$sql[]  = $this->_db->nameQuote( $key ) . '=' . $this->_db->Quote( $value );
			}
		}

		if( !empty( $sql ) )
		{
			$query	.= implode( ' AND ' , $sql );
		}

		$db->setQuery( $query );

		return $db->loadObjectList();
	}

	/**
	 * Get's a list of position given the current field id.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique field id.
	 * @return	Array	An array of string positions
	 */
	public function getPositions( $fieldId )
	{
		$db 		= FD::db();

		$query		= array();
		$query[]	= 'SELECT * FROM ' . $db->nameQuote( '#__social_fields_position' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'field_id' ) . '=' . $db->Quote( $fieldId );

		// Glue the query.
		$query		= implode( ' ' , $query );

		$db->setQuery( $query );

		$positions	= $db->loadObjectList();

		return $positions;
	}

	/**
	 * Retrieves fields from a specific position
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique item id.
	 * @param	string	The unique item type.
	 */
	public function getPositionData( $uid , $type , $position )
	{
		$db 		= FD::db();

		$query		= array();
		$query[]	= 'SELECT a.*,b.*, c.' . $db->nameQuote( 'element') . ' FROM ' . $db->nameQuote( '#__social_fields_data' ) . ' AS a';
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_fields' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'field_id' ) . ' = b.' . $db->nameQuote( 'id' );

		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS c';
		$query[]	= 'ON b.' . $db->nameQuote( 'app_id' ) . ' = c.' . $db->nameQuote( 'id' );

		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_fields_position' ) . ' AS d';
		$query[]	= 'ON d.' . $db->nameQuote( 'field_id' ) . ' = a.' . $db->nameQuote( 'field_id' );

		$query[]	= 'WHERE a.' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid );
		$query[]	= 'AND a.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );
		$query[]	= 'AND d.' . $db->nameQuote( 'position' ) . '=' . $db->Quote( $position );

		// Glue back the query.
		$query 		= implode( ' ' , $query );
		// dump( $query );
		$db->setQuery( $query );

		$data 	= $db->loadObjectList();

		return $data;
	}

	/**
	 * Retrieves a list of data for a type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array		$options	The array of options.
	 */
	public function getFieldsData($options = array())
	{
		$db 		= FD::db();
		$sql 		= $db->sql();

		$sql->select( '#__social_fields_data' , 'a' );
		$sql->column( 'a.*' );
		$sql->column( 'b.*' );
		$sql->column( 'c.element' );
		$sql->join( '#__social_fields' , 'b' , 'LEFT' );
		$sql->on( 'a.field_id' , 'b.id' );
		$sql->join( '#__social_apps' , 'c' );
		$sql->on( 'b.app_id' , 'c.id' );

		if (isset($options['uid'])) {
			$sql->where('a.uid', $options['uid']);
		}

		if (isset($options['type'])) {
			$sql->where('a.type', $options['type']);
		}

		if (isset($options['key'])) {
			$sql->where('b.unique_key', $options['key']);
		}

		$db->setQuery( $sql );

		$data 	= $db->loadObjectList();

		if( !$data )
		{
			return false;
		}

		$fields 	= array();

		foreach( $data as $row )
		{
			$table		= FD::table( 'Field' );
			$table->bind( $row );

			if (isset($options['uid'])) {
				$table->uid = $options['uid'];
			}

			if (isset($options['type'])) {
				$table->type = $options['type'];
			}

			$fields[]	= $table;
		}

		return $fields;
	}

	/*
	 * Retrieves a list of fields which is editable by the user.
	 *
	 */
	public function getFieldItems( &$groups )
	{
		$db     = FD::db();

		foreach( $groups as $group )
		{
			$query  = 'SELECT a.*,b.title AS addon_title , b.element AS addon_element FROM ' . $db->nameQuote( '#__social_fields' ) . ' AS a '
					. 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS b '
					. 'ON b.id=a.field_id '
					. 'WHERE a.`group_id`=' . $db->Quote( $group->id );
			$db->setQuery( $query );

			$data	= $db->loadObjectList();

			$group->childs	= $data;
		}

		return $groups;
	}

	/*
	 * Metod to save a user's custom profile.
	 *
	 * @param   array   $post   Posted data from $_POST
	 * @param   SocialTablePerson   $user   A person node
	 */
	public function store( $post , SocialTablePerson $user )
	{
		// @rule: Prepare data to be passed on to the caller.
		$data       = array( &$post , $user );

		// @rule: Get applications.
		//$apps		= FD::get( 'Model' , 'Applications' )->getFields();

		// only get fields that associate with the profile type.
		// we do not want to load all the application fields.
		$fieldIds		= array();
		$fieldOptions	= array();
		foreach( $post as $key => $value)
		{
			if( stristr( $key , SOCIAL_CUSTOM_FIELD_PREFIX ) !== false )
			{
				$fieldIds[]		= str_ireplace( SOCIAL_CUSTOM_FIELD_PREFIX.'-', '', $key );
				$fieldOptions[]	= '';;
			}
		}
		$apps	= FD::get( 'Model', 'Applications' )->getFieldsByID( $fieldIds );

		// @trigger: onBeforeSave
		// Triggers all field applications which wants to manipulate data before saving.
		$result		= FD::get( 'Fields' )->onBeforeSave( $apps , $data );

		// @rule: Saving was intercepted by one of the field applications.
		if( in_array( false , $result , true ) )
		{
			return false;
		}

		// @rule: Since $post is passed by reference to caller, the data will automatically be modified.
		foreach( $post as $key => $value )
		{
			if( stristr( $key , SOCIAL_CUSTOM_FIELD_PREFIX ) !== false )
			{
				// @rule: Remove all unwanted data
				$id     = str_ireplace( SOCIAL_CUSTOM_FIELD_PREFIX.'-' , '' , $key );
				$user->updateField( $id , $value );
			}
		}

		// @trigger: onAfterSave
		// Triggers all field application which wants to manipulate data after saving
		FD::get( 'Fields' )->onAfterSave( $apps , $data );

		return true;
	}

	public function getElement( $fieldId )
	{
		$db		= FD::db();
		$query	= 'SELECT ' . $db->nameQuote('element') . ' '
				. 'FROM ' .$db->nameQuote('#__social_apps') . ' '
				. 'WHERE ' . $db->nameQuote('type') . ' = ' . $db->quote('fields') . ' '
				. 'AND ' . $db->nameQuote('id') . ' = ' . $db->quote($fieldId);
		$db->setQuery($query);
		$element	= $db->loadResult();

		return $element;
	}

	public function getPagination()
	{
		if ( empty( $this->pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->pagination = new JPagination( $this->total , $this->getState('limitstart') , $this->getState('limit') );
		}

		return $this->pagination;
	}

	/*
	 * Retrieves a list of fields that can be chained
	 *
	 * @param   array   $options    A list of sql filters.
	 * @returns array   An array of SocialTableField objects
	 */
	public function getChildItems( $fieldId )
	{
		$db     = FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields_rules' );
		$sql->where( 'parent_id', $fieldId );

		$db->setQuery( $sql );

		$result     = $db->loadObjectList();
		$total  	= count( $result );
		$rules      = array();

		if( !$result )
		{
			return $rules;
		}

		// @rule: Bind them in the table representation layer
		for( $i = 0; $i < $total; $i++ )
		{
			$table	= FD::table( 'FieldRule' );
			$table->bind( $result[ $i ] );

			$rules[] = $table;
		}
		return $rules;
	}

	/**
	 * Sets a custom field value
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string	The unique key of the custom field
	 * @param 	mixed 	The data
	 * @return	boolean
	 */
	public function setValue( $uid , $type , SocialTableField $field , $value )
	{
		// Store the data now
		$data 	= FD::table( 'FieldData' );
		$exists	= $data->load( array( 'field_id' => $field->id , 'uid' => $uid , 'type' => $type ) );

		// If the data was never stored before, try storing them.
		if( !$exists )
		{
			$data->uid 		= $uid;
			$data->type 	= $type;
			$data->field_id = $field->id;
		}

		// Set the value of course.
		if( is_array( $value ) || is_object( $value ) )
		{
			$value = FD::makeArray( $value );

			if( isset( $value['data'] ) && isset( $value['raw'] ) )
			{
				$data->data	= $value['data'];
				$data->raw	= $value['raw'];
			}
			else
			{
				$data->data = FD::json()->encode( $value );
				$data->raw = implode( ' ', $value );
			}
		}
		else
		{
			$data->data = $value;
			$data->raw = $value;
		}

		$state 	= $data->store();

		return $state;
	}

	/**
	 * Retrieves a list of fields which should be displayed during the registration process.
	 * This should not be called elsewhere apart from the registration since it uses different steps, for processes.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options( 'step_id' , 'profile_id' )
	 * @return	Mixed	An array of group and field items as it's child items.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCustomFieldsValue( $fieldId , $uid , $type )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields_data' );
		$sql->column( 'data' );
		$sql->where( 'field_id', $fieldId );
		$sql->where( 'uid', $uid );
		$sql->where( 'type', $type );

		$db->setQuery( $sql );

		$data		= $db->loadResult();

		return $data;
	}

	/**
	 * Deprecated since 1.3. Use getCustomFields instead.
	 * Retrieves a list of fields which should be displayed during the registration / creation process.
	 * This should not be called elsewhere apart from the registration / creation since it uses different steps, for processes.
	 *
	 * @deprecated Depcreated since 1.3. Use getCustomFields instead.
	 * @since	1.0
	 * @access	public
	 * @param	Array	Existing values that are previously posted from $_POST.
	 * @return	Mixed	An array of group and field items as it's child items.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCustomFieldsForNode( $nodeId , $nodeType )
	{
		$db     	= FD::db();
		$fields 	= array();

		$query 		= array();
		$query[]	= 'SELECT b.*, c.' . $db->nameQuote( 'element' ) . ' AS element,d.' . $db->nameQuote( 'field_id' ) . ' as smartfield';

		$query[]	= 'FROM ' . $db->nameQuote( '#__social_fields_steps' ) . ' AS a';

		// Only want fields from the steps associated to the profile.
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_fields' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'step_id' );

		// Join with apps table to obtain the element
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__social_apps' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'app_id' );

		// Join with rules table.
		$query[]	= 'LEFT JOIN ' . $db->nameQuote( '#__social_fields_rules' ) . ' AS d';
		$query[]	= 'ON d.' . $db->nameQuote( 'parent_id' ) . ' = b.' . $db->nameQuote( 'id' );

		// Core fields should not be dependent on the state because it can never be unpublished.
		$query[]	= 'WHERE(';
		$query[]	= 'b.' . $db->nameQuote( 'core' ) . '=' . $db->Quote( 1 );
		$query[]	= 'OR';
		$query[]	= 'b.' . $db->nameQuote( 'state' ) . '=' . $db->Quote( SOCIAL_STATE_PUBLISHED );
		$query[]	= ')';

		// Registration field should not select dependant fields by default unless it is selected.
		$query[]	= 'AND b.' . $db->nameQuote( 'id' ) . ' NOT IN (';
		$query[]	= 'SELECT ' . $db->nameQuote( 'field_id' ) . ' FROM ' . $db->nameQuote( '#__social_fields_rules' );
		$query[]	= 'WHERE ' . $db->nameQuote( 'field_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query[]	= ')';

		// Make sure that the field is set to be visible during registrations.
		$query[]	= 'AND b.' . $db->nameQuote( 'visible_registration' ) . '=' . $db->Quote( 1 );
		// $query[]	= 'AND b.' . $db->nameQuote( 'core' ) . '=' . $db->Quote( 1 );

		// Make sure that only visible_registration is enabled only.


		// Make sure to load fields that are in the current step only.
		$query[]	= 'AND a.' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $nodeId );
		$query[]	= 'AND a.' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $nodeType );

		// Join back the queries.
		$query 		= implode( ' ' , $query );

		// echo str_ireplace( '#__' , 'jos_' , $query );
		// exit;

		$db->setQuery( $query );

		$rows	= $db->loadObjectList();

		// If there's no fields at all, just skip this whole block.
		if( !$rows )
		{
			return false;
		}

		$fields 	= array();

		// We need to bind the fields with SocialTableField
		foreach( $rows as $row )
		{
			$field 	= FD::table( 'Field' );
			$field->bind( $row );

			$fields[]	= $field;
		}

		return $fields;
	}

	/**
	 * Retrieves a list of fields which should be displayed during the registration process.
	 * This should not be called elsewhere apart from the registration since it uses different steps, for processes.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array	An array of options( 'step_id' , 'profile_id' )
	 * @return	Mixed	An array of group and field items as it's child items.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function getCustomFields( $options = array() )
	{
		$db     	= FD::db();
		$sql		= $db->sql();

		$fields 	= array();

		$sql->select( '#__social_fields', 'a' );
		$sql->column( 'a.*' );
		$sql->column( 'b.element', 'element' );
		// Temporarily not using this
		// $sql->column( 'c.field_id', 'smartfield' );
		$sql->column( 'd.uid', 'profile_id' );

		// Shift this part lower where data is retrieved through SocialTableField
		// Determines if we want to get the field data.
		// if( isset( $options[ 'data' ] ) && isset( $options[ 'dataId' ] ) && isset( $options[ 'dataType' ] ) )
		// {
		// 	$sql->column( 'f.data', 'data' );
		// }

		$sql->leftjoin( '#__social_apps', 'b' );
		$sql->on( 'b.id', 'a.app_id' );

		// Temporarily not using this
		// $sql->leftjoin( '#__social_fields_rules', 'c' );
		// $sql->on( 'c.parent_id', 'a.id' );

		$sql->leftjoin( '#__social_fields_steps', 'd' );
		$sql->on( 'a.step_id', 'd.id' );

		// Gets field based on positions
		if( isset( $options[ 'position' ] ) )
		{
			$sql->innerjoin( '#__social_fields_position', 'e' );
			$sql->on( 'e.field_id', 'a.id' );
		}

		// Shift this part lower where data is retrieved through SocialTableField
		// Get field data if necessary.
		// if( isset( $options[ 'data' ] ) && isset( $options[ 'dataId' ] ) && isset( $options[ 'dataType' ] ) )
		// {
		// 	$sql->leftjoin( '#__social_fields_data', 'f' );
		// 	$sql->on( 'f.field_id', 'a.id' );
		// 	$sql->on( 'f.uid', $options[ 'dataId' ] );
		// 	$sql->on( 'f.type', $options[ 'dataType' ] );
		// }

		if( isset( $options[ 'state' ] ) )
		{
			if( $options[ 'state' ] !== 'all' )
			{
				// Core fields should not be dependent on the state because it can never be unpublished.
				$sql->where( '(' );
				$sql->where( 'a.core', '1' );
				$sql->where( 'a.state', SOCIAL_STATE_PUBLISHED, '=', 'or' );
				$sql->where( ')' );
			}
		}

		// Test for unique key.
		if( isset( $options[ 'key' ] ) )
		{
			$sql->where( 'a.unique_key' , $options[ 'key' ] );
		}

		// Filter by visibility
		if( isset( $options[ 'visible' ] ) )
		{
			$sql->where( 'a.visible_' . $options[ 'visible' ], SOCIAL_STATE_PUBLISHED );

			// Do not filter profile table is the visible is visible_mini_registration
			if ($options['visible'] != SOCIAL_PROFILES_VIEW_MINI_REGISTRATION) {
				$sql->where( 'd.visible_' . $options[ 'visible' ], SOCIAL_STATE_PUBLISHED );
			}
		}

		// If position is specified, only fetch data from proper positions.
		if( isset( $options[ 'position' ] ) )
		{
			$sql->where( 'e.position', $options[ 'position' ] );
		}

		// Make sure to load fields that are in the current step only if step id is null
		if( isset( $options[ 'step_id' ] ) )
		{
			$sql->where( 'a.step_id', $options[ 'step_id' ] );
		}

		// Gets field based on group
		if( isset( $options[ 'group' ] ) )
		{
			switch( $options['group'] )
			{
				case SOCIAL_TYPE_GROUP:
				case SOCIAL_TYPE_EVENT:
					$stepType = SOCIAL_TYPE_CLUSTERS;
					break;

				default:
				case SOCIAL_TYPE_USER:
					$stepType = SOCIAL_TYPE_PROFILES;
					break;
			}

			$sql->where('d.type', $stepType );
		}

		// Detect if caller wants to filter by profile.
		if( isset( $options[ 'profile_id' ] ) )
		{
			$sql->where( 'd.uid', $options[ 'profile_id' ] );

			if( !isset( $options['group'] ) )
			{
				$sql->where( 'd.type', SOCIAL_TYPE_PROFILES );
			}
		}

		// Detect if caller wants to filter by group category id.
		if( isset( $options[ 'uid' ] ) )
		{
			$sql->where( 'd.uid', $options[ 'uid' ] );
		}

		// Filter by searchable fields
		if( isset( $options[ 'searchable' ] ) )
		{
			$sql->where( 'a.searchable', $options[ 'searchable' ] );
		}

		// Filter by app group
		if (isset($options['appgroup'])) {
			$sql->where('b.group', $options['appgroup']);
		}

		// Test for unique key.
		if (isset($options['element'])) {
			$sql->where('b.element', $options['element']);
		}

		// Ordering should by default ordered by `ordering` column.
		$sql->order( 'd.sequence' );
		$sql->order( 'a.ordering' );

		// // Debug
		// if( $key == 'ADDRESS' )
		// {
		// 	echo $sql->debug();
		// }

		// echo $sql;exit;

		$db->setQuery( $sql );

		$rows		= $db->loadObjectList();

		$fields 	= array();

		// Load language file from the back end as we need to translate them.

		// We need to bind the fields with SocialTableField
		$fieldIds = array();
		foreach( $rows as $row )
		{
			$field 	= FD::table( 'Field' );
			$field->bind( $row );

			$fieldIds[] = $field->id;

			$field->data = '';

			if (isset( $options['data']) && isset($options['dataId']) && isset($options['dataType'])) {
				$field->data = $field->getData($options['dataId'], $options['dataType']);
				$field->uid = $options['dataId'];
				$field->type = $options['dataType'];
			}

			$field->profile_id = isset( $row->profile_id ) ? $row->profile_id : '';

			$fields[]	= $field;
		}

		// set the field options in batch.
		$field 	= FD::table( 'Field' );
		$field->setBatchFieldOptions( $fieldIds );


		return $fields;
	}

	/**
	 * Removes all profile fields related to the unique item and id.
	 *
	 * Example:
	 * <code>
	 * <?php
	 * $profileId	= JRequest::getInt( 'id' );
	 * $model 	= FD::model( 'Profiles' );
	 * $model->removeFields( $profileId );
	 * ?>
	 * </code>
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique profile id.
	 * @return	bool	True on success false otherwise.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function deleteFields( $uid , $type = SOCIAL_TYPE_PROFILES )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields_steps' );
		$sql->where( 'uid', $uid );
		$sql->where( 'type', $type );

		$db->setQuery( $sql );

		$steps 		= $db->loadObjectList();

		// If there's no steps at all, we shouldn't be doing anything.
		if( !$steps )
		{
			return false;
		}

		foreach( $steps as $step )
		{
			// Delete the fields associated with this step.
			$sql->clear();

			$sql->delete( '#__social_fields' );
			$sql->where( 'step_id', $step->id );

			$db->setQuery( $sql );
			$db->Query();

			// Delete this step.
			$sql->clear();

			$sql->delete( '#__social_fields_steps' );
			$sql->where( 'id', $step->id );

			$db->setQuery( $sql );
			$db->Query();
		}

		return true;
	}

	/**
	 * Method to remove field steps given the node id and the node type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int			The node id.
	 * @param	string		The node type.
	 * @return
	 */
	public function deleteSteps( $nodeId , $nodeType )
	{
		$db 	= FD::db();
		$sql 	= $db->sql();

		$sql->delete( '#__social_fields_steps' );
		$sql->where( 'uid' , $nodeId );
		$sql->where( 'type', $nodeType );

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function deleteFieldsWithStep( $stepid )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_fields' );
		$sql->where( 'step_id', $stepid );

		$db->setQuery( $sql );
		$result = $db->query();

		return $result;
	}

	/**
	 * Helper function to retrieve the list of Joomla editors.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getEditors()
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__extensions' );
		$sql->where( 'folder', 'editors' );
		$sql->where( 'enabled', '1' );

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		// Load language strings.
		$lang		= JFactory::getLanguage();

		foreach( $result as $i => $option )
		{
			$lang->load('plg_editors_'.$option->element, JPATH_ADMINISTRATOR, null, false, false)
			||	$lang->load('plg_editors_'.$option->element, JPATH_PLUGINS .'/editors/'.$option->element, null, false, false)
			||	$lang->load('plg_editors_'.$option->element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load('plg_editors_'.$option->element, JPATH_PLUGINS .'/editors/'.$option->element, $lang->getDefault(), false, false);

			$option->name	= JText::_( $option->name );
		}

		return $result;
	}

	/**
	 * Retrieves a list of options for a particular field item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique field id. FK to `#__social_fields`.
	 * @return	Array
	 */
	public function getOptions( $fieldId )
	{
		$db 	= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields_options' );
		$sql->where( 'parent_id', $fieldId );
		$sql->order( 'key' );

		$db->setQuery( $sql );

		$result 	= $db->loadObjectList();

		$options	= array();

		if( !empty( $result ) )
		{
			foreach( $result as $row )
			{
				$options[$row->key][$row->id] = $row->title;
			}
		}

		return $options;
	}

	/**
	 * Delete options for a particular field item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The unique field id. FK to `#__social_fields`.
	 * @return	boolean	State of the action
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function deleteOptions( $fieldId )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->delete( '#__social_fields_options' );
		$sql->where( 'parent_id', $fieldId );

		$db->setQuery( $sql );

		return $db->query();
	}

	/**
	 * Get a list of unique keys
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$stepId		The step id to look
	 * @param	int		$exclude	The id to exclude
	 *
	 * @return	Array	list of unique keys
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function getStepUniqueKeys( $stepId, $exclude = null )
	{
		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields' );
		$sql->column( 'unique_key' );
		$sql->where( 'step_id', $stepId );

		if( !is_null( $exclude ) )
		{
			$sql->where( 'id', $exclude, '<>' );
		}

		$db->setQuery( $sql );

		return $db->loadColumn();
	}

	/**
	 * Get a list of unique keys
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$stepId		The step id to look
	 * @param	int		$exclude	The id to exclude
	 *
	 * @return	Array	list of unique keys
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function getProfileUniqueKeys( $stepId, $exclude = null )
	{
		// Get the profile id from this step first
		$table = FD::table( 'fieldstep' );
		$table->load( $stepId );

		$db		= FD::db();
		$sql	= $db->sql();

		$sql->select( '#__social_fields_steps', 'a' )
			->column( 'b.unique_key' )
			->leftjoin( '#__social_fields', 'b' )
			->on( 'a.id', 'b.step_id' )
			->where( 'a.type', $table->type )
			->where( 'a.uid', $table->uid );

		if( !is_null( $exclude ) )
		{
			$sql->where( 'b.id', $exclude, '<>' );
		}

		$db->setQuery( $sql );

		return $db->loadColumn();
	}

	/**
	 * Retrieves a list of all unique keys from custom fields available on the site
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getUniqueKeys()
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select( '#__social_fields' )
			->column( 'unique_key', 'unique_key', 'distinct' )
			->where( 'state', SOCIAL_STATE_PUBLISHED );

		$db->setQuery( $sql );

		$result = $db->loadColumn();

		return $result;
	}

	public function getFieldUniqueKeys( $app )
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->select( '#__social_fields' )
			->column( 'unique_key', 'unique_key', 'distinct' )
			->where( 'app_id', $app->id );

		$db->setQuery( $sql );
		$result = $db->loadColumn();

		$keys = array();

		foreach( $result as $row )
		{
			$data = new stdClass();
			$data->title = $row;
			$data->value = $row;

			$keys[] = $data;
		}

		return $keys;
	}

	public function clearData($options = array())
	{
		$db = FD::db();
		$sql = $db->sql();

		$sql->delete('#__social_fields_data');

		if (isset($options['field_id'])) {
			$sql->where('field_id', $options['field_id']);
		}

		if (isset($options['uid'])) {
			$sql->where('uid', $options['uid']);
		}

		if (isset($options['type'])) {
			$sql->where('type', $options['type']);
		}

		if (isset($options['datakey'])) {
			$sql->where('datakey', $options['datakey']);
		}

		$db->setQuery($sql);
		return $db->query();
	}
}
