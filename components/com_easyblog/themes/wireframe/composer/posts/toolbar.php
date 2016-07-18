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
<div class="eb-composer-toolbar">
    <div>
        <div class="eb-composer-toolbar-set row-table is-primary" data-name="posts">
            <div class="col-cell toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell">
                        <i class="fa fa-file-text"></i>
                        <strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_TITLE_POSTS'); ?></strong>
                    </div>
                </div>
            </div>
            <div class="col-cell cell-tight">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item is-button col-cell" data-eb-composer-posts-search-toggle-button>
                        <i class="fa fa-search"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_SEARCH_BUTTON');?></span>
                    </div>
                    <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-posts-close-button" data-eb-posts-close-button>
                        <i class="fa fa-times-circle"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_CLOSE_BUTTON');?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="eb-composer-toolbar-set row-table" data-name="posts-search">
            <div class="col-cell toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell cell-tight join-right">
                        <i class="fa fa-search"></i>
                    </div>
                    <div class="eb-composer-toolbar-item col-cell">
                        <input type="text" class="form-control" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_SEARCH_POSTS');?>" data-eb-composer-posts-search-textfield />
                    </div>
                </div>
            </div>
            <div class="col-cell cell-tight toolbar-right">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item is-button col-cell eb-mm-search-toggle-button" data-eb-composer-posts-search-cancel-button>
                        <i class="fa fa-close"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_CANCEL_BUTTON');?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>