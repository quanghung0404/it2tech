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
<div class="eb-composer-viewport" data-scrolly="y">
    <div class="eb-composer-viewport-content" data-scrolly-viewport>

        <div class="eb-composer-posts-item-group posts-result" data-eb-composer-posts-result></div>

        <div class="eb-hint hint-loading layout-overlay style-gray">
            <div>
                <i class="eb-hint-icon"><span class="eb-loader-o"></span></i>
                <span class="eb-hint-text"><?php echo JText::_('Loading...');?></span>
            </div>
        </div>

        <div class="eb-hint hint-empty layout-overlay style-gray">
            <div>
                <i class="eb-hint-icon fa fa-file-text"></i>
                <span class="eb-hint-text">
                    <?php echo JText::_('COM_EASYBLOG_COMPOSER_NO_POSTS_FOUND'); ?>
                </span>
            </div>
        </div>

    </div>
</div>