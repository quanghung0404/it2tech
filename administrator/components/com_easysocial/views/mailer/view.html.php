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

// Include main views file.
FD::import( 'admin:/views/views' );

class EasySocialViewMailer extends EasySocialAdminView
{
	/**
	 * Default user listings page.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	public function display( $tpl = null )
	{
		// Set page heading
		$this->setHeading('COM_EASYSOCIAL_HEADING_EMAIL_ACTIVITIES');
		$this->setDescription('COM_EASYSOCIAL_DESCRIPTION_EMAIL_ACTIVITIES');

		// Add buttons
		JToolbarHelper::publishList( 'publish' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_MARK_SENT') );
		JToolbarHelper::unpublishList( 'unpublish' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_MARK_PENDING' ) );
		JToolbarHelper::divider();
		JToolbarHelper::trash( 'purgeSent' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PURGE_SENT' ) , false );
		JToolbarHelper::trash( 'purgePending' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PURGE_PENDING' ) , false );
		JToolbarHelper::trash( 'purgeAll' , JText::_( 'COM_EASYSOCIAL_TOOLBAR_TITLE_BUTTON_PURGE_ALL' ) , false );

		// Get the model
		$model			= FD::model( 'Mailer' , array( 'initState' => true ) );

		// Load site's language file
		FD::language()->loadSite();


		$emails 		= $model->getItemsWithState();
		$pagination 	= $model->getPagination();
		$state 			= $model->getState( 'published' );
		$limit 			= $model->getState( 'limit' );
		$search 		= $model->getState( 'search' );
		$ordering 		= $model->getState( 'ordering' );
		$direction 		= $model->getState( 'direction' );

		if( $state != 'all' )
		{
			$state 	= (int) $state;
		}

		$lib = FD::mailer();

		// Need to do some processing on the emails
		foreach ($emails as $mail) {

			// Load necessary language required by the mail
			$mail->loadLanguage();

			// Translate the title with necessary parameters
			$mail->title = $lib->translate($mail->title, $mail->params);
		}

		$this->set( 'ordering'	, $ordering );
		$this->set( 'direction'	, $direction );
		$this->set( 'search'	, $search );
		$this->set( 'limit'		, $limit );
		$this->set( 'published'	, $state );
		$this->set( 'emails'	, $emails );
		$this->set( 'pagination', $pagination );

		echo parent::display( 'admin/mailer/default' );
	}

	/**
	 * Previews an email
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function preview()
	{
		// Get the id.
		$id 	= JRequest::getInt( 'id' );

		$mail 	= FD::table( 'Mailer' );
		$mail->load( $id );

		// Load front end language file
		FD::language()->loadSite();

		echo $mail->preview();
		exit;
	}

	/**
	 * Method is invoked when the user purges all the mails.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function purgeAll()
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}

	/**
	 * Method is invoked when the user purges all pending mails.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function purgePending()
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}

	/**
	 * Method is invoked when the user purges all sent mails.
	 *
	 * @since	1.0
	 * @access	public
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function purgeSent()
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}

	/**
	 * Method is invoked when the user clicks on the apply button.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableMailer	The mailer object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function apply( $mailer )
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer&layout=form&cid=' . $mailer->id );
	}

	/**
	 * Method is invoked when the user publishes the mail items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableMailer	The mailer object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function save( $mailer )
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}

	/**
	 * Method is invoked when the user publishes the mail items.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableMailer	The mailer object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function save2new( $mailer )
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer&layout=form' );
	}

	/**
	 * Method is invoked when the user publishes the mail items.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function publish()
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}

	/**
	 * Method is invoked when the user unpublishes the mail items.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unpublish()
	{
		$this->redirect( 'index.php?option=com_easysocial&view=mailer' );
	}
}
