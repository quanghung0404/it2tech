<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

include_once JPATH_ROOT . '/administrator/components/com_easyslider/classes/jsn.easyslider.render.php';

/**
 * EasySlider Content Plugin
 *
 * @package     Joomla.Plugin
 *
 * @subpackage  Content.joomla
 *
 * @since       1.6
 */
class plgContentJSNEasySlider extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 *
	 * @param   array   $config    An array that holds the plugin configuration
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		$this->_application = JFactory::getApplication();
	}

	/**
	 * Replace JSN EasySlider Syntax.
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   mixed    &$row     An object with a "text" property or the string to be cloaked.
	 * @param   mixed    &$params  Additional parameters. See {@see PlgContentEmailcloak()}.
	 * @param   integer  $page     Optional page number. Unused. Defaults to zero.
	 *
	 * @return  boolean	True on success.
	 */
	public function onContentPrepare($context, &$article, &$params, $page=0)
	{
		// Don't run this plugin when the area is admin
		if ($this->_application->isAdmin()) return;
		
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer')
		{
			return true;
		}
		
		$objJSNEasySliderRender = new JSNEasySliderRender();
		
		if (!empty($article->text))
		{
			// Find all instances of plugin and put in $matches for loadposition
		
			preg_match_all('/\{jsn_easyslider (.*)\/\}/U', $article->text, $matches, PREG_SET_ORDER);
	
			// No matches, skip this
			if (count($matches))
			{
				foreach ($matches as $index => $match)
				{
					$matcheslist = explode(' ', $match[1]);
					$tmpSliderID = explode('=', $matcheslist[1]);
					$sliderID = trim($tmpSliderID[1]);
					if (isset($sliderID))
					{
 						$output = $objJSNEasySliderRender->render($sliderID, true);
 						$article->text = @preg_replace("|$match[0]|", addcslashes($output, '\\$'), $article->text, 1);
					}
					// We should replace only first occurrence in order to allow positions with the same name to regenerate their content:
				}
			}
			
			return true;
		}
	}
}
