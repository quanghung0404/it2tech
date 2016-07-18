<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_DotMailer extends JTable
{
	var $form_id = null;	
	var $dm_list_id = '';
	var $dm_merge_vars = '';
	var $dm_action = '1';
	var $dm_action_field = '';
	var $dm_audience_type = 'Unknown';
	var $dm_audience_type_field = '';
	var $dm_optin_type = 'Unknown';
	var $dm_optin_type_field = '';
	var $dm_email_type = 'Html';
	var $dm_email_type_field = '';
	var $dm_email = '';
	var $dm_published = 0;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__rsform_dotmailer', 'form_id', $db);
	}
}