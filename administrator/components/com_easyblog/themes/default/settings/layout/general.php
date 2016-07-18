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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_TITLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_LAYOUT_BREADCRUMB_BLOGGER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_blogger_breadcrumb', $this->config->get('layout_blogger_breadcrumb'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DISPLAY_NAME_FORMAT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_( 'select.option' , 'name' , JText::_( 'COM_EASYBLOG_REAL_NAME_OPTION' ) );
                            $listLength[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYBLOG_NICKNAME_OPTION' ) );
                            $listLength[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYBLOG_USERNAME_OPTION' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_nameformat', 'class="form-control"', 'value', 'text', $this->config->get('layout_nameformat' , 'name'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_HIDE_EMPTY_CATEGORIES_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_categories_hideempty', $this->config->get('main_categories_hideempty'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ZERO_AS_PLURAL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_zero_as_plural', $this->config->get('layout_zero_as_plural'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_LAYOUT_ENABLE_RESPONSIVE_LAYOUT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_responsive', $this->config->get('layout_responsive'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ALLOW_HTML_FOR_BIOGRAPHY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ALLOW_HTML_FOR_BIOGRAPHY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_DASHBOARD_ALLOW_HTML_FOR_BIOGRAPHY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_dashboard_biography_editor', $this->config->get('layout_dashboard_biography_editor')); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGER_THEME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGER_THEME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_ENABLE_BLOGGER_THEME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'layout_enablebloggertheme', $this->config->get('layout_enablebloggertheme')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_SELECTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_SELECTION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGER_THEME_SELECTION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->getBloggerThemes() ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-6">

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_ORDERING');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_ORDERING_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_( 'select.option' , 'modified' , JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LAST_MODIFIED' ) );
                            $listLength[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_LATEST' ) );
                            $listLength[] = JHTML::_('select.option', 'alphabet', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_ALPHABET' ) );
                            $listLength[] = JHTML::_('select.option', 'popular', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_OPTIONS_HITS' ) );
                            $listLength[] = JHTML::_('select.option', 'published', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_ORDERING_PUBLISHED' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_postorder', 'class="form-control"', 'value', 'text', $this->config->get('layout_postorder' , 'latest'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_('select.option', 'desc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_DESCENDING' ) );
                            $listLength[] = JHTML::_('select.option', 'asc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_POSTS_SORTING_OPTIONS_ASCENDING' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_postsort', 'class="form-control input-box"', 'value', 'text', $this->config->get('layout_postsort' , 'desc'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOG_LISTING_POSTS_SORTING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOG_LISTING_POSTS_SORTING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOG_LISTING_POSTS_SORTING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_('select.option', 'desc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOG_LISTING_POSTS_SORTING_OPTIONS_DESCENDING' ) );
                            $listLength[] = JHTML::_('select.option', 'asc', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TEAMBLOG_LISTING_POSTS_SORTING_OPTIONS_ASCENDING' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_teamblogsort', 'class="form-control input-box"', 'value', 'text', $this->config->get('layout_teamblogsort' , 'desc'));
                        ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_( 'select.option' , 'featured' , JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_FEATURED' ) );
                            $listLength[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATEST' ) );
                            $listLength[] = JHTML::_('select.option', 'alphabet', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ALPHABET' ) );
                            $listLength[] = JHTML::_('select.option', 'latestpost', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_LATESTPOST' ) );
                            $listLength[] = JHTML::_('select.option', 'active', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_BLOGGERS_ORDERING_OPTIONS_ACTIVE' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_bloggerorder', 'class="form-control"', 'value', 'text', $this->config->get('layout_bloggerorder' , 'latest'));
                        ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_TAGSTYLE');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_TAGSTYLE_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_( 'select.option' , '1' , JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE1' ) );
                            $listLength[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE2' ) );
                            $listLength[] = JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE3' ) );
                            $listLength[] = JHTML::_('select.option', '4', JText::_( 'COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_STYLE4' ) );
                            echo JHTML::_('select.genericlist', $listLength, 'layout_tagstyle', 'class="form-control"', 'value', 'text', $this->config->get('layout_tagstyle' , '1'));
                        ?>
                        <span><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_TAG_STYLE_NOTICE'); ?></span>
                    </div>
                </div>

            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CUSTOM_FIELDS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CUSTOM_FIELDS_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELD_DATE_FORMAT'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELD_DATE_FORMAT'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELD_DATE_FORMAT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php
                            $listLength = array();
                            $listLength[] = JHTML::_('select.option', 'l, d F Y' , JText::_('DATE_FORMAT_LC1'));
                            $listLength[] = JHTML::_('select.option', 'l, d F Y H:i', JText::_('DATE_FORMAT_LC2'));
                            $listLength[] = JHTML::_('select.option', 'd F Y', JText::_('DATE_FORMAT_LC3'));
                            echo JHTML::_('select.genericlist', $listLength, 'custom_field_date_format', 'class="form-control"', 'value', 'text', $this->config->get('custom_field_date_format' , JText::_('DATE_FORMAT_LC1')));
                        ?>
                        <span><a href="http://php.net/manual/en/function.date.php" target="_blank"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LAYOUT_CUSTOM_FIELD_DATE_NOTICE'); ?></a></span>
                    </div>
                </div>

            </div>
        </div>        

    </div>
</div>
