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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_ANTI_SPAM');?></b>

                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_ANTI_SPAM_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_ENABLE_MINIMUM_CONTENT_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_post_min', $this->config->get('main_post_min')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_MINIMUM_CONTENT_LENGTH_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="form-inline">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="main_post_length" value="<?php echo $this->escape( $this->config->get( 'main_post_length' ) );?>" class="form-control text-center" />
                                    <span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_CHARACTERS' );?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_CONTENT_FILTERING');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_CONTENT_FILTERING_INFO');?></div>
            </div>

            <div class="panel-body">
                <p><?php echo JText::_('COM_EASYBLOG_SETTINGS_BLOCKED_WORDS_INFO'); ?></p>

                <div class="form-group">
                    <label for="page_title" class="col-md-3">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_BLOCKED_WORDS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_BLOCKED_WORDS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_BLOCKED_WORDS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <textarea class="form-control" name="main_blocked_words"><?php echo $this->config->get( 'main_blocked_words' ); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>