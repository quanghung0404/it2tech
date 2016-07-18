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

jimport( 'joomla.filesystem.file' );

class SocialAssets
{
	private $headers = array();

	public function __construct()
	{
		return $this;
	}

	public static function factory()
	{
		return new self();
	}

	public function addHeader( $key , $value=null )
	{
		$header	= "/*<![CDATA[*/ " . (isset($value)) ? "$key" : "var $key = '$value';" . "/*]]>*/ ";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration( $header );

		return $this;
	}

	/**
	 * Attaches any assets type on the header object
	 *
	 * @param	string	$name	File name that needs to be attached.
	 * @param	string	$type	Type of asset. css - Css files, js - javascript files
	 * @param	string	$location	Location of the assets. site - For site , admin - For back end
	 *
	 * @return	boolean	True on success false otherwise
	 */
	public function attach( $name , $type = 'images' , $location = 'media' )
	{
		static $medias	= array();

		// Already loaded, we don't want to load it again.
		if( isset( $medias[ $location ][ $name ] ) )
		{
			return $medias[ $location ][ $name ];
		}

		$path = $this->uri($location, $type . '/' . $name);

		$document	= JFactory::getDocument();

		switch( $type )
		{
			case 'styles':
				$document->addStyleSheet( $path );
				break;

			case 'scripts':
				$document->addScript( $path );
				break;
		}

		$medias[$location][ $name ]	= true;

		return $medias[ $location ][ $name ];
	}

	public function locations($uri=false)
	{
		static $locations = array();

		$type = ($uri) ? 'uri' : 'path';

		if (isset($locations[$type])) {

			return $locations[$type];
		}

		$config = FD::config();
		$URI = ($uri) ? '_URI' : '';
		$DS  = '/';

		$locations[$type] = array(
			'site'				=> constant("SOCIAL_SITE_THEMES" . $URI) . $DS . strtolower($config->get('theme.site')),
			'site_base'			=> constant("SOCIAL_SITE_THEMES" . $URI) . $DS . strtolower($config->get('theme.site_base')),
			'site_override'     => constant("SOCIAL_JOOMLA_SITE_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('site') . $DS . "html" . $DS . SOCIAL_COMPONENT_NAME,
			'admin'				=> constant("SOCIAL_ADMIN_THEMES" . $URI) . $DS . strtolower($config->get('theme.admin')),
			'admin_base'		=> constant("SOCIAL_ADMIN_THEMES" . $URI) . $DS . strtolower($config->get('theme.admin_base')),
			'admin_override'    => constant("SOCIAL_JOOMLA_ADMIN_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('admin') . $DS . "html" . $DS . SOCIAL_COMPONENT_NAME,
			'module'            => constant("SOCIAL_JOOMLA_MODULES" . $URI),
			'module_override'   => constant("SOCIAL_JOOMLA_SITE_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('site') . $DS . "html",
			'media'				=> constant("SOCIAL_MEDIA" . $URI),
			'foundry'			=> constant("SOCIAL_FOUNDRY" . $URI),
			'root'			    => constant("SOCIAL_JOOMLA" . $URI)
		);

		return $locations[$type];
	}

	public function path($location, $type='')
	{
		$locations = $this->locations();

		if (isset($locations[$location])) {
			$path = $locations[$location];
		} else {
			$path = '';
		}

		if ($type!=='') {
			$path .= '/' . $type;
		}

		return $path;
	}

	public function uri($location, $type='')
	{
		$locations = $this->locations(true);

		if (isset($locations[$location])) {
			$path = $locations[$location];
		} else {
			$path = '';
		}

		if ($type!=='') {
			$path .= '/' . $type;
		}

		return $path;
	}

	public function fileUri($location, $type='')
	{
		return "file://" . $this->path($location, $type);
	}

	public function relative($dest, $root='', $dir_sep='/')
	{
		$root = explode($dir_sep, $root);
		$dest = explode($dir_sep, $dest);
		$path = '.';
		$fix = '';

		$diff = 0;
		for ($i = -1; ++$i < max(($rC = count($root)), ($dC = count($dest)));)
		{
			if(isset($root[$i]) and isset($dest[$i]))
			{
				if($diff)
				{
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}

				if($root[$i] != $dest[$i])
				{
					$diff = 1;
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}
			}
			elseif(!isset($root[$i]) and isset($dest[$i]))
			{
				for($j = $i-1; ++$j < $dC;)
				{
					$fix .= $dir_sep. $dest[$j];
				}
				break;
			}
			elseif(isset($root[$i]) and !isset($dest[$i]))
			{
				for($j = $i-1; ++$j < $rC;)
				{
					$fix = $dir_sep. '..'. $fix;
				}
				break;
			}
		}

		return $path . $fix;
	}

	public function relativeUri($dest, $root)
	{
		$dest = new JURI($dest);
		$dest = $dest->getPath();

		$root = new JURI($root);
		$root = $root->getPath();

		return $this->relative($dest, $root);
	}

	/**
	 * Convert path to URI
	 *
	 * Convert /var/public_html/components/theme/simplistic/styles/blabla.less
	 * to http://mysite.com/components/theme/simplistic/styles/blabla.less
	 *
	 * @param	string	$path
	 *
	 * @return	string	Full path URI
	 */
	public function toUri( $path )
	{
		jimport('joomla.filesystem.path');
		$path = JPath::clean($path);

		if( strpos($path, SOCIAL_JOOMLA) === 0 )
		{
			$result = substr_replace($path, '', 0, strlen(SOCIAL_JOOMLA));
			$result = str_ireplace(DIRECTORY_SEPARATOR, '/', $result);
			$result = ltrim( $result, '/');
		}
		else
		{
			$parts = explode(DIRECTORY_SEPARATOR, $path);
			foreach ($parts as $i => $part) {
				if( $part == 'components' ) {
					break;
				}
				unset($parts[$i]);
			}

			$result = implode('/', $parts);
		}

		$result = SOCIAL_JOOMLA_URI . '/' . $result;
		return $result;
	}

	public static function getJoomlaTemplate( $client = 'site' )
	{
		static $template = array();

		if( !array_key_exists($client, $template) )
		{
			$clientId = ($client == 'site') ? 0 : 1;

			$db = FD::db();

			$query	= 'SELECT template FROM `#__template_styles` AS s'
					. ' LEFT JOIN `#__extensions` AS e ON e.type = `template` AND e.element=s.template AND e.client_id=s.client_id'
					. ' WHERE s.client_id = ' . $db->quote($clientId) . ' AND home = 1';

			$db->setQuery( $query );

			$result 	= $db->loadResult();

			// Fallback template
			if( !$result )
			{
				$result = ($client == 'site') ? 'beez_20' : 'bluestork';
			}

			$template[$client] = $result;
		}

		return $template[$client];
	}
}
