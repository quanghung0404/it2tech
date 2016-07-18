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
	 * Previews an email
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function preview()
	{
		$id = $this->input->get('id', 0, 'int');

		$table = ES::table('Mailer');
		$table->load($id);

		// Load the language
		$table->loadLanguage();

		// Load the mailer library
		$mailer = ES::mailer();

		$table->title = $mailer->translate($table->title, $table->params);

		$theme = FD::themes();
		$theme->set('mailer', $table);
		$contents = $theme->output('admin/mailer/preview');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Confirmation before purging everything
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmPurgeAll()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$contents 	= $theme->output( 'admin/mailer/dialog.purge.all' );

		$ajax->resolve( $contents );
	}

	/**
	 * Confirmation before purging pending e-mails
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmPurgeSent()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$contents 	= $theme->output( 'admin/mailer/dialog.purge.sent' );

		$ajax->resolve( $contents );
	}

	/**
	 * Confirmation before purging pending e-mails
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function confirmPurgePending()
	{
		$ajax 	= FD::ajax();

		$theme 	= FD::themes();
		$contents 	= $theme->output( 'admin/mailer/dialog.purge.pending' );

		$ajax->resolve( $contents );
	}
}
