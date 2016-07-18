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
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_STRIPED'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_STRIPED'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_STRIPED_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'striped', $data->striped, 'striped', 'data-table-striped'); ?>
            </div>
        </div>
        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_BORDERED'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_BORDERED'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_BORDERED_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'bordered', $data->bordered, 'bordered', 'data-table-bordered'); ?>
            </div>
        </div>
        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_HOVER'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_HOVER'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_HOVER_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'hover', $data->hover, 'hover', 'data-table-hover'); ?>
            </div>
        </div>
        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_CONDENSED'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_CONDENSED'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_STYLE_CONDENSED_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'condensed', $data->condensed, 'condensed', 'data-table-condensed'); ?>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_ROWS_COLUMNS'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_ROWS'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_ROWS'); ?>" 
                    data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_ROWS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <div class="input-group">
                    <input type="text" name="rows" id="rows" value="<?php echo $data->rows; ?>" class="form-control text-center" 
                    data-table-rows />
                    <span class="input-group-btn">
                        <a href="javascript:void(0);" class="btn btn-default" data-table-rows-remove><i class="fa fa-minus"></i></a>
                        <a href="javascript:void(0);" class="btn btn-default" data-table-rows-add><i class="fa fa-plus"></i></a>
                    </span>
                </div>
            </div>
        </div>

        <div class="eb-composer-field row">
            <label class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_COLUMNS'); ?>
                <i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_COLUMNS'); ?>" data-content="<?php echo JText::_('COM_EASYBLOG_BLOCKS_TABLE_COLUMNS_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <div class="input-group">
                    <input type="text" name="columns" id="columns" value="<?php echo $data->columns; ?>" class="form-control text-center" data-table-columns />
                    <span class="input-group-btn">
                        <a href="javascript:void(0);" class="btn btn-default" data-table-columns-remove><i class="fa fa-minus"></i></a>
                        <a href="javascript:void(0);" class="btn btn-default" data-table-columns-add><i class="fa fa-plus"></i></a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
