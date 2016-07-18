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

jimport( 'joomla.installer.installer' );
jimport( 'joomla.installer.helper' );
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

require_once( dirname( __FILE__ ) . DS . 'joomla.php' );

class SocialInstallerJoomla17 extends SocialInstallerJoomla
{
	public function load( $path )
	{
		// Locate for the manifest file in the folder.
		$files			= FD::get( 'Folders' )->files( $path , '.xml' , self::RECURSIVE_SEARCH , self::RETRIEVE_FULL_PATH );

		$this->source	= $path;

		if (!count($files))
		{
			// Throw errors
			FD::get( 'Errors' )->set( 'installer.xml' , self::XML_NOT_FOUND );
			return false;
		}

		// Load through the list of manifest files to perform the installation.
		foreach( $files as $file )
		{
			$xml	= JFactory::getXML( $file );

			if( !$xml )
			{
				FD::get( 'Errors' )->set( 'installer' , self::XML_NOT_VALID );
				unset( $xml );
				continue;
			}

			$this->type = (string) $xml->attributes()->type;

			if( $xml->getName() != 'social' || !in_array( $this->type , $this->allowed ) )
			{
				FD::get( 'Errors' )->set( 'installer' , self::XML_NOT_VALID );
				unset( $parser );
				continue;
			}

			$this->parser		= JFactory::getXMLParser( 'Simple' );
			$this->parser->loadFile( $file );

			// Set the path of the current xml file.
			$this->path			= $file;

			// Retrieves the element
			$this->element		= $this->parser->document->getElementByPath( 'element' )->data();

			unset( $xml );
			return true;
		}
		return false;
	}
}
