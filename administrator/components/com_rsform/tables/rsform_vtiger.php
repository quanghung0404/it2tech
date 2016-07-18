<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_vTiger extends JTable
{
	// Form ID
	public $form_id;
	
	// All setup fields
	public $vt_fields;
	
	// Auth information
	public $vt_accesskey;
    public $vt_username;
    public $vt_hostname;
	
	// Published
	public $vt_published = 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_vtiger', 'form_id', $db);
	}
}