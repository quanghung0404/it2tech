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
<div class="eb-composer-toolbar-set row-table" data-name="block-actions">

    <div class="col-cell cell-tight toolbar-left">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item is-button col-cell eb-blocks-parent-button" data-eb-blocks-parent-button>
                <i class="fa fa-level-up"></i>
                <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_UP_BUTTON');?></span>
            </div>
        </div>
    </div>

    <div class="col-cell toolbar-center mobile-hide">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item col-cell">
                <i class="fa fa-cube" data-eb-block-icon></i>
                <span class="eb-block-title" data-eb-block-title><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_BLOCKS_BUTTON');?></span>
            </div>
        </div>
    </div>

    <div class="col-cell cell-tight toolbar-right">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item is-button col-cell" data-eb-blocks-remove-button>
                <i class="fa fa-trash"></i>
                <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_REMOVE_BUTTON');?></span>
            </div>

            <div class="eb-composer-toolbar-item is-button col-cell" data-eb-blocks-move-button>
                <i class="fa fa-arrows"></i>
                <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MOVE_BUTTON');?></span>
            </div>

            <div class="eb-composer-toolbar-item is-button col-cell" data-eb-blocks-done-button>
                <i class="fa fa-check"></i>
                <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_DONE_EDITING_BUTTON');?></span>
            </div>
        </div>
    </div>

</div>

