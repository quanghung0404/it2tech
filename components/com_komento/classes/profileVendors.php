<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

abstract class KomentoProfileVendor
{
	protected $profile	= null;
	protected $link		= null;
	protected $avatar	= null;
	protected $paths	= array();
	protected $komentoprofile = 0;

	public $state 		= null;

	public function __construct( $profile )
	{
		$this->profile	= $profile;

		settype($this->paths, 'array');

		if( !empty($this->paths) )
		{
			foreach ($this->paths as $path)
			{
				if( !JFile::exists($path) )
				{
					$this->state 	= false;

					return false;
				}

				require_once($path);
			}
		}
		$this->state 	= true;

		$this->komentoprofile = Komento::getConfig()->get( 'use_komento_profile' );
	}

	public function addFile( $path )
	{
		$path = trim($path);
		array_unshift($this->paths, $path);
	}

	public function getAvatar() {}
	public function getLink() {}
}

class KomentoProfileDefault extends KomentoProfileGravatar
{
}

class KomentoProfileEasysocial extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$user   = Foundry::user( $this->profile->id );
		$avatar = $user->getAvatar();

		return $avatar;
	}
	
	public function getLink()
	{
		$user   = Foundry::user( $this->profile->id );
		
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		
		return $user->getPermalink();
	}
}

class KomentoProfileK2 extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_k2' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'route.php' );
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_k2' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'utilities.php' );

		$class = parent::__construct($profile);

		if( $this->state )
		{
			$sql = Komento::getSql();
			$sql->select( '#__k2_users' )
				->where( 'userID', $this->profile->id );

			$this->result	= $sql->loadObject();

			if( !$this->profile->id || !$this->result || !$this->result->image )
			{
				$this->state = false;
			}
		}

		return $class;
	}

	public function getAvatar()
	{
		$avatar	= new stdClass();
		$avatar->link	= rtrim( JURI::root() , '/' ) . '/media/k2/users/' . $this->result->image;

		return $avatar->link;
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}

		return K2HelperRoute::getUserRoute( $this->profile->id );
	}
}

class KomentoProfileAnahita extends KomentoProfileVendor
{
	public function __construct()
	{
		if( !class_exists( 'KFactory' ) )
		{
			return false;
		}

		return parent::__construct();
	}

	public function getAvatar()
	{
		return KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $this->profile->id )->getAvatar()->getURL( AnSeAvatar::SIZE_MEDIUM );
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		// JRoute::_( 'index.php?option=com_socialengine&view=person&id=' . $profile->id );
		return KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $this->id )->getURL();
	}
}

class KomentoProfileCommunitybuilder extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' .DIRECTORY_SEPARATOR. 'com_comprofiler' .DIRECTORY_SEPARATOR. 'plugin.foundation.php' );

		$ret = parent::__construct($profile);

		if( $this->state )
		{
			cbimport('cb.database');
			cbimport('cb.tables');
			cbimport('cb.tabs');
		}

		return $ret;
	}

	public function getAvatar()
	{
		$user	= CBuser::getInstance( $this->profile->id );
		// $field	= $user->getField( 'avatar', null, 'php', 'none', 'list' );
		// return $field['avatar'];

		$field	= $user->avatarFilePath();
		return $field;
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		//return JRoute::_('index.php?option=com_comprofiler&task=userProfile&user=' . $this->id, false);
		return cbSef( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='. $this->profile->id );
	}
}

class KomentoProfileHwdmediashare extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hwdmediashare' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR .'factory.php' );
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hwdmediashare' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'route.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$sql = Komento::getSql();

		$sql->select( '#__hwdms_users' )
			->where( 'id', $this->profile->id );

		$result = $sql->loadObject();

		if( isset( $result->key ) )
		{
			hwdMediaShareFactory::load('files');
			hwdMediaShareFiles::getLocalStoragePath();

			$folders = hwdMediaShareFiles::getFolders($result->key);
			$filename = hwdMediaShareFiles::getFilename($result->key, 10);
			$ext = hwdMediaShareFiles::getExtension($result, 10);

			$path = hwdMediaShareFiles::getPath($folders, $filename, $ext);

			if (file_exists($path))
			{
			        return hwdMediaShareFiles::getUrl($folders, $filename, $ext);
			}
		}

		return JURI::root( true ).'/media/com_hwdmediashare/assets/images/default-avatar.png';
	}

	public function getLink()
	{
		return JRoute::_( hwdMediaShareHelperRoute::getUserRoute( $this->profile->id ) );
	}
}

class KomentoProfileEasyblog extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$profileEB	= EasyBlogHelper::getTable( 'Profile','Table' );
		$profileEB->load( $this->profile->id );

		return $profileEB->getAvatar();
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		$profileEB	= EasyBlogHelper::getTable( 'Profile','Table' );
		$profileEB->load( $this->profile->id );

		return $profileEB->getLink();
	}
}

class KomentoProfileEasyDiscuss extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'image.php' );
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$EDProfile = DiscussHelper::getTable( 'Profile' );
		$EDProfile->load( $this->profile->id );
		return $EDProfile->getAvatar();
		//return JURI::root() . DiscussImageHelper::getAvatarRelativePath() . '/' . $this->profile->avatar;
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		return DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&id='.$this->profile->id, false);
	}
}
class KomentoProfileGravatar extends KomentoProfileVendor
{
	public function getAvatar( $email = null )
	{
		if( empty( $email ) )
		{
			$email = $this->profile->email;
		}

		$image = '';

		$config = Komento::getConfig();

		$emailKey = md5( strtolower( trim ( $email ) ) );

		if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
		{
			$image = 'https://secure.gravatar.com';
		}
		else
		{
			$image = 'http://www.gravatar.com';
		}

		$image .= '/avatar/' . $emailKey . '?s=100&amp;d=' . $config->get( 'gravatar_default_avatar', 'mm' );
		return $image;
	}

	public function getLink( $email = null )
	{
		if( empty( $email ) )
		{
			$email = $this->profile->email;
		}

		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}

		$link = '';

		if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' )
		{
			$link = 'https://secure.gravatar.com/' . md5($email);
		}
		else
		{
			$link = 'http://www.gravatar.com/' . md5($email);
		}
		return $link;
	}
}
class KomentoProfileJomsocial extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		return CFactory::getUser($this->profile->id)->getThumbAvatar();
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		return CRoute::_('index.php?option=com_community&view=profile&userid=' . $this->profile->id );
	}
}
class KomentoProfileKunena extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR. 'com_kunena' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'factory.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$userKNN = KunenaFactory::getUser($this->profile->id);
		return $userKNN->getAvatarURL('kavatar');
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		//return JRoute::_('index.php?option=com_kunena&func=fbprofile&userid=' . $this->id, false);
		$profileKNN		= KunenaFactory::getProfile($this->profile->id);
		return $profileKNN->getProfileURL($this->profile->id, '');
	}
}
class KomentoProfileKunena3 extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$this->addFile( JPATH_PLATFORM . DIRECTORY_SEPARATOR . 'kunena' . DIRECTORY_SEPARATOR . 'factory.php' );

		return parent::__construct($profile);
	}

	public function getAvatar()
	{
		$userKNN = KunenaFactory::getUser($this->profile->id);
		return $userKNN->getAvatarURL();
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}

		$userKNN = KunenaFactory::getUser($this->profile->id);
		return $userKNN->getURL();
	}
}
class KomentoProfileMightyregistration extends KomentoProfileVendor
{
	public function getAvatar()
	{

	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		return JRoute::_( 'index.php?option=com_community&view=profile&user_id=' . $this->id , false );
	}
}
class KomentoProfilePhpbb extends KomentoProfileVendor
{
	public function __construct($profile)
	{
		$config 		= Komento::getConfig();
		$phpbbpath		= $config->get( 'layout_phpbb_path' );
		$phpbburl		= $config->get( 'layout_phpbb_url' );
		$phpbburl		= Jstring::rtrim( $phpbburl, '/', '');

		$phpbbDB		= $this->_getPhpbbDBO( $phpbbpath );
		$phpbbConfig	= $this->_getPhpbbConfig();
		$phpbbuserid	= 0;

		if(empty($phpbbConfig))
		{
			return false;
		}

		$juser	= JFactory::getUser( $profile->id );

		$sql	= 'SELECT '.$phpbbDB->nameQuote('user_id').', '.$phpbbDB->nameQuote('username').', '.$phpbbDB->nameQuote('user_avatar').', '.$phpbbDB->nameQuote('user_avatar_type').' '
				. 'FROM '.$phpbbDB->nameQuote('#__users').' WHERE LOWER('.$phpbbDB->nameQuote('username').') = '.$phpbbDB->quote(strtolower($juser->username)).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if(!empty($result->user_avatar))
		{
			switch($result->user_avatar_type)
			{
				case '1':
					$subpath	= $phpbbConfig->avatar_upload_path;
					$phpEx 		= JFile::getExt(__FILE__);
					$source		= $phpbburl.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case '2':
					$source		= $result->user_avatar;
					break;
				case '3':
					$subpath	= $phpbbConfig->avatar_gallery_path;
					$source		= $phpbburl.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath 	= '';
					$source		= '';
			}
		}
		else
		{
			$sql	= 'SELECT '.$phpbbDB->nameQuote('theme_name').' '
					. 'FROM '.$phpbbDB->nameQuote('#__styles_theme').' '
					. 'WHERE '.$phpbbDB->nameQuote('theme_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath	= 'styles/'.$theme->theme_name.'/theme/images/no_avatar.gif';
			$source			= $phpbburl.'/'.$defaultPath;
		}

		$this->avatar		= $source;

		$this->link	= $phpbburl.'/memberlist.php?mode=viewprofile&u='.$phpbbuserid;

		return parent::__construct($profile);
	}

	private static function _getPhpbbDBO( $phpbbpath = null )
	{
		static $phpbbDB = null;

		if($phpbbDB == null)
		{
			$files			= JPATH_ROOT . DIRECTORY_SEPARATOR . $phpbbpath . DIRECTORY_SEPARATOR . 'config.php';

			if (!JFile::exists($files)) {
				$files	= $phpbbpath . DIRECTORY_SEPARATOR . 'config.php';
				if (!JFile::exists($files)) {
					return false;
				}
			}

			require_once( $files );

			$host		= $dbhost;
			$user		= $dbuser;
			$password	= $dbpasswd;
			$database	= $dbname;
			$prefix		= $table_prefix;
			$driver		= $dbms;
			$debug		= 0;

			$options = array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			$phpbbDB = JDatabase::getInstance( $options );
		}

		return $phpbbDB;
	}

	private function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();

		if (!$phpbbDB)
		{
			return false;
		}

		$sql	= 'SELECT '.$phpbbDB->nameQuote('config_name').', '.$phpbbDB->nameQuote('config_value').' '
				. 'FROM '.$phpbbDB->nameQuote('#__config') . ' '
				. 'WHERE '.$phpbbDB->nameQuote('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
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

	public function getAvatar()
	{
		return $this->avatar;
	}

	public function getLink()
	{
		if( $this->komentoprofile )
		{
			return JRoute::_('index.php?option=com_komento&view=profile&id=' . $this->profile->id);
		}
		return $this->link;
	}
}
