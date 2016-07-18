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
// Note: Image popup uses classnames from .eb-image-source
?>
<div class="eb-composer-fieldset eb-image-popup-fieldset is-disabled eb-image-source-fieldset" data-eb-image-popup-fieldset data-name="image-popup">
    <label class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_POPUP'); ?></strong>
        <?php echo $this->output('site/composer/fields/checkbox', array(
                'classname' => 'eb-composer-fieldset-toggle eb-image-popup-toggle',
                'attributes' => 'data-eb-image-popup-toggle'
            )); ?>
    </label>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered eb-image-source-field" data-eb-image-popup-field>

            <div class="eb-composer-field eb-image-source-header">
                <div class="eb-image-source-thumbnail" data-eb-image-popup-thumbnail></div>
                <div class="row-table">
                    <div class="col-cell cell-ellipse eb-image-source-info">
                        <div class="row-table">
                            <div class="col-cell cell-ellipse eb-image-source-title" data-eb-image-popup-title></div>
                            <div class="col-cell cell-tight eb-image-source-size" data-eb-image-popup-size></div>
                        </div>
                        <div class="eb-image-source-url" data-eb-image-popup-url></div>
                    </div>
                    <div class="col-cell cell-tight">
                        <button class="btn btn-sm btn-default"
                        data-eb-image-popup-change-button
                        data-eb-mm-browse-button
                        data-eb-mm-start-uri="_cG9zdA--"
                        data-eb-mm-filter="image"
                        ><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE'); ?></button>
                    </div>
                </div>
            </div>

            <div class="eb-composer-field eb-image-variation-field" data-eb-image-popup-variation-field>
                <div class="eb-image-variation-list-container" data-eb-image-popup-variation-list-container></div>
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
                            <button type="button" class="btn btn-sm btn-default" data-eb-image-popup-change-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_CHANGE_IMAGE'); ?></button>
                            <button type="button" class="btn btn-sm btn-primary" data-eb-image-popup-retry-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_RETRY'); ?></button>
                        </span>
                    </span>
                </div>
            </div>

        </div>

        <div class="eb-hint hint-different style-light">
            <div>
                <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_USING_DIFFERENT_IMAGE_FOR_POPUP'); ?></span>
            </div>
        </div>

    </div>
</div>