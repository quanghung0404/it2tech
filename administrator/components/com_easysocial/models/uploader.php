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

jimport('joomla.application.component.model');

FD::import( 'admin:/includes/model' );

class EasySocialModelUploader extends EasySocialModel
{
	private $data			= null;
	protected $pagination		= null;

	function __construct()
	{
		parent::__construct( 'uploader' );
	}

	/**
	 * Uploads the given file to a temporary location on the site.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	string	The path to the uploaded item.
	 */
	public function upload($file, $hash, $userId)
	{
		// Check if file exists on the server
		if( !isset( $file[ 'tmp_name' ] ) || empty( $file ) )
		{
			$this->setError( JText::_( 'COM_EASYSOCIAL_UPLOADER_FILE_NOT_FOUND' ) );
			return false;
		}


		// Lets figure out the storage path.
		$config 	= FD::config();

		// Test if the folder exists for this upload type.
		$path 		= JPATH_ROOT . '/' . FD::cleanPath( $config->get( 'uploader.storage.container' ) );

		if( !FD::makeFolder( $path ) )
		{
			$this->setError( JText::sprintf( 'COM_EASYSOCIAL_UPLOADER_UNABLE_TO_CREATE_DESTINATION_FOLDER' , $path ) );
			return false;
		}

		// Let's finalize the storage path.
		$storage 	= $path . '/' . $userId;

		if( !FD::makeFolder( $storage ) )
		{
			$this->setError( JText::sprintf( 'COM_EASYSOCIAL_UPLOADER_UNABLE_TO_CREATE_DESTINATION_FOLDER' , $storage ) );
			return false;
		}

		// Once the script reaches here, we assume everything is good now.
		// Copy the files over.
		jimport( 'joomla.filesystem.file' );

		$absolutePath 	= $storage . '/' . $hash;

		if( !JFile::copy( $file[ 'tmp_name' ] , $absolutePath ) )
		{
			$this->setError( JText::sprintf( 'COM_EASYSOCIAL_UPLOADER_UNABLE_TO_COPY_TO_DESTINATION_FOLDER' , $absolutePath ) );
			return false;
		}

		return $absolutePath;
	}
}
