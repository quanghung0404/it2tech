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
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_AUTHOR_DETAILS');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_AUTHOR_DETAILS_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NAME'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <input class="form-control" type="text" id="name" name="name" value="<?php echo $this->html('string.escape', $user->get('name'));?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USERNAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USERNAME'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USERNAME_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <input class="form-control" type="text" name="username" value="<?php echo $user->get('username');?>" id="username" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_EMAIL'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_EMAIL'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_EMAIL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="email" name="email" value="<?php echo $user->get('email');?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NEW_PASSWORD'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NEW_PASSWORD'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NEW_PASSWORD_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <input id="password" name="password" class="form-control" type="password" value="<?php echo isset( $this->post['password'] ) ?  $this->post['password'] : '' ;?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_VERIFY_PASSWORD'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_VERIFY_PASSWORD'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_VERIFY_PASSWORD_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8">
                                <input id="password2" name="password2" class="form-control" type="password" value="" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USER_GROUP'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USER_GROUP'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_USER_GROUP_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('tree.groups', 'gid', $user->groups); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOCK_USER'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOCK_USER'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BLOCK_USER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'block', $user->block); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_RECEIVE_SYSTEM_EMAILS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_RECEIVE_SYSTEM_EMAILS'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_RECEIVE_SYSTEM_EMAILS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'sendEmail', $user->get('sendEmail')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_REGISTER_DATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_REGISTER_DATE'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_REGISTER_DATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('string.date', $user->get('registerDate')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_LAST_VISIT_DATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_LAST_VISIT_DATE'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_LAST_VISIT_DATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo ($user->get('lastvisitDate') == "0000-00-00 00:00:00") ? JText::_('NEVER') : $this->html('string.date', $user->get('lastvisitDate')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_BASIC_SETTINGS');?></b>
                <p class="panel-info"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_FORM_BASIC_SETTINGS_INFO');?></p>
            </div>
            
            <div class="panel-body">
                <?php foreach ($form->getFieldset('settings') as $field) { ?>
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo $field->label; ?>
                    </label>

                    <div class="col-md-8">
                        <?php echo $field->input;?>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>