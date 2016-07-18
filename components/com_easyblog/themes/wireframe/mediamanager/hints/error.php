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
?><div class="eb-mm-hint hint-error">

    <div class="eb-composer-toolbar">
    <div>

        <div class="eb-composer-toolbar-set row-table" data-name="media-loading">
            <div class="col-cell cell-tight toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell is-button eb-mm-folder-back-button" data-eb-mm-folder-back-button>
                        <i class="fa fa-chevron-left"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_BACK_BUTTON');?></span>
                    </div>
                </div>
            </div>
            <div class="col-cell toolbar-center">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell cell-ellipse">
                        <?php // TODO: This should show the place that we are going to ?>
                        <strong><?php echo JText::_('COM_EASYBLOG_MM_ERROR_HEADING');?></strong>
                    </div>
                </div>
            </div>
            <div class="col-cell cell-tight toolbar-right">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-mm-close-button" data-eb-mm-close-button>
                        <i class="fa fa-times-circle"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_CLOSE');?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <div class="eb-composer-viewport" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>
            <div class="eb-hint layout-overlay style-gray">
                <div>
                    <i class="eb-hint-icon fa fa-warning"></i>
                    <span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_MM_ERROR_LOADING_HINT');?></span>
                </div>
            </div>
        </div>
    </div>

</div>
