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
<div class="eb-thumbs is-empty col-4">
    <div class="eb-thumbs-col"></div>
    <div class="eb-thumbs-col"></div>
    <div class="eb-thumbs-col"></div>
    <div class="eb-thumbs-col"></div>
</div>
<div class="eb-composer-placeholder eb-thumbs-upload-placeholder text-center"
    data-key="_cG9zdA--"
    data-type="image"
    contenteditable="false">
    <div class="eb-composer-placeholder-content" data-plupload-drop-element>
        <i class="eb-composer-placeholder-icon fa fa-th"></i>
        <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS');?></b>
        <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS_INFO');?></p>

        <p data-eb-file-error class="hide eb-composer-placeholder-error text-error"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_THUMBNAILS_INVALID_FILE');?></p>

        <span class="eb-plupload-btn">
            <button type="button" class="btn btn-sm btn-primary" data-plupload-browse-button>
                <?php echo JText::_('COM_EASYBLOG_COMPOSER_SELECT_A_FILE');?>
            </button>
        </span>

        <button type="button" class="btn btn-sm btn-default eb-thumbs-add-thumbnail-button" data-plupload-browse-button>
            <i class="fa fa-plus"></i> <?php echo JText::_('COM_EASYBLOG_ADD_THUMBNAIL_BUTTON');?>
        </button>

        <?php echo $this->output('site/composer/progress'); ?>
    </div>
</div>
