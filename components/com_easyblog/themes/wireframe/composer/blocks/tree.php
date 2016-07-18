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
<div class="eb-composer-fieldset eb-blocks-tree-field tree-minimal" data-eb-blocks-tree-field>
    <div class="eb-block row-table" data-eb-block>
        <div class="col-cell cell-tight eb-block-icon">
            <i data-eb-block-icon></i>
        </div>
        <div class="col-cell cell-ellipse">
            <div class="eb-block-title" data-eb-block-title><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS');?></div>
            <div class="eb-block-stat text-muted" data-eb-block-stat>
                <span class="eb-block-level"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_LEVEL'); ?> <span class="eb-block-level-count" data-eb-block-level-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ONE');?></span></span>
                <span class="eb-block-child"> &middot; <span class="eb-block-child-count" data-eb-block-child-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ZERO');?></span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_CHILD_BLOCKS'); ?></span>
            </div>
        </div>
        <div class="col-cell cell-tight">
            <button type="button" class="btn btn-success btn-sm eb-blocks-tree-toggle-button" data-eb-blocks-tree-toggle-button><span class="text-show-tree"><i class="fa fa-plus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SHOW_FULL_TREE'); ?></span><span class="text-hide-tree"><i class="fa fa-minus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_HIDE_FULL_TREE'); ?></span></button>
        </div>
    </div>
    <div class="eb-blocks-tree" data-eb-blocks-tree>
        <div class="eb-list">
            <div class="eb-list-item-group" data-eb-blocks-tree-item-group>
            </div>
        </div>
    </div>
    <div class="hide" data-eb-blocks-tree-item-template>
        <div class="eb-list-item eb-blocks-tree-item" data-eb-blocks-tree-item data-type="" data-uid="">
            <i class="" data-eb-blocks-tree-item-icon></i>
            <strong data-eb-blocks-tree-item-title></strong>
        </div>
    </div>
</div>
