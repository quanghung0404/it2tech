<?php
/**
* @package        EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license        GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-content">
    <div class="es-content-wrap">
        <form action="<?php echo JRoute::_('index.php'); ?>" method="post">
            <div class="app-news news-form app-group">
                <h3><?php echo JText::_('APP_EVENT_NEWS_CREATE_ANNOUNCEMENT'); ?></h3>

                <hr />

                <div class="form-group">
                    <input type="text" name="title" value="<?php echo $this->html('string.escape', $news->title); ?>" placeholder="<?php echo JText::_('APP_EVENT_NEWS_TITLE_PLACEHOLDER', true); ?>" class="form-control input-sm news-title" />

                    <?php if ($params->get('allow_comments', true)) { ?>
                    <label for="comments" class="checkbox">
                        <input type="checkbox" name="comments"<?php echo $news->comments ? ' checked="checked"' : ''; ?>/> <?php echo JText::_('APP_EVENT_NEWS_ALLOW_COMMENTS'); ?>
                    </label>
                    <?php } ?>

                    <div class="editor-wrap fd-cf">
                        <?php echo $editor->display('news_content', $news->content, '100%', '200', '10', '5', array('image', 'pagebreak','ninjazemanta'), null, 'com_easysocial'); ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?php echo $event->getPermalink(); ?>" class="pull-left btn btn-es-danger btn-large"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></a>
                    <button type="submit" class="pull-right btn btn-es-primary btn-large" data-news-save-button><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON'); ?> &rarr;</button>
                </div>
            </div>

            <?php echo $this->html('form.token'); ?>
            <input type="hidden" name="controller" value="apps" />
            <input type="hidden" name="task" value="controller" />
            <input type="hidden" name="appController" value="news" />
            <input type="hidden" name="appTask" value="save" />
            <input type="hidden" name="appId" value="<?php echo $app->id; ?>" />
            <input type="hidden" name="cluster_id" value="<?php echo $event->id; ?>" />
            <input type="hidden" name="newsId" value="<?php echo $news->id; ?>" />
            <input type="hidden" name="option" value="com_easysocial" />
        </form>
    </div>
</div>
