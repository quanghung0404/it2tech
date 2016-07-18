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
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_TITLE'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ENABLE_RATINGS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_ratings', $this->config->get('main_ratings'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCKED_ON_FRONTPAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_ratings_frontpage_locked', $this->config->get('main_ratings_frontpage_locked'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_ALLOW_GUEST_RATING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_ratings_guests', $this->config->get('main_ratings_guests'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_ALLOW_AUTHOR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_ALLOW_AUTHOR'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_RATINGS_ALLOW_AUTHOR_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_ratings_allow_author', $this->config->get('main_ratings_allow_author'));?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_DISPLAY_PEOPLE_RATED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'main_ratings_display_raters', $this->config->get('main_ratings_display_raters'));?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-6">
    </div>
</div>