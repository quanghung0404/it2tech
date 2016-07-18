<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-alert row-table alert alert-<?php echo $class;?>">
    <?php if ($class == 'success') { ?>
    <div class="col-cell cell-tight cell-sign">
        <i class="fa fa-check-circle"></i>
    </div>
    <?php } ?>

    <?php if ($class == 'error') { ?>
    <div class="col-cell cell-tight cell-sign">
        <i class="fa fa-times-circle"></i>
    </div>
    <?php } ?>

    <div class="col-cell cell-text"><?php echo $content; ?></div>

    <div class="col-cell cell-tight cell-close" data-bp-dismiss="alert">
        <b class="fa fa-times"></b>
     </div>
</div>
