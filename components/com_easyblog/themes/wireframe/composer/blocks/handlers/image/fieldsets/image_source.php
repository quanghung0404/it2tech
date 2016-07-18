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
<div class="eb-composer-fieldset eb-image-source-fieldset" data-eb-image-source-fieldset data-name="image-source">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SOURCE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered eb-image-source-field" data-eb-image-source-field>
            <div class="eb-composer-field eb-image-source-header">
                <div class="eb-image-source-thumbnail" data-eb-image-source-thumbnail></div>
                <div class="row-table">
                    <div class="col-cell cell-ellipse eb-image-source-info">
                        <div class="row-table">
                            <div class="col-cell cell-ellipse eb-image-source-title" data-eb-image-source-title></div>
                            <div class="col-cell cell-tight eb-image-source-size" data-eb-image-source-size></div>
                        </div>
                        <div class="eb-image-source-url" data-eb-image-source-url></div>
                    </div>
                    <div class="col-cell cell-tight">
                        <button class="btn btn-sm btn-default eb-image-source-change-button"
                            data-eb-image-source-change-button
                            data-eb-mm-browse-button
                            data-eb-mm-start-uri="_cG9zdA--"
                            data-eb-mm-filter="image"
                        ><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE'); ?></button>
                    </div>
                </div>
            </div>

            <div class="eb-composer-field eb-image-variation-field can-create can-delete" data-eb-image-variation-field>
                <div class="eb-image-variation-list-container" data-eb-image-variation-list-container></div>
                <div class="eb-image-variation-create-container eb-composer-field">
                    <div class="eb-composer-fieldrow-group">
                        <div class="row-table eb-composer-fieldrow">
                            <div class="col-cell eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME'); ?></div>
                            <div class="col-cell"><input type="text" class="form-control input-sm eb-image-variation-name" data-eb-image-variation-name placeholder="<?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_NAME_PLACEHOLDER'); ?>"></div>
                        </div>
                    </div>
                    <div class="eb-composer-fieldrow-group eb-image-variation-size-field">
                        <div class="row-table eb-composer-fieldrow">
                            <div class="col-cell eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_WIDTH'); ?></div>
                            <div class="col-cell"><input type="text" class="form-control input-sm eb-image-variation-width" data-eb-image-variation-width></div>
                        </div>
                        <div class="row-table eb-composer-fieldrow">
                            <div class="col-cell eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_HEIGHT'); ?></div>
                            <div class="col-cell"><input type="text" class="form-control input-sm eb-image-variation-height" data-eb-image-variation-height></div>
                        </div>

                    </div>
                </div>
                <div class="eb-image-variation-action">
                    <button type="button" class="eb-image-variation-new-button btn btn-sm btn-primary" data-eb-image-variation-new-button><i class="fa fa-plus-circle"></i> <?php echo JText::_('COM_EASYBLOG_MM_NEW_SIZE'); ?></button>
                    <button type="button" class="eb-image-variation-rebuild-button btn btn-sm btn-default" data-eb-image-variation-rebuild-button><i class="fa fa-undo"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_REBUILD'); ?></button>
                    <button type="button" class="eb-image-variation-delete-button btn btn-sm btn-default" data-eb-image-variation-delete-button><i class="fa fa-trash"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_DELETE'); ?></button>

                    <button type="button" class="eb-image-variation-cancel-button btn btn-sm btn-default" data-eb-image-variation-cancel-button><i class="fa fa-close"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?></button>
                    <button type="button" class="eb-image-variation-create-button btn btn-sm btn-primary" data-eb-image-variation-create-button><i class="fa fa-check"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATE'); ?></button>
                </div>

                <div class="eb-hint hint-creating-variation layout-overlay style-gray size-sm">
                    <div>
                        <i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
                        <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE'); ?></span>
                    </div>
                </div>

                <div class="eb-hint hint-failed-variation layout-overlay style-gray size-sm">
                    <div>
                        <i class="eb-hint-icon fa fa-warning"></i>
                        <span class="eb-hint-text">
                            <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CREATING_IMAGE_SIZE_ERROR'); ?>
                            <span class="eb-image-source-failed-action">
                                <button type="button" class="btn btn-sm btn-primary" data-eb-image-variation-cancel-failed-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CANCEL'); ?></button>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="eb-hint hint-loading layout-overlay style-gray size-sm">
                <div>
                    <i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
                    <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS'); ?></span>
                </div>
            </div>

            <div class="eb-hint hint-failed layout-overlay style-gray size-sm">
                <div>
                    <i class="eb-hint-icon fa fa-warning"></i>
                    <span class="eb-hint-text">
                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_VARIATIONS_ERROR'); ?>
                        <span class="eb-image-source-failed-action">
                            <button type="button" class="btn btn-sm btn-default" data-eb-image-source-change-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE_IMAGE'); ?></button>
                            <button type="button" class="btn btn-sm btn-primary" data-eb-image-source-retry-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_RETRY'); ?></button>
                        </span>
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
