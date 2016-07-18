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

FD::import( 'admin:/includes/fields/dependencies' );

class SocialFieldViewUserFile extends SocialFieldView
{
	public function download()
	{
		$fileid = JRequest::getInt( 'uid' );

		if( empty( $fileid ) )
		{
			FD::info()->set( (object) array( 'message' => JText::_( 'PLG_FIELDS_FILE_ERROR_INVALID_FILE_ID' ), 'type' => SOCIAL_MSG_ERROR ) );
			$this->redirect( FRoute::dashboard( array(), false ) );
		}

		if( !$this->params->get( 'allow_download' ) )
		{
			FD::info()->set( (object) array( 'message' => JText::_( 'PLG_FIELDS_FILE_ERROR_DOWNLOAD_NOT_ALLOWED' ), 'type' => SOCIAL_MSG_ERROR ) );
			$this->redirect( FRoute::dashboard( array(), false ) );
		}

		$file = FD::table( 'file' );
		$state = $file->load( $fileid );

		$file->download();
		exit;
	}

	public function preview()
	{
		$fileid = JRequest::getInt( 'uid' );

		if( empty( $fileid ) )
		{
			FD::info()->set( (object) array( 'message' => JText::_( 'PLG_FIELDS_FILE_ERROR_INVALID_FILE_ID' ), 'type' => SOCIAL_MSG_ERROR ) );
			$this->redirect( FRoute::dashboard( array(), false ) );
		}

		$file = FD::table( 'file' );
		$state = $file->load( $fileid );

		if( !$state || !$file->hasPreview() || !$this->params->get( 'allow_preview' ) )
		{
			FD::info()->set( (object) array( 'message' => JText::_( 'PLG_FIELDS_FILE_ERROR_PREVIEW_NOT_ALLOWED' ), 'type' => SOCIAL_MSG_ERROR ) );
			$this->redirect( FRoute::dashboard( array(), false ) );
		}

		$file->preview();
	}
}
