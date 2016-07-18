<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_GENERAL');?></b>
                <div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_INFO'); ?></div>
            </div>

            <div class="panel-body">
                <?php echo $this->html('settings.toggle', 'amazon_enable', 'COM_EASYBLOG_SETTINGS_STORAGE_S3_ENABLE'); ?>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_ACCESS_KEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_ACCESS_KEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_ACCESS_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="amazon_key" class="form-control " value="<?php echo $this->config->get('amazon_key');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_SECRET_KEY'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_SECRET_KEY'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_SECRET_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                            <input type="text" name="amazon_secret" class="form-control " value="<?php echo $this->config->get('amazon_secret');?>" size="5" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="page_title" class="col-md-5">
                        <?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_BUCKET'); ?>

                        <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_BUCKET'); ?>"
                            data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_BUCKET_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
                    </label>

                    <div class="col-md-7">
                        <?php if ($buckets) { ?>
                            <select name="amazon_bucket" class="form-control" data-amazon-bucket>
                                <option value=""><?php echo JText::_('COM_EASYBLOG_PLEASE_SELECT_BUCKET');?>
                                <?php foreach ($buckets as $bucket) { ?>
                                <option value="<?php echo $bucket->title;?>" data-region="<?php echo $bucket->location;?>"<?php echo $this->config->get('amazon_bucket') == $bucket->title ? ' selected="selected"' : '';?>>
                                    <?php echo $bucket->title;?> (<?php echo $bucket->locationTitle;?>)
                                </option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <p style="border: 1px dashed #d7d7d7;padding: 5px;" class="muted"><?php echo JText::_('COM_EASYBLOG_SETTINGS_STORAGE_S3_SAVE_FIRST'); ?>
                        <?php } ?>

                        <input type="hidden" name="amazon_region" value="" data-amazon-region />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
