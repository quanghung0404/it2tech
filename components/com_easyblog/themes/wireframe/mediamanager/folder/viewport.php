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
    <div class="eb-composer-viewport-content" data-scrolly-viewport
     <?php if ($place->acl->canUploadItem) { ?>
     data-plupload-drop-element="<?php echo $dropElementId; ?>"
     <?php } ?>
     >
        <div class="eb-mm-folder-content-panel" data-eb-mm-folder-content-panel>

            <?php foreach ($folder->contents as $type => $files) { ?>
                <?php echo $this->output('site/mediamanager/filegroup', array('type' => $type, 'files' => $files)); ?>
            <?php } ?>
        </div>

        <?php echo $this->output('site/mediamanager/hints/upload'); ?>
        <?php echo $this->output('site/mediamanager/hints/empty'); ?>
        <?php echo $this->output('site/mediamanager/hints/notfound'); ?>

    </div>
</div>