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
class JSNTplMMShortcodeCommon {

    /**
     * element type: layout/element
     */
    public $type;

    /**
     * config information of this element
     */
    public $config;

    /**
     * setting options of this element
     */
    public $items;

    public $element_url;
    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct() {
		$this->element_url = JUri::root(true) . '/plugins/system/jsntplframework/libraries/joomlashine/megamenu/shortcodes/elements';
    }

    /*
     * HTML structure of an element in MegaMenu area
    */
    
    public function elementInMegamenu() {
    
    }
    
    /**
     * Include backend assets
     *
     * @return type
     */
    public function backendElementAssets() {
    
    }
    
    /**
     * Include frontend assets
     *
     * @return type
     */
    public function frontendElementAssets() {
    
    } 

    /*
     * HTML structure of an element in SELECT ELEMENT modal box
    */
    
    public function elementButton($sort) 
    {
    
    }    
}