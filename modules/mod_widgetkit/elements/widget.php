<?php

defined('_JEXEC') or die;

class JFormFieldWidget extends JFormField
{
    protected $type = 'Widget';

    function getInput()
    {
        if (!$app = @include(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit-app.php')) {
            return;
        }

        $app->trigger('init.admin', array($app));

        $value = htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8');
        return <<<EOT
    <button type="button" class="btn btn-small widgetkit-widget">
        <span>{$app['translator']->trans('Select Widget')}</span>
    </button>
    <input type="hidden" name="$this->name" value="$value">
EOT;
    }
}
