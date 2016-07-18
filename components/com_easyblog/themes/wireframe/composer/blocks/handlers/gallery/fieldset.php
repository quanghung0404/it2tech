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

$ratioList = array(
    array(
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_WIDE'),
        'caption'   => '16:9',
        'value'     => '16:9',
        'padding'   => '56.25%',
        'classname' => 'ar-wide'
    ),
    array(
        'name'      => JText::_('35mm'),
        'caption'   => '3:2',
        'value'     => '3:2',
        'padding'   => '66.666667%',
        'classname' => 'ar-35mm'
    ),
    array(
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_NORMAL'),
        'caption'   => '4:3',
        'value'     => '4:3',
        'padding'   => '75%',
        'classname' => 'ar-photo'
    ),
    array(
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_RATIO_SQUARE'),
        'caption'   => '1:1',
        'value'     => '1:1',
        'padding'   => '100%',
        'classname' => 'ar-square'
    )
);
?>
<div class="eb-composer-fieldset eb-gallery-size-fieldset" data-eb-gallery-size-fieldset data-name="gallery-size">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_SIZE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered">

            <div class="eb-composer-fieldgroup eb-gallery-ratio-info-field" data-eb-gallery-ratio-info-field data-name="gallery-ratio-info">
                <div class="eb-composer-fieldgroup-content">
                    <div class="row-table eb-composer-fieldrow">
                        <div class="col-cell cell-tight eb-composer-fieldrow-label"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_ASPECT_RATIO'); ?></div>
                        <div class="col-cell cell-ellipse eb-gallery-ratio-label" data-eb-gallery-ratio-label>16:9</div>
                        <div class="col-cell cell-tight"><button type="button" class="btn btn-sm btn-default" data-eb-gallery-ratio-button><?php echo JText::_('COM_EASYBLOG_CHANGE_BUTTON'); ?></button></div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-gallery-ratio-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
                        </div>
                        <div class="eb-swatch swatch-grid eb-gallery-ratio-swatch">
                            <div class="row">
                                <?php foreach ($ratioList as $ratio) { ?>
                                <div class="col-xs-3">
                                    <div class="eb-swatch-item eb-gallery-ratio-selection <?php echo $ratio['classname']; ?>" data-eb-gallery-ratio-selection data-value="<?php echo $ratio['value']; ?>">
                                        <div class="eb-swatch-preview is-responsive">
                                            <div><div>
                                                <div class="eb-gallery-ratio-preview <?php echo $ratio['classname']; ?>" data-eb-gallery-ratio-preview>
                                                    <div style="padding-top: <?php echo $ratio['padding']; ?>">
                                                        <div><span><?php echo $ratio['caption']; ?></span></div>
                                                    </div>
                                                </div>
                                            </div></div>
                                        </div>
                                        <div class="eb-swatch-label">
                                            <span><?php echo $ratio['name']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="eb-gallery-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-gallery-ratio-customize-button>
                                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-gallery-ratio-cancel-button>
                                <span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
        </div>

            <div class="eb-composer-fieldgroup eb-gallery-ratio-custom-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('COM_EASYBLOG_USE_CUSTOM_ASPECT_RATIO'); ?>
                        </div>
                        <input type="text" class="form-control eb-gallery-ratio-input" placeholder="16:9 or 1.77" data-eb-gallery-ratio-input/>
                        <div class="eb-gallery-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-gallery-ratio-use-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-gallery-ratio-cancel-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="eb-composer-field eb-gallery-strategy-field" data-eb-gallery-strategy-field>
            <div class="eb-tabs pill-style">
                <div class="eb-tabs-menu eb-pill">
                    <div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
                         data-eb-gallery-strategy-menu-item
                         data-strategy="fill">
                        <i class="eb-gallery-strategy-icon icon-fill"><b></b></i>
                        <span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FILL'); ?></span>
                    </div>
                    <div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
                         data-eb-gallery-strategy-menu-item
                         data-strategy="fit">
                        <i class="eb-gallery-strategy-icon icon-fit"><b></b></i>
                        <span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FIT'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset eb-gallery-items-fieldset is-empty" data-eb-gallery-items-fieldset data-name="gallery-items">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_ITEMS'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered eb-gallery-items-field">
            <div class="eb-composer-field eb-list eb-gallery-list" data-type="list" data-eb-gallery-list>
                <div class="eb-list-item-group eb-gallery-list-item-group" data-eb-gallery-list-item-group>
                </div>
                <div class="eb-gallery-hints">
                    <div class="eb-hint hint-empty layout-overlay style-gray size-sm">
                        <div>
                            <span class="eb-hint-text">
                                <?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_EMPTY'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="eb-gallery-list-actions">
                <div class="row-table">
                    <div class="col-cell">
                        <button type="button" class="btn btn-sm btn-default" data-eb-gallery-list-item-delete-button><?php echo JText::_('COM_EASYBLOG_DELETE_BUTTON'); ?></button>
                    </div>
                    <div class="col-cell">
                        <button class="btn btn-sm btn-default" data-eb-gallery-list-item-primary-button><?php echo JText::_('COM_EASYBLOG_SET_AS_PRIMARY_BUTTON'); ?></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>