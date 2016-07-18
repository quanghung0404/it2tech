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

jimport( 'joomla.application.component.controller' );

FD::import( 'admin:/controllers/controller' );

class EasySocialControllerIndexer extends EasySocialController
{
	public function __construct()
	{
		parent::__construct();

		// Register task aliases here.
		$this->registerTask( 'remove' 	, 'remove' );
		$this->registerTask( 'purge'	, 'purge' );
		$this->registerTask( 'reindex'	, 'reindex' );
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function remove()
	{
		// Check for request forgeries
		FD::checkToken();

		$view 	= $this->getCurrentView();
		$cid 	= JRequest::getVar('cid', '');

		if( !$cid )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_INDEXER_INVALID_ID_PROVIDED' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$model = FD::model( 'Indexer' );

		foreach( $cid as $id )
		{
			$model->deleteById( $id );
		}

		$view->setMessage( JText::_( 'COM_EASYSOCIAL_INDEXER_ITEMS_DELETED' ) , SOCIAL_MSG_SUCCESS );
		return $view->call( __FUNCTION__ );
	}

	/**
	 * Allows user to purge indexed items
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		// Check for request forgeries
		FD::checkToken();

		// Get the current view
		$view 	= $this->getCurrentView();

		$model 	= FD::model( 'Indexer' );
		$state 	= $model->purge();

		if( $state !== true )
		{
			$view->setMessage( JText::_('COM_EASYSOCIAL_INDEXER_PURGE_FAILED' ), SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		$message 	= JText::_( 'COM_EASYSOCIAL_INDEXER_PURGED_SUCCESS' );

		$view->setMessage( $message , SOCIAL_MSG_SUCCESS );
		$view->call( __FUNCTION__ );
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function reindex()
	{
		$view 	= FD::view( 'Indexer' , true );
		$view->call( 'reindex' );
	}

	/**
	 * Some desc
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function indexing()
	{
		$view 	= FD::view( 'Indexer' , true );

		$max 	= JRequest::getVar( 'max', 0 );

		$indexer = FD::get( 'Indexer' );
		$tmax 	 = $indexer->reindex();

		if( empty( $max ) )
		{
			$max = $tmax;
		}

		if( empty( $max ) )
		{
			$view->call( 'indexing', -1, '100' );
			return;
		}

		$progress = ( ( $max - $tmax ) * 100 ) / $max ;
		$progress = round( $progress );

		if( $progress >= 100 )
		{
			$progress = 100;
			$max = -1;
		}

		$view->call( 'indexing', $max, $progress );
	}


}
