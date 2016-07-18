<?php
/**
* @package    EasyBlog
* @copyright  Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-mm-file type-<?php echo $file->type; ?><?php echo empty($file->extension) ? '' : ' ext-' . $file->extension; ?>"
     data-eb-mm-file
     data-type="<?php echo $file->type;?>"
     data-key="<?php echo $file->key; ?>">
    <i class="<?php echo $file->icon; ?>"
       <?php if (isset($file->thumbnail)) { ?>
           style="background-image: url('<?php echo $file->thumbnail; ?>');"
       <?php } ?>></i>
    <div>
      <span><?php echo $file->title; ?></span>
    </div>
    <?php if ($file->type=='folder') { ?>
        <b class="fa fa-angle-right"></b>
    <?php } ?>
</div>