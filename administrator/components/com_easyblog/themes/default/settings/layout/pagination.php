<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
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
                <b><?php echo JText::_('COM_EASYBLOG_PAGINATION_LIST_LIMIT'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_PAGINATION_LIST_LIMIT_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_LATEST_POSTS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_LATEST_POSTS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_LATEST_POSTS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings('layout_listlength', $this->config->get('layout_listlength'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_CATEGORIES_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_CATEGORIES_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_CATEGORIES_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings('layout_pagination_categories', $this->config->get('layout_pagination_categories'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_BLOGGER_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_BLOGGER_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_BLOGGER_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings('layout_pagination_bloggers', $this->config->get('layout_pagination_bloggers'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_TEAMBLOG_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_TEAMBLOG_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_TEAMBLOG_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings('layout_pagination_teamblogs', $this->config->get('layout_pagination_teamblogs'));?>
                    </div>
                </div>                

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_CATEGORIES_IN_CATEGORIES_LIST_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_CATEGORIES_IN_CATEGORIES_LIST_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_CATEGORIES_IN_CATEGORIES_LIST_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings( 'layout_pagination_categories_per_page' , $this->config->get( 'layout_pagination_categories_per_page' ) );?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_BLOGGERS_IN_BLOGGERS_LIST_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_BLOGGERS_IN_BLOGGERS_LIST_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_BLOGGERS_IN_BLOGGERS_LIST_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings( 'layout_pagination_bloggers_per_page' , $this->config->get( 'layout_pagination_bloggers_per_page' ) );?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_ARCHIVE_LIST_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_ARCHIVE_LIST_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_ENTRIES_IN_ARCHIVE_LIST_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings( 'layout_pagination_archive' , $this->config->get( 'layout_pagination_archive' ) );?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_POSTS_IN_DASHBOARD_LIST_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_POSTS_IN_DASHBOARD_LIST_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_POSTS_IN_DASHBOARD_LIST_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings( 'layout_pagination_dashboard_post_per_page' , $this->config->get( 'layout_pagination_dashboard_post_per_page' ) );?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_COMMENTS_IN_DASHBOARD_LIST_PAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_COMMENTS_IN_DASHBOARD_LIST_PAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TOTAL_COMMENTS_IN_DASHBOARD_LIST_PAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getPaginationSettings( 'layout_pagination_dashboard_comment_per_page' , $this->config->get( 'layout_pagination_dashboard_comment_per_page' ) );?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>