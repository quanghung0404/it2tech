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

FD::import( 'admin.includes.nodes.nodes' );
FD::import( 'admin:/tables/table' );

class SocialUploader
{
	public $name = 'file';
	public $maxsize = null;
	public $multiple = false;

	public function __construct($options=array())
	{
		if (isset($options['name'])) {
			$this->name = $options['name'];
		}

		if (isset($options['maxsize'])) {
			$this->maxsize = $options['maxsize'];
		}

		if (isset($options['multiple'])) {
			$this->multiple = $options['multiple'];
		}
	}

	public static function factory($options=array())
	{
		$obj = new self($options);
		return $obj;
	}

	/**
	 * Generates a unique token for the current session.
	 * Without this token, the caller isn't allowed to upload the file.
	 *
	 * @since	1.0
	 * @param	null
	 * @return	string		A 12 digit token that can only be used once.
	 */
	public function generateToken()
	{
		// Generate a unique id.
		$id 	= uniqid();

		// Add md5 hash
		$id 	= md5( $id );

		$table			= FD::table( 'UploaderToken' );
		$table->token	= $id;
		$table->created = FD::date()->toMySQL();

		$table->store();

		return $id;
	}

	/**
	 * Performs validity checks of files
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getFile($name = null, $filter = '')
	{
		// Check if post_max_size is exceeded.
		if (empty($_FILES) && empty($_POST)) {
			return FD::exception('COM_EASYSOCIAL_EXCEPTION_UPLOAD_POST_SIZE');
		}

		// Get the file
		if (empty($name)) {
			$name = $this->name;
		}

		if ($this->multiple) {

			$tmp = JRequest::getVar($name, '', 'FILES');

			$file = array();

			foreach ($tmp as $k => $v) {
				$file[$k] = $v['file'];
			}

		} else {

			$file = JRequest::getVar($name, '', 'FILES');			
		}

		// Check for invalid file object
		if (empty($file)) {
			return ES::exception('COM_EASYSOCIAL_EXCEPTION_UPLOAD_NO_OBJECT');
		}

		// If there's an error in this file
		if ($file['error']) {
			return ES::exception($file, SOCIAL_EXCEPTION_UPLOAD);
		}

		// Check if file exceeds max upload filesize
		$maxsize = ES::math()->convertBytes($this->maxsize);

		if ($maxsize > 0 && $file['size'] > $maxsize) {
			return ES::exception(
				JText::sprintf(
					'COM_EASYSOCIAL_EXCEPTION_UPLOAD_MAX_SIZE',
					ES::math()->convertUnits($maxsize, 'B', 'MB', false, true)
				)
			);
		}

		// Ensure that the file is valid
		if ($filter && $filter == 'image') {
			$result = $this->filter($filter, $file);

			if ($result !== true) {
				return $result;
			}
		}

		// Return file
		return $file;
	}

	/**
	 * Filters a file
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function filter($filter = '', $file)
	{
		if (!$filter) {
			return true;
		}

		if ($filter == 'image') {
			if (!$this->isImageExtension($file['name'])) {
				return ES::exception('COM_EASYSOCIAL_EXCEPTION_INVALID_IMAGE');
			}

			$info = getimagesize($file['tmp_name']);

			if (!$info) {
				return ES::exception('COM_EASYSOCIAL_EXCEPTION_INVALID_IMAGE');
			}
			
			$containsXSS = $this->containsXSS($file['tmp_name']);

			if ($containsXSS) {
				return ES::exception('COM_EASYSOCIAL_EXCEPTION_INVALID_IMAGE');	
			}
		}

		return true;
	}

	/**
	 * Determines if an extension is an image type
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function isImageExtension($fileName)
	{
		static $imageTypes = 'gif|jpg|jpeg|png';
		
		return preg_match("/$imageTypes/i",$fileName);
	}

	/**
	 * Checks if the file contains any funky html tags
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function containsXSS($path)
	{
		// Sanitize the content of the files
		$contents = JFile::read($path, false, 256);
		$tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');

		// If we can't read the file, just skip this altogether
		if (!$contents) {
			return false;
		}

		foreach ($tags as $tag) {
			// If this tag is matched anywhere in the contents, we can safely assume that this file is dangerous
			if (stristr($contents, '<' . $tag . ' ') || stristr($contents, '<' . $tag . '>') || stristr($contents, '<?php') || stristr($contents, '?\>')) {
				return true;
			}			
		}

		return false;
	}
}
