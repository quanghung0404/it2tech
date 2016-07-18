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
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="row">

        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo JText::_('COM_EASYBLOG_COMMENTS_EDIT_COMMENT_DETAILS');?></b>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="title" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_TITLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_TITLE'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <input type="text" name="title" class="form-control" value="<?php echo $this->html('string.escape', $comment->title);?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_NAME'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_NAME'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <input type="text" name="name" class="form-control" value="<?php echo $this->html('string.escape', $comment->name);?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_EMAIL'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_EMAIL'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_EMAIL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <input type="text" name="email" class="form-control" value="<?php echo $this->html('string.escape', $comment->email);?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="url" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_WEBSITE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_WEBSITE'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_WEBSITE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <input type="text" name="url" class="form-control" value="<?php echo $this->html('string.escape', $comment->url);?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comment" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <textarea name="comment" rows="5" class="form-control" cols="35" data-comment-editor><?php echo $this->html('string.escape',  $comment->comment );?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="created" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_CREATED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_CREATED'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_CREATED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-lg-5">
                                    <?php echo $this->html('form.calendar', 'created', $comment->created); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="published" class="col-md-3">
                            <?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_PUBLISHED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                           <?php echo $this->html('grid.boolean', 'published', $comment->published); ?>
                        </div>
                    </div>
                </div>
            </div>
    	</div>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="id" value="<?php echo $comment->id;?>" />
</form>
