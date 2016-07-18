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
<div class="eb-composer-placeholder eb-composer-placeholder-file text-center"
    data-eb-composer-file-placeholder
    data-key="_cG9zdA--"
    data-type="file"
    contenteditable="false"
    data-plupload-multi-selection="0"
>

    <div data-plupload-drop-element>
        <i class="eb-composer-placeholder-icon fa fa-file"></i>
        <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_PLACEHOLDER_TITLE');?></b>
        <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_PLACEHOLDER_DESC');?></p>

        <button type="button" class="btn btn-default"
            data-eb-mm-browse-button
            data-eb-mm-start-uri="_cG9zdA--"
            data-eb-mm-filter="file"
        >
            <i class="fa fa-file-o"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BROWSE_FOR_FILE'); ?>
        </button>

        <span class="eb-plupload-btn">
            <button type="button" class="btn btn-primary" data-plupload-browse-button>
                <i class="fa fa-upload"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_FILE');?>
            </button>
        </span>

        <?php echo $this->output('site/composer/progress'); ?>

        <?php echo $this->output('site/composer/blocks/error'); ?>
    </div>
</div>
