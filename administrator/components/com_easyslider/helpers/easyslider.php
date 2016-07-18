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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * EasySlider component helper.
 *
 * @package  JSN_EasySlider
 * @since    1.0.0
 */
include_once JPATH_COMPONENT_ADMINISTRATOR . '/classes/jsn.easyslider.sliders.php';

class JSNEasySliderHelper
{
    /**
     * Add toolbar button.
     *
     * @return    void
     */
    public static function addToolbarMenu()
    {
        // Get 5 most-recent sliders
       
        $objJSNEasySliderSliders 	= new JSNEasySliderSliders();
        $sliders 					= $objJSNEasySliderSliders->getSlidersWithoutState(5);
        
        // Create a toolbar button that drop-down a sub-menu when clicked
        JSNMenuHelper::addEntry(
            'toolbar-menu', 'Menu', '', false, 'icon-list-view', 'toolbar'
        );

        // Declare 1st-level menu sliders

        JSNMenuHelper::addEntry(
            'sliders',
            'JSN_MENU_SLIDERS',
            '',
            false,
            'administrator/components/com_easyslider/assets/images/icons-16/icon-items.png',
            'toolbar-menu'
        );

        JSNMenuHelper::addEntry(
            'configuration',
            'JSN_MENU_CONFIGURATION_AND_MAINTENANCE',
            'index.php?option=com_easyslider&view=configuration',
            false,
            'administrator/components/com_easyslider/assets/images/icons-16/icon-configuration.png',
            'toolbar-menu'
        );

        JSNMenuHelper::addEntry(
            'about',
            'JSN_MENU_ABOUT',
            'index.php?option=com_easyslider&view=about',
            false,
            'administrator/components/com_easyslider/assets/images/icons-16/icon-about.png',
            'toolbar-menu'
        );

        // Declare 2nd-level menu sliders	for 'sliders' entry
        JSNMenuHelper::addEntry(
            'slider-new', JText::_('JSN_EASYSLIDER_CREATE_NEW_SLIDER', true), 'index.php?option=com_easyslider&view=slider&layout=edit', false, '', 'toolbar-menu.sliders'
        );

        JSNMenuHelper::addSeparator('toolbar-menu.sliders');

        if ($sliders)
        {
            JSNMenuHelper::addEntry(
                'recent-sliders', JText::_('JSN_EASYSLIDER_RECENT_SLIDERS', true), '', false, '', 'toolbar-menu.sliders'
            );

            foreach ($sliders AS $slider)
            {
                JSNMenuHelper::addEntry(
                    'slider-' . $slider->slider_id,
                    $slider->slider_title,
                    'index.php?option=com_easyslider&view=slider&layout=edit&slider_id=' . $slider->slider_id,
                    false,
                    '',
                    'toolbar-menu.sliders.recent-sliders'
                );
            }
        }
        JSNMenuHelper::addEntry(
            'all-sliders', JText::_('JSN_EASYSLIDER_ALL_SLIDERS', true), 'index.php?option=com_easyslider&view=sliders', false, '', 'toolbar-menu.sliders'
        );
    }

    /**
     * Configure the linkbar
     *
     * @param   string $vName The name of the active view
     *
     * @return    void
     */
    public static function addSubmenu($vName)
    {
        if (JFactory::getApplication()->input->getCmd('tmpl', null) == null)
        {
            // Get 5 most-recent sliders
            $objJSNEasySliderSliders 	= new JSNEasySliderSliders();
        	$sliders 					= $objJSNEasySliderSliders->getSlidersWithoutState(5);

            JSNMenuHelper::addEntry(
                'sliders',
                'JSN_MENU_SLIDERS',
                '',
                $vName == 'sliders',
                'administrator/components/com_easyslider/assets/images/icons-16/icon-items.png',
                'sub-menu'
            );

            JSNMenuHelper::addEntry(
                'configuration',
                'JSN_MENU_CONFIGURATION_AND_MAINTENANCE',
                '',
                $vName == 'maintenance' OR $vName == 'configuration',
                'administrator/components/com_easyslider/assets/images/icons-16/icon-configuration.png',
                'sub-menu'
            );
            
            JSNMenuHelper::addEntry(
                'about',
                'JSN_MENU_ABOUT',
                'index.php?option=com_easyslider&view=about',
                $vName == 'about',
                'administrator/components/com_easyslider/assets/images/icons-16/icon-about.png',
                'sub-menu'
            );

            // Declare 2nd-level menu sliders	for 'sliders' entry
            JSNMenuHelper::addEntry(
                'slider-new', JText::_('JSN_EASYSLIDER_CREATE_NEW_SLIDER', true), 'index.php?option=com_easyslider&view=slider&layout=edit', false, '', 'sub-menu.sliders'
            );

            JSNMenuHelper::addSeparator('sub-menu.sliders');

            if ($sliders)
            {
                JSNMenuHelper::addEntry(
                    'recent-sliders', JText::_('JSN_EASYSLIDER_RECENT_SLIDERS', true), '', false, '', 'sub-menu.sliders'
                );

                foreach ($sliders AS $slider)
                {
                    JSNMenuHelper::addEntry(
                        'slider-' . $slider->slider_id,
                        $slider->slider_title,
                        'index.php?option=com_easyslider&view=slider&layout=edit&slider_id=' . $slider->slider_id,
                        false,
                        '',
                        'sub-menu.sliders.recent-sliders'
                    );
                }
            }

            JSNMenuHelper::addEntry(
                'all-sliders', JText::_('JSN_EASYSLIDER_ALL_SLIDERS', true), 'index.php?option=com_easyslider&view=sliders', false, '', 'sub-menu.sliders'
            );

            // Declare 2nd-level menu sliders	for 'configuration' entry
            JSNMenuHelper::addEntry(
                'global-params', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_PARAMETERS', true), 'index.php?option=com_easyslider&view=configuration&s=configuration&g=configs', false, '', 'sub-menu.configuration'
            );

            JSNMenuHelper::addEntry(
                'messages', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_MESSAGES', true), 'index.php?option=com_easyslider&view=configuration&s=configuration&g=msgs', false, '', 'sub-menu.configuration'
            );

            JSNMenuHelper::addEntry(
                'languages', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_LANGUAGES', true), 'index.php?option=com_easyslider&view=configuration&s=configuration&g=langs', false, '', 'sub-menu.configuration'
            );

            JSNMenuHelper::addEntry(
                'update', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_PRODUCT_UPDATE', true), 'index.php?option=com_easyslider&view=configuration&s=configuration&g=update', false, '', 'sub-menu.configuration'
            );

            JSNMenuHelper::addEntry(
                'maintenance', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_PRODUCT_MAINTENANCE', true), '', false, '', 'sub-menu.configuration'
            );

            // Declare 3rd-level menu sliders	for 'maintenance' entry
            JSNMenuHelper::addEntry(
                'data', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_PRODUCT_DATA', true), 'index.php?option=com_easyslider&view=configuration&s=maintenance&g=data', false, '', 'sub-menu.configuration.maintenance'
            );

            JSNMenuHelper::addEntry(
                'permissions', JText::_('JSN_EASYSLIDER_ALL_GLOBAL_PERMISSIONS', true), 'index.php?option=com_easyslider&view=configuration&s=maintenance&g=permissions', false, '', 'sub-menu.configuration.maintenance'
            );

            // Render the sub-menu
            JSNMenuHelper::render('sub-menu');
        }
    }

    /**
     * Add assets
     *
     * @return    void
     */
    public static function addAssets($slider = '')
    {
        // Load common assets
        !class_exists('JSNBaseHelper') OR JSNBaseHelper::loadAssets();

        // Load proprietary assets
        if (empty($slider))
        {
            if (class_exists('JSNHtmlAsset'))
            {
                JSNHtmlAsset::addStyle(JURI::root(true) . '/administrator/components/com_easyslider/assets/css/easyslider.css');
            }
            else
            {
                $doc = JFactory::getDocument();
                $doc->addStyleSheet(JURI::root(true) . '/administrator/components/com_easyslider/assets/css/easyslider.css');
            }
        }
        else
        {
			// do nothing
        }
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public static function getModuleInfo()
    {
        $db = JFactory::getDbo();
        $q = $db->getQuery(true);

        $q->select('*');
        $q->from($db->quoteName('#__extensions'));
        $q->where($db->quoteName('element') . '=' . $db->quote('mod_easyslider') . ' AND ' . $db->quoteName('type') . '=' . $db->quote('module'));

        $db->setQuery($q);

        try
        {
            $result = $db->loadObject();
        } 
        catch (Exception $e)
        {
            return false;
        }

        return $result;
    }

   
    public static function getComponentInfo()
    {
    	$db = JFactory::getDbo();
    	$q = $db->getQuery(true);
    
    	$q->select('*');
    	$q->from($db->quoteName('#__extensions'));
    	$q->where($db->quoteName('element') . '=' . $db->quote('com_easyslider') . ' AND ' . $db->quoteName('type') . '=' . $db->quote('component'));
    
    	$db->setQuery($q);
    
    	try
    	{
    		$result = $db->loadObject();
    	}
    	catch (Exception $e)
    	{
    		return false;
    	}
    	
    	return $result;
    }

	/**
	 * Get Data Slider
	 */
	public  static function getSliders($limit = "")
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query -> select('*');
		$query -> from('#__jsn_easyslider_sliders');
		$query -> order('slider_id DESC');
		if (empty($limit))
		{
			$db->setQuery($query);
		}
		else
		{
			$db->setQuery($query, 0, $limit);
		}
		$sliders = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
		}
		return $sliders;
	}
}
