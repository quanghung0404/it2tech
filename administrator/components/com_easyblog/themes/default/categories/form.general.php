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
    <div class="col-lg-7">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_GENERAL'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DETAILS');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_NAME'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_NAME'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_TITLE_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input class="form-control" id="catname" name="title" size="55" maxlength="255" value="<?php echo $category->title;?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_ALIAS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_ALIAS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_ALIAS_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input class="form-control" id="alias" name="alias" size="55" maxlength="255" value="<?php echo $category->alias;?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORY_PARENT_CATEGORY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORY_PARENT_CATEGORY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_PARENT_CATEGORY_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $parentList; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_LANGUAGE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_LANGUAGE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_LANGUAGE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if ($this->config->get('main_multi_language')) { ?>
                        <select name="language" class="form-control">
                            <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
                            <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' , $category->language);?>
                        </select>
                        <?php } ?>
                    </div>
                </div>


                <?php if ($this->config->get('layout_categoryavatar', true)){ ?>
                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_AVATAR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_AVATAR'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_AVATAR_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php if(! empty($category->avatar)) { ?>
                            <img class="img-rounded" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/>
                            <br /><br />
                        <?php }?>

                        <?php if ($this->acl->get('upload_cavatar')) {?>
                            <input id="file-upload" type="file" name="Filedata" size="33"/>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_PUBLISHED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_PUBLISHED'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_PUBLISH_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'published', $category->published); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_AUTOPOST'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_AUTOPOST'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_AUTOPOST_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo $this->html('grid.boolean', 'autopost', $category->autopost); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_AUTHOR'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_AUTHOR'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_AUTHOR_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <input type="hidden" name="created_by" id="created_by" value="<?php echo $category->get( 'created_by' );?>" />
                        <span id="author-name" class="bubble-item"<?php if( empty($category->created_by)){ ?> style="display:none;"<?php } ?>>
                            <?php if(!empty($category->created_by)) { ?>
                                <?php echo JFactory::getUser( $category->get( 'created_by') )->name; ?>
                            <?php } ?>
                        </span>
                        <a class="btn btn-default btn-sm" href="javascript:void(0);" data-browse-user>
                            <i class="fa fa-user"></i> <?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DESCRIPTION'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_DESCRIPTION'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_DESC_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-12">
                        <?php echo $editor->display('description', $category->get( 'description') , '99%', '200', '10', '10', array('image', 'readmore', 'pagebreak'), array(), 'com_easyblog'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS_DESC');?></div>
            </div>

            <div class="panel-body">
                <div>
                    <span class="label label-danger"><?php echo JText::_('COM_EASYBLOG_NOTE');?></span> <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOMFIELDS_PERMISSIONS_DESC');?>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_SELECT_FIELD_GROUP');?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_SELECT_FIELD_GROUP'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_SELECT_FIELD_GROUP_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <select name="field_group" class="form-control">
                            <option value=""><?php echo JText::_('COM_EASYBLOG_CATEGORIES_NO_CUSTOM_FIELDS');?></option>
                            <?php foreach ($fieldGroups as $fieldGroup) { ?>
                            <option value="<?php echo $fieldGroup->id;?>"<?php echo $category->getCustomFieldGroup()->group_id == $fieldGroup->id ? ' selected="selected"' : '';?>><?php echo JText::_($fieldGroup->title);?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOM_TEMPLATE'); ?></b>
                <div class="panel-info"><?php echo JText::sprintf('COM_EASYBLOG_CATEGORIES_EDIT_CUSTOM_TEMPLATE_INFO', $template);?></div>
            </div>

            <div class="panel-body">

                <div class="form-group">
                    <?php echo JText::sprintf('COM_EASYBLOG_CATEGORIES_TEMPLATE_INFO', $template);?>
                </div>

                <div class="form-group">
                    <label for="catname" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">

                        <?php if ($themes) { ?>
                        <select name="theme" class="form-control">
                            <option value=""<?php echo !$category->theme ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_SELECT_CUSTOM_TEMPLATE_DEFAULT'); ?></option>
                            <?php foreach ($themes as $theme) { ?>
                            <option value="<?php echo $theme;?>"<?php echo $category->theme == $theme ? ' selected="selected"' : '';?>><?php echo ucfirst($theme);?></option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                            <span class="text-warning"><?php echo JText::_('COM_EASYBLOG_SELECT_CUSTOM_TEMPLATE_EMPTY');?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
