<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Akismet extends JTable
{
	public $form_id 		= null;
	public $aki_merge_vars 	= '';
	public $aki_published 	= 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_akismet', 'form_id', $db);
	}
}