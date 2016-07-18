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
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_GENERAL'); ?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_GENERAL_INFO');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_PRIVACY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PRIVACY'); ?>" 
                            data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_PRIVACY_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <?php echo JHTML::_('select.genericlist' , EB::privacy()->getOptions('category') , 'private' , 'class="form-control"' , 'value' , 'text', $category->private);?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-lg-6">
        <div class="panel<?php echo $category->private != 2 ? ' hide' : '';?>" data-category-access>
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ASSIGNED_PERMISSIONS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_CATEGORIES_ASSIGNED_PERMISSIONS_INFO');?></div class="panel-info">
            </div>

            <div class="panel-body">
                <?php foreach ($rules as $rule) { ?>
                <div class="form-group">
                    <label for="page_title" class="col-md-4">
                        <?php echo $rule->title;?>

                        <i data-html="true" data-placement="top" data-title="<?php echo $rule->title;?>" 
                            data-content="<?php echo $rule->desc;?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-8">
                        <select multiple="multiple" name="category_acl_<?php echo $rule->action; ?>[]" class="form-control" style="height: 150px;">
                            <?php foreach ($groups[$rule->id] as $group) { ?>
                            <option value="<?php echo $group->groupid; ?>" style="padding:2px;" <?php echo ($group->status) ? 'selected="selected"' : ''; ?> ><?php echo $group->groupname; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>
</div>