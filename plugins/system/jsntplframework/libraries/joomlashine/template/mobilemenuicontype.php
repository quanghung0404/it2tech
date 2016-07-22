<?php
/**
 * @version     $Id$
 * @package     JSNExtension
 * @subpackage  JSNTPLFramework
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Helper class to generate Cookie Law for template
 *
 * @package     JSNTPLFramework
 * @subpackage  Template
 * @since       1.0.0
 */
class JSNTplTemplateMobilemenuicontype
{
	/**
	 * Instance of template administrator object
	 *
	 * @var  JSNTplTemplateMobilemenu
	 */
	private static $instance;

	/**
	 * Return an instance of JSNTplTemplateMobilemenu class.
	 *
	 * @return  JSNTplTemplateMobilemenu
	 */
	public static function getInstance()
	{
		if ( ! isset(self::$instance))
		{
			self::$instance = new JSNTplTemplateMobilemenuicontype;
		}
	
		return self::$instance;
	}
		
	/**
	 * Render mobile menu type
	 *
	 * @return string
	 */
	public static function render($icon)
	{
		$html 		= '';
		$document 	= JFactory::getDocument();
			
		// Prepare template parameters
		$templateParams = isset($document->params) ? $document->params : null;
			
		if (empty($templateParams))
		{
			$templateParams = JFactory::getApplication()->getTemplate(true);
			$templateParams = $templateParams->params;
		}
		$jversion = new JVersion();	
		
		if ((string) $templateParams->get('mobileMenu') == 'text' && version_compare($jversion->getShortVersion(), "3.0", ">"))
		{			
			$html = (string) $templateParams->get('mobileMenuText');
		} else {
			$html = $icon;
		}
		return $html;
	
	}
	
}
