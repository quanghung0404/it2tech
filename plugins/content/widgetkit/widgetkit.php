<?php

defined('_JEXEC') or die;

class plgContentWidgetkit extends JPlugin
{
    public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
    {
        if (!$app = @include(JPATH_ADMINISTRATOR . '/components/com_widgetkit/widgetkit-app.php')) {
            return;
        }

        $article->text = $app['shortcode']->parse('widgetkit', $article->text, function($attrs) use ($app) {
            return $app->renderWidget($attrs);
        });

        return '';
    }
}
