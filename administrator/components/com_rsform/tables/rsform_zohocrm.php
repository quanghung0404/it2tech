<?php
/**
* @package RSform!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class TableRSForm_ZohoCrm extends JTable
{
	var $form_id 			= null;	
	var $zh_wf_trigger 		= 1;
	var $zh_duplicate_check = 1;
	var $zh_is_approval 	= 0;
	var $zh_format 			= 1;
	var $zh_merge_vars 		= '';
	var $zh_debug 			= 0;
	var $zh_published 		= 0;
		
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(& $db) {
		parent::__construct('#__rsform_zohocrm', 'form_id', $db);
	}
}