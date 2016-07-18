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
                    <b><?php echo JText::_('COM_EASYBLOG_GENERAL'); ?></b>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_META_TYPE_TITLE'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_META_TYPE_TITLE'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_MAILCHIMP_APIKEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <strong><?php echo $meta->getTitle(); ?></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_META_TAG_ALLOW_INDEXING'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_META_TAG_ALLOW_INDEXING'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_META_TAG_ALLOW_INDEXING_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <?php echo $this->html('grid.boolean', 'indexing', $meta->indexing); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_META_TAG_EDIT_KEYWORDS'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_META_TAG_EDIT_KEYWORDS'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_META_KEYWORD_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <textarea id="keywords" name="keywords" class="form-control"><?php echo $meta->keywords; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="page_title" class="col-md-5">
                            <?php echo JText::_('COM_EASYBLOG_META_TAG_EDIT_DESCRIPTION'); ?>

                            <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_META_TAG_EDIT_DESCRIPTION'); ?>"
                                data-content="<?php echo JText::_('COM_EASYBLOG_META_DESC_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                        </label>

                        <div class="col-md-7">
                            <textarea id="description" name="description" class="form-control"><?php echo $meta->description; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $meta->id;?>" />
    <?php echo $this->html('form.action', 'meta.save'); ?>
</form>