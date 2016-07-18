<?php
/**
* @package      EasyBlog
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
<form action="index.php" method="post" name="adminForm" autocomplete="off" id="adminForm">
    <div class="app-tabs">
        <ul class="app-tabs-list list-unstyled">
            <?php $i = 0; ?>
            <?php foreach ($groups as $group) { ?>
            <li class="tabItem<?php echo $i == 0 ? ' active' : '';?>">
                <a data-bp-toggle="tab" href="#<?php echo str_ireplace(array('.','-',' '), '_', $group);?>" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_ACL_GROUP_' . strtoupper($group));?>
                </a>
            </li>
            <?php $i++; ?>
            <?php } ?>

            <li class="tabItem">
                <a data-bp-toggle="tab" href="#textfilters" data-form-tabs>
                    <?php echo JText::_('COM_EASYBLOG_ACL_TEXT_FILTERS');?>
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <?php $i = 0; ?>
        <?php foreach ($groups as $group) { ?>
        <div id="<?php echo str_ireplace(array('.','-',' '), '_', $group);?>" class="tab-pane<?php echo $i == 0 ? ' active in' : '';?>">
            <div class="row">
                <div class="col-lg-10">
                    <div class="panel">
                        <div class="panel-head">
                            <b><?php echo JText::_('COM_EASYBLOG_ACL_GROUP_' . strtoupper($group)); ?></b>
                            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_ACL_GROUP_' . strtoupper($group) . '_DESC');?></div>
                        </div>

                        <div class="panel-body">
                            <?php if (isset($ruleset->rules[$group]) && is_array($ruleset->rules[$group])) { ?>
                                <?php foreach ($ruleset->rules[$group] as $key => $value) { ?>
                                    <div class="form-group">
                                        <label class="col-md-4">
                                            <?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key)); ?>
                                            &nbsp;
                                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key)); ?>"
                                                data-content="<?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key) . '_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                                        </label>

                                        <div class="col-md-8 acl-<?php echo $value ? 'yes' : 'no';?>">
                                            <?php echo $this->html('grid.boolean', $key, $value); ?>

                                            <span class="hidden acl-result-yes text-success"><i class="fa fa-check-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key) . '_RESULT_YES'); ?></span>
                                            <span class="hidden acl-result-no text-danger"><i class="fa fa-times-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_ACL_OPTION_' . strtoupper($key) . '_RESULT_NO'); ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $i++;?>
        <?php } ?>

        <div id="textfilters" class="tab-pane">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel">
                        <div class="panel-head">
                            <b><?php echo JText::_('COM_EASYBLOG_ACL_TEXT_FILTERS');?></b>
                            <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_ACL_TEXT_FILTERS_INFO');?></div>
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-md-5">
                                    <?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_TAGS' ); ?>
                                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_DISALLOWED_HTML_TAGS'); ?>"
                                        data-content="<?php echo JText::_('COM_EASYBLOG_DISALLOWED_HTML_TAGS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                                </label>
                                <div class="col-md-7">
                                    <textarea id="disallow-tags" name="disallow_tags" class="form-control"><?php echo $filter->disallow_tags;?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5">
                                    <?php echo JText::_( 'COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES' ); ?>
                                    <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES'); ?>"
                                    data-content="<?php echo JText::_('COM_EASYBLOG_DISALLOWED_HTML_ATTRIBUTES_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                                </label>
                                <div class="col-md-7">
                                    <textarea id="disallow-attributes" name="disallow_attributes" class="form-control"><?php echo $filter->disallow_attributes;?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_easyblog" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="id" id="id" value="<?php echo !empty($ruleset->id)? $ruleset->id : ''; ?>" />
    <input type="hidden" name="name" value="<?php echo !empty($ruleset->name)? $ruleset->name : ''; ?>" />
    <input type="hidden" name="add" value="<?php echo $add; ?>" />
</form>
