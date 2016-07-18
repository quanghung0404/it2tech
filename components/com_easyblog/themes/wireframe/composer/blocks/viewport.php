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
<div class="eb-composer-viewport" data-scrolly="y">
    <div class="eb-composer-viewport-content" data-scrolly-viewport>

        <?php echo $this->output('site/composer/blocks/categories'); ?>

        <div class="eb-hint hint-empty layout-overlay style-gray">
            <div>
                <i class="eb-hint-icon fa fa-frown-o"></i>
                <span class="eb-hint-text">
                    <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NOT_FOUND'); ?>
                </span>
            </div>
        </div>
    </div>
</div>