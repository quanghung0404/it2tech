<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Payment extends JTable
{
	public $form_id;
	public $params;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_payment', 'form_id', $db);
	}
}