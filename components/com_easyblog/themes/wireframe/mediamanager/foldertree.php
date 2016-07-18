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
<div class="eb-mm-foldertree" data-eb-mm-foldertree>
    <div class="eb-mm-tree" data-eb-mm-tree>

        <?php foreach ($places as $place) { ?>
            <?php if (!EBMM::isMoveablePlace($place->id)) { continue; } ?>

            <div class="eb-mm-tree-item" data-eb-mm-tree-item data-key="<?php echo $place->key; ?>">
                <span>
                    <i class="fa fa-folder"></i>
                    <i class="fa fa-circle-o-notch fa-spin"></i>
                    <span><?php echo $place->title; ?></span>
                </span>
            </div>
        <?php } ?>

    </div>
</div>