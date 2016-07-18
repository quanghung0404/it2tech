<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easyblog/views.php');

class EasyBlogViewSpools extends EasyBlogAdminView
{
	/**
	 * Display a list of email activities
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tpl = null)
	{
		$this->checkAccess('easyblog.manage.mail');

		$layout = $this->getLayout();

		if (method_exists($this, $layout)) {
			return $this->$layout();
		}

		// Load frontend language file
		EB::loadLanguages();
		
		// Set heading
		$this->setHeading('COM_EASYBLOG_TITLE_MAIL_ACTIVITIES', '', 'fa-send-o');

		$filter_state	= $this->app->getUserStateFromRequest( 'com_easyblog.spools.filter_state', 		'filter_state', 	'*', 		'word' );
		$search			= $this->app->getUserStateFromRequest( 'com_easyblog.spools.search', 			'search', 			'', 		'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $this->app->getUserStateFromRequest( 'com_easyblog.spools.filter_order', 		'filter_order', 	'created', 	'cmd' );
		$orderDirection	= $this->app->getUserStateFromRequest( 'com_easyblog.spools.filter_order_Dir',	'filter_order_Dir',	'asc', 		'word' );

		$mails			= $this->get('Data');
		$pagination		= $this->get( 'Pagination' );

		$this->set('mails', $mails );
		$this->set('pagination', $pagination );
		$this->set('state', JHTML::_('grid.state', $filter_state, JText::_('COM_EASYBLOG_SENT'), JText::_('COM_EASYBLOG_PENDING')));
		$this->set('search', $search );
		$this->set('order', $order );
		$this->set('orderDirection', $orderDirection );

		parent::display('spools/default');
	}

	/**
	 * Previews a mail
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function preview()
	{
		// Check for acl rules.
		$this->checkAccess('mail');

		// Get the mail id
		$id = $this->input->get('id', 0, 'int');

		$mailq	= EB::table('Mailqueue');
		$mailq->load($id);

		echo $mailq->getBody();
		exit;
	}

	public function registerToolbar()
	{
		JToolBarHelper::title(JText::_('COM_EASYBLOG_TITLE_MAIL_ACTIVITIES'), 'spools');

		JToolbarHelper::deleteList('COM_EASYBLOG_ARE_YOU_SURE_CONFIRM_DELETE', 'spools.remove');
		JToolBarHelper::divider();
		JToolBarHelper::custom('spools.purgeSent','purge','icon-32-unpublish.png', 'COM_EASYBLOG_PURGE_SENT', false);
		JToolBarHelper::custom('spools.purge','purge','icon-32-unpublish.png', 'COM_EASYBLOG_PURGE_ALL', false);
	}
}
