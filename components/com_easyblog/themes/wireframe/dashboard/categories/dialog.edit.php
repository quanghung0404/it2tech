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
<dialog>
    <width>650</width>
    <height>450</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function()
        {
            this.parent.close();
        },
        "{submitButton} click" : function()
        {
            this.form().submit();
        }
    }
    </bindings>
    <title>
        <?php if ($category->id) { ?>
            <?php echo JText::_('COM_EASYBLOG_DIALOG_EDIT_CATEGORY_TITLE'); ?>
        <?php } else { ?>
            <?php echo JText::_('COM_EASYBLOG_DIALOG_CREATE_CATEGORY_TITLE'); ?>
        <?php } ?>
    </title>
    <content>

        <form method="post" action="<?php echo JRoute::_('index.php');?>" class="form-horizontal mt-20" enctype="multipart/form-data" data-form-response>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_NAME'); ?></label>
                <div class="col-md-7">
                    <input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->escape($category->title);?>"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_CATEGORY_ALIAS'); ?></label>
                <div class="col-md-7">
                    <input name="alias" id="alias" class="form-control input-sm" maxlength="255" value="<?php echo $this->escape($category->alias);?>" />
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_DESCRIPTION');?></label>
                <div class="col-md-7">
                    <textarea class="form-control" rows="5" name="description"><?php echo $category->description;?></textarea>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PARENT'); ?></label>
                <div class="col-md-5">
                    <?php echo $parents; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PRIVACY'); ?></label>
                <div class="col-md-5">
                    <?php echo JHTML::_('select.genericlist', EB::privacy()->getOptions('category'), 'private', 'size="1" class="form-control input-sm"' , 'value' , 'text', $category->private);?>
                </div>
            </div>

            <?php foreach ($rules as $rule) { ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACL_' . $rule->action . '_TITLE'); ?></label>
                <div class="col-md-5">
                    <select multiple="multiple" name="category_acl_<?php echo $rule->action; ?>[]" class="float-l">
                        <?php foreach($assigned[$rule->id] as $assignedAcl) { ?>
                            <option value="<?php echo $assignedAcl->groupid; ?>" <?php echo ($assignedAcl->status) ? 'selected="selected"' : ''; ?> ><?php echo $assignedAcl->groupname; ?></option>
                        <?php } ?>
                    </select>

                    <?php echo JText::_('COM_EASYBLOG_CATEGORIES_ACL_' . $rule->action . '_DESC'); ?>
                </div>
            </div>
            <?php } ?>


            <?php if ($this->config->get('layout_categoryavatar')) { ?>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_AVATAR'); ?></label>
                <div class="col-md-7">
                    <?php if(! empty($category->avatar)) { ?>
                    <img style="border-style:solid;" src="<?php echo $category->getAvatar(); ?>" width="60" height="60"/><br />
                    <?php } ?>

                    <?php if($this->acl->get('upload_cavatar')){ ?>
                    <input id="file-upload" type="file" name="Filedata" size="33" title="<?php echo JText::_('COM_EASYBLOG_PICK_AN_IMAGE');?>" />
                    <?php } ?>
                </div>
            </div>
            <?php } ?>

            <input type="hidden" name="id" value="<?php echo $category->id;?>" />
            <?php echo $this->html('form.action', 'categories.save'); ?>
        </form>

    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm">
            <?php if ($category->id) { ?>
                <?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?>
            <?php } else { ?>
                <?php echo JText::_('COM_EASYBLOG_CREATE_BUTTON'); ?>
            <?php } ?>
        </button>
    </buttons>
</dialog>
