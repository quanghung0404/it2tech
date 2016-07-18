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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_CONTENT_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_ENABLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_ENABLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_COMPOSER_ENABLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'composer_truncation_enabled', $this->config->get('composer_truncation_enabled')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_MAX_CHARS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_MAX_CHARS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_MAX_CHARS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-4">
                                <input type="text" name="composer_truncation_chars" class="form-control text-center" value="<?php echo $this->config->get('composer_truncation_chars' , '350');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_DISPLAY_READMORE_WHEN_NECESSARY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_DISPLAY_READMORE_WHEN_NECESSARY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_TRUNCATION_COMPOSER_DISPLAY_READMORE_WHEN_NECESSARY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'composer_truncation_readmore', $this->config->get('composer_truncation_readmore'));?>
                    </div>
                </div>

                <?php $mediaTypes   = array('image', 'video', 'audio'); ?>

                <?php foreach ($mediaTypes as $media) { ?>
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="composer_truncate_<?php echo $media; ?>_position" class="form-control">
                            <option value="top"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'top' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_TOP_OPTION' ); ?></option>
                            <option value="bottom"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'bottom' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_BOTTOM_OPTION' );?></option>
                            <option value="hidden"<?php echo $this->config->get('composer_truncate_' . $media  . '_position' ) == 'hidden' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_DO_NOT_SHOW_OPTION' );?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_LIMITS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_LIMITS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_LIMITS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-3">
                                <input type="text" name="composer_truncate_<?php echo $media; ?>_limit" class="form-control text-center" value="<?php echo $this->config->get('composer_truncate_'.$media.'_limit' , '0');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_AUTOMATED_TRUNCATION_NORMAL_CONTENT_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_CONTENT_AS_INTROTEXT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_CONTENT_AS_INTROTEXT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_CONTENT_AS_INTROTEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_blogasintrotext', $this->config->get('layout_blogasintrotext')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_BLOG_TYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="main_truncate_type" class="form-control" data-truncate-type>
                            <option value="chars"<?php echo $this->config->get( 'main_truncate_type' ) == 'chars' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_CHARACTERS' ); ?></option>
                            <option value="words"<?php echo $this->config->get( 'main_truncate_type' ) == 'words' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_WORDS' ); ?></option>
                            <option value="paragraph"<?php echo $this->config->get( 'main_truncate_type' ) == 'paragraph' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_PARAGRAPH' ); ?></option>
                            <option value="break"<?php echo $this->config->get( 'main_truncate_type' ) == 'break' ? ' selected="selected"':'';?>><?php echo JText::_( 'COM_EASYBLOG_BY_BREAK' );?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group <?php echo $this->config->get('main_truncate_type') == 'chars' || $this->config->get('main_truncate_type') == 'words' ? '' : 'hide';?>" data-max-chars>
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_OF_BLOG_CONTENT_AS_INTROTEXT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-4">
                                <input type="text" name="layout_maxlengthasintrotext" class="form-control text-center" value="<?php echo $this->config->get('layout_maxlengthasintrotext' , '150');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group <?php echo $this->config->get('main_truncate_type') == 'break' || $this->config->get('main_truncate_type') == 'paragraph' ? '' : 'hide';?>" data-max-tag>
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_MAX_LENGTH_TAGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-lg-3">
                                <input type="text" name="main_truncate_maxtag" class="form-control text-center" value="<?php echo $this->config->get('main_truncate_maxtag' , '150');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_ADD_ELLIPSES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_ADD_ELLIPSES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_ADD_ELLIPSES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_truncate_ellipses', $this->config->get('main_truncate_ellipses'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SHOW_READMORE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SHOW_READMORE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_SHOW_READMORE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_respect_readmore', $this->config->get('layout_respect_readmore'));?>
                    </div>
                </div>

                <?php $mediaTypes   = array('image', 'video', 'audio', 'gallery'); ?>

                <?php foreach ($mediaTypes as $media) { ?>
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TRUNCATE_' . strtoupper($media) . '_POSITIONS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="main_truncate_<?php echo $media; ?>_position" class="form-control">
                            <option value="top"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'top' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_TOP_OPTION' ); ?></option>
                            <option value="bottom"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'bottom' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_BOTTOM_OPTION' );?></option>
                            <option value="hidden"<?php echo $this->config->get( 'main_truncate_' . $media  . '_position' ) == 'hidden' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_DO_NOT_SHOW_OPTION' );?></option>
                        </select>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
