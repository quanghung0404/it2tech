<?php

defined('_JEXEC') or die;

class plgSystemWidgetkit_zooInstallerScript
{
    public function install($parent)
    {
        // enable plugin only if ZOO installed and enabled
        if (file_exists(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php') && JComponentHelper::getComponent('com_zoo', true)->enabled) {
            JFactory::getDBO()->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND `element` = 'widgetkit_zoo'")->execute();
        }
    }

    public function uninstall($parent) {}

    public function update($parent) {}

    public function preflight($type, $parent) {}

    public function postflight($type, $parent) {}
}