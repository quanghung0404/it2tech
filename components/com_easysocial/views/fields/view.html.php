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

// Include main view file.
FD::import( 'site:/views/views' );

/**
 * Fields's view.
 *
 * @author	Jason Rey <jasonrey@stackideas.com>
 * @since	1.0
 */
class EasySocialViewFields extends EasySocialSiteView
{
	public function display( $tpl = null )
	{
		$fieldid = JRequest::getInt( 'id' );

		$task = JRequest::getWord( 'task' );

		$field = FD::table( 'field' );
		$state = $field->load( $fieldid );

		if( !$state )
		{
			FD::info()->set( JText::_( 'COM_EASYSOCIAL_FIELDS_INVALID_ID' ), SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::dashboard( array(), false ) );
			$this->close();
		}

		$app = $field->getApp();

		if( !$app )
		{
			FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_FIELDS_APP_DOES_NOT_EXIST', $app->element ), SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::dashboard( array(), false ) );
			$this->close();
		}

		$base = SOCIAL_FIELDS . '/' . $app->group . '/' . $app->element . '/views';

		$classname = 'SocialFieldView' . ucfirst( $app->group ) . ucfirst( $app->element );

		if( !class_exists( $classname ) )
		{
			if( !JFile::exists( $base . '/' . $app->element . '.php' ) )
			{
				FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_FIELDS_VIEW_DOES_NOT_EXIST', $app->element ), SOCIAL_MSG_ERROR );
				$this->redirect( FRoute::dashboard( array(), false ) );
				$this->close();
			}

			require_once( $base . '/' . $app->element . '.php' );
		}

		if( !class_exists( $classname ) )
		{
			FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_FIELDS_CLASS_DOES_NOT_EXIST', $classname ), SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::dashboard( array(), false ) );
			$this->close();
		}

		$view = new $classname( $app->group, $app->element );

		if( !is_callable( array( $view, $task ) ) )
		{
			FD::info()->set( JText::sprintf( 'COM_EASYSOCIAL_FIELDS_METHOD_DOES_NOT_EXIST', $task ), SOCIAL_MSG_ERROR );
			$this->redirect( FRoute::dashboard( array(), false ) );
			$this->close();
		}

		$view->init( $field );

		return $view->$task();
	}
}
