<?php

defined('_JEXEC') or die;

$render = function() use ($params) {

    if (!$app = @include(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit-app.php')) {
        return;
    }

    $output = $app->renderWidget(json_decode($params->get('widgetkit', '[]'), true));
    echo $output === false ? $app['translator']->trans('Could not load widget') : $output;
};

return $render();
