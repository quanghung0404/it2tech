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
class JSNTplTemplateCookielaw
{
	/**
	 * Instance of template administrator object
	 *
	 * @var  JSNTplTemplateCookielaw
	 */
	private static $_instance;

	/**
	 * Return an instance of JSNTplTemplateCookielaw class.
	 *
	 * @return  JSNTplTemplateCookielaw
	 */
	public static function getInstance()
	{
		if ( ! isset(self::$instance))
		{
			self::$instance = new JSNTplTemplateCookielaw;
		}
	
		return self::$instance;
	}
	
	/**
	 * Load Cookie EU Law
	 * 
	 */
	public static function loadCookie()
	{
		$document 	= JFactory::getDocument();
		$url		= JUri::root(true);
		// Only site pages that are html docs
		if ($document->getType() !== 'html') return false;
		
		// Prepare template parameters
		$templateParams = isset($document->params) ? $document->params : null;
		
		if (empty($templateParams))
		{
			$templateParams = JFactory::getApplication()->getTemplate(true);
			$templateParams = $templateParams->params;
		}

		if ((int) $templateParams->get('cookieEnableCookieConsent'))
		{
			self::loadCookieLibrary();
			$jsParams = array();
			$jsParams ['learnMore'] = (string) $templateParams->get('cookieLearnMore');
			$jsParams ['dismiss'] = (string) $templateParams->get('cookieDismiss');
			$jsParams ['message'] = (string) $templateParams->get('cookieMessage');
			if ((string) $templateParams->get('cookieLink') != '')
			{	
				$jsParams ['link'] = (string) $templateParams->get('cookieLink');
			}
			else
			{
				$jsParams ['link'] = null;
			}
			
			$theme = $url . '/plugins/system/jsntplframework/assets/3rd-party/cookieconsent/styles/';
			$theme .= (string) $templateParams->get('cookieStyle') . '-' . (string) $templateParams->get('cookieBannerPlacement') . '.css'; 
			
			$jsParams ['theme'] = $theme;
			
			$js = 'window.cookieconsent_options = ' . json_encode($jsParams) . ';';
			
			$document->addScriptDeclaration($js);
		}
			
	}
	
	/**
	 * Load Cookie Lirary
	 */
	public static function loadCookieLibrary()
	{
		$document = JFactory::getDocument();
		$url = JUri::root(true);		
		$document->addScript($url . '/plugins/system/jsntplframework/assets/3rd-party/cookieconsent/cookieconsent.js');
	}
	 
}
