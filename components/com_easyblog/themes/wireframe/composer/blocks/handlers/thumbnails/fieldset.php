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
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_WIDE'),
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
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_NORMAL'),
        'caption'   => '4:3',
        'value'     => '4:3',
        'padding'   => '75%',
        'classname' => 'ar-photo'
    ),
    array(
        'name'      => JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT_SQUARE'),
        'caption'   => '1:1',
        'value'     => '1:1',
        'padding'   => '100%',
        'classname' => 'ar-square'
    )
);
?>
<div class="eb-composer-fieldset eb-thumbnails-layout-fieldset" data-eb-thumbnails-layout-fieldset data-name="thumbnails-layout">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_LAYOUT'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field">
            <div class="eb-swatch swatch-grid">
                <div class="row">
                    <div class="col-xs-6">
                        <div class="eb-swatch-item active" data-eb-thumbnails-layout-selection data-value="stack">
                            <div class="eb-swatch-preview is-responsive layout-stack">
                                <div>
                                    <b class="col-1">
                                        <s style="height: 30%"></s>
                                        <s style="height: 50%"></s>
                                        <s style="height: 20%"></s>
                                    </b>
                                    <b class="col-2">
                                        <s style="height: 50%"></s>
                                        <s style="height: 30%"></s>
                                        <s style="height: 20%"></s>
                                    </b>
                                    <b class="col-3">
                                        <s style="height: 20%"></s>
                                        <s style="height: 30%"></s>
                                        <s style="height: 50%"></s>
                                    </b>
                                    <b class="col-4">
                                        <s style="height: 30%"></s>
                                        <s style="height: 50%"></s>
                                        <s style="height: 20%"></s>
                                    </b>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_STACK'); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="eb-swatch-item" data-eb-thumbnails-layout-selection data-value="grid">
                            <div class="eb-swatch-preview is-responsive layout-grid">
                                <div>
                                    <b class="col-1"><s></s><s></s><s></s></b>
                                    <b class="col-2"><s></s><s></s><s></s></b>
                                    <b class="col-3"><s></s><s></s><s></s></b>
                                    <b class="col-4"><s></s><s></s><s></s></b>
                                </div>
                            </div>
                            <div class="eb-swatch-label">
                                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_GRID'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="eb-composer-fieldset eb-thumbnails-size-fieldset preset-stack" data-eb-thumbnails-size-fieldset data-name="thumbnails-size">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_THUMBNAIL_SIZE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered">

            <div class="eb-composer-fieldgroup eb-thumbnails-columns-field" data-eb-thumbnails-columns-field data-name="thumbnails-columns">
                <div class="eb-composer-fieldgroup-content">
                    <?php echo $this->output('site/composer/fields/numslider', array(
                            'name'   => 'thumbnails-columns',
                            'type'   => 'thumbnails-columns',
                            'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_COLUMNS'),
                            'toggle' => false,
                            'units'  => array(),
                            'input'  => false
                        )); ?>

                    <div class="eb-thumbnails-ratio-toggle">
                        <div>
                            <button type="button" class="btn btn-default eb-thumbnails-ratio-button" data-eb-thumbnails-ratio-button>
                                <i class="fa fa-lock"></i>
                                <i class="fa fa-unlock-alt"></i>
                                <span class="eb-thumbnails-ratio-label" data-eb-thumbnails-ratio-label>4:3</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-thumbnails-ratio-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
                        </div>
                        <div class="eb-swatch swatch-grid eb-thumbnails-ratio-swatch">
                            <div class="row">
                                <?php foreach ($ratioList as $ratio) { ?>
                                <div class="col-xs-3">
                                    <div class="eb-swatch-item eb-thumbnails-ratio-selection <?php echo $ratio['classname']; ?>" data-eb-thumbnails-ratio-selection data-value="<?php echo $ratio['value']; ?>">
                                        <div class="eb-swatch-preview is-responsive">
                                            <div><div>
                                                <div class="eb-thumbnails-ratio-preview <?php echo $ratio['classname']; ?>" data-eb-thumbnails-ratio-preview>
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
                        <div class="eb-thumbnails-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-thumbnails-ratio-customize-button>
                                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-thumbnails-ratio-cancel-button>
                                <span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-thumbnails-ratio-custom-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('Enter custom aspect ratio'); ?>
                        </div>
                        <input type="text" class="form-control eb-thumbnails-ratio-input" placeholder="16:9 or 1.77" data-eb-thumbnails-ratio-input/>
                        <div class="eb-thumbnails-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-thumbnails-ratio-use-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-thumbnails-ratio-cancel-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="eb-composer-field eb-thumbnails-strategy-field" data-eb-thumbnails-strategy-field>
            <div class="eb-tabs pill-style">
                <div class="eb-tabs-menu eb-pill">
                    <div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
                         data-eb-thumbnails-strategy-menu-item
                         data-strategy="fill">
                        <i class="eb-thumbnails-strategy-icon icon-fill"><b></b></i>
                        <span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FILL'); ?></span>
                    </div>
                    <div class="eb-tabs-menu-item eb-pill-item cell-ellipse"
                         data-eb-thumbnails-strategy-menu-item
                         data-strategy="fit">
                        <i class="eb-thumbnails-strategy-icon icon-fit"><b></b></i>
                        <span><?php echo JText::_('COM_EASYBLOG_IMAGE_RESIZE_TO_FIT'); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

