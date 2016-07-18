<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_MailChimp extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $form_id = null;
	
	var $mc_list_id = '';
	var $mc_action = 1; // 0 - unsubscribe; 1 - subscribe, 2 - let the user decide
	var $mc_action_field = '';
	var $mc_merge_vars = '';
	var $mc_interest_groups = '';
	var $mc_email_type = 'html';
	var $mc_email_type_field = '';
	var $mc_double_optin = 1;
	var $mc_update_existing = 0;
	var $mc_replace_interests = 1;
	var $mc_send_welcome = 0;
	var $mc_delete_member = 0;
	var $mc_send_goodbye = 1;
	var $mc_send_notify = 1;
	
	var $mc_published = 0;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableRSForm_MailChimp(& $db)
	{
		parent::__construct('#__rsform_mailchimp', 'form_id', $db);
	}
}