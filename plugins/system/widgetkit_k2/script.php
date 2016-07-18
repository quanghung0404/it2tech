<?php

defined('_JEXEC') or die;

class plgSystemWidgetkit_k2InstallerScript
{
	public function install($parent)
	{
		jimport('joomla.filesystem.file');

		// enable plugin only if K2 installed and enabled
        if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_k2/k2.xml') && JComponentHelper::getComponent('com_k2', true)->enabled) {
        	JFactory::getDBO()->setQuery("UPDATE `#__extensions` SET `enabled` = 1 WHERE `type` = 'plugin' AND `element` = 'widgetkit_k2'")->execute();
        }
	}

    public function uninstall($parent) {}

    public function update($parent) {}

    public function preflight($type, $parent) {}

    public function postflight($type, $parent) {}
}