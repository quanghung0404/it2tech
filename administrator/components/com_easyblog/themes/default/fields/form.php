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
                <b><?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_DETAILS');?></b>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_GROUP'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_GROUP'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_GROUP_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="group_id" class="form-control">
                            <option value=""><?php echo JText::_('COM_EASYBLOG_FIELDS_SELECT_FIELD_GROUP');?></option>
                            <?php foreach ($groups as $group) { ?>
                                <option value="<?php echo $group->id;?>"<?php echo $field->group_id == $group->id ? ' selected="selected"' : '';?>><?php echo JText::_($group->title);?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TYPE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TYPE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <select name="type" class="form-control" data-field-type>
                            <option value=""><?php echo JText::_('COM_EASYBLOG_FIELDS_SELECT_FIELD_TYPE');?></option>
                            <?php foreach ($fields as $fieldItem) { ?>
                                <option value="<?php echo $fieldItem->getElement();?>"<?php echo $field->type == $fieldItem->getElement() ? ' selected="selected"' : '';?>><?php echo $fieldItem->getTitle();?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TITLE'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TITLE'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <input type="text" class="form-control" name="title" value="<?php echo $this->html('string.escape', $field->title);?>" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_HELP'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_HELP'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_TITLE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <textarea name="help" class="form-control"><?php echo $this->html('string.escape', $field->help);?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_PUBLISHED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_PUBLISHED'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_PUBLISHED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'state', is_null($field->state) ? true : $field->state); ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_REQUIRED'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_REQUIRED'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_REQUIRED_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php echo $this->html('grid.boolean', 'required', is_null($field->required) ? false : $field->required); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_PROPERTIES');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_FIELDS_FIELD_PROPERTIES_DESC');?></div class="panel-info">
            </div>

            <div class="panel-body">
                <div data-field-form>
                    <?php echo $form;?>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="id" value="<?php echo $field->id;?>" />
<?php echo $this->html('form.action');?>
</form>
