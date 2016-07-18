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

class EasyBlogAvatarPhpBB
{
	var $files;
	var $phpbbpath;
	var $phpbbuserid;

	function _init()
	{
		$config = EB::config();
		$this->phpbbpath = $config->get( 'layout_phpbb_path' );

		$this->files = JPATH_ROOT . DIRECTORY_SEPARATOR . $this->phpbbpath . DIRECTORY_SEPARATOR . 'config.php';

	    if (!JFile::exists($this->files)) {
			return false;
		}

		return true;
	}

	function getAvatar($profile)
	{
		$this->_init();
		$phpbbDB = $this->_getPhpbbDBO();
		$phpbbConfig = $this->_getPhpbbConfig();

		EasyBlogHelper::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		if(empty($phpbbConfig))
		{
			return false;
		}

		$juser	= JFactory::getUser( $profile->id );

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('user_id').', '.$phpbbDB->{$nameQuote}('username').', '.$phpbbDB->{$nameQuote}('user_avatar').', '.$phpbbDB->{$nameQuote}('user_avatar_width').', '.$phpbbDB->{$nameQuote}('user_avatar_height').', '.$phpbbDB->{$nameQuote}('user_avatar_type').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__users').' WHERE LOWER('.$phpbbDB->{$nameQuote}('username').') = '.$phpbbDB->quote( strtolower( $juser->username) ).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$this->phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if (!empty($result->user_avatar)) {
			
			switch($result->user_avatar_type)
			{
				case 'avatar.driver.upload':
					$subpath = $phpbbConfig->avatar_upload_path;
					$phpEx = JFile::getExt(__FILE__);
					$source = JURI::root().$this->phpbbpath.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case 'avatar.driver.remote':
					$source = $result->user_avatar;
					break;
				case 'avatar.driver.local':
					$subpath = $phpbbConfig->avatar_gallery_path;
					$source = JURI::root().$this->phpbbpath.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath = '';
			}
		} else {
			$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('style_name').' '
					. 'FROM '.$phpbbDB->{$nameQuote}('#__styles').' '
					. 'WHERE '.$phpbbDB->{$nameQuote}('style_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath = $this->phpbbpath.'/styles/'.$theme->style_name.'/theme/images/no_avatar.gif';
			$source = JURI::root().$defaultPath;
		}

		$avatar = new stdClass();
		$avatar->link	= $source;

		return $avatar->link;
	}

	function _getPhpbbDBO()
	{
		static $phpbbDB = null;

		if ($phpbbDB == null) {

			require($this->files);

			// Split the path to get the exact driver
			$dbmsArray = explode("\\",$dbms);

			// Alwasy get the last array
			$driver = end($dbmsArray);

			$options = array('driver' => $driver, 'host' => $dbhost, 'user' => $dbuser, 'password' => $dbpasswd, 'database' => $dbname, 'prefix' => $table_prefix);
		
			$phpbbDB = JDatabase::getInstance( $options );
		}

		return $phpbbDB;
	}

	function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();

		EasyBlogHelper::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('config_name').', '.$phpbbDB->{$nameQuote}('config_value').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__config') . ' '
				. 'WHERE '.$phpbbDB->{$nameQuote}('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();

		if(empty($result))
		{
			return false;
		}

		$phpbbConfig = new stdClass();
        $phpbbConfig->avatar_gallery_path	= null;
        $phpbbConfig->avatar_upload_path	= null;
		$phpbbConfig->default_style			= 1;

		foreach($result as $row)
		{
			switch($row->config_name)
			{
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}

		return $phpbbConfig;
	}
}