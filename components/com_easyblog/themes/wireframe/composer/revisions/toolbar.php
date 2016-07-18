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
    <div class="eb-composer-toolbar-set row-table is-primary" data-name="revisions">
        <div class="col-cell toolbar-left">
            <div class="eb-composer-toolbar-group row-table">
                <div class="eb-composer-toolbar-item col-cell cell-tight join-right">
                    <i class="fa fa-code-fork"></i>
                </div>
                <div class="eb-composer-toolbar-item col-cell">
                    <strong><?php echo JText::sprintf('COM_EASYBLOG_COMPARED_COPY', $target->getTitle());?></strong>
                    <div class="text-muted"><?php echo $target->getPost()->getAuthor()->getName(); ?> &middot; <?php echo $target->getPost()->getCreationDate()->toFormat(JText::_('DATE_FORMAT_LC1')); ?></div>
                </div>
            </div>
        </div>
        <div class="col-cell cell-tight toolbar-right">
            <div class="eb-composer-toolbar-group row-table">
                <div class="eb-composer-toolbar-item is-button col-cell eb-mm-search-toggle-button" data-revisions-close-comparison>
                    <i class="fa fa-times-circle"></i>
                    <span><?php echo JText::_('COM_EASYBLOG_CLOSE'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>