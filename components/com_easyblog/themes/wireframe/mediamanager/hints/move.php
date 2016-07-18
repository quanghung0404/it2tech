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
<div class="eb-mm-hint hint-move">

    <div class="eb-composer-toolbar">
    <div>
        <div class="eb-composer-toolbar-set row-table" data-name="media-loading">
            <div class="col-cell cell-tight toolbar-left">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell is-button eb-mm-folder-back-button" data-eb-mm-open-button>
                        <i class="fa fa-chevron-left"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_BACK_BUTTON'); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-cell toolbar-center">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item col-cell cell-ellipse">
                        <strong><?php echo JText::_('COM_EASYBLOG_MM_MOVE_INFO'); ?></strong>
                        <div class="text-muted" data-eb-mm-move-filename></div>
                    </div>
                </div>
            </div>
            <div class="col-cell cell-tight toolbar-right">
                <div class="eb-composer-toolbar-group row-table">
                    <div class="eb-composer-toolbar-item is-button col-cell" data-eb-mm-file-move-button data-key>
                        <i class="fa fa-exchange"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_MM_MOVE');?></span>
                    </div>
                    <div class="eb-composer-toolbar-item is-button hide-label col-cell eb-mm-close-button" data-eb-mm-close-button>
                        <i class="fa fa-times-circle"></i>
                        <span><?php echo JText::_('COM_EASYBLOG_MM_CLOSE_BUTTON'); ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <div class="eb-composer-viewport" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>

            <?php echo $this->output('site/mediamanager/foldertree'); ?>
        </div>
    </div>

</div>
