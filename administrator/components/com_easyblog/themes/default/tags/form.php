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
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel">
                <div class="panel-head">
                    <b><?php echo jText::_('Details'); ?></b>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="title" class="col-md-4">
                            <?php echo JText::_('COM_EASYBLOG_TAG_TITLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TAG_TITLE'); ?>" 
                                data-content="<?php echo JText::_('COM_EASYBLOG_TAG_TITLE_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-8">
                            <input class="form-control" type="text" name="title" value="<?php echo $this->html('string.escape', $tag->title);?>" id="title" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alias" class="col-md-4">
                            <?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>" 
                                data-content="<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-8">
                            <input class="form-control" type="text" name="alias" value="<?php echo $this->html('string.escape', $tag->alias);?>" id="alias" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alias" class="col-md-4">
                            <?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>" 
                                data-content="<?php echo JText::_('COM_EASYBLOG_TAG_PUBLISH_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-8">
                            <?php echo $this->html('grid.boolean', 'published', $tag->published); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="alias" class="col-md-4">
                            <?php echo JText::_('COM_EASYBLOG_DEFAULT_TAG'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_DEFAULT_TAG'); ?>" 
                                data-content="<?php echo JText::_('COM_EASYBLOG_TAG_DEFAULT_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-8">
                            <?php echo $this->html('grid.boolean', 'default', $tag->default); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="id" value="<?php echo $tag->id;?>" />
</form>
