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
<div class="eb-composer-toolbar-set row-table" data-name="blocks-search">
    <div class="col-cell toolbar-left">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item col-cell cell-tight join-right">
                <i class="fa fa-search"></i>
            </div>
            <div class="eb-composer-toolbar-item col-cell">
                <input type="text" class="form-control eb-blocks-search-input" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SEARCH');?>" data-eb-blocks-search-input />
            </div>
        </div>
    </div>
    <div class="col-cell cell-tight toolbar-right">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item is-button col-cell eb-mm-search-toggle-button" data-eb-blocks-search-toggle-button>
                <i class="fa fa-close"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CANCEL');?></span>
            </div>
        </div>
    </div>
</div>