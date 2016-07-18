<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

FD::import( 'site:/views/views' );

class EasySocialViewActivities extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function toggle( $id, $curState )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		// Set the label
		$label	= $curState ? JText::_( 'COM_EASYSOCIAL_ACTIVITY_HIDE' ) : JText::_( 'COM_EASYSOCIAL_ACTIVITY_SHOW' );

		$isHidden	= $curState ? 0 : 1;

		return $ajax->resolve( $label, $isHidden );
	}

	public function delete()
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$html = JText::_( 'COM_EASYSOCIAL_ACTIVITY_ITEM_DELETED' );

		return $ajax->resolve( $html );
	}

	public function getActivities( $filterType, $data, $nextlimit, $isloadmore = false )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'activities' , $data );
		$theme->set( 'nextlimit' , $nextlimit );
		$theme->set( 'active' , $filterType );


		$output = '';
		if( $isloadmore )
		{
			if( $data )
			{
				foreach( $data as $activity )
				{
					$output .= $theme->loadTemplate( 'site/activities/item' , array( 'activity' => $activity, 'active' => $filterType ) );
				}
			}

			return $ajax->resolve( $output, $nextlimit );
		}
		else
		{
			$output 	= $theme->output( 'site/activities/content.items' );

			return $ajax->resolve( $output );
		}

	}

	/**
	 * Confirmation for deleting an activity item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax 	= FD::ajax();

		$theme 		= FD::themes();
		$contents	= $theme->output( 'site/activities/dialog.delete' );

		return $ajax->resolve( $contents );
	}

	/**
	 * Retrieves a list of hidden apps from the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 * @return
	 */
	public function getHiddenApps( $data )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'apps' , $data );

		$output = $theme->output( 'site/activities/default.activities.hiddenapp' );

		return $ajax->resolve( $output );

	}

	/**
	 * Retrieves a list of hidden apps from the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 * @return
	 */
	public function getHiddenActors( $data )
	{
		// Load ajax lib
		$ajax	= FD::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		$theme 		= FD::get( 'Themes' );
		$theme->set( 'actors' , $data );

		$output = $theme->output( 'site/activities/default.activities.hiddenactor' );

		return $ajax->resolve( $output );

	}

	public function unhideapp()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}

	public function unhideactor()
	{
		// Load ajax library.
		$ajax 	= FD::ajax();

		$error 	= $this->getError();

		if( $error )
		{
			return $ajax->reject( $error );
		}

		return $ajax->resolve();
	}

}
