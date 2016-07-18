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
<div class="eb-mm-folder <?php echo empty($folder->total) ? 'is-empty' : ''; ?> <?php echo $place->acl->canUploadItem ? 'can-upload' : ''; ?>"
     data-eb-mm-folder
     data-key="<?php echo $folder->key; ?>"
     <?php if ($place->acl->canUploadItem) { ?>
     data-plupload
     data-plupload-browse-button="<?php echo $browseButtonId; ?>"
     data-plupload-url="<?php echo $uploadUrl; ?>"
     data-plupload-max-file-size="<?php echo $this->config->get('main_upload_image_size'); ?>mb"
     data-plupload-extensions="<?php echo $this->config->get('main_media_extensions'); ?>"
     <?php } ?>
    >

    <?php echo $this->output('site/mediamanager/folder/toolbar'); ?>
    <?php echo $this->output('site/mediamanager/folder/viewport'); ?>
    <?php echo $this->output('site/mediamanager/folder/upload'); ?>
</div>
