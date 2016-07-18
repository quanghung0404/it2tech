<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
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
                <b><?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_GENERAL');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_GENERAL_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group" data-category-avatar data-hasavatar="<?php echo $category->hasAvatar(); ?>" data-defaultavatar="<?php echo $category->getDefaultAvatar(); ?>">
                    <label class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_AVATAR');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_AVATAR'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_AVATAR_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <?php if ($category->id){ ?>
                        <div class="mb-20">
                            <img src="<?php echo $category->getAvatar();?>" class="es-avatar es-avatar-md es-avatar-border-sm" data-category-avatar-image />
                        </div>
                        <?php } ?>

                        <div>
                            <input type="file" name="avatar" data-uniform data-category-avatar-upload />
                            <span data-category-avatar-remove-wrap <?php if (!$category->hasAvatar()) { ?>style="display: none;"<?php } ?>>
                                <?php echo JText::_('COM_EASYSOCIAL_OR'); ?>
                                <a href="javascript:void(0);" class="btn btn-es-danger btn-sm" data-id="<?php echo $category->id;?>" data-category-avatar-remove-button><i class="fa fa-remove"></i> <?php echo JText::_('COM_EASYSOCIAL_EVENT_FORM_REMOVE_AVATAR'); ?></a>
                            </span>
                        </div>
                    </div>
                </div>

                <?php if (FD::get('multisites')->exists()) { ?>
                <div class="form-group">
                    <label class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_CATEGORIES_FORM_SITE_ID');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_CATEGORIES_FORM_SITE_ID' ) , JText::_('COM_EASYSOCIAL_CATEGORIES_FORM_SITE_ID_DESCRIPTION'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8"><?php echo FD::get('multisites')->getForm('site_id', $category->site_id); ?></div>
                </div>
                <?php } ?>
                
                <div class="form-group">
                    <label for="title" class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_TITLE');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_TITLE'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_TITLE_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <input type="text" name="title" id="title" class="form-control input-sm" value="<?php echo $category->title;?>" placeholder="<?php echo $this->html('string.escape', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_TITLE_PLACEHOLDER'));?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_ALIAS');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_ALIAS'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_ALIAS_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <input type="text" name="alias" id="alias" class="form-control input-sm" value="<?php echo $category->alias;?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_DESCRIPTION');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_DESCRIPTION'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_DESCRIPTION_DESC'), 'bottom'); ?>
                        ></i>
                    </label>
                    <div class="col-md-8">
                        <textarea name="description"
                            id="description"
                            class="form-control input-sm"
                            data-category-description
                        ><?php echo $category->description;?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_PUBLISHING_STATUS');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_PUBLISHING_STATUS'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_PUBLISHING_STATUS_DESC'), 'bottom'); ?>
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
                <b><?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_USER_ACCESS');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_USER_ACCESS_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label class="col-md-4">
                        <?php echo JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_SELECT_PROFILES');?>
                        <i class="fa fa-question-circle pull-right"
                            <?php echo $this->html('bootstrap.popover', JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_SELECT_PROFILES'), JText::_('COM_EASYSOCIAL_EVENT_CATEGORY_FORM_SELECT_PROFILES_DESC'), 'bottom'); ?>
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
