<?php

defined('_JEXEC') or die;

class plgSystemWidgetkit_zoo extends JPlugin
{
    public function onAfterInitialise()
    {
        if (!($app = (@include JPATH_ADMINISTRATOR.'/components/com_widgetkit/widgetkit-app.php')
            and file_exists($path = JPATH_ADMINISTRATOR.'/components/com_zoo/config.php')
            and JComponentHelper::getComponent('com_zoo', true)->enabled
            and (include_once $path)
            and class_exists('App')
            and $zoo = App::getInstance('zoo')
            and version_compare($zoo->zoo->version(), '2.5', '>='))
        ) {
            return;
        }

        $app['plugins']->addPath(__DIR__.'/plugin/plugin.php');
        $app['locator']->addPath('plugins/content/zoo', __DIR__.'/plugin');
    }
}
