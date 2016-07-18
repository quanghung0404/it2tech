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
<div class="eb-composer-fieldset" data-name="category">

    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_CATEGORY'); ?></strong>
        <small><span data-eb-composer-category-count>0</span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_CATEGORY_SELECTED');?></small>
    </div>

    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-category">
            <div class="eb-composer-category-list">
                <div class="eb-composer-category-viewport" data-eb-composer-category-viewport>
                    <div class="eb-composer-category-tree" data-eb-composer-category-tree></div>
                </div>
            </div>
            <div class="eb-composer-category-search">
                <i class="fa fa-search"></i>
                <input type="text" class="eb-composer-category-search-textfield" data-eb-composer-category-search-textfield placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH_CATEGORY', true);?>"/>
            </div>
        </div>

        <textarea style="display:none;" data-eb-composer-category-jsondata><?php echo json_encode($categories); ?></textarea>
    </div>

    <div class="hide" data-category-item-group-template>
        <div class="eb-composer-category-item-group" data-eb-composer-category-item-group="$" data-id="">
            <div class="eb-composer-category-item-group-header" data-eb-composer-category-item-group-header>
                <i class="fa fa-angle-left"></i> <span data-title></span>
            </div>
            <div class="eb-composer-category-item-group-body">
                <div class="eb-composer-category-item-group-viewport" data-eb-composer-category-item-group-viewport></div>
            </div>
        </div>
    </div>

    <div class="hide" data-category-item-template>
        <div class="eb-composer-category-item" data-eb-composer-category-item data-id="">
            <b data-eb-composer-category-item-checkbox>
                <b>
                    <i class="fa fa-check"></i><em class="fa fa-square"></em>
                </b>
            </b>
            <span data-title></span> <small><?php echo JText::_('COM_EASYBLOG_COMPOSER_CATEGORY_IS_PRMARY'); ?></small>
            <div class="eb-composer-category-item-count" data-eb-composer-category-item-count><span></span><i class="fa fa-angle-right"></i></div>
        </div>
    </div>
</div>