<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2015 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JSNCheckbox field
 *
 * @package     JSNTPL
 * @subpackage  Form
 * @since       2.0.0
 */

include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/loader.php';
include_once JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/libraries/element.php';

class JFormFieldJSNMegaMenu extends JSNTPLFormField
{
	public $type 				= 'JSNMegaMenu';

	public $objJSNTplMMElement 	= null;
	
	/**
	 * Field constructor
	 *
	 * @param   JForm  $form  Form object
	 */
	public function __construct ($form = null)
	{
		// Call parent constructor
		parent::__construct($form);

		JSNTplMMLoader::register(JSN_PATH_TPLFRAMEWORK_MEGAMENU_LIBRARIES . '/helpers', 'JSNTplMMHelper');
		$this->objJSNTplMMElement = new JSNTplMMElement;
	}
	
	/* default layouts for Row */
	public $layouts = array(
			array( 6, 6 ),
			array( 4, 4, 4 ),
			array( 3, 3, 3, 3 ),
			array( 4, 8 ),
			array( 8, 4 ),
			array( 3, 9 ),
			array( 9, 3 ),
			array( 3, 6, 3 ),
			array( 3, 3, 6 ),
			array( 6, 3, 3 ),
			array( 2, 2, 2, 2, 2, 2 ),
	);
	
	/**
	 * Disable label by default.
	 *
	 * @return  string
	 */
	protected function getLabel()
	{
		return '';
	}
	
	/**
	 * Parse field declaration to render input.
	 *
	 * @return  void
	 */
	public function getInput()
	{
		//$objJSNTplMMElement = new JSNTplMMElement;
		$this->objJSNTplMMElement->init();
		//var_dump($objJSNTplMMElement->getElements());
		return parent::getInput();
	}
	
	public function getElements()
	{
		//$objJSNTplMMElement = new JSNTplMMElement;
		return $this->objJSNTplMMElement->getElements();	
	}
	
}
