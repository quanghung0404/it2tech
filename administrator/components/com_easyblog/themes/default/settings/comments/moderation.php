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
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATION_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_NEW_COMMENT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_moderatecomment', $this->config->get('comment_moderatecomment')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_BLOG_AUTHORS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_moderateauthorcomment', $this->config->get('comment_moderateauthorcomment')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_COMMENTS_MODERATE_GUEST_COMMENTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'comment_moderateguestcomment', $this->config->get('comment_moderateguestcomment')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>