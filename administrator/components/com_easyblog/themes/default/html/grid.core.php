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
<a class="eb-state-<?php echo $class;?> badge"
    style="cursor:default;"
    href="javascript:void(0);"
    data-eb-provide="tooltip"
    data-original-title="<?php echo $tooltip;?>"
    data-placement="bottom"
	<?php echo !$allowed ? ' disabled="disabled"' : '';?>
>
    <?php if ($class == 'core') { ?>
    <i class="fa fa-check"></i>
    <?php } ?>

    <?php if ($class == 'no-core') { ?>
    <i class="fa fa-times"></i>
    <?php } ?>
</a>

