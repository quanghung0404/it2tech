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
<div class="eb-composer-placeholder eb-composer-placeholder-image text-center"
    <?php if ($this->acl->get('upload_image')) { ?>
    data-eb-composer-image-placeholder
    data-key="_cG9zdA--"
    data-type="image"
    data-plupload-multi-selection="0"
    <?php } ?>
>

    <div data-plupload-drop-element>
        <i class="eb-composer-placeholder-icon fa fa-camera"></i>

        <?php if ($this->acl->get('upload_image')) { ?>
        <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_DROP_IMAGE_FILE_HERE'); ?></b>
        <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_PLACEHOLDER_DESC'); ?></p>
        <?php } else { ?>
        <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_BROWSE_FOR_IMAGE'); ?></b>
        <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_IMAGE_BROWSE_FOR_IMAGE_DESC'); ?></p>
        <?php } ?>

        <input type="checkbox" id="use-url">

        <div class="eb-composer-place-options">
            <label class="btn btn-sm btn-primary" for="use-url">
                <i class="fa fa-link"></i>
                <?php echo JText::_('Via URL'); ?>
            </label>

            <button type="button" class="btn btn-sm btn-primary"
                data-eb-mm-browse-button
                data-eb-mm-start-uri="_cG9zdA--"
                data-eb-mm-filter="image"
            >
                <i class="fa fa-photo"></i>
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_BROWSE_IMAGE_FILE'); ?>
            </button>

            <?php if ($this->acl->get('upload_image')) { ?>
            <!-- <span class="eb-plupload-btn"> -->
                <button type="button" class="btn btn-sm btn-primary" data-plupload-browse-button>
                    <i class="fa fa-upload"></i>
                    <?php echo JText::_('COM_EASYBLOG_BLOCKS_UPLOAD_IMAGE_FILE'); ?>
                </button>
            <!-- </span> -->
            <?php } ?>
        </div>

        <div class="eb-composer-place-url" data-eb-image-url-form>
            <div class="col-cell">
                <input type="text" class="form-control" style="max-width: 250px;" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_IMAGE_BLOCK_URL_PLACEHOLDER');?>" data-eb-image-url-textbox>
            </div>
            <div class="col-cell pl-10">
                <button type="button" class="btn btn-sm btn-primary" data-eb-image-url-add>
                    <?php echo JText::_('COM_EASYBLOG_ADD'); ?>
                </button>

                <label class="btn btn-sm btn-danger" for="use-url" data-eb-image-url-cancel>
                    <?php echo JText::_('COM_EASYBLOG_CANCEL'); ?>
                </label>
            </div>
        </div>


        <?php echo $this->output('site/composer/progress'); ?>

        <?php echo $this->output('site/composer/blocks/error'); ?>
    </div>
</div>
