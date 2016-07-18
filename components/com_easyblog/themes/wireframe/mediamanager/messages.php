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
<div class="eb-mm-messages">
    <div class="eb-mm-msg-select row-table">
        <div class="col-cell">
            <i class="fa fa-info-circle"></i>
            <span>Select a file from media manager</span>
        </div>
    </div>
    <div class="eb-mm-msg-move row-table" data-eb-mm-selecting>
        <div class="col-cell file-message">
            <i class="fa fa-check"></i>
            <span ><?php echo JText::_('COM_EASYBLOG_MM_FILE_MOVED');?></span>
        </div>
        <div class="col-cell folder-message">
            <i class="fa fa-check"></i>
            <span><?php echo JText::_('COM_EASYBLOG_MM_FOLDER_MOVED');?></span>
        </div>
        <div class="col-cell cell-tight" data-eb-mm-go-to-folder>
            <button class="btn btn-xs btn-default">
                <i class="fa fa-external-link-square"></i>
                <?php echo JText::_('COM_EASYBLOG_MM_GO_TO_FOLDER');?>
            </button>
        </div>
        <div class="col-cell cell-tight" data-eb-mm-close-message-button>
            &nbsp;
            <button class="btn btn-xs btn-default">
                <i class="fa fa-close"></i>
                <?php echo JText::_('Dismiss'); ?>
            </button>
        </div>
    </div>
</div>