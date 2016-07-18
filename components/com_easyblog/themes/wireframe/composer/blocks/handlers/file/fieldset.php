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
<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_SHOW_EXTENSION_ICON'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="text-center">
            <?php echo $this->html('grid.boolean', 'showicon', 1, 'showicon', 'data-file-fieldset-icon'); ?>
        </div>
    </div>
</div>

<div class="eb-composer-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_FILE_SHOW_FILE_SIZE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="text-center">
            <?php echo $this->html('grid.boolean', 'showsize', 1, 'showsize', 'data-file-fieldset-size'); ?>
        </div>
    </div>
</div>
