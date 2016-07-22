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

// No direct access to this file.
defined('_JEXEC') || die('Restricted access');

/**
 * Parent class for all elements of page builder
 *
 * @package  IG_PageBuilder
 * @since    1.0.0
 */
class JSNTplMMElement
{
	private $elements = array();
	
	public function init() 
	{
		$this->registerElement();
		//$this->elementTpl();
	}
	/**
	 * Get array of shortcode elements
	 * @return type
	 */
	public function getElements() 
	{
		return $this->elements;
	}
	
	/**
	 * Add shortcode element
	 * @param type $type: type of element ( element/layout )
	 * @param type $class: name of class
	 * @param type $element: instance of class
	 */
	public function setElement($type, $class, $element = null) 
	{
		if (empty($element))
		{
			$this->elements[$type][strtolower($class)] = new $class();
		}
		else
		{	 
			$this->elements[$type][strtolower($class)] = $element;
		}
	}
	
	/**
	 * Register all Parent & No-child element, for Add Element popover
	 */
	public function registerElement() 
	{
		$currentShortcode      = null;
				
		$megamenuShortcodes = JSNTplMMHelperShortcode::getshortcodeTags();
				
		foreach ($megamenuShortcodes as $name => $scInfo)
		{
			$arr  = explode( '_', $name );
			
			$type = $scInfo['type'];
			
			if (! $currentShortcode || in_array($currentShortcode, $arr) || (! $currentShortcode && $type == 'layout')) 
			{
				$class   = JSNTplMMHelperShortcode::getShortcodeClass( $name );
				
				$element = new $class();
				
				$this->setElement($type, $class, $element);
				$this->registerSubEl($class, 1);
			}
		}
	}

	/**
	 * Regiter sub element
	 *
	 * @param string $class
	 * @param int $level
	 */
	private function registerSubEl($class, $level = 1) 
	{
		$item  = str_repeat('Item', intval( $level ) - 1);
		$class = str_replace('JSNTplMMShortcode' . $item, 'JSNTplMMShortcode' . $item . 'Item', $class);

		if (class_exists($class)) 
		{
			// 1st level sub item
			$element = new $class();
			$this->setElement('element', $class, $element);
			// 2rd level sub item
			$this->registerSubEl($class, 2);
		}
	}
	
	public function elementTpl()
	{
		$elements = $this->getElements();
		
		foreach ($elements as $typeList) 
		{
			foreach ($typeList as $element) 
			{
				// Get element type
				$elementType = $element->elementInMegamenu();

				// Print template tag
				foreach ($elementType as $elementStructure) 
				{
					echo "<script type='text/html' id='tmpl-{$element->config['shortcode']}'>\n{$elementStructure}\n</script>\n";
				}
			}
		}
			
	}
}