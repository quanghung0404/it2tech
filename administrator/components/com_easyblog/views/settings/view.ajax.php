<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewSettings extends EasyBlogAdminView
{
	/**
	 * Brings up the import dialog form
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function import()
	{
		$template = EB::template();

		$output = $template->output('admin/settings/dialog.import');

		return $this->ajax->resolve($output);
	}

	/**
	 * Runs mailbox testing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function testMailbox()
	{
		$server = $this->input->get('server', '', 'default');
		$port = $this->input->get('port', '', 'default');
		$service = $this->input->get('service', '', 'default');
		$ssl = $this->input->get('ssl', true, 'bool');
		$mailbox = $this->input->get('mailbox', 'INBOX', 'default');
		$user = $this->input->get('user', '', 'default');
		$pass = $this->input->get('pass', '', 'default');

		// Ensure that all properties are set
		if (empty($server)) {
			return $this->ajax->reject(JText::_('Please enter the server address for your mail server.'));
		}

		if (empty($port)) {
			return $this->ajax->reject(JText::_('Please enter the server port for your mail server.'));
		}

		if (empty($user)) {
			return $this->ajax->reject(JText::_('Please enter your mailbox username.'));
		}

		if (empty($pass)) {
			return $this->ajax->reject(JText::_('Please enter your mailbox password.'));
		}

		$mailbox = EB::mailbox();
		$result = $mailbox->test($server, $port, $service, $ssl, $mailbox, $user, $pass);

		return $this->ajax->resolve($result);
	}
}
