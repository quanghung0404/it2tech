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
<div class="eb-composer-toolbar-set row-table is-primary" data-name="blocks">
    <div class="col-cell cell-tight toolbar-left">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item col-cell eb-blocks-menu-hint">
                <strong>
                    <i class="fa fa-hand-o-up"></i>
                    <span class="mobile-hide"><?php echo JText::_('COM_EASYBLOG_COMPOSER_TIPS_SELECT_BLOCK');?></span>
                    <span class="mobile-show"><?php echo JText::_('COM_EASYBLOG_COMPOSER_TIPS_TAP_SELECT_BLOCK');?></span>
                </strong>
            </div>
        </div>
    </div>
    <div class="col-cell toolbar-center">&nbsp;</div>
    <div class="col-cell cell-tight toolbar-right">
        <div class="eb-composer-toolbar-group row-table">
            <div class="eb-composer-toolbar-item is-button col-cell eb-blocks-search-toggle-button" data-eb-blocks-search-toggle-button>
                <i class="fa fa-search"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH');?></span>
            </div>
            <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-blocks-close-button" data-eb-blocks-close-button>
                <i class="fa fa-times-circle"></i>
                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CLOSE');?></span>
            </div>
        </div>
    </div>
</div>
