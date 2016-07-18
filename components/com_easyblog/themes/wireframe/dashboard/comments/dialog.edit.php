<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
    <width>600</width>
    <height>350</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        "{submitButton} click" : function() {
            this.form().submit();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYBLOG_DIALOG_EDIT_COMMENTS_TITLE'); ?></title>
    <content>

        <form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal mt-20" data-form-response>
            <?php if ($this->config->get('comment_requiretitle')) { ?>
            <div class="form-group">
                <label for="comment-title" class="col-md-3">
                    <?php echo JText::_('COM_EASYBLOG_TITLE'); ?>
                </label>

                <div class="col-md-9">
                    <input class="form-control input-sm" type="text" id="comment-title" name="title" size="45" value="<?php echo $this->html('string.escape', $comment->title); ?>" />
                </div>
            </div>
            <?php } ?>

            <div class="form-group">
                <label for="comment-author-name" class="col-md-3">
                    <?php echo JText::_('COM_EASYBLOG_NAME'); ?>
                </label>

                <div class="col-md-9">
                    <input class="form-control input-sm" type="text" id="comment-author-name" name="name" value="<?php echo $this->html('string.escape', $comment->name); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="comment-author-email" class="col-md-3">
                    <?php echo JText::_('COM_EASYBLOG_EMAIL'); ?>
                </label>

                <div class="col-md-9">
                    <input class="form-control input-sm" type="text" id="comment-author-email" name="email" value="<?php echo $this->html('string.escape', $comment->email); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="comment-author-website" class="col-md-3">
                    <?php echo JText::_('COM_EASYBLOG_WEBSITE'); ?>
                </label>

                <div class="col-md-9">
                    <input class="form-control input-sm" type="text" id="comment-author-website" name="website" value="<?php echo $this->html('string.escape', $comment->url); ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="comment-message" class="col-md-3">
                    <?php echo JText::_('COM_EASYBLOG_COMMENT'); ?>
                </label>

                <div class="col-md-9">
                    <textarea id="comment-message" name="comment" class="form-control input-sm" cols="50" rows="5"><?php echo $this->escape($comment->comment); ?></textarea>
                </div>
            </div>

            <input type="hidden" name="id" value="<?php echo $comment->id;?>" />
            <?php echo $this->html('form.action', 'comments.update'); ?>
        </form>

    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_UPDATE_COMMENT_BUTTON'); ?></button>
    </buttons>
</dialog>
