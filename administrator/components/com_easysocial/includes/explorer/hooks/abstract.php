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

abstract class SocialExplorerHooks
{
	private $storagePath	= null;

	public function __construct($uid, $type)
	{
		// Get the storage path
		$config = FD::config();
		$this->config = $config;

		// Build the path to the storage path
		$container = $this->config->get('files.storage.container');
		$this->storagePath	= $container . '/' . $config->get('files.storage.' . strtolower($type) . '.container');

		// Set the current uid and type
		$this->uid  = $uid;
		$this->type = $type;

		// Get the current logged in user
		$this->my   = FD::user();

		$this->app = JFactory::getApplication();
		$this->input = FD::request();
	}

	/**
	 * Retrieves a list of folders
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFolders()
	{
		$model = FD::model('FileCollections');

		$folders = $model->getCollections($this->uid, $this->type);

		// Generate a default collection which cannot be deleted.
		$default = $this->getDefaultFolder();

		if (!is_array($folders)) {
			$folders = array($folders);
		}

		// Merge the data
		$folders = array_merge(array($default), $folders);

		// Format the result
		$result 	= $this->format($folders);

		return $result;
	}

	/**
	 * Retrieves the default folder
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getDefaultFolder()
	{
		$obj 			= FD::table( 'FileCollection' );
		$obj->id 		= 0;
		$obj->title		= JText::_( 'COM_EASYSOCIAL_EXPLORER_DEFAULT_FOLDER' );
		$obj->uid 		= $this->uid;
		$obj->type 		= $this->type;

		return $obj;
	}

	/**
	 * Creates a new collection
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFolder( $title = null )
	{
		// Determines if the user has access to create folders
		if (!$this->hasWriteAccess()) {
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_CREATE_FOLDER' ) );
		}

		$title 	= is_null( $title ) ? JRequest::getString( 'name' ) : $title;

		if (!$title) {
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_INVALID_FOLDER_NAME_PROVIDED' ) );
		}

		$collection 			= FD::table( 'FileCollection' );
		$collection->title		= $title;
		$collection->owner_id 	= $this->uid;
		$collection->owner_type = $this->type;
		$collection->user_id 	= FD::user()->id;
		$collection->store();

		$result 	= $this->format( array( $collection ) );

		return $result[ 0 ];
	}

	/**
	 * Retrieves a list of files from a specific collection.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFiles($collectionId = null)
	{
		$model = FD::model('Files');
		$options = array();

		$options['collection_id'] = JRequest::getInt('id', 0);

		if (!is_null($collectionId)) {
			$options['collection_id'] = $collectionId;
		}

		$files = $model->getFiles($this->uid, $this->type, $options);

		// Format the result
		$result = $this->format($files);

		return $result;
	}

	/**
	 * Allows caller to upload files
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function addFile( $title = null )
	{
		if( !$this->hasWriteAccess() )
		{
			return FD::exception( JText::_( 'COM_EASYSOCIAL_EXPLORER_NO_ACCESS_TO_UPLOAD' ) );
		}

		// Ensure that the storage path really exists on the site
		FD::makeFolder( $this->storagePath );

		// Get the maximum size allowed from the child
		$max 		= $this->getMaxSize();

		// Define uploader options
		$options 	= array( 'name' => 'file' , 'maxsize' => $max );

		// Get uploaded file from $_FILE
		$file		= FD::uploader( $options )->getFile();

		// If there was an error getting uploaded file, stop.
		if( $file instanceof SocialException )
		{
			return $file;
		}

		// Get filename
		$name			= $file['name'];

		// Get the folder to store this item to.
		$collectionId	= JRequest::getInt( 'id' , 0 );

		$table 					= FD::table( 'File' );
		$table->name			= $name;
		$table->collection_id 	= $collectionId;
		$table->hits 			= 0;
		$table->hash 			= md5( 'tmp' );
		$table->uid 			= $this->uid;
		$table->type			= $this->type;
		$table->created			= JFactory::getDate()->toSql();
		$table->user_id 		= FD::user()->id;
		$table->size 			= filesize( $file[ 'tmp_name' ] );
		$table->mime 			= $file[ 'type' ];
		$table->state 			= SOCIAL_STATE_PUBLISHED;
		$table->storage 		= SOCIAL_STORAGE_JOOMLA;

		// Try to store the data on the database.
		$table->store();

		// Now we need to really upload the file.
		$state	= $table->storeWithFile( $file );

		// Format the data now
		$result	= $this->format( array( $table ) );

		return $result[ 0 ];
	}

	protected function format( $data )
	{
		// Ensure that it's an array
		$data = FD::makeArray($data);

		if (!$data) {
			return array();
		}

		$result 	= array();

		foreach ($data as $item) {

			if ($item instanceof SocialTableFileCollection) {
				$result[] = $this->formatFolder($item);
			}

			if ($item instanceof SocialTableFile) {
				$result[] = $this->formatFile($item);
			}
		}

		return $result;
	}

	private function getIcon( SocialTableFile $row )
	{
		$mime = $row->mime;

		// TODO: Expand this?
		if (strpos($mime, 'image')===0) return 'fa-photo';

		return 'fa-file';
	}

	/**
	 * Formats the file
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function formatFile(SocialTableFile $row)
	{
		$file 			= new stdClass();
		$file->id 		= $row->id;
		$file->name 	= $row->name;
		$file->folder 	= $row->collection_id;
		$file->canDelete = $this->hasDeleteAccess($row);
		$file->data 	= (object) array(
			'hits'    => $row->hits,
			'hash'    => $row->hash,
			'uid'     => $row->uid,
			'type'    => $row->type,
			'created' => $row->created,
			'user_id' => $row->user_id,
			'size'    => $row->size,
			'mime'    => $row->mime,
			'state'   => $row->state,
			'storage' => $row->storage,
			'icon'    => $this->getIcon($row),
			'previewUri' => $row->getPreviewURI()
		);
		$file->settings	= array();

		$theme = FD::themes();
		$theme->set('file', $file);
		$file->html = $theme->output( 'site/explorer/file' );

		return $file;
	}

	private function formatFolder( SocialTableFileCollection $row )
	{
		// Get a list of files from a specific collection
		$files					= $this->getFiles( $row->id );

		$collection 			= new stdClass();
		$collection->id 		= $row->id;
		$collection->name 		= $row->title;
		$collection->count 		= $row->getTotalFiles();
		$collection->data 		= $files;
		$collection->settings	= array();
		$collection->map		= array();

		if( $files )
		{
			foreach( $files as $file )
			{
				$collection->map[]	= $file->id;
			}
		}


		return $collection;
	}
}
