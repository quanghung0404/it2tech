<?php
/**
* @package RSForm!Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class TableRSForm_Salesforce extends JTable
{
	public $form_id;
	
	public $slsf_lead_source;
	public $slsf_first_name;
	public $slsf_last_name;
	public $slsf_title;
	public $slsf_company;
	public $slsf_email;
	public $slsf_phone;
	public $slsf_street;
	public $slsf_city;
	public $slsf_state;
	public $slsf_zip;
	public $slsf_country;
	public $slsf_debug;
	public $slsf_oid = 0;
	public $slsf_debugEmail;
	public $slsf_industry;
	public $slsf_description;
	public $slsf_mobile;
	public $slsf_fax;
	public $slsf_website;
	public $slsf_salutation;
	public $slsf_revenue;
	public $slsf_employees;
	public $slsf_custom_fields;
	public $slsf_campaign_id;
	public $slsf_donotcall;
	public $slsf_emailoptout;
	public $slsf_faxoptout;
	
	public $slsf_published = 0;
	
	public function __construct(& $db) {
		parent::__construct('#__rsform_salesforce', 'form_id', $db);
	}
}