<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYSOCIAL_SETTINGS_VIDEOS_CATEGORY_GENERAL_SETTINGS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYSOCIAL_SETTINGS_VIDEOS_CATEGORY_GENERAL_SETTINGS_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="title" class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_SETTINGS_TITLE');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_SETTINGS_TITLE'), JText::_('COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_TITLE_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php echo $this->html('form.text', 'title', 'title', $category->title, array('placeholder' => 'COM_EASYSOCIAL_VIDEOS_CATEGORY_FORM_TITLE_PLACEHOLDER', 'attr' => 'data-category-title')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-4">
                        <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_ALIAS' );?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_ALIAS' ) , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_ALIAS_DESC' ) , 'bottom' ); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php echo $this->html('form.text', 'alias', 'alias', $category->alias, array('placeholder' => 'COM_EASYSOCIAL_VIDEOS_CATEGORY_FORM_ALIAS_PLACEHOLDER', 'attr' => 'data-category-alias')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="col-md-4">
                        <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_DESCRIPTION' );?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_DESCRIPTION' ) , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_DESCRIPTION_DESC' ) , 'bottom' ); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php echo $this->html('form.textarea', 'description', 'description', $category->description, array('placeholder' => 'COM_EASYSOCIAL_VIDEOS_CATEGORY_FORM_DESCRIPTION_PLACEHOLDER')); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">
                        <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_PUBLISHING_STATUS' );?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html( 'bootstrap.popover' , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_PUBLISHING_STATUS' ) , JText::_( 'COM_EASYSOCIAL_GROUPS_CATEGORY_FORM_PUBLISHING_STATUS_DESC' ) , 'bottom' ); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'state', $category->state, 'state'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYSOCIAL_VIDEO_CATEGORY_FORM_USER_ACCESS');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_VIDEO_CATEGORY_FORM_USER_ACCESS_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_FORM_ALLOWED_PROFILE_TYPES');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_FORM_ALLOWED_PROFILE_TYPES'), JText::_('COM_EASYSOCIAL_FORM_ALLOWED_PROFILE_TYPES_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php echo $this->html('form.profiles', 'create_access[]', 'create_access', $createAccess, array('multiple' => true, 'style="height:150px;"')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
