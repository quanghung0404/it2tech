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

class FD40_FoundryCompiler_EasySocial extends FD40_FoundryCompiler_Foundry
{
	public $name = 'EasySocial';

	public $path = SOCIAL_MEDIA;

	public function __construct($compiler)
	{
		$this->loadLanguage();

		return parent::__construct($compiler);
	}

	public function createModule($moduleName, $moduleType, $adapterName)
	{
		// Rollback to foundry script when the module type if library
		if ($moduleType=='library') {
			$adapterName = 'Foundry';
			$moduleType  = 'script';
		}

		if ($adapterName=='EasySocial') {
			if ($moduleType!=='language') {
				$moduleName = 'easysocial/' . $moduleName;
			}
		}

		$module = new FD40_FoundryModule($this->compiler, $adapterName, $moduleName, $moduleType);

		return $module;
	}

	public function getPath($name, $type='script', $extension='')
	{
		switch ($type) {
			case 'script':
				$folder = 'scripts';
				break;

			case 'stylesheet':
				$folder = 'styles';
				break;

			case 'template':
				$folder = 'scripts';
				break;
		}

		return $this->path . '/' . $folder . '/' . str_replace('easysocial/', '', $name) . '.' . $extension;
	}

	public function getLanguage($name)
	{
		return JText::_($name);
	}

	/**
	 * We cannot rely on PHP's Foundry object here because this might be called through CLI
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getView( $name )
	{
		$name	= str_replace('easysocial/', '', $name);

		if( defined( 'SOCIAL_COMPONENT_CLI' ) )
		{
			// Break down the namespace to segments
			$segments	= explode( '/' , $name );

			// Determine the current location
			$location 	= $segments[ 0 ];

			unset( $segments[ 0 ] );

			// @TODO: We should read the db and see which is the default theme
			$path 		= JPATH_ROOT . '/components/com_easysocial/themes/wireframe';

			if( $location == 'admin' )
			{
				$path	= JPATH_ADMINISTRATOR . '/components/com_easysocial/themes/default';
			}

			$path	= $path . '/' . implode( '/' , $segments ) . '.ejs';

			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $path ) )
			{
				return '';
			}

			ob_start();
			include( $path );
			$contents	= ob_get_contents();
			ob_end_clean();
		}
		else
		{
			// Do something here.
			$theme = FD::get( 'Themes' );
			$theme->extension = 'ejs';

			jimport( 'joomla.filesystem.file' );

			if( !JFile::exists( $theme->resolve( $name ) . '.ejs' ) )
			{
				return '';
			}

			$contents 	= $theme->output( $name );
		}

		return $contents;
	}

	private function loadLanguage()
	{
		// Load up language files
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT );
		JFactory::getLanguage()->load( 'com_easysocial' , JPATH_ROOT . '/administrator' );
	}
}
