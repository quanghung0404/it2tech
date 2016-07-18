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
<div class="eb-composer-fieldset eb-image-size-fieldset preset-simple" data-eb-image-size-fieldset data-name="image-size">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SIZE'); ?></strong>
        <span class="eb-composer-fieldset-dropdown">
            <span class="dropdown-toggle_" data-bp-toggle="dropdown" data-eb-image-preset-toggle>
                <span class="eb-image-size-current-preset" data-eb-image-size-current-preset><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SIZE_SIMPLE'); ?></span>
                <span class="caret"></span>
            </span>
            <ul class="dropdown-menu">
                <li class="active" data-eb-image-size-preset data-type="simple">
                    <a><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SIZE_SIMPLE'); ?></a>
                </li>
                <li data-eb-image-size-preset data-type="advanced">
                    <a><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_SIZE_ADVANCED'); ?></a>
                </li>
            </ul>
        </span>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field eb-image-size-simple-field style-bordered" data-eb-image-size-simple-field>
            <?php echo $this->output('site/composer/blocks/handlers/image/fieldsets/image_size/dimension'); ?>
            <?php echo $this->output('site/composer/blocks/handlers/image/fieldsets/image_size/ratio'); ?>
            <?php echo $this->output('site/composer/blocks/handlers/image/fieldsets/image_size/alignment'); ?>

            <div class="eb-hint hint-loading layout-overlay style-gray size-sm">
                <div>
                    <i class="eb-hint-icon"><span class="eb-loader-o size-sm"></span></i>
                    <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_IMAGE_SIZES'); ?></span>
                </div>
            </div>

            <div class="eb-hint hint-failed layout-overlay style-gray size-sm">
                <div>
                    <i class="eb-hint-icon fa fa-warning"></i>
                    <span class="eb-hint-text">
                        <?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_LOADING_IMAGE_SIZES_ERROR'); ?>
                        <span class="eb-image-source-failed-action">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-image-size-retry-button><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_RETRY'); ?></button>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <?php echo $this->output('site/composer/blocks/handlers/image/fieldsets/image_size/advanced'); ?>

    </div>
</div>

