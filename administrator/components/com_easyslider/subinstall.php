<?php
/**
 * @version    $Id$
 * @package    JSNEasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Get path to JSN Installer class file
is_readable($base = dirname(__FILE__) . '/admin/jsninstaller.php')
OR is_readable($base = dirname(__FILE__) . '/administrator/components/com_easyslider/jsninstaller.php')
OR is_readable($base = dirname(__FILE__) . '/jsninstaller.php')
	OR is_readable($base = JPATH_COMPONENT_ADMINISTRATOR . '/jsninstaller.php')
	OR $base = null;

if ( ! empty($base))
{
	require_once $base;
}

/**
 * Class for finalizing JSN EasySlider installation.
 *
 * @package  JSNEasySlider
 * @since    1.0.0
 */
class Com_EasySliderInstallerScript extends JSNInstallerScript
{
    public function postflight($type, $parent)
    {
        parent::postflight($type, $parent);
        $db	= JFactory::getDbo();
        $query	= $db->getQuery(true);
        $query->update('#__extensions');
        $query->set('enabled = 1');
        $query->where("folder = 'easyslider'");
        $db->setQuery($query);
        $db->execute();
    }
}
