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
<form id="adminForm" name="adminForm" method="post" action="index.php">
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_DETAILS');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="title" value="<?php echo $this->html('string.escape', $group->getTitle());?>" />
                        <span class="small hide" style="color:red;" data-title-error><?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_TITLE_EMPTY_WARNING');?></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_PUBLISH_STATE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_PUBLISH_STATE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_PUBLISH_STATE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'state', $group->state); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_PERMISSIONS');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_PERMISSIONS_DESC');?></div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_VIEW_ITEMS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_VIEW_ITEMS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_VIEW_ITEMS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('tree.groups', 'read', $group->read); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_USE_ITEMS'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_USE_ITEMS'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_GROUP_USE_ITEMS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('tree.groups', 'write', $group->write); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="id" value="<?php echo $group->id;?>" />
<?php echo $this->html('form.action');?>
</form>
