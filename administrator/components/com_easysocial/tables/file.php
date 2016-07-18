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

FD::import( 'admin:/tables/table' );

class SocialTableFile extends SocialTable
{
	/**
	 * The unique id of the file.
	 * @var int
	 */
	public $id 		= null;

	/**
	 * Determines if the file is stored in a collection
	 * @var int
	 */
	public $collection_id 	= null;

	/**
	 * The name for the file.
	 * @var string
	 */
	public $name 	= null;

	/**
	 * The hit count for the file.
	 * @var string
	 */
	public $hits 	= null;

	/**
	 * The unique file name for the file.
	 * @var string
	 */
	public $hash	= null;

	/**
	 * The unique id for this file.
	 * @var int
	 */
	public $uid 	= null;

	/**
	 * The unique type for this file.
	 * @var string
	 */
	public $type 	= null;

	/**
	 * The date time the file has been created.
	 * @var datetime
	 */
	public $created	= null;

	/**
	 * The owner of this file.
	 * @var int
	 */
	public $user_id	= null;

	/**
	 * The size of this file.
	 * @var string
	 */
	public $size	= null;

	/**
	 * The mime type of this file.
	 * @var string
	 */
	public $mime 	= null;

	/**
	 * The state of the uploaded file.
	 * @var int
	 */
	public $state	= null;

	/**
	 * The storage type of the uploaded file.
	 * @var int
	 */
	public $storage	= null;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 */
	public function __construct( $db )
	{
		parent::__construct( '#__social_files' , 'id', $db);
	}

	/**
	 * Override parent's implementation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string 	$token	The token that is generated.
	 * @return	boolean			True if exists, false otherwise.
	 */
	public function loadByType($uid, $type)
	{
		$db 	= FD::db();

		$query 	= 'SELECT * FROM ' . $db->nameQuote( $this->_tbl )
				. ' WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $uid )
				. ' AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type );

		$db->setQuery( $query );
		$obj 	= $db->loadObject();

		return parent::bind( $obj );
	}

	/**
	 * Returns the formatted file size
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string 	$format		The format of the size.
	 * @return	string				The post formatted size.
	 */
	public function getSize( $format = 'kb' )
	{
		$size 	= $this->size;

		switch( $format )
		{
			case 'kb':
			default:
				$size 	= round( $this->size / 1024 );

				break;
		}

		return $size;
	}

	/**
	 * Retrieves the icon type.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getIconClass()
	{
		// Image files
		if( $this->mime == 'image/jpeg' )
		{
			return 'album';
		}

		// Zip files
		if( $this->mime == 'application/zip' )
		{
			return 'zip';
		}

		// Txt files
		if( $this->mime == 'text/plain' )
		{
			return 'text';
		}

		// SQL files
		if( $this->mime == 'text/x-sql' )
		{
			return 'sql';
		}

		// Php files
		if( $this->mime == 'text/x-php' )
		{
			return 'php';
		}

		if( $this->mime == 'text/x-sql' )
		{
			return 'sql';
		}

		if( $this->mime == 'application/pdf' )
		{
			return 'pdf';
		}

		return 'unknown';
	}

	/**
	 * Determines if this file is preview-able.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	bool		True if item can be previewed, false otherwise.
	 */
	public function hasPreview()
	{
		$allowed 	= array('image/jpeg', 'image/png', 'image/gif');

		if (in_array($this->mime, $allowed)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the current user is the owner of this item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	$userId 		The user's id to check against.
	 * @return	boolean 				True if the user owns the item, false otherwise.
	 */
	public function isOwner($userId)
	{
		if ($this->user_id == $userId) {
			return true;
		}

		return false;
	}

	/**
	 * Resizes an image file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function resize($width, $height)
	{
		$width = (int) $width;
		$height = (int) $height;

		// Get the storage path to this image
		$path = $this->getStoragePath() . '/' . $this->hash;

		$image = ES::image();
		$image->load($path);

		// We should check if the width / height exceeds
		$image->fit($width, $height);

		$tmp = $path . '_2';
		$state = $image->save($tmp);

		// Delete the main file first
		JFile::delete($path);

		// Rename the temporary stored file to the original file name
		JFile::move($tmp, $path);

		return $state;
	}

	/**
	 * Gets the formatted date of the uploaded date.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string		The formatted date time.
	 */
	public function getCreator()
	{
		$creator 	= FD::user( $this->user_id );

		return $creator;
	}

	/**
	 * Gets the formatted date of the uploaded date.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string		The formatted date time.
	 */
	public function getUploadedDate()
	{
		$date 	= FD::date( $this->created );

		return $date;
	}

	/**
	 * Override
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function delete( $pk = null )
	{
		// Delete the record from the database first.
		$state = parent::delete();

		// Get the storage path
		$path = $this->getStoragePath( true );
		$path = $path . '/' . $this->hash;

		$storage = FD::storage($this->storage);
		$state = $storage->delete($path);

		// Delete the stream item related to this file
		ES::stream()->delete($this->id, SOCIAL_TYPE_FILES);

		if (!$state) {
			$this->setError( JText::_( 'Unable to delete the file from ' . $storage ) );
			return false;
		}

		return true;
	}

	/**
	 * Determines if the file is delete-able by the user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 	The user's id (optional)
	 * @return	boolean	True if allowed, false otherwise.
	 */
	public function deleteable( $id = null )
	{
		$user 	= FD::user( $id );

		if( $this->user_id == $user->id )
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns the absolute uri to the item.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	null
	 * @return	string		The absolute URI to the current item.
	 */
	public function getURI()
	{
		$config = ES::config();
		
		if ($this->storage != 'joomla') {

			$storage = FD::storage($this->storage);
			$path = $this->getStoragePath(true);
			$path = $path . '/' . $this->hash;

			return $storage->getPermalink($path);

		} 

		$path = $this->getStoragePath(true);
		$path = $path . '/' . $this->hash;
		$uri = rtrim(JURI::root(), '/') . $path;

		return $uri;
	}

	/**
	 * Gets the content of the file.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	string
	 */
	public function getContents()
	{
		$config	= FD::getInstance( 'Config' );
		$path	= ltrim( $config->get( strtolower( $this->type ) . '_uploads_path' ) , '\\/' );
		$path 	= SOCIAL_MEDIA . '/' . $path . '/' . $this->uid . '/' . $this->hash;

		$contents	= JFile::read( $path );

		return $contents;
	}

	/**
	 * Copies the temporary file from the table `#__social_uploader` and place the item in the appropriate location.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int 		The foreign key to `#__social_uploader`.`id`
	 *
	 * @return	boolean		True if success, false otherwise.
	 */
	public function copyFromTemporary($id)
	{
		$uploader = FD::table('Uploader');
		$uploader->load($id);

		// Bind the properties from uploader over.
		$this->name = $uploader->name;
		$this->mime = $uploader->mime;
		$this->size = $uploader->size;
		$this->user_id = $uploader->user_id;
		$this->created = $uploader->created;

		jimport('joomla.filesystem.file');

		$this->hash = JFile::makeSafe($uploader->name);

		// Lets figure out the storage path.
		$config = ES::config();

		if ($this->type == 'comments') {
			$path = JPATH_ROOT . '/' . ES::cleanPath($config->get('comments.storage'));
		} else {
			$path = ES::cleanPath($config->get('files.storage.container'));
			$path = JPATH_ROOT . '/' . $path . '/' . ES::cleanPath($config->get('files.storage.' . $this->type . '.container'));
		}


		// Test if the folder exists for this upload type.
		if (!FD::makeFolder($path)) {
			$this->setError(JText::sprintf('COM_EASYSOCIAL_UPLOADER_UNABLE_TO_CREATE_DESTINATION_FOLDER', $path));
			return false;
		}

		// Let's finalize the storage path.
		$storage = $path . '/' . $this->uid;

		if (!FD::makeFolder($storage)) {
			$this->setError(JText::sprintf( 'COM_EASYSOCIAL_UPLOADER_UNABLE_TO_CREATE_DESTINATION_FOLDER' , $storage ) );
			return false;
		}

		// Once the script reaches here, we assume everything is good now.
		// Copy the files over.
		jimport('joomla.filesystem.file');

		// Copy the file over.
		$source	= $uploader->path;
		$dest = $storage . '/' . $this->hash;

		// Try to copy the files.
		$state = JFile::copy($source, $dest);

		if (!$state) {
			$this->setError(JText::sprintf('COM_EASYSOCIAL_UPLOADER_UNABLE_TO_COPY_TO_DESTINATION_FOLDER', $dest));
			return false;
		}

		// Once it is copied, we should delete the temporary data.
		$uploader->delete();

		return $state;
	}

	/**
	 * Identical to the store method but it also stores the file properties.
	 * Maps a file object into the correct properties.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	$_FILES	$file 	File data
	 *
	 * @return	boolean			True if success, false otherwise.
	 */
	public function storeWithFile( $file )
	{
		// Check if file exists on the server
		if( !isset( $file[ 'tmp_name' ] ) || empty( $file ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_UPLOADER_FILE_NOT_FOUND' ) );
			return false;
		}

		// Get the name of the uploaded file.
		if( isset( $file[ 'name' ] ) && !empty( $file[ 'name' ] ) )
		{
			$this->name 	= $file[ 'name' ];
		}

		// Get the mime type of the file.
		if( isset( $file[ 'type' ] ) && !empty( $file[ 'type' ] ) )
		{
			$this->mime 	= $file[ 'type' ];
		}

		// Get the file size.
		if( isset( $file[ 'size' ] ) && !empty( $file[ 'size' ] ) )
		{
			$this->size 	= $file[ 'size' ];
		}

		// If there's no type or the unique id is invalid we should break here.
		if( !$this->type || !$this->uid )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_UPLOADER_COMPOSITE_ITEMS_NOT_DEFINED' ) );
			return false;
		}

		// Generate a random hash for the file.
		$this->hash 	= md5( $this->name . $file[ 'tmp_name' ] );

		// Try to store the item first.
		$state 		= $this->store();

		// Once the script reaches here, we assume everything is good now.
		// Copy the files over.
		jimport( 'joomla.filesystem.file' );

		$storage	= $this->getStoragePath();

		// Ensure that the storage path exists.
		FD::makeFolder( $storage );

		$state 		= JFile::copy( $file[ 'tmp_name' ] , $storage . '/' . $this->hash );

		if( !$state )
		{
			$this->setError( JText::sprintf( 'COM_EASYSOCIAL_UPLOADER_UNABLE_TO_COPY_TO_DESTINATION_FOLDER' , $typePath . '/' . $this->uid . '/' . $this->hash ) );
			return false;
		}

		return $state;
	}

	/**
	 * Returns the file path
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getStoragePath( $relative = false )
	{
		// Lets figure out the storage path.
		$config = FD::config();
		$path = '';

		if (!$relative) {
			$path = JPATH_ROOT;
		}

		if ($this->type == 'comments') {
			$path .= '/' . rtrim(ES::cleanPath($config->get('comments.storage')), '/');	
		} else {
			// Get the storage path
			$path .= '/' . ES::cleanPath($config->get('files.storage.container'));
			$path = $path .'/' . ES::cleanPath($config->get('files.storage.' . $this->type . '.container'));
		}

		// Let's finalize the storage path.
		$storage = $path . '/' . $this->uid;

		return $storage;
	}

	public function getHash( $forceNew = false )
	{
		if( empty( $this->hash ) || $forceNew )
		{
			$key = $this->name . $this->size;

			if( empty( $key ) )
			{
				$key = uniqid();
			}

			$this->hash = md5( $key );
		}

		return $this->hash;
	}

	/**
	 * Retrieves the permalink to the item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPermalink( $xhtml = true )
	{
		$url 	= FRoute::conversations( array( 'layout' => 'download' , 'fileid' => $this->id ) , $xhtml );

		return $url;
	}

	/**
	 * Returns the download link for the file.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	string		The absolute URI for previewing an item.
	 */
	public function getDownloadURI($customView = '', $customTask = '')
	{
		if ($this->storage != 'joomla') {
			$storage = FD::storage($this->storage);
			$path = $this->getStoragePath(true);
			$path = $path . '/' . $this->hash;

			return $storage->getPermalink($path);
		}

		// We need to fix the path for groups!
		$view = $this->type;

		if ($this->type == SOCIAL_TYPE_GROUP) {
			$view = 'groups';
		}

		if ($this->type == SOCIAL_TYPE_EVENT) {
			$view = 'events';
		}

		$task = 'download';

		if ($customView) {
			$view = $customView;
		}

		if ($customTask) {
			$task = $customTask;
		}

		$uri = FRoute::raw('index.php?option=com_easysocial&view=' . $view . '&layout=' . $task . '&fileid=' . $this->id . '&tmpl=component');

		return $uri;
	}

	/**
	 * Returns the php version of the source item.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return 	string		The absolute URI for previewing an item.
	 */
	public function getPreviewURI()
	{
		if ($this->storage != 'joomla') {
			$storage 	= FD::storage( $this->storage );
			$path 		= $this->getStoragePath( true );
			$path 		= $path . '/' . $this->hash;

			return $storage->getPermalink($path);
		}

		// We need to fix the path for groups!
		$type = $this->type;

		if ($type == 'group') {
			$type = 'groups';
		}

		if ($type == 'event') {
			$type = 'events';
		}

		$uri 	= FRoute::raw( 'index.php?option=com_easysocial&view=' . $type . '&layout=preview&fileid=' . $this->id . '&tmpl=component' );

		return $uri;
	}

	/**
	 * Ends the output and allow user to preview the file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		$storage 	= $this->getStoragePath();
		$file 		= $storage . '/' . $this->hash;

		jimport( 'joomla.filesystem.file' );

		// If the file no longer exists, throw a 404
		if( !JFile::exists( $file ) )
		{
			JError::raiseError( 404 );
		}

		if( !$this->hasPreview() )
		{
			return $this->download();
		}

		// Get the real file name
		$fileName 	= $this->name;

		// Get the file size
		$fileSize	= filesize( $file );


		header('Content-Description: File Transfer');
		header('Content-Type: ' . $this->mime);
		header('Content-Disposition: inline');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . $fileSize );

		// http://dtbaker.com.au/random-bits/how-to-cache-images-generated-by-php.html
		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
		       &&
		  (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))) {
		  // send the last mod time of the file back
		  header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT',
		  true, 304);
		}

		ob_clean();
		flush();
		readfile($file);
		exit;
	}

	/**
	 * Ends the output and allow user to download the file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function download()
	{
		// Update the hit counter
		$this->hits	+= 1;
		$this->store();

		if( $this->storage != 'joomla' )
		{
			$storage 	= FD::storage( $this->storage );
			$path 		= $this->getStoragePath( true );
			$path 		= $path . '/' . $this->hash;

			return JFactory::getApplication()->redirect( $storage->getPermalink( $path ) );
		}

		$storage 	= $this->getStoragePath();
		$file 		= $storage . '/' . $this->hash;

		jimport( 'joomla.filesystem.file' );

		// If the file no longer exists, throw a 404
		if( !JFile::exists( $file ) )
		{
			JError::raiseError( 404 );
		}

		// Get the real file name
		$fileName 	= $this->name;

		// Get the file size
		$fileSize	= filesize( $file );

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'. $fileName . '"' );
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $fileSize );
		ob_clean();
		flush();
		readfile($file);
		exit;
	}
}
