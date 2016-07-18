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
<div class="eb-composer-art eb-composer-blogimage <?php echo !empty($post->image) ? " has-image" : ""; ?>"
    data-eb-composer-blogimage-placeholder
    data-eb-composer-art
    data-id="cover"
    data-key="_cG9zdA--"
    data-type="image"
    data-plupload-multi-selection="0"
>
    <form data-eb-composer-form="blogimage">

        <div class="eb-composer-art-workarea" data-eb-composer-blogimage-workarea data-plupload-drop-element>
            <div class="eb-composer-art-placeholder">
                <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_COMPOSER_DROP_BLOG_IMAGE_HERE');?></b>

                <div class="eb-composer-art-actions">
                    <button type="button" class="btn btn-default"
                        data-eb-mm-browse-button
                        data-eb-mm-start-uri="_cG9zdA--"
                        data-eb-mm-filter="image"
                        data-eb-mm-browse-place="local"
                        data-eb-mm-browse-type="cover"
                    >
                        <i class="fa fa-photo"></i> <?php echo JText::_('COM_EASYBLOG_BLOCKS_BROWSE_IMAGE_FILE'); ?>
                    </button>

                    <span class="eb-plupload-btn">
                        <button data-eb-composer-blogimage-browse-button="" class="btn btn-primary" data-plupload-browse-button>
                            <i class="fa fa-upload"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_UPLOAD_BLOG_IMAGE');?>
                        </button>
                    </span>
                </div>
            </div>

            <div class="eb-composer-blogimage-uploader">
                <b><b></b></b>
            </div>

            <div class="eb-composer-art-remove-button" data-eb-composer-blogimage-remove-button>
                <i class="fa fa-close"></i><span>&nbsp; <?php echo JText::_('COM_EASYBLOG_COMPOSER_REMOVE');?></span>
            </div>

            <?php echo $this->output('site/composer/progress'); ?>
        </div>

        <div class="eb-composer-blogimage-image" data-eb-composer-blogimage-image
            <?php if ($post->image) { ?>
            style="background-image: url('<?php echo $post->getImage();?>');"
            <?php } ?>
        >
        </div>

        <input type="hidden" name="image" value="<?php echo $post->image;?>" data-eb-composer-blogimage-value />
    </form>

    <?php echo $this->output('site/composer/blocks/error'); ?>
</div>
