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

// Include the fields library
FD::import( 'admin:/includes/fields/dependencies' );

// Import necessary library
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

/**
 * Field application for File
 *
 * @since	1.0
 * @author	Jason Rey <jasonrey@stackideas.com>
 */
class SocialFieldsUserFile extends SocialFieldItem
{
	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegister( &$post , &$registration )
	{
		// Get error.
		$error 	= $registration->getErrors( $this->inputName );

		// Set error.
		$this->set( 'error', $error );

		// Set file limit.
		$this->set( 'limit', $this->params->get( 'file_limit', 0 ) );

		// Get the value.
		$value = !empty( $post[$this->inputName] ) ? FD::json()->decode( $post[$this->inputName] ) : array();

		// Set the value.
		$this->set( 'value', $value );

		// Count uploaded file.
		$count = empty( $value ) ? 0 : count( $value );

		// Set the count.
		$this->set( 'count', $count );

		// Display the output.
		return $this->display();
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onRegisterValidate( &$post, &$registration )
	{
		// Check if this field is required and if there are files uploaded
		if( $this->isRequired() )
		{
			if( empty( $post[$this->inputName] ) )
			{
				$this->setError( JText::_( 'PLG_FIELDS_FILE_VALIDATION_REQUIRED_TO_UPLOAD' ) );
				return false;
			}

			$json = FD::json();

			$files = $json->decode( $post[$this->inputName] );

			if( empty( $files ) )
			{
				$this->setError( JText::_( 'PLG_FIELDS_FILE_VALIDATION_REQUIRED_TO_UPLOAD' ) );
				return false;
			}
		}

		return true;
	}

	/**
	 * Once a user registration is completed, the field should automatically
	 * move the temporary file into the user's folder if required.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	bool	State of the registration saving
	 */
	public function onRegisterBeforeSave( &$post )
	{
		// Copy the files over
		if( !empty( $post[$this->inputName] ) )
		{
			$json = FD::json();

			$result = array();
			$filenames = array();

			$files = $json->decode( $post[$this->inputName] );

			foreach( $files as $row )
			{
				$state = true;

				$data = new stdClass();

				// If it is a tmp file, then we gotta move the file out to user directory
				if( $row->tmp )
				{
					$data = $this->copyFromTemporary( $row->id );

					if( $data === false )
					{
						continue;
					}

					$result[] = $data;
					$filenames[] = $data->name;
				}
			}

			$post[$this->inputName] = array(
				'data' => $json->encode($result),
				'raw' => implode(' ', $filenames)
			);
		}

		return true;
	}

	/**
	 * Displays the field form when user is being edited.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onEdit(&$post, &$user, $errors)
	{
		$value = !empty( $post[$this->inputName] ) ? $post[$this->inputName] : $this->value;

		if (is_array($value) && isset($value['data'])) {
			$value = $value['data'];
		}

		$value = json_decode($value);
		$value = $this->prepareFiles($value);

		$count = empty($value) ? 0 : count($value);
		$limit = $this->params->get('file_limit', 0);
		$error = $this->getError($errors);


		$this->set('user', $user);
		$this->set('error', $error);
		$this->set('value', $value);
		$this->set('count', $count);
		$this->set('limit', $limit);

		return $this->display();
	}

	public function onEditValidate( &$post )
	{
		if( $this->isRequired() && empty( $post[ $this->inputName ] ) )
		{
			$this->setError( JText::_( 'PLG_FIELDS_FILE_VALIDATION_REQUIRED_TO_UPLOAD' ) );
			return false;
		}

		return true;
	}

	public function onEditBeforeSave( &$post, &$user )
	{
		$json = FD::json();

		$result = array();
		$filenames = array();

		$originals = $json->decode( $this->field->data['data']);

		if (empty($originals)) {
			$originals = array();
		}

		$existings = array();

		if (!empty($post[$this->inputName])) {
			$files = $json->decode( $post[$this->inputName] );

			foreach ($files as $row) {
				if (!$row) {
					continue;
				}

				$state = true;

				$data = new stdClass();

				// If it is a tmp file, then we gotta move the file out to user directory
				if ($row->tmp) {
					$data = $this->copyFromTemporary( $row->id );

					if ($data === false) {
						continue;
					}
				} else {
					// If it is not a tmp file, means it is an existing file

					$state = false;

					// Search for the data from the originals
					foreach( $originals as $original ) {
						if ( $row->id == $original->id ) {
							$state = true;

							$existings[] = $original->id;

							$data = $original;

							break;
						}
					}
				}

				if ( $state ) {
					$result[] = $data;
					$filenames[] = $data->name;
				}
			}
		}

		foreach ($originals as $original) {
			// If the original files is not in the new set of existings file, then we delete it
			if ( !in_array( $original->id, $existings ) ) {
				// Get the file table data
				$file = FD::table( 'file' );
				$file->load( $original->id );
				$file->delete();
			}
		}

		$post[$this->inputName] = array(
			'data' => $json->encode($result),
			'raw' => implode(' ', $filenames)
		);
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array
	 * @return	string	The html output.
	 *
	 * @author	Jason Rey <jasonrey@stackideas.com>
	 */
	public function onSample()
	{
		$this->set( 'limit', $this->params->get( 'file_limit', 0 ) );

		return $this->display();
	}

	private function copyFromTemporary( $id )
	{
		$json = FD::json();

		// Get tmp table
		$tmp = FD::table( 'tmp' );
		$state = $tmp->load( $id );

		if( !$state )
		{
			$this->setError( JText::_( 'PLG_FIELDS_FILE_ERROR_UNABLE_TO_LOAD_TEMPORARY_DATA' ) );
			return false;
		}

		$value = $json->decode( $tmp->value );

		// Get file table
		$file = FD::table( 'file' );
		$file->name = $value->name;
		$file->size = $value->size;
		$file->mime = $value->mime;
		$file->uid = $this->field->id;
		$file->type = SOCIAL_APPS_TYPE_FIELDS;
		$file->state = 1;
		// The user id does not necessary have to be the profile id because this file could be uploaded by admin for this user
		$file->user_id = FD::user()->id;

		// Get the source path
		$source = $value->path . '/' . $value->hash;

		$state = JFolder::create( $file->getStoragePath() );

		if( !$state )
		{
			$this->setError( JText::_( 'PLG_FIELDS_FILE_ERROR_UNABLE_TO_CREATE_STORAGE_FOLDER' ) );
			return false;
		}

		// Get the destination path.
		$destination = $file->getStoragePath() . '/' . $file->getHash();

		// Move the file from tmp directory to user path
		$state = JFile::move( $source, $destination );

		if( !$state )
		{
			$this->setError( JText::_( 'PLG_FIELDS_FILE_ERROR_UNABLE_TO_MOVE_FILE' ) );
			return false;
		}

		$state = $file->store();

		if( !$state )
		{
			$this->setError( JText::_( 'PLG_FIELDS_FILE_ERROR_UNABLE_TO_STORE_FILE_DATA' ) );
			return false;
		}

		$data = new stdClass();

		$data->id = $file->id;
		$data->name = $file->name;

		return $data;
	}

	public function onDisplay( $user )
	{
		if( empty( $this->value ) )
		{
			return;
		}

		if( !$this->allowedPrivacy( $user ) )
		{
			return;
		}

		$value = $this->value;

		if (is_array($value) && isset($value['data'])) {
			$value = $value['data'];
		}

		$value = FD::json()->decode( $value );

		$value = $this->prepareFiles( $value );

		$count = empty( $value ) ? 0 : count( $value );

		if( $count < 1 )
		{
			return ;
		}

		$result = array();

		$this->set( 'count', $count );
		$this->set( 'value', $value );

		return $this->display();
	}

	/**
	 * Prepares the data of the files
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function prepareFiles($value)
	{
		if (!is_array($value)) {
			$value = array();
		}

		$files = array();

		foreach ($value as $file) {

			$table = FD::table( 'file' );
			$state = $table->load($file->id);

			if ($state) {

				$table->downloadLink = FRoute::fields(array('group' => $this->group, 'element' => $this->element, 'task' => 'download', 'id' => $this->field->id, 'uid' => $file->id, 'external' => true));
				$table->previewLink = FRoute::fields(array('group' => $this->group, 'element' => $this->element, 'task' => 'preview', 'id' => $this->field->id, 'uid' => $file->id, 'external' => true));

				$files[] = $table;
			}
		}

		return $files;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.2
	 * @access public
	 * @param  array         $data Fields data.
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		if (!$this->isRequired()) {
			return true;
		}

		if (empty($this->value)) {
			$this->setError(JText::_('PLG_FIELDS_FILE_VALIDATION_REQUIRED_TO_UPLOAD'));
			return false;
		}

		$value = FD::json()->decode($value);

		$value = $this->prepareFiles($value);

		$count = empty($value) ? 0 : count($value);

		if (empty($value) || empty($count)) {
			$this->setError(JText::_('PLG_FIELDS_FILE_VALIDATION_REQUIRED_TO_UPLOAD'));
			return false;
		}

		return true;
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @author Jason Rey <jasonrey@stackideas.com>
	 * @since  1.3
	 * @access public
	 * @param  array        $data   The post data.
	 * @param  SocialUser   $user   The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		if (empty($this->value)) {
			return false;
		}

		$obj = FD::makeObject($this->value);

		if (empty($obj)) {
			return false;
		}

		return true;
	}
}
