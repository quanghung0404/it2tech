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
<div class="eb-mm" data-eb-mm-frame
     data-mm-uploader-url="<?php echo $uploadUrl; ?>"
     data-mm-uploader-max-file-size="<?php echo $this->config->get('main_upload_image_size'); ?>mb"
     data-mm-uploader-extensions="<?php echo $this->config->get('main_media_extensions'); ?>">

    <div class="eb-mm-viewport">
        <?php echo $this->output('site/mediamanager/messages'); ?>
        <div class="pageslide eb-mm-pages" data-pageslide data-eb-mm-pages>
            <div class="pageslide-viewport eb-mm-foldergroup" data-eb-mm-foldergroup>
                <div class="pageslide-page">
                    <?php echo $this->output('site/mediamanager/places'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="eb-mm-infobox is-collapsed" data-eb-mm-infobox></div>

    <?php echo $this->output('site/mediamanager/hints'); ?>

    <div class="hide" data-eb-mm-upload-template>
        <div class="eb-mm-file type-upload is-new"
             data-eb-mm-file
             data-id="">
            <i data-eb-mm-upload-thumbnail></i>
            <div>
                <span data-eb-mm-upload-name></span>
                <small data-eb-mm-upload-size></small>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" data-eb-mm-upload-progress-bar></div>
                </div>
            </div>
        </div>
    </div>
</div>