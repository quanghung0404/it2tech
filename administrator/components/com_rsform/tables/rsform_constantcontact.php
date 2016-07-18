<?php
/**
* @package RSForm!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_ConstantContact extends JTable
{
	public $form_id = null;
	
	public $cc_list_id 			= '';
	public $cc_action 			= 1; // 0 - unsubscribe; 1 - subscribe, 2 - let the user decide
	public $cc_action_field 	= '';
	public $cc_merge_vars 		= '';
	public $cc_update 			= 0;
	public $cc_delete_member 	= 0;	
	public $cc_published 		= 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_constantcontact', 'form_id', $db);
	}
}