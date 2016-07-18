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

$folders = $folder->contents['folder'];
?>
<?php if (count($folders) > 0) { ?>
<div class="eb-mm-tree" data-eb-mm-tree>
    <?php foreach($folders as $item) { ?>
    <div class="eb-mm-tree-item" data-eb-mm-tree-item data-key="<?php echo $item->key; ?>">
        <span>
            <i class="fa fa-angle-right"></i>
            <i class="fa fa-angle-down"></i>
            <i class="fa fa-folder"></i>
            <i class="fa fa-circle-o-notch fa-spin"></i>
            <span><?php echo $item->title; ?></span>
        </span>
    </div>
    <?php } ?>
</div>
<?php } else { ?>
<div class="eb-mm-tree is-empty" data-eb-mm-tree>
    <div><?php echo JText::_("COM_EASYBLOG_COMPOSER_MM_NO_FOLDERS_HERE"); ?></div>
</div>
<?php } ?>