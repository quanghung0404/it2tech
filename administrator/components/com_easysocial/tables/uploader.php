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
defined('_JEXEC') or die('Unauthorized Access');

// Include the main table.
FD::import( 'admin:/tables/table' );

class SocialTableUploader extends SocialTable
{
	/**
	 * The unique id for this temporary uploaded item.
	 * @var int
	 */
	public $id = null;

	/**
	 * The path to the file.
	 * @var string
	 */
	public $path = null;

	/**
	 * The name to the file.
	 * @var string
	 */
	public $name = null;

	/**
	 * The mime for the file.
	 * @var string
	 */
	public $mime = null;

	/**
	 * The size for the file.
	 * @var string
	 */
	public $size = null;

	/**
	 * The created date time.
	 * @var datetime
	 */
	public $created = null;

	/**
	 * The owner of this temporary item.
	 * @var int
	 */
	public $user_id = null;

	public function __construct(&$db)
	{
		parent::__construct('#__social_uploader' , 'id' , $db );
	}

	/**
	 * Uploads the file to the temporary location.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	$_FILES	$file 	File data
	 *
	 * @return	boolean			True if success, false otherwise.
	 */
	public function bindFile($file)
	{
		$this->name = $file['name'];
		$this->mime = $file['type'];
		$this->size = $file['size'];

		// Generate a hash for the new file
		jimport('joomla.filesystem.file');

		$hash = JFile::makeSafe($file['name']);
		$hash = JFile::stripExt($hash);

		// We need to set an extension for this file
		$extension = explode('.', $this->name);

		if (count($extension) > 1) {
			$hash .= '.' . $extension[1];
		}

		// Upload the file now
		$model = ES::model('Uploader');
		$path = $model->upload($file, $hash, $this->user_id);

		if ($path === false) {
			$this->setError($model->getError());
			return false;
		}

		$this->path = $path;

		return true;
	}

	/**
	 * Retrieves the preview url
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getPermalink()
	{
		$config = ES::config();

		$url = rtrim(JURI::root(), '/');
		$url .= '/' . FD::cleanPath($config->get('uploader.storage.container'));
		$url .= '/' . $this->user_id;
		$url .= '/' . basename($this->path);
		
		return $url;
	}

	/**
	 * Override parent's delete behavior.
	 *
	 * @since	1.0
	 * @access	public
	 * @return	boolean	True on success false otherwise.
	 */
	public function delete( $pk = null )
	{
		// Delete the record from the database
		$state 	= parent::delete();

		if( !$state )
		{
			return false;
		}

		// Delete the temporary file.
		jimport( 'joomla.filesystem.file' );

		$file 	= JPATH_ROOT . '/' . $this->path;

		if( !JFile::exists( $file ) )
		{
			$this->setError( JText::_( 'File does not exist on the site' ) );
			return false;
		}

		$state 	= JFile::delete( JPATH_ROOT . '/' . $this->path );

		if( !$state )
		{
			$this->setError( JText::_( 'Unable to delete the phsyical file due to permission issues' ) );
			return false;
		}

		return true;
	}
}
