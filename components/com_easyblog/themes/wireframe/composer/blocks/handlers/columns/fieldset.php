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
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_TABS_COLUMNS'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
    	<div class="eb-composer-field">
            <div class="eb-composer-manage-tabs" data-columns-control
                data-listbox
                data-listbox-sortable="false"
                data-listbox-toggleDefault="false"
                data-listbox-allowAdd="1"
                data-listbox-allowRemove="1"
                data-listbox-itemTitle="<?php echo JText::_('COM_EASYBLOG_BLOCKS_COLUMNS_SELECT_COLUMNS');?>"
                data-listbox-max="6"
                data-listbox-min="2"
            >
                <div class="eb-composer-manage-tab row-table" data-listbox-item>
                    <div class="col-cell eb-composer-manage-tab-name" data-listbox-item-content>
                    </div>
                    <div class="col-cell eb-composer-manage-tab-remove" data-listbox-button-remove>&times;</div>
                </div>

                <div class="eb-composer-manage-tab row-table" data-listbox-item>
                    <div class="col-cell eb-composer-manage-tab-name" data-listbox-item-content>
                    </div>
                    <div class="col-cell eb-composer-manage-tab-remove" data-listbox-button-remove>&times;</div>
                </div>

                <div class="eb-composer-manage-tab row-table is-add" data-listbox-button-add>
                    <div class="col-cell eb-composer-manage-tab-add">&plus;</div>
                    <div class="col-cell eb-composer-manage-tab-name"><?php echo JText::_('COM_EASYBLOG_GRID_LISTBOX_ADD_NEW_ITEM_TITLE'); ?></div>
                </div>

                <div data-listbox-custom-html style="display:none;">
                    <div class="col-cell no-wrap" style="padding-right: 10px;"><?php echo JText::_('COM_EASYBLOG_BLOCKS_COLUMNS_SIZE'); ?></div>
                    <div class="col-cell">
                        <select data-select-width>
                            <option><?php echo JText::_('COM_EASYBLOG_BLOCKS_COLUMNS_SELECT_SIZE'); ?></option>

                            <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option value="<?php echo $i;?>" <?php echo $i == 6 ? ' selected="selected"' : '';?>><?php echo $i;?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

