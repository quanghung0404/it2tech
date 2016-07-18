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
<div class="eb-composer-placeholder eb-gallery-upload-placeholder text-center"
    data-key="_cG9zdA--"
    data-type="image"
    contenteditable="false">

<div class="eb-gallery is-empty">

    <div class="eb-gallery-stage" data-plupload-drop-element>
        <div class="eb-gallery-viewport">
            <div class="eb-gallery-item is-placeholder">
                <div class="eb-composer-placeholder-content" >
                    <i class="eb-composer-placeholder-icon fa fa-image"></i>
                    <b class="eb-composer-placeholder-title"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_PREVIEW_TITLE');?></b>
                    <p class="eb-composer-placeholder-brief"><?php echo JText::_('COM_EASYBLOG_BLOCKS_GALLERY_PREVIEW_INFO');?></p>
                    <p data-eb-file-error class="hide eb-composer-placeholder-error text-error"><?php echo JText::_('COM_EASYBLOG_INVALID_FILE');?></p>
                    <span class="eb-plupload-btn">
                        <button type="button" class="btn btn-sm btn-primary" data-plupload-browse-button>
                            <?php echo JText::_('COM_EASYBLOG_COMPOSER_SELECT_A_FILE');?>
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="eb-gallery-button eb-gallery-next-button"><i class="fa fa-chevron-right"></i></div>
        <div class="eb-gallery-button eb-gallery-prev-button"><i class="fa fa-chevron-left"></i></div>
    </div>

    <div class="eb-gallery-menu">
        <div class="eb-gallery-menu-item is-placeholder active" data-id="placeholder">
            <div></div>
        </div>
    </div>

</div>

<?php echo $this->output('site/composer/progress'); ?>

</div>
