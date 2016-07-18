<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-posts-list" data-composer-association-posts>

    <div class="eb-composer-posts-toolbar">
        <form action="<?php echo JRoute::_('index.php?option=com_easyblog&view=composer&layout=getPosts&code=' . $langcode . '&codeid=' . $langid . '&tmpl=component&browse=1'); ?>" method="get">
            <div class="input-group">
                <input type="text" name="query" value="<?php echo $search; ?>" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_TOOLBAR_PLACEHOLDER_SEARCH');?>" />
                <span class="input-group-btn">
                    <input class="btn btn-default" type="submit" value="<?php echo JText::_('search');?>" />
                </span>
            </div>
            <input type="hidden" name="option" value="com_easyblog" />
            <input type="hidden" name="view" value="composer" />
            <input type="hidden" name="layout" value="getPosts" />
            <input type="hidden" name="tmpl" value="component" />
            <input type="hidden" name="browse" value="1" />
            <input type="hidden" name="code" value="<?php echo $langcode; ?>" />
            <input type="hidden" name="codeid" value="<?php echo $langid; ?>" />
            <?php echo $this->html('form.token'); ?>
        </form>
    </div>

    <?php if ($posts) { ?>

        <?php foreach ($posts as $post) { ?>
        <div class="eb-composer-posts-item">
            <a onclick="parent.insertAssoc('<?php echo $post->id; ?>','<?php echo $post->title;?>', '<?php echo $langid;?>');" class="text-sm" href="javascript:void(0);"
                data-eb-composer-assoc-insert-link
                data-id="<?php echo $post->id; ?>"
                data-title="<?php echo $post->title; ?>"
                data-permalink="<?php echo $post->permalink; ?>"
            ><?php echo $post->title; ?></a>
        </div>
        <?php } ?>

        <?php if ($pagination) { ?>
            <?php echo $pagination->getPagesLinks(); ?>
        <?php } ?>

    <?php } else { ?>
        <div class="eb-composer-posts-item">
            <div class="text-center"><?php echo JText::_('COM_EASYBLOG_ASSOCIATION_NO_ITEM_FOUND'); ?></div>
        </div>
    <?php } ?>
</div>
