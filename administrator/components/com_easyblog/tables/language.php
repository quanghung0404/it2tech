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

class EasyBlogTableLanguage extends EasyBlogTable
{
	/**
	 * The unique id of the application
	 * @var int
	 */
	public $id			= null;

	/**
	 * The type of the application. E.g: fields, applications
	 * @var string
	 */
	public $title		= null;

	/**
	 * Determines if the application is a core application.
	 * @var int
	 */
	public $locale		= null;

	/**
	 * Determines if the application is only used for processing only.
	 * @var int
	 */
	public $updated		= null;

	/**
	 * Determines if the application is a unique application.
	 * @var int
	 */
	public $state		= null;

	/**
	 * The unique element of the application.
	 * @var string
	 */
	public $translator		= null;

	/**
	 * The group type of the application. E.g: people, groups , events etc.
	 * @var string
	 */
	public $progress 		= null;

	/**
	 * The title of the application
	 * @var string
	 */
	public $params		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(&$db)
	{
		parent::__construct( '#__easyblog_languages' , 'id' , $db );
	}


	/**
	 * Installs a language file
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function install()
	{
		$params 	= new JRegistry($this->params);

		// Get the api key
		$config = EB::config();
		$key 	= $config->get('main_apikey');

		if (!$key) {
			$this->setError(JText::_('COM_EASYBLOG_INVALID_API_KEY_PROVIDED'));
			return false;			
		}
		
		// Get the download url
		$url = $params->get( 'download' );

		if (!$url) {
			$this->setError(JText::_('COM_EASYBLOG_INVALID_DOWNLOAD_URL'));
			return false;
		}

		// Download the language file
		$connector 	= EB::connector();
		$connector->addUrl($url);
		$connector->addQuery('key', $key);
		$connector->setMethod('POST');
		$connector->execute();
		$result = $connector->getResult($url);

		// Generate a random hash
		$hash = md5($this->locale . JFactory::getDate()->toSql());

		// Build storage path
		$storage = JPATH_ROOT . '/tmp/' . $hash . '.zip';

		// Save the file
		$state = JFile::write($storage, $result);

		$folder = JPATH_ROOT . '/tmp/' . $hash;

		jimport( 'joomla.filesystem.archive' );

		// Extract the language's archive file
		$state 		= JArchive::extract($storage, $folder);

		// Throw some errors when we are unable to extract the zip file.
		if (!$state) {
			$this->setError(JText::_('COM_EASYBLOG_UNABLE_TO_EXTRACT_ARCHIVE'));
			return false;
		}

		// Read the meta data
		$raw  = JFile::read($folder . '/meta.json');
		$meta = json_decode($raw);

		foreach ($meta->resources as $resource) {

			// Get the correct path based on the meta's path
			$dest = $this->getPath($resource->path) . '/language/' . $this->locale;

			// If language folder don't exist, create it first.
			if (!JFolder::exists($dest)) {
				JFolder::create($dest);
			}

			// Build the source and target files
			$destFile 	= $dest . '/' . $this->locale . '.' . $resource->title;
			$sourceFile = $folder . '/' . $resource->path . '/' . $this->locale . '.' . $resource->title;

			// Ensure that the source file exists
			if (!JFile::exists($sourceFile)) {
				continue;
			}

			// If the destination file already exists, delete it first
			if (JFile::exists($destFile)) {
				JFile::delete($destFile);
			}
			
			// Try to copy the file
			$state		= JFile::copy($sourceFile, $destFile);

			if (!$state) {
				$this->setError(JText::_('COM_EASYBLOG_LANGUAGES_ERROR_COPYING_FILES'));
				return false;
			}
		}

		// After everything is copied, ensure that the extracted folder is deleted to avoid dirty filesystem
		JFile::delete($storage);
		JFolder::delete($folder);

		// Once the language files are copied accordingly, update the state
		$this->state 	= EBLOG_LANGUAGES_INSTALLED;

		return $this->store();
	}

	public function getPath( $metaPath )
	{
		switch( $metaPath )
		{
			case 'admin':
			case 'fields':
			case 'plugins':
			case 'plugin':
			case 'menu':
			case 'apps':
				$path 	= JPATH_ROOT . '/administrator';
			break;

			case 'site':
			case 'module':
			default:
				$path	= JPATH_ROOT;
			break;
		}

		return $path;
	}
}
